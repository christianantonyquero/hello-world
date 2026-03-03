import jwt from "jsonwebtoken";

export interface TokenPayload {
  sub: string;
  email: string;
  role: string;
}

const accessSecret = () => {
  const s = process.env.JWT_ACCESS_SECRET;
  if (!s) throw new Error("JWT_ACCESS_SECRET not set");
  return s;
};

const refreshSecret = () => {
  const s = process.env.JWT_REFRESH_SECRET;
  if (!s) throw new Error("JWT_REFRESH_SECRET not set");
  return s;
};

export function signAccessToken(payload: TokenPayload): string {
  return jwt.sign(payload, accessSecret(), { expiresIn: "15m" });
}

export function verifyAccessToken(token: string): TokenPayload {
  return jwt.verify(token, accessSecret()) as TokenPayload;
}

export function signRefreshToken(payload: TokenPayload): string {
  return jwt.sign(payload, refreshSecret(), { expiresIn: "7d" });
}

export function verifyRefreshToken(token: string): TokenPayload {
  return jwt.verify(token, refreshSecret()) as TokenPayload;
}
