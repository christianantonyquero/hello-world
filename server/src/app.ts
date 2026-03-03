import express from "express";
import cookieParser from "cookie-parser";
import publicRouter from "./routes/public";
import authRouter from "./routes/auth";
import adminNodesRouter from "./routes/adminNodes";
import adminUsersRouter from "./routes/adminUsers";
import { verifyToken } from "./middleware/auth";
import { errorHandler, createError } from "./middleware/errorHandler";

const app = express();

app.use(express.json());
app.use(cookieParser());

app.use("/api/v1", publicRouter);
app.use("/auth", authRouter);
app.use("/admin/nodes", verifyToken, adminNodesRouter);
app.use("/admin/users", verifyToken, adminUsersRouter);

app.use((_req, _res, next) => next(createError("Not found", 404)));
app.use(errorHandler);

export default app;
