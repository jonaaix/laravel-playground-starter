/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
import { startLiveReload } from './live-reload';

import { startVueIslands } from '@aaix/laravel-islands/vue';
import { searchIslands } from '@aaix/laravel-islands-search';

const featureIslands = Object.fromEntries(
    Object.entries(import.meta.glob('../../app/Islands/**/*.island.vue', { eager: true }))
        .map(([path, module]) => [`./islands/${path.split('/').pop()}`, module]),
);

startVueIslands({
    ...searchIslands,
    ...import.meta.glob('./islands/**/*.island.vue', { eager: true }),
    ...featureIslands,
});

if (__WATCH_BUILD__) {
    // Through a variable, because Vite rewrites a literal new URL('…', import.meta.url) into an asset import.
    const bundleUrl = import.meta.url;
    startLiveReload(new URL('../manifest.json', bundleUrl));
}
