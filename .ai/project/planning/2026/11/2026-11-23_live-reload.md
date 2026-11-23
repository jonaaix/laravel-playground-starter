# Live Reload Without Dev Server

## Goal

Style and island changes show up on the online playground within about a second, without a
Vite dev server reachable from the internet.

## Milestones

- [x] Watch build started by an after-boot hook (`docker/after-boot/vite-watch.sh`)
- [ ] Remove `ENV_DEV`/`DEV_NPM_RUN_DEV` and the `vite.` Caddy route from `compose.prod.yaml`
- [ ] Optional: signal the browser over Reverb after each rebuild (CSS swap / reload)

## State

`compose.prod.yaml` mounts the hook to `/custom-scripts/after-boot`. The hook skips itself
while the dev server is enabled (`ENV_DEV` + `DEV_NPM_RUN_DEV`), which `compose.prod.yaml`
still sets — so the watcher only takes over once those flags go.

## Decisions

- Watch build instead of HMR: ~0.5 s per rebuild, accepted by the user. A dev server on the
  public instance is rejected as a security hole.
- `npm run build` stays one-shot: the laravel-aio entrypoint runs it synchronously during boot,
  in maintenance mode, so `--watch` there would never let the container finish booting.
- The hook detaches (`nohup setsid`) because the entrypoint runs after-boot hooks before
  leaving maintenance mode. A shell loop restarts the watcher if it exits; supervisor is not
  used since the image has no watch flag.
- The hook removes `public/hot`, otherwise Laravel keeps pointing at a dev server.
