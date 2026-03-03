import { Router, Request, Response, NextFunction } from "express";
import { prisma } from "../lib/prisma";
import { requireRole } from "../middleware/auth";
import { createError } from "../middleware/errorHandler";

const router = Router();

router.get("/", requireRole("editor"), async (_req: Request, res: Response, next: NextFunction) => {
  try {
    const nodes = await prisma.node.findMany({ orderBy: { sortOrder: "asc" } });
    res.json(nodes);
  } catch (err) {
    next(err);
  }
});

router.post("/", requireRole("editor"), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const node = await prisma.node.create({ data: req.body });
    res.status(201).json(node);
  } catch (err) {
    next(err);
  }
});

router.put("/:id", requireRole("editor"), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const node = await prisma.node.update({ where: { id: req.params.id as string }, data: req.body });
    res.json(node);
  } catch (err) {
    next(err);
  }
});

router.delete("/:id", requireRole("super_admin"), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const id = req.params.id as string;
    const existing = await prisma.node.findUnique({ where: { id } });
    if (!existing) return next(createError("Not found", 404));
    await prisma.node.delete({ where: { id } });
    res.status(204).send();
  } catch (err) {
    next(err);
  }
});

router.patch("/:id/status", requireRole("editor"), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { status } = req.body as { status?: string };
    if (!status) return next(createError("status required", 400));
    const node = await prisma.node.update({
      where: { id: req.params.id as string },
      data: { status: status as "published" | "draft" },
    });
    res.json(node);
  } catch (err) {
    next(err);
  }
});

export default router;
