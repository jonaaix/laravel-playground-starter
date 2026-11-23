const POLL_INTERVAL_MS = 2000;

const isTyping = () => {
    const element = document.activeElement;

    return element instanceof HTMLInputElement
        || element instanceof HTMLTextAreaElement
        || element instanceof HTMLSelectElement
        || element?.isContentEditable === true;
};

export function startLiveReload(manifestUrl) {
    let knownVersion = null;
    let reloadPending = false;

    const fetchVersion = async () => {
        const response = await fetch(manifestUrl, { method: 'HEAD', cache: 'no-store' });

        return response.ok ? (response.headers.get('etag') ?? response.headers.get('last-modified')) : null;
    };

    const reloadWhenIdle = () => {
        if (isTyping()) {
            return;
        }

        window.location.reload();
    };

    const check = async () => {
        if (document.visibilityState !== 'visible') {
            return;
        }

        if (reloadPending) {
            reloadWhenIdle();

            return;
        }

        try {
            const version = await fetchVersion();

            if (version === null) {
                return;
            }

            if (knownVersion !== null && version !== knownVersion) {
                reloadPending = true;
                reloadWhenIdle();
            }

            knownVersion = version;
        } catch {
            return;
        }
    };

    check();
    setInterval(check, POLL_INTERVAL_MS);
    document.addEventListener('visibilitychange', check);
}
