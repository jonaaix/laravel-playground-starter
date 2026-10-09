# Guideline Library

Domain guidelines for projects built on this starter template. Nothing here is active —
Boost only reads `.ai/guidelines/`. The template itself stays domain-neutral.

## Activate

```bash
cp .ai/guideline-lib/threejs-game.md .ai/guidelines/
php artisan boost:update
```

Copy every file the chosen one requires. To deactivate, delete the copy from
`.ai/guidelines/` and run `php artisan boost:update` again.

## Catalog

| File | Covers | Requires |
| --- | --- | --- |
| `threejs-game.md` | Browser 3D game on three.js: structure, loop, game UI, building blocks, first start | — |
| `threejs-assets.md` | Models, textures, audio, optimisation pipeline, loading | `threejs-game` |
| `threejs-multiplayer.md` | Real-time play over Reverb/Echo, rooms, authority | `threejs-game` |

## Adding a guideline

- **One domain or one optional extension per file**, named `<domain>[-<extension>].md`.
- **Opens with a `# Title`** — Boost uses it as the description — followed by a
  `Requires …` line when it builds on another file.
- **Says where it deviates** from the playground and TALL Architect rules, explicitly.
  Silence means the base rules apply.
- **Written for a project that has nothing yet.** If the domain needs a starting point, a
  `First start` section describes what to build once the file is activated.
- Add the file to the catalog above in the same commit.
