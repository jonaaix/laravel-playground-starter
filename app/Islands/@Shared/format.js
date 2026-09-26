const RELATIVE_STEPS = [
    ['year', 60 * 60 * 24 * 365],
    ['month', 60 * 60 * 24 * 30],
    ['week', 60 * 60 * 24 * 7],
    ['day', 60 * 60 * 24],
    ['hour', 60 * 60],
    ['minute', 60],
    ['second', 1],
];

function resolveLocale(locale) {
    return locale || document.documentElement.lang || undefined;
}

export function formatDate(value, { locale, withTime = false } = {}) {
    if (!value) {
        return '';
    }

    return new Intl.DateTimeFormat(resolveLocale(locale), {
        dateStyle: 'medium',
        ...(withTime ? { timeStyle: 'short', hourCycle: 'h23' } : {}),
    }).format(new Date(value));
}

export function formatRelative(value, { locale } = {}) {
    if (!value) {
        return '';
    }

    const seconds = Math.round((new Date(value).getTime() - Date.now()) / 1000);
    const [unit, size] = RELATIVE_STEPS.find(([, step]) => Math.abs(seconds) >= step) ?? RELATIVE_STEPS.at(-1);

    return new Intl.RelativeTimeFormat(resolveLocale(locale), { numeric: 'auto' }).format(Math.round(seconds / size), unit);
}
