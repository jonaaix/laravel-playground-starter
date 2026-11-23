# Live Reload Without Dev Server

## Goal

Style and island changes show up on the online playground within about a second, without a
Vite dev server reachable from the internet. Done when a watch build runs in the container,
admins' open tabs swap CSS in place and reload on JS changes, and `vite.<domain>` is gone.

## Milestones

- [ ] Decide how the container starts the watch process (image flag vs. repo hook)
- [ ] `npm run watch` script, keeping previous hashed assets until the new manifest is written
- [ ] Vite plugin: after a successful build, signal "css" or "full" depending on what changed
- [ ] Broadcast over Reverb on a private admin channel; client script for admins only
- [ ] Config flag gating script and channel
- [ ] Remove `DEV_NPM_RUN_DEV` and the `vite.` Caddy route from `compose.prod.yaml`
- [ ] Test the channel authorization and the flag gating

## State

Nothing built yet. `compose.prod.yaml` currently runs the Vite dev server (`DEV_NPM_RUN_DEV`)
and exposes it publicly at `vite.${LARAVEL_DOMAIN}`.

## Decisions

- Watch build instead of HMR: measured ~0.5 s per rebuild, accepted by the user. A dev server
  on the public instance is rejected as a security hole, even behind the admin login.
- The container image (laravel-aio) only knows `DEV_NPM_RUN_DEV`, which runs
  `npm run dev -- --host` under supervisor; there is no watch flag yet.

## Open

- Container start: new image flag `DEV_NPM_RUN_WATCH` in laravel-aio, or an after-boot hook
  mounted from this repo.
