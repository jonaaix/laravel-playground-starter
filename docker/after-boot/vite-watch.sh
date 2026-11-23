#!/usr/bin/env bash
set -euo pipefail

cd /app

if [ "${ENV_DEV:-}" = "true" ] && [ "${DEV_NPM_RUN_DEV:-}" = "true" ]; then
    echo "Vite dev server is enabled, skipping the watch build."
    exit 0
fi

if pgrep -f "vite build --watch" > /dev/null; then
    echo "Vite watch build is already running."
    exit 0
fi

rm -f public/hot

# The entrypoint waits for this hook before leaving maintenance mode, so the watcher must detach.
nohup setsid bash -c '
    while true; do
        npx vite build --watch
        sleep 2
    done
' >> storage/logs/vite-watch.log 2>&1 < /dev/null &

echo "Vite watch build started, log: storage/logs/vite-watch.log"
