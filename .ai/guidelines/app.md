# App Specific Guidelines

## Frontend assets

- On the online instance (`compose.prod.yaml`) no Vite dev server runs. A `vite build --watch`
  (`npm run watch`, started by `docker/after-boot/vite-watch.sh`) keeps `public/build` current and
  rebuilds within seconds; open tabs reload by themselves. Never run `npm run build` or
  `npm run dev` there.
- Locally (`compose.local.yaml`) the dev server with HMR runs as before.
- Vite does not reload its own config in watch mode. After editing `vite.config.js` or a Vite plugin,
  restart the watcher: `kill $(pgrep -f '^node .*vite build --watch')` — the hook's loop starts it again
  within 5 s. Never `pkill -f 'vite build --watch'` from a shell whose own command line contains that string.
