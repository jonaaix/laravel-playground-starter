# Live Reload Without Dev Server

## Goal

Asset changes show up on the online playground within about a second and open tabs reload by
themselves, without a Vite dev server reachable from the internet.

## Milestones

- [x] Public dev server removed: no `DEV_NPM_RUN_DEV`, no `vite.` Caddy route, no `VITE_HMR_*`
- [x] Online instance in prod mode (`ENV_DEV` off, `PROD_SKIP_OPTIMIZE`)
- [x] After-boot hook starts `npm run watch` in a restart loop, logging to `docker compose logs`
- [x] `watch-build` Vite plugin: keeps `public/build` on watch start, rescans Tailwind on new templates
- [x] Rolldown `incrementalBuild`
- [x] Polling live reload, shipped only in watch builds
- [x] Agent instructions in `.ai/guidelines/app.md`

## State

Merged into `main`, not yet deployed to the online instance. Verified locally: one-shot build ships no live
reload; watch start keeps old assets; a new Blade file in `resources/views` or `app/Filament`
lands in the right stylesheet; rebuilds 0.3–0.5 s; local dev server defines the flag as `false`.
Hook verified in the laravel-aio image (returns at once, guard holds, runs the package script).

## Decisions

- Follows the user's concept `vite-watch-build.md` (prod mode included), with two deviations for this
  project: the plugin touches both stylesheets (`app.css` and the Filament theme, which scans its own
  directories), and the local dev server with HMR stays (localhost only, CORS limited to localhost).
- Prod mode still runs `filament:optimize` and `icons:cache` at boot — `PROD_SKIP_OPTIMIZE` skips only
  `artisan optimize`. New Filament pages need `php artisan filament:optimize-clear`. Accepted by the user.
- `npm run build` stays one-shot: the entrypoint runs it synchronously during boot in maintenance mode.
- The plugin defines `__WATCH_BUILD__` in every mode (not only builds), so the local dev server does
  not hit an undefined global.
- Polling instead of Reverb push: no extra channel, no new surface; full reload instead of CSS swap.
