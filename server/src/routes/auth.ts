import { Router, Request, Response, NextFunction } from "express";
import bcrypt from "bcryptjs";
import { prisma } from "../lib/prisma";
import { signAccessToken, signRefreshToken, verifyRefreshToken, TokenPayload } from "../lib/jwt";
import { createError } from "../middleware/errorHandler";

const router = Router();

const COOKIE_NAME = "refreshToken";
const COOKIE_MAX_AGE = 7 * 24 * 60 * 60 * 1000;

const cookieOpts = {
  httpOnly: true,
  secure: process.env.NODE_ENV === "production",
  sameSite: "strict" as const,
  maxAge: COOKIE_MAX_AGE,
};

router.post("/login", async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { email, password } = req.body as { email?: string; password?: string };
    if (!email || !password) return next(createError("email and password required", 400));

    const user = await prisma.user.findUnique({ where: { email } });
    if (!user || !user.isActive) return next(createError("Invalid credentials", 401));

    const valid = await bcrypt.compare(password, user.passwordHash);
    if (!valid) return next(createError("Invalid credentials", 401));

    await prisma.user.update({ where: { id: user.id }, data: { lastLogin: new Date() } });

    const payload: TokenPayload = { sub: user.id, email: user.email, role: user.role };
    const accessToken = signAccessToken(payload);
    const refreshToken = signRefreshToken(payload);

    res.cookie(COOKIE_NAME, refreshToken, cookieOpts);
    res.json({ accessToken });
  } catch (err) {
    next(err);
  }
});

router.post("/refresh", (req: Request, res: Response, next: NextFunction) => {
  try {
    const token = req.cookies?.[COOKIE_NAME] as string | undefined;
    if (!token) return next(createError("No refresh token", 401));

    const payload = verifyRefreshToken(token);
    const newPayload: TokenPayload = { sub: payload.sub, email: payload.email, role: payload.role };
    const accessToken = signAccessToken(newPayload);
    res.json({ accessToken });
  } catch {
    next(createError("Invalid refresh token", 401));
  }
});

router.post("/logout", (_req: Request, res: Response) => {
  res.clearCookie(COOKIE_NAME);
  res.json({ ok: true });
});

export default router;
