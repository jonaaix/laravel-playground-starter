#!/bin/bash

cd /app || exit 0

rm -f public/hot

if pgrep -f "while true; do [a-z]* run watch" > /dev/null; then
    exit 0
fi

runner=npm
if command -v bun > /dev/null; then
    runner=bun
fi

# The entrypoint waits for this hook before leaving maintenance mode, so the watcher must detach.
nohup setsid bash -c "while true; do $runner run watch; sleep 5; done" \
    >> /proc/1/fd/1 2>> /proc/1/fd/2 < /dev/null &
