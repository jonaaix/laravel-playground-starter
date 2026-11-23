import { existsSync, utimesSync, watch } from 'node:fs';
import { resolve } from 'node:path';

export default function watchBuild({ stylesheets, sourceDirectories }) {
    let isWatching = false;
    let watchers = [];
    let pendingTouch = null;

    const touchStylesheets = () => {
        clearTimeout(pendingTouch);
        pendingTouch = setTimeout(() => {
            const now = new Date();
            stylesheets.forEach((stylesheet) => utimesSync(stylesheet, now, now));
        }, 200);
    };

    const watchSourceDirectory = (directory) => watch(directory, { recursive: true }, (event, filename) => {
        if (event === 'rename' && filename && existsSync(resolve(directory, filename))) {
            touchStylesheets();
        }
    });

    return {
        name: 'watch-build',
        config(config, { command }) {
            isWatching = command === 'build' && Boolean(config.build?.watch);

            return {
                build: isWatching ? { emptyOutDir: false } : {},
                define: {
                    __WATCH_BUILD__: JSON.stringify(isWatching),
                },
            };
        },
        buildStart() {
            if (!isWatching || watchers.length > 0) {
                return;
            }

            watchers = sourceDirectories.filter((directory) => existsSync(directory)).map(watchSourceDirectory);
        },
        closeWatcher() {
            watchers.forEach((watcher) => watcher.close());
            watchers = [];
        },
    };
}
