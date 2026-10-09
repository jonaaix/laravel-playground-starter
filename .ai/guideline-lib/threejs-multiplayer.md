# Three.js Multiplayer

Real-time play between browsers for the three.js game. Requires `threejs-game`.

## Transport

- **Reverb through Echo** — both installed. The host passes the shared Echo instance from
  `resources/js/echo.js` into `createGame` options; the game never opens a second
  connection or imports app code for it.
- **Rooms are presence channels** (`<game>.room.{id}`), authorized in
  `routes/channels.php` against a `<game>_rooms` table in the module. Presence gives
  joins, leaves and the member list for free.
- **Two kinds of message, never mixed:**
  - **Cosmetic, high-frequency** — positions, rotations, animation state. Client events
    (`whisper`) straight through Reverb; they never touch Laravel and nothing trusts them.
  - **Authoritative** — hits, pickups, scores, round results. An HTTP call to the island's
    endpoint, validated, then broadcast as an event (`ShouldBroadcastNow`). Anything that
    persists or decides a winner takes this path.

## Rates & payloads

- **Send state at 10–20 Hz**, not per frame. The sending rate is one constant in the
  game client.
- **Payloads are small and flat:** arrays of rounded numbers, short keys, no serialized
  objects. Stay well under Reverb's message size limit.
- **Remote entities are interpolated** about 100 ms behind the latest snapshot; the local
  player is predicted and corrected, never waits for its own echo.
- A missing or late snapshot holds the last pose — no extrapolation beyond a short window.

## Authority

- **Laravel is not a game server.** It authorizes, validates and persists; it does not run
  the simulation. A request-cycle "tick" endpoint is never the answer.
- **Server-authoritative simulation** (anti-cheat, physics on the server) means a separate
  long-running process — a sideproject such as a Node WebSocket server sharing the
  game's `World/` code. That is an explicit decision before it is built, not a drift.
- Disconnects and reconnects are part of the design: a player who drops keeps their slot
  for a grace period, a reconnect receives the current state, not a replay.

## Verification

- **Two Playwright browser contexts**, each with its own user, joined to the same room.
  Step both through `window.__game`, then assert on each side's `state()`.
- Throttle one context's network to see interpolation hold up under latency.
