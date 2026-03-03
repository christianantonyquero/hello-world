import { Router, Request, Response, NextFunction } from "express";
import { prisma } from "../lib/prisma";
import { createError } from "../middleware/errorHandler";

const router = Router();

router.get("/nodes", async (_req: Request, res: Response, next: NextFunction) => {
  try {
    const nodes = await prisma.node.findMany({
      where: { status: "published", layerDepth: 0 },
      orderBy: { sortOrder: "asc" },
    });
    res.json(nodes);
  } catch (err) {
    next(err);
  }
});

router.get("/nodes/:slug", async (req: Request, res: Response, next: NextFunction) => {
  try {
    const node = await prisma.node.findUnique({ where: { slug: req.params.slug as string } });
    if (!node || node.status !== "published") return next(createError("Not found", 404));
    res.json(node);
  } catch (err) {
    next(err);
  }
});

router.get("/nodes/:slug/children", async (req: Request, res: Response, next: NextFunction) => {
  try {
    const parent = await prisma.node.findUnique({ where: { slug: req.params.slug as string } });
    if (!parent || parent.status !== "published") return next(createError("Not found", 404));
    const children = await prisma.node.findMany({
      where: { parentId: parent.id, status: "published" },
      orderBy: { sortOrder: "asc" },
    });
    res.json(children);
  } catch (err) {
    next(err);
  }
});

router.get("/flights", (_req: Request, res: Response) => {
  res.status(501).json({ error: "Not implemented" });
});

router.get("/hotels", (_req: Request, res: Response) => {
  res.status(501).json({ error: "Not implemented" });
});

export default router;
