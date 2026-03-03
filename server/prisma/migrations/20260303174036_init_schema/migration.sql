-- CreateEnum
CREATE TYPE "SceneType" AS ENUM ('map', 'grid', 'article', 'gallery');

-- CreateEnum
CREATE TYPE "NodeStatus" AS ENUM ('published', 'draft');

-- CreateEnum
CREATE TYPE "VideoStatus" AS ENUM ('uploading', 'queued', 'processing', 'ready', 'error');

-- CreateEnum
CREATE TYPE "UserRole" AS ENUM ('super_admin', 'editor', 'viewer');

-- CreateEnum
CREATE TYPE "VideoJobStatus" AS ENUM ('pending', 'submitted', 'processing', 'completed', 'failed');

-- CreateTable
CREATE TABLE "nodes" (
    "id" TEXT NOT NULL,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMP(3) NOT NULL,
    "name" TEXT NOT NULL,
    "slug" TEXT NOT NULL,
    "layer_depth" INTEGER NOT NULL,
    "parent_id" TEXT,
    "scene_type" "SceneType" NOT NULL,
    "coordinates" JSONB,
    "icon_url" TEXT,
    "cover_image_url" TEXT,
    "weight" INTEGER NOT NULL DEFAULT 50,
    "sort_order" INTEGER NOT NULL DEFAULT 0,
    "content" JSONB,
    "status" "NodeStatus" NOT NULL DEFAULT 'draft',
    "video_status" "VideoStatus",
    "stream_url" TEXT,
    "raw_video_s3_key" TEXT,
    "video_duration_ms" INTEGER,
    "mediaconvert_job_id" TEXT,

    CONSTRAINT "nodes_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "users" (
    "id" TEXT NOT NULL,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMP(3) NOT NULL,
    "email" TEXT NOT NULL,
    "password_hash" TEXT NOT NULL,
    "name" TEXT NOT NULL,
    "role" "UserRole" NOT NULL DEFAULT 'viewer',
    "is_active" BOOLEAN NOT NULL DEFAULT true,
    "last_login" TIMESTAMP(3),

    CONSTRAINT "users_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "video_jobs" (
    "id" TEXT NOT NULL,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "node_id" TEXT NOT NULL,
    "s3_input_key" TEXT NOT NULL,
    "s3_output_prefix" TEXT NOT NULL,
    "mediaconvert_job_id" TEXT,
    "status" "VideoJobStatus" NOT NULL DEFAULT 'pending',
    "error_message" TEXT,
    "renditions" JSONB,
    "completed_at" TIMESTAMP(3),

    CONSTRAINT "video_jobs_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE UNIQUE INDEX "nodes_slug_key" ON "nodes"("slug");

-- CreateIndex
CREATE INDEX "idx_nodes_parent_id" ON "nodes"("parent_id");

-- CreateIndex
CREATE INDEX "idx_nodes_slug" ON "nodes"("slug");

-- CreateIndex
CREATE INDEX "idx_nodes_status" ON "nodes"("status");

-- CreateIndex
CREATE INDEX "idx_nodes_layer_depth" ON "nodes"("layer_depth");

-- CreateIndex
CREATE INDEX "idx_nodes_status_layer_depth" ON "nodes"("status", "layer_depth");

-- CreateIndex
CREATE UNIQUE INDEX "users_email_key" ON "users"("email");

-- CreateIndex
CREATE INDEX "idx_users_email" ON "users"("email");

-- CreateIndex
CREATE INDEX "idx_video_jobs_node_id" ON "video_jobs"("node_id");

-- CreateIndex
CREATE INDEX "idx_video_jobs_status" ON "video_jobs"("status");

-- AddForeignKey
ALTER TABLE "nodes" ADD CONSTRAINT "nodes_parent_id_fkey" FOREIGN KEY ("parent_id") REFERENCES "nodes"("id") ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "video_jobs" ADD CONSTRAINT "video_jobs_node_id_fkey" FOREIGN KEY ("node_id") REFERENCES "nodes"("id") ON DELETE CASCADE ON UPDATE CASCADE;
