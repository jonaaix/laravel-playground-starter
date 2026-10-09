# Three.js Game

This workspace is the development environment for a browser 3D game built on three.js.
The playground and TALL Architect rules still apply; this file only adds what is specific
to a real-time 3D client, and says explicitly where it deviates from them.

## Where the game lives

- **Backend half is a module** — `app/Modules/<Game>/`: saves, scores, levels, rooms,
  settings. Tables carry the module prefix like any other module.
- **Client half is an island** — `app/Islands/<Game>/`, scaffolded with
  `php artisan make:island <Game>`. The island folder gains one role the stub does not
  have: `Engine/` — plain JavaScript, no Vue import, no DOM access beyond the canvas it is
  handed.
- **The Laravel app is the development hub**, not the player-facing product. The panel
  hosts the game, its tools and its data while it is built; how the game ships to players
  is not decided yet. So `Engine/` assumes nothing about Laravel — every URL and setting
  arrives through `createGame` options — and could be lifted out unchanged.
- **The same island mounts in two places:**
  - **In the panel** — a Filament page in the module catalog, the game beside its debug
    and tuning tools. This is where development happens.
  - **Full-viewport** — `/<game>/play` from the module's `routes.php`, a minimal Blade
    shell that mounts the island and nothing else. Deliberate exception to "every app view
    is a Filament page": it is the play-test view, without chrome that costs pixels and
    swallows input.
- **The island sizes itself to its container, never to the page**, so both hosts work
  without a branch on where it runs. Which tools are visible is a prop, not a guess.
- **Further tooling stays in the panel** — level lists, leaderboard moderation, tuning
  tables are Filament pages hosting islands, listed in the module catalog as usual.
- **A public landing page, once there is one, is Blade** per the indexability rule. The
  canvas never is.

```text
app/Islands/<Game>/
├── <Game>.island.vue      mounts the canvas, owns the HUD, calls createGame / dispose
├── Engine/                the game itself, framework-free
│   ├── createGame.js      the only entry point the island calls
│   ├── Loop/              clock, fixed-step loop
│   ├── Input/             devices mapped to actions
│   ├── World/             simulation state and systems
│   ├── Render/            scene, camera, renderer, views of the world state
│   └── Assets/            loading, caching, disposal
├── Components/            HUD, menus, overlays (Vue)
└── …                      Queries/, Writers/, Presenters/ as for any island
```

## Dependencies

- **`three` is the renderer.** Import from `three` and `three/addons/…` through Vite —
  never a CDN script tag, never a global.
- **three.js changes its API between releases.** Check the installed revision in
  `package.json` and confirm APIs through Context7 before using them. No removed or
  deprecated API (`Geometry`, `outputEncoding`, `sRGBEncoding`, legacy lights).
- **Propose on need, never preinstall:** physics (`@dimforge/rapier3d-compat`), a tuning
  panel (`lil-gui`, dev only), a JS test runner (Vitest). Say what it buys first.
- **No framework over three.** No react-three-fiber (React is not used here), no TresJS or
  a game engine wrapper without an explicit decision.
- `WebGLRenderer` is the default. `WebGPURenderer` only as an explicit decision.

## The Vue ↔ engine boundary

- **One seam.** The island calls `createGame(canvas, options)` in `onMounted` and
  `game.dispose()` in `onBeforeUnmount`. Nothing else crosses except the two channels
  below.
- **three.js objects never become reactive.** No scene, mesh, camera or renderer inside
  `ref`, `reactive` or component data — the proxy breaks identity checks and costs frame
  time. Keep them in plain variables inside `Engine/`; if the island must hold the game
  handle, use `shallowRef` + `markRaw`.
- **Engine → UI:** the engine emits events or exposes a small snapshot the HUD reads when
  it changes — health, score, objective. Never per-frame reactive writes.
- **UI → engine:** commands on the game handle (`game.pause()`, `game.setVolume(0.5)`).
  The UI never mutates engine state directly.
- **The frame loop never touches Vue or the DOM** — except the canvas.

## Loop & simulation

- `renderer.setAnimationLoop(tick)`, not a hand-rolled `requestAnimationFrame`.
- **Fixed-timestep simulation** (60 Hz unless the game needs otherwise) with an
  accumulator; rendering interpolates between the last two states. Clamp the frame delta
  (≤ 0.25 s) so a background tab does not fast-forward the world.
- **Simulation state is plain data; three.js objects are views of it.** Systems update the
  data, the render layer syncs meshes from it. That keeps the rules testable without WebGL
  and keeps the door open for server-side authority.
- **Seeded randomness** inside the engine, so a run can be replayed from its seed.
- Pause on `visibilitychange`; resume needs a user action when audio or pointer lock is
  involved.

## Lifecycle & memory

- **Everything created is disposed:** geometries, materials, textures, render targets,
  controls, the renderer, event listeners, observers, and the loop
  (`setAnimationLoop(null)`). `dispose()` walks all of it.
- **HMR remounts the island.** A missing dispose shows up as a doubled loop, doubled input
  or a growing `renderer.info.memory`. Check those numbers after every change to the
  engine.
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

- **Devices map to actions** (`jump`, `moveX`, `interact`) in `Engine/Input/`; game code
  reads actions, never keys. Rebinding then is a table change.
- Keyboard by `event.code` (layout-independent), mouse look via pointer lock, gamepads
  polled once per tick. Touch only when mobile is a stated target.
- Input listeners stop at the canvas container while a menu is open — no leaking
  keystrokes into the game behind a modal.

## HUD & menus

- **HUD and menus are Vue components layered over the canvas**, built with the design
  system: Tailwind, `dark:` variants, Heroicons, the islands' modal and tooltip helpers,
  strings through `__()`. In-world (diegetic) UI is the only UI drawn in the scene.
- The canvas is the base layer; HUD at 10–20, menus and modals on the app's stacking ladder.
- **The art direction owns the scene, the design system owns the chrome.** Panel rules
  (density, radius, flat rings) do not apply to the 3D world.

## Persistence

- Saves, scores, progress and unlocks go through the island's endpoints into module tables.
- **The client is never trusted** with what decides a ranking or a reward — the server
  checks plausibility (time, rate, bounds) before writing.
- Player settings (bindings, volume, quality preset) are per user on the server, cached
  locally — like any other view choice.

## Debugging & verification

- **Dev hook:** behind `import.meta.env.DEV`, expose `window.__game` with `state()`,
  `step(n)` and `pause()`, so a test or an agent can drive the game deterministically.
- Debug overlays (`lil-gui`, frame stats, helpers, wireframes) only in dev or behind a
  `?debug` query — never shipped on by default.
- **Visual check with Playwright:** headless Chromium renders WebGL through SwiftShader —
  slow but correct. Pause, step a fixed number of ticks, then screenshot; never screenshot
  a running loop and compare.
- Read the browser console after every engine change — WebGL warnings (shader compile,
  texture size, context loss) only show up there.
- Rules that can be expressed without WebGL (collision response, scoring, spawning, state
  machines) get unit tests once a test runner is approved.
