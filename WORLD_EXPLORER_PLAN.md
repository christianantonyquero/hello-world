# 🌍 World Explorer — Project Plan

---

## Vision

A cinematic, interactive world explorer where users navigate through a hierarchy of visual **Nodes** placed on maps or displayed in grids. Clicking a Node triggers a cinematic zoom animation — backed by a DASH video stream — and loads a new Scene with deeper content. The entire structure is fully dynamic, managed via an admin dashboard, and built to scale with future features like flights, hotels, and user accounts.

---

## Core Concepts

| Term | Definition |
|---|---|
| **Node** | A clickable icon/button representing a place, theme, or category |
| **Layer** | The depth level of a Node (0 = world root, 1 = region, 2 = country…) |
| **Scene** | The visual layout rendered at a given layer |
| **Scene Type** | One of: `map`, `grid`, `article`, `gallery` |
| **Weight** | A value (1–100) controlling icon display size via log-normalization |
| **Parent Node** | The Node that owns the current Scene (`null` for root) |

Nodes are self-referencing — any Node can be a parent of any other Node, enabling infinite depth and any content hierarchy an admin wants to define.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | React 18 + TypeScript + Tailwind CSS |
| Animations | Framer Motion |
| Map Rendering | Leaflet.js + React-Leaflet |
| Video Player | Shaka Player (DASH, adaptive bitrate) |
| Backend | Node.js + Express |
| ORM | Prisma |
| Database | PostgreSQL |
| Auth | JWT (Access + Refresh tokens) |
| Media (images) | Cloudinary |
| Video Upload | AWS S3 (multipart) |
| Video Processing | AWS MediaConvert |
| Video CDN | AWS CloudFront |
| Job Queue | AWS SQS + DLQ |
| Frontend Hosting | Vercel |
| Backend Hosting | Railway or Render |

---

## Dynamic Node Sizing

Each Node has a `weight` (1–100). A **log-normalization function** keeps size differences subtle across the map:

```ts
const getNodeSize = (weight: number): number => {
  const minSize = 32, maxSize = 64;
  const normalized = Math.log(weight + 1) / Math.log(101);
  return Math.round(minSize + normalized * (maxSize - minSize));
};
// weight 1 → 32px | weight 50 → 54px | weight 100 → 64px
```

---

## Phase 1 — Database Schema & Seed Data

**Goal:** Define the full PostgreSQL schema and seed launch data.

**Nodes table** — self-referencing, powers all content hierarchy:
- `id`, `name`, `slug`, `parent_id` (self-ref), `layer_depth`
- `scene_type` — `map | grid | article | gallery`
- `coordinates` — `{ lat, lng }` (required for map scenes)
- `icon_url` — bouncing GIF/SVG (Cloudinary)
- `cover_image_url`, `weight` (1–100), `sort_order`
- `content` — flexible JSONB (facts, itinerary, gallery images, etc.)
- `status` — `published | draft`
- `video_status` — `null | uploading | queued | processing | ready | error`
- `stream_url` — CloudFront `.mpd` manifest URL
- `raw_video_s3_key`, `video_duration_ms`, `mediaconvert_job_id`

**Users table** — role-based auth:
- `id`, `email`, `password_hash`, `name`
- `role` — `super_admin | editor | viewer`
- `is_active`, `last_login`

**Video Jobs table** — transcode audit log:
- `id`, `node_id`, `s3_input_key`, `s3_output_prefix`
- `mediaconvert_job_id`, `status`, `error_message`
- `renditions` — JSONB `{ "360p": url, "720p": url, "1080p": url }`
- `created_at`, `completed_at`

**Indexes:** `parent_id`, `slug`, `status`, `layer_depth`

**Seed data:** 1 root world Node + 8 country Nodes (France, UAE, Japan, USA, Italy, UK, Turkey, Japan) with coordinates and weights.

---

## Phase 2 — Backend API + Auth

**Goal:** Build the full Express REST API with JWT auth and role-based access control.

### Public Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/v1/nodes` | All root nodes (published) |
| GET | `/api/v1/nodes/:slug` | Single node by slug |
| GET | `/api/v1/nodes/:slug/children` | Published child nodes |

### Admin Endpoints (JWT protected)

