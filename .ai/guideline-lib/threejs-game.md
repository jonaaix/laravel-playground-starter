# Three.js Game

This workspace is the development environment for a browser 3D game built on three.js.
The playground and TALL Architect rules still apply to the Laravel app around the game;
this file adds what is specific to a real-time 3D client and says where the game is free
of them.

## Two worlds

- **The Laravel app is the development hub**, not the player-facing product. Panel pages,
  record management, server settings, level and resource pages are built with the usual
  stack — Filament, islands, Vue, the design system. How the game ships to players is not
  decided yet.
- **The game client is its own frontend.** It does not use Vue, the panel's design system,
  Tailwind recipes, Heroicons or the islands helpers — none of the app's frontend rules
  bind it. Visuals, UI and interaction follow the game's art direction. Engineering rules
  (clean code, no comments, naming, file size) still apply.
- **The game client assumes nothing about Laravel.** Every URL, setting and initial state
  arrives through `createGame` options, so it could be lifted out unchanged.

## Where the game lives

- **Backend half is a module** — `app/Modules/<Game>/`: saves, scores, levels, rooms,
  settings. Tables carry the module prefix like any other module.
- **The game client sits in an island** — `app/Islands/<Game>/`, scaffolded with
  `php artisan make:island <Game>`. The island is only the host: it mounts the canvas,
  hands over the options and, in the panel, draws the dev tools beside it. The game itself
  lives in `Game/`, a role the stub does not have.
- **The same island mounts in two places:**
  - **In the panel** — a Filament page in the module catalog, the game beside its debug
    and tuning tools (Vue, design system). This is where development happens.
  - **Full-viewport** — `/<game>/play` from the module's `routes.php`, a minimal Blade
    shell that mounts the island and nothing else. Deliberate exception to "every app view
    is a Filament page": it is the play-test view, without chrome that costs pixels and
    swallows input.
- **The game sizes itself to its container, never to the page**, so both hosts work
  without a branch on where it runs. Which dev tools are visible is a prop, not a guess.
- **A public landing page, once there is one, is Blade** per the indexability rule. The
  canvas never is.

```text
app/Islands/<Game>/
├── <Game>.island.vue      host: mounts the canvas, calls createGame / dispose
├── Components/            panel-side dev tools around the game (Vue)
├── Game/                  the game client, framework-free
│   ├── createGame.js      the only entry point the host calls
│   ├── Loop/              clock, fixed-step loop
│   ├── Input/             devices mapped to actions
│   ├── World/             simulation state and systems
│   ├── Render/            scene, camera, renderer, views of the world state
│   ├── Assets/            loading, caching, disposal
│   ├── Media/             shipped models, textures, audio
│   └── Ui/                HUD, menus, in-game settings, loading screen
└── …                      Queries/, Writers/, Presenters/ as for any island
```

## First start

When this file is active and no game exists yet, the first task is a skeleton that shows
the direction — genre-neutral, nothing a specific game would have to tear out. Planning
file first, as for any multi-step work.

1. **Ask for the game's name** — it names the module, island, routes and tables. Nothing
   else needs deciding up front.
2. **Propose installing `three`** — the only dependency the skeleton needs.
3. **Backend:** `app/Modules/<Game>/` with its `routes.php` serving `/<game>/play`. No
   tables yet — they arrive with the first feature that stores something.
4. **Host:** `php artisan make:island <Game>`; a Filament page extending `IslandPage`,
   implementing `ListedInModules` under a new `<Game>` case in `ModuleGroupEnum`; the
   full-viewport Blade shell for `/<game>/play`.
5. **Game client** in `Game/`, every role folder from the tree above with its first file:
   - fixed-step loop with interpolation, pause on hidden tab;
   - input mapped to actions — move, look, jump, pause — for keyboard, mouse and gamepad;
   - a test scene: ground plane, sky colour, one directional light with shadow, a few
     instanced props for scale;
   - a controllable stand-in (capsule) with a third-person follow camera, movement as
     plain simulation code without physics;
   - `dispose()` covering all of it, and the `window.__game` dev hook.
6. **Game UI** in `Game/Ui/`: a minimal HUD and a pause menu on Escape.
7. **Panel dev tools** in the island's `Components/`: the live snapshot (fps, draw calls,
   position, state) and two or three tuning values (move speed, camera distance) sent as
   commands.
8. **Verify:** both hosts render, the console is clean, an HMR remount does not double the
   loop or grow `renderer.info.memory`, a Playwright screenshot after stepping a fixed
   number of ticks shows the scene.

Not part of the skeleton — each arrives when the game needs it: physics, real assets and
their pipeline, persistence, multiplayer, audio.

## Dependencies

- **`three` is the renderer.** Import from `three` and `three/addons/…` through Vite —
  never a CDN script tag, never a global.
- **three.js changes its API between releases.** Check the installed revision in
  `package.json` and confirm APIs through Context7 before using them. No removed or
  deprecated API (`Geometry`, `outputEncoding`, `sRGBEncoding`, legacy lights).
- **No engine or framework over three.** No react-three-fiber, TresJS, Needle or a
  wrapper engine — they would own the structure this file defines.
- `WebGLRenderer` is the default. `WebGPURenderer` only as an explicit decision.

### Building blocks

Solved problems are not rebuilt. When the game first needs one of these, propose the
library named here — say what it buys, install after approval. Before proposing, check it
is still maintained and compatible with the installed three revision.

