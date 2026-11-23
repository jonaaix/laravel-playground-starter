# Live Reload Without Dev Server

## Goal

Style and island changes show up on the online playground within about a second, without a
Vite dev server reachable from the internet.

## Milestones

- [x] `npm run build` runs `vite build --watch`
- [ ] Remove `DEV_NPM_RUN_DEV` and the `vite.` Caddy route from `compose.prod.yaml`
- [ ] Optional: signal the browser over Reverb after each rebuild (CSS swap / reload)

## State

`build` script watches. `compose.prod.yaml` still runs the public Vite dev server.

## Decisions

- Watch build instead of HMR: ~0.5 s per rebuild, accepted by the user. A dev server on the
  public instance is rejected as a security hole.
- `build` itself carries `--watch`, by the user's explicit choice. The laravel-aio entrypoint
  runs `npm run build` synchronously during boot (in maintenance mode, before migrations and
  supervisor), so a boot with `ENV_DEV` off never gets past the build — the user was told.
  One-shot builds: `npx vite build`.
