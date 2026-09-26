const PRIMARY = 'var(--color-primary-500)';

export function accentColor(accent) {
    if (accent === 'muted') {
        return `oklch(from ${PRIMARY} l calc(c * 0.15) h)`;
    }

    const shift = Number(accent) || 0;

    return shift === 0 ? PRIMARY : `oklch(from ${PRIMARY} l c calc(h + ${shift}))`;
}

export function accentStyle(accent) {
    return { '--accent': accentColor(accent) };
}
