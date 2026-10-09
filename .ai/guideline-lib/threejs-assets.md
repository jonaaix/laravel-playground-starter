# Three.js Assets

Models, textures and audio for the three.js game. Requires `threejs-game`.

## Formats

- **Models are glTF binary (`.glb`)** — nothing else is loaded at runtime. FBX, OBJ or
  `.blend` are converted in the asset pipeline, never parsed in the browser.
- **Geometry is compressed** with Meshopt (preferred) or Draco; **textures are KTX2**
  (Basis Universal) once a scene carries more than a handful. PNG/JPEG only for UI images
  and prototypes.
- **Audio** in one compressed format chosen per game (`.ogg` Opus or `.mp3`) — not mixed.
- **Units and axes:** 1 unit = 1 metre, Y-up, glTF's forward axis. A model that needs a
  scale or rotation fix in code is fixed in the source file instead.
- **Node and material names are an API.** Code looks objects up by name; renaming one in
  the source is a breaking change.

## Where files live

- **Shipped assets** live with the island: `app/Islands/<Game>/Assets/`, referenced via
  `new URL('./…', import.meta.url)` or a `?url` import, so Vite hashes and cache-busts
  them and they leave with the island.
- **Decoder files** (Draco, Basis transcoder, Meshopt) come from the installed `three`
  package — their revision must match it. Never a CDN copy of a different release.
- **Player-made content** (uploaded levels, custom skins) is a record with files on the
  storage disk through `HasMediaDirectory`, like any other upload.
- **Source files and the optimisation pipeline are a sideproject:**
  `sideprojects/<game>-assets/` with its own `package.json`, running
  `@gltf-transform/cli` (dedupe, weld, Meshopt/Draco, KTX2 resize and encode). Its output
  is written into the island's `Assets/`. Raw sources over a few MB need an explicit
  decision (Git LFS or outside the repository) before they are committed.
- **Third-party assets** carry their origin and licence in a `credits.json` next to them,
  added in the same commit as the asset.

## Loading

- **One asset service in `Engine/Assets/`** owns the loaders, a cache keyed by URL and the
  `LoadingManager`. Nothing else calls `GLTFLoader` directly.
- **Loading is per level:** preload what the level needs behind a loading screen fed by
  the manager's progress, release what the next level does not need.
- **Clone, don't reload.** Instances of a cached model are clones; skinned meshes are
  cloned with `SkeletonUtils.clone`. Shared geometry and materials are disposed once,
  when the last user is gone — not per clone.
- **Audio context unlocks on the first user gesture**; one `AudioListener` on the camera,
  positional audio for anything in the world.

## Budgets

Set per game when it starts and recorded in its planning file. Starting points to argue
with, not targets:

- Initial download before the first playable frame: ≤ 10 MB.
- Texture size: 2048² for hero assets, 1024² or less for the rest.
- Triangles on screen: a few hundred thousand on the reference laptop.
