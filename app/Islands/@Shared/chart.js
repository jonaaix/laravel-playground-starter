import { Chart, registerables } from 'chart.js';
import { merge } from 'chart.js/helpers';
import { accentColor } from './accent.js';

Chart.register(...registerables);

export const CHART_HEIGHT = 300;

const SERIES_ACCENTS = [0, 60, 120, 180, 'muted'];
const SLICED_TYPES = ['pie', 'doughnut', 'polarArea'];
const FILLED_TYPES = ['bar', 'radar', ...SLICED_TYPES];

function resolveColor(element, color) {
    const previousColor = element.style.color;
    element.style.color = color;
    const resolvedColor = getComputedStyle(element).color;
    element.style.color = previousColor;

    return resolvedColor;
}

function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

function readChartTheme(element) {
    const isDark = isDarkMode();
    const series = SERIES_ACCENTS.map((accent) => resolveColor(element, accentColor(accent)));

    return {
        series,
        seriesFills: series.map((color) => resolveColor(element, `color-mix(in oklab, ${color} 20%, transparent)`)),
        text: resolveColor(element, isDark ? 'var(--color-gray-400)' : 'var(--color-gray-500)'),
        grid: resolveColor(element, isDark ? 'color-mix(in oklab, white 10%, transparent)' : 'var(--color-gray-200)'),
        fontFamily: getComputedStyle(element).fontFamily,
    };
}

function baseOptions(theme) {
    const font = { family: theme.fontFamily };

    return {
        responsive: true,
        maintainAspectRatio: false,
        locale: document.documentElement.lang || undefined,
        color: theme.text,
        borderColor: theme.grid,
        font,
        plugins: {
            legend: { position: 'top', labels: { color: theme.text, font } },
            tooltip: { titleFont: font, bodyFont: font, footerFont: font },
        },
    };
}

function scaleOptions(theme) {
    const axis = {
        grid: { color: theme.grid },
        border: { color: theme.grid },
        ticks: { color: theme.text, font: { family: theme.fontFamily } },
    };

    return { scales: { x: axis, y: axis } };
}

function paintDataset(type, dataset, index, theme) {
    const seriesIndex = index % theme.series.length;

    if (SLICED_TYPES.includes(type)) {
        const sliceColors = dataset.data.map((_, sliceIndex) => theme.series[sliceIndex % theme.series.length]);

        return { backgroundColor: sliceColors, borderColor: theme.grid, ...dataset, data: [...dataset.data] };
    }

    const fill = FILLED_TYPES.includes(type) ? theme.series[seriesIndex] : theme.seriesFills[seriesIndex];

    return {
        borderColor: theme.series[seriesIndex],
        backgroundColor: fill,
        pointBackgroundColor: theme.series[seriesIndex],
        ...dataset,
        data: [...dataset.data],
    };
}

export function buildChartConfig(element, { type, data, options = {} }) {
    const theme = readChartTheme(element);
    const themedOptions = SLICED_TYPES.includes(type)
        ? baseOptions(theme)
        : merge(baseOptions(theme), scaleOptions(theme));

    return {
        type,
        data: {
            labels: [...(data.labels ?? [])],
            datasets: (data.datasets ?? []).map((dataset, index) => paintDataset(type, dataset, index, theme)),
        },
        options: merge(themedOptions, options),
    };
}
