import { Router, Request, Response, NextFunction } from "express";
import bcrypt from "bcryptjs";
import { prisma } from "../lib/prisma";
import { requireRole } from "../middleware/auth";
import { createError } from "../middleware/errorHandler";

const router = Router();
const superAdmin = requireRole("super_admin");

const USER_SELECT = {
  id: true,
  createdAt: true,
  updatedAt: true,
  email: true,
  name: true,
  role: true,
  isActive: true,
  lastLogin: true,
};

router.get("/", superAdmin, async (_req: Request, res: Response, next: NextFunction) => {
  try {
    const users = await prisma.user.findMany({ select: USER_SELECT });
    res.json(users);
  } catch (err) {
    next(err);
  }
});

router.post("/", superAdmin, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { email, password, name, role } = req.body as {
      email?: string;
      password?: string;
      name?: string;
      role?: string;
    };
    if (!email || !password || !name) return next(createError("email, password, name required", 400));
    const passwordHash = await bcrypt.hash(password, 12);
    const user = await prisma.user.create({
      data: { email, passwordHash, name, role: (role ?? "viewer") as "super_admin" | "editor" | "viewer" },
      select: USER_SELECT,
    });
    res.status(201).json(user);
  } catch (err) {
    next(err);
  }
});

router.put("/:id", superAdmin, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { email, name, role, isActive } = req.body as {
      email?: string;
      name?: string;
      role?: string;
      isActive?: boolean;
    };
    const data: Record<string, unknown> = {};
    if (email !== undefined) data.email = email;
    if (name !== undefined) data.name = name;
    if (role !== undefined) data.role = role;
    if (isActive !== undefined) data.isActive = isActive;
    const user = await prisma.user.update({ where: { id: req.params.id as string }, data, select: USER_SELECT });
    res.json(user);
  } catch (err) {
    next(err);
  }
});

router.delete("/:id", superAdmin, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const id = req.params.id as string;
    const existing = await prisma.user.findUnique({ where: { id } });
    if (!existing) return next(createError("Not found", 404));
    await prisma.user.delete({ where: { id } });
    res.status(204).send();
  } catch (err) {
    next(err);
  }
});

export default router;
