import { Request, Response, NextFunction } from "express";
import { verifyAccessToken, TokenPayload } from "../lib/jwt";
import { createError } from "./errorHandler";

declare global {
  namespace Express {
    interface Request {
      user?: TokenPayload;
    }
  }
}

export const ROLE_RANK: Record<string, number> = {
  viewer: 0,
  editor: 1,
  super_admin: 2,
};

export function verifyToken(req: Request, _res: Response, next: NextFunction): void {
  const auth = req.headers.authorization;
  if (!auth?.startsWith("Bearer ")) {
    return next(createError("Unauthorized", 401));
  }
  const token = auth.slice(7);
  try {
    req.user = verifyAccessToken(token);
    next();
  } catch {
    next(createError("Unauthorized", 401));
  }
}

export function requireRole(min: string) {
  return (req: Request, _res: Response, next: NextFunction): void => {
    if (!req.user) return next(createError("Unauthorized", 401));
    const userRank = ROLE_RANK[req.user.role] ?? -1;
    const minRank = ROLE_RANK[min] ?? 999;
    if (userRank < minRank) return next(createError("Forbidden", 403));
    next();
  };
}