| Need | Library |
| --- | --- |
| Rigid-body physics, character controller | `@dimforge/rapier3d-compat` |
| Raycasts and collision against level geometry | `three-mesh-bvh` |
| Entity organisation (ECS) | `miniplex`; `bitecs` once entity counts demand it |
| Post-processing (bloom, tone mapping, colour grading) | `postprocessing` |
| Navigation meshes, pathfinding, crowds | `recast-navigation` |
| Dev tuning panel | `lil-gui` (dev only) |
| Unit tests for game rules | `vitest` |

The three.js examples (`games_fps`, controls, loaders) are starting points to copy from,
not dependencies.

## Host ↔ game boundary

- **One seam.** The host calls `createGame(canvas, options)` on mount and `game.dispose()`
  on unmount. Nothing else crosses except the two channels below.
- **Game → host:** events and a small snapshot (state, stats) the panel's dev tools read
  when it changes — never per-frame writes into Vue.
- **Host → game:** commands on the game handle (`game.pause()`, `game.setTuning(…)`). The
  host never mutates game state directly.
- **three.js objects never enter Vue.** If the host keeps the game handle, it is a
  `shallowRef` holding a `markRaw` object — a reactive proxy on a scene graph breaks
  identity checks and costs frame time.

## Loop & simulation

- `renderer.setAnimationLoop(tick)`, not a hand-rolled `requestAnimationFrame`.
- **Fixed-timestep simulation** (60 Hz unless the game needs otherwise) with an
  accumulator; rendering interpolates between the last two states. Clamp the frame delta
  (≤ 0.25 s) so a background tab does not fast-forward the world.
- **Simulation state is plain data; three.js objects are views of it.** Systems update the
  data, the render layer syncs meshes from it. That keeps the rules testable without WebGL
  and keeps the door open for server-side authority.
- **Seeded randomness** inside the game, so a run can be replayed from its seed.
- Pause on `visibilitychange`; resume needs a user action when audio or pointer lock is
  involved.

## Lifecycle & memory

- **Everything created is disposed:** geometries, materials, textures, render targets,
  controls, the renderer, DOM the UI created, event listeners, observers, and the loop
  (`setAnimationLoop(null)`). `dispose()` walks all of it.
- **HMR remounts the island.** A missing dispose shows up as a doubled loop, doubled input
  or a growing `renderer.info.memory`. Check those numbers after every change to the game.
- **No allocation in the hot path.** Reuse scratch vectors and quaternions declared at
  module level; no `new Vector3()`, array spread or closure per frame.

## Rendering & performance

- **Budget:** a steady 60 fps on a mid-range laptop with integrated graphics. Watch
  `renderer.info.render.calls` and triangles; a few hundred draw calls is the ceiling to
  question, not to reach.
- `setPixelRatio(Math.min(devicePixelRatio, 2))`; size from a `ResizeObserver` on the
  container, not `window.resize`.
- **Repeated objects are `InstancedMesh`**, static geometry is merged, distant detail uses
  `LOD`. One shadow-casting light with a tight shadow camera unless the look demands more.
- **Colour management on:** colour textures `SRGBColorSpace`, data textures (normal,
  roughness) linear. Lighting values are physical — tune them, don't fight them.
- Post-processing only once the base frame fits the budget, and measured after adding.

## Input

- **Devices map to actions** (`jump`, `moveX`, `interact`) in `Game/Input/`; game code
  reads actions, never keys. Rebinding then is a table change.
- Keyboard by `event.code` (layout-independent), mouse look via pointer lock, gamepads
  polled once per tick. Touch only when mobile is a stated target.
- Input listeners stop at the game's container — no leaking keystrokes from the panel's
  dev tools into the game, or from an open game menu into the world behind it.

## Game UI

- **HUD, menus, in-game settings and the loading screen live in `Game/Ui/`** — plain DOM
  and CSS layered over the canvas, or drawn in the scene where the UI is diegetic.
  Styles are scoped to the game's container so they neither leak into the panel nor
  inherit from it.
- **A UI library for the game is a decision**, proposed once hand-written DOM stops paying
  off — not a default. React, Vue or Tailwind are not assumed for it.
- **UI reads the game's snapshot and sends commands**, like the host does. It never holds
  game state of its own.
- Strings still go through one translation table owned by the game, English as the key.

## Persistence

- Saves, scores, progress and unlocks go through the island's endpoints into module tables.
- **The client is never trusted** with what decides a ranking or a reward — the server
  checks plausibility (time, rate, bounds) before writing.
- Player settings (bindings, volume, quality preset) are per user on the server, cached
  locally — like any other view choice.

## Debugging & verification

- **Dev hook:** behind `import.meta.env.DEV`, expose `window.__game` with `state()`,
  `step(n)` and `pause()`, so a test or an agent can drive the game deterministically.
- **Tuning and debug tools belong to the panel host**, not the game: the Vue side panel
  reads the snapshot and sends commands. In the full-viewport view they appear only behind
  a `?debug` query.
- **Visual check with Playwright:** headless Chromium renders WebGL through SwiftShader —
  slow but correct. Pause, step a fixed number of ticks, then screenshot; never screenshot
  a running loop and compare.
- Read the browser console after every game change — WebGL warnings (shader compile,
  texture size, context loss) only show up there.
- Rules that can be expressed without WebGL (collision response, scoring, spawning, state
  machines) get unit tests once Vitest is approved.
