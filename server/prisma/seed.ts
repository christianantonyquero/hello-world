import { PrismaClient, Prisma, SceneType, NodeStatus } from "@prisma/client";

const prisma = new PrismaClient();

function slugify(name: string): string {
  return name
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, "")
    .replace(/\s+/g, "-");
}

const ROOT_NODE = {
  name: "World",
  slug: "world",
  layerDepth: 0,
  sceneType: SceneType.map,
  weight: 100,
  sortOrder: 0,
  status: NodeStatus.published,
  coordinates: Prisma.JsonNull,
  iconUrl: null,
  coverImageUrl: null,
  content: Prisma.JsonNull,
};

interface CountryDef {
  name: string;
  coordinates: { lat: number; lng: number };
  weight: number;
  sortOrder: number;
}

const COUNTRIES: CountryDef[] = [
  { name: "France",                coordinates: { lat: 46.2276, lng: 2.2137 },    weight: 85, sortOrder: 1 },
  { name: "United Arab Emirates",  coordinates: { lat: 23.4241, lng: 53.8478 },   weight: 78, sortOrder: 2 },
  { name: "Japan",                 coordinates: { lat: 36.2048, lng: 138.2529 },  weight: 90, sortOrder: 3 },
  { name: "United States",         coordinates: { lat: 37.0902, lng: -95.7129 },  weight: 95, sortOrder: 4 },
  { name: "Italy",                 coordinates: { lat: 41.8719, lng: 12.5674 },   weight: 82, sortOrder: 5 },
  { name: "United Kingdom",        coordinates: { lat: 55.3781, lng: -3.4360 },   weight: 80, sortOrder: 6 },
  { name: "Turkey",                coordinates: { lat: 38.9637, lng: 35.2433 },   weight: 75, sortOrder: 7 },
  { name: "Morocco",               coordinates: { lat: 31.7917, lng: -7.0926 },   weight: 70, sortOrder: 8 },
];

async function main() {
  console.log("Seeding database...");

  const worldNode = await prisma.node.upsert({
    where: { slug: ROOT_NODE.slug },
    update: {},
    create: ROOT_NODE,
  });

  console.log(`  Root: "${worldNode.name}" (${worldNode.id})`);

  for (const country of COUNTRIES) {
    const slug = slugify(country.name);
    const node = await prisma.node.upsert({
      where: { slug },
      update: {
        coordinates: country.coordinates,
        weight: country.weight,
        sortOrder: country.sortOrder,
      },
      create: {
        name: country.name,
        slug,
        layerDepth: 1,
        sceneType: SceneType.map,
        parentId: worldNode.id,
        coordinates: country.coordinates,
        weight: country.weight,
        sortOrder: country.sortOrder,
        status: NodeStatus.published,
        iconUrl: null,
        coverImageUrl: null,
        content: Prisma.JsonNull,
      },
    });
    console.log(`  Country: "${node.name}" (${node.slug})`);
  }

  console.log(`\nDone. ${1 + COUNTRIES.length} nodes upserted.`);
}

main()
  .catch((e) => { console.error(e); process.exit(1); })
  .finally(async () => { await prisma.$disconnect(); });