| Method | Endpoint | Role | Description |
|---|---|---|---|
| POST | `/auth/login` | — | Returns access + refresh token |
| POST | `/auth/refresh` | — | Refresh access token |
| POST | `/auth/logout` | — | Invalidate refresh token |
| GET/POST | `/admin/nodes` | editor+ | List / create nodes |
| PUT/DELETE | `/admin/nodes/:id` | editor+ / super_admin | Update / delete node |
| PATCH | `/admin/nodes/:id/status` | editor+ | Publish or unpublish |
| GET/POST | `/admin/users` | super_admin | List / create users |
| PUT/DELETE | `/admin/users/:id` | super_admin | Update / delete user |

### Auth Details
- Access token: **15 min expiry**
- Refresh token: **7 days**, stored as HttpOnly cookie
- Role hierarchy: `super_admin > editor > viewer`

### Future-Ready Stubs (not implemented)
- `GET /api/v1/flights` → `501 Not Implemented`
- `GET /api/v1/hotels` → `501 Not Implemented`
- `reviews`, `wishlists` tables scaffolded in schema, no routes yet

---

## Phase 3 — Frontend World Map + Node System

**Goal:** Build the interactive world map with bouncing Node icons and scene routing.

### Pages

| Route | Description |
|---|---|
| `/` | Root scene — world map (layer 0) |
| `/node/[slug]` | Dynamic scene renderer for any node |
| `/admin` | Admin dashboard |
| `/admin/login` | Admin login |

### Scene Types

Each Node's `scene_type` determines which component renders:
- **Map** — Leaflet map with positioned, bouncing Node icons
- **Grid** — Visual card grid of child Nodes
- **Article** — Rich content: description, facts, itinerary, things to do
- **Gallery** — Full photo/media showcase

### Node Bounce Animation

Framer Motion `y: [0, -6, 0]` loop, 1.8s duration, sized by `getNodeSize(weight)`.

### Frontend Structure

```
/src
  /components — map, grid, article, gallery, admin, ui
  /pages      — index, node/[slug], admin
  /hooks      — useNode, useAuth, useZoom
  /utils      — nodeSize, sceneRouter
  /store      — authStore, navigationStore (Zustand)
  /types      — node, user
```

---

## Phase 4 — Cinematic Zoom + DASH Video Streaming

**Goal:** Click a Node → CSS zoom → DASH stream plays → fade into next scene.

### Video Pipeline (AWS)

```
Admin uploads video (multipart → S3 raw-uploads bucket)
  └─► S3 event fires → SQS message (transcode-jobs queue)
        └─► Node.js SQS consumer picks up job
              └─► Submits to AWS MediaConvert
                    └─► Outputs 360p / 720p / 1080p DASH segments
                          └─► Saved to S3 processed-videos bucket
                                └─► CloudFront CDN serves .mpd manifest
                                      └─► EventBridge webhook → updates DB (status: ready)
```

### Video Renditions (DASH / H.264)

| Rendition | Resolution | Bitrate |
|---|---|---|
| 360p | 640×360 | 800 Kbps |
| 720p | 1280×720 | 3,000 Kbps |
| 1080p | 1920×1080 | 6,000 Kbps |

Segment length: 4s · Fragment length: 2s · Audio: AAC 128 Kbps stereo

### Multipart Upload API

| Method | Endpoint | Description |
|---|---|---|
| POST | `/admin/videos/multipart/init` | Start upload, returns `uploadId` + `s3Key` |
| GET | `/admin/videos/multipart/presign` | Presigned URL per chunk |
| POST | `/admin/videos/multipart/complete` | Finalize upload, queues transcode job |
| DELETE | `/admin/videos/multipart/abort` | Abort and clean up |
| GET | `/admin/videos/:nodeId/status` | Poll transcode status |

Chunks are uploaded **directly from the browser to S3** via presigned URLs — the backend never handles raw video bytes.

### Zoom State Machine

```
idle → [click] → zooming (CSS scale+blur, 400ms)
              → streaming (Shaka Player loads .mpd, plays to end)
              → transitioning (fade out, 300ms)
              → done (React Router navigates to /node/[slug])
              → idle (new scene)
```

If a Node has no video (`stream_url = null`), the `streaming` state is skipped and a CSS-only zoom fallback is used.

### Shaka Player Config
- Adaptive bitrate: **enabled** (auto-switches rendition by connection speed)
- Buffering goal: 10s · Rebuffering goal: 2s
- DASH `.mpd` manifest fetched from CloudFront

### SQS Consumer

Runs as a **separate Node.js worker process**, fully decoupled from the Express API. Uses long-polling (20s). Failed jobs retry up to 3 times before landing in the Dead Letter Queue (DLQ).

