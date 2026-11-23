# Live Reload Without Dev Server

## Goal

Style and island changes show up on the online playground within about a second, without a
Vite dev server reachable from the internet.

## Milestones

- [x] Watch build started by an after-boot hook (`docker/after-boot/vite-watch.sh`)
- [x] Public dev server removed: no `DEV_NPM_RUN_DEV`, no `vite.` Caddy route, no `VITE_HMR_*`
- [ ] Optional: signal the browser over Reverb after each rebuild (CSS swap / reload)

## State

On the online instance the hook runs the watch build; the dev server no longer starts there.
`vite.config.js` keeps a localhost-only dev server for local Docker setups (port 5178, CORS
limited to localhost origins, foreign hosts rejected).

## Decisions

- Watch build instead of HMR: ~0.5 s per rebuild, accepted by the user. A dev server on the
  public instance is rejected as a security hole.
- `npm run build` stays one-shot: the laravel-aio entrypoint runs it synchronously during boot,
  in maintenance mode, so `--watch` there would never let the container finish booting.
- The hook detaches (`nohup setsid`) because the entrypoint runs after-boot hooks before
  leaving maintenance mode. A shell loop restarts the watcher if it exits.
- The hook removes `public/hot`, otherwise Laravel keeps pointing at a dev server.
- `ENV_DEV` stays on in `compose.prod.yaml`: off would make the image run `optimize` and
  `filament:optimize` at boot, caching routes, config and Filament discovery — live edits on
  the online instance would not show up until the caches are cleared.
- `cors: true` was dropped: it answered every origin with `*`.