---

## Phase 5 — Admin Dashboard

**Goal:** Full CRUD interface for managing Nodes, users, and media with role-based access.

### Pages

| Page | Description |
|---|---|
| Dashboard Home | Stats: total nodes, published, drafts, processing videos |
| Node Manager | Searchable/filterable table, status toggle, bulk actions |
| Node Editor | Full form: name, slug, parent, scene type, coordinates, weight slider, icon upload, content editor, status |
| Video Upload | Multipart uploader with progress bar + status badge per node |
| User Manager | List users, assign roles, activate/deactivate (super_admin only) |

### Role Permissions

| Action | super_admin | editor | viewer |
|---|---|---|---|
| View nodes | ✅ | ✅ | ✅ |
| Create / edit node | ✅ | ✅ | ❌ |
| Delete node | ✅ | ❌ | ❌ |
| Publish / unpublish | ✅ | ✅ | ❌ |
| Upload video | ✅ | ✅ | ❌ |
| Manage users | ✅ | ❌ | ❌ |

### Node Form — Key Fields
- **Weight slider** (1–100) with live px size preview
- **Parent node** searchable dropdown (shows full tree path)
- **Coordinates** lat/lng inputs (shown only for `scene_type = map`)
- **Scene type** select — switches content editor layout dynamically
- **Status toggle** — Draft / Published

---

## Phase 6 — Media Integration (Cloudinary)

**Goal:** Connect image/icon uploads to Cloudinary from the admin dashboard.

- Node icons (GIFs/SVGs) and cover images stored on Cloudinary
- Signed uploads from backend to keep API keys server-side
- Automatic format optimization (`f_auto`, `q_auto`) for web delivery
- GIF icons preserved as-is for bounce animation fidelity

---

## Phase 7 — Polish & UX

**Goal:** Production-ready feel across all user-facing surfaces.

- Breadcrumb trail showing layer path (World → France → Paris)
- Loading skeletons for map and grid scenes
- Error boundaries per scene with retry
- Mobile responsive map + grid layouts
- Keyboard navigation support (tab to nodes, enter to zoom)
- Page transition history (browser back button navigates up the Node tree)
- SEO: `<title>` and meta tags per Node using slug + name
- Smooth scroll-to-node on deep link load

---

## Phase 8 — Deployment

**Goal:** Production deployment with environment parity.

| Service | Platform |
|---|---|
| Frontend | Vercel (auto-deploy from `main` branch) |
| Backend API | Railway or Render |
| Worker (SQS consumer) | Railway background worker or EC2 t3.small |
| Database | Railway PostgreSQL or Supabase |
| Video Storage | AWS S3 (2 buckets) |
| Video CDN | AWS CloudFront |
| Image CDN | Cloudinary |

### Environment Variables Summary

```bash
# Backend
DATABASE_URL, JWT_ACCESS_SECRET, JWT_REFRESH_SECRET
AWS_REGION, AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY
S3_RAW_BUCKET, S3_PROCESSED_BUCKET
SQS_QUEUE_URL, MEDIACONVERT_ENDPOINT, MEDIACONVERT_ROLE_ARN
CLOUDFRONT_BASE_URL, CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET

# Frontend
VITE_API_BASE_URL
```

---

## Future Roadmap (Scaffolded, Not Built)

| Feature | Notes |
|---|---|
| Flight search | Link flights to Nodes via `node_id`, integrate Amadeus or Skyscanner API |
| Hotel recommendations | Link hotels to Nodes, integrate Booking.com or similar |
| User accounts & wishlists | Auth for public users, save favourite Nodes |
| Reviews & ratings | Per-Node user reviews, moderated by editors |
| Social sharing | Share a Node page with OG image preview |
| AI travel assistant | Chatbot powered by Claude API, context-aware per Node |

---

## Development Order

| Phase | Scope | Depends On |
|---|---|---|
| 1 | DB schema + Prisma + seed | — |
| 2 | Express API + JWT auth | Phase 1 |
| 3 | React frontend + map + scene routing | Phase 2 |
| 4 | Zoom animation + DASH streaming + AWS pipeline | Phase 3 |
| 5 | Admin dashboard | Phase 2 |
| 6 | Cloudinary media integration | Phase 5 |
| 7 | Polish + UX + SEO | Phase 3–5 |
| 8 | Deployment | All phases |
