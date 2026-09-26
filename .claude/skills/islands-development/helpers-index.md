---
title: Helpers & Composables Index
scope: aaix/laravel-islands + aaix/laravel-islands-datagrid
---

# Helpers & Composables Index

The inventory of what the two packages ship. Look here before writing markup —
if a helper exists, import it; if it almost fits, extend it in the package
rather than forking it in the island. Props, slots and edge cases live in each
package's own docs.

## `@aaix/laravel-islands/vue`

Composables the runtime provides to any island component.

| Import | What it is |
| --- | --- |
| `useIsland()` | the raw payload the server sent — `props`, `_island.locale`, `_island.subscriptions`, `_island.translations` |
| `useTranslations()` | `t(key, replacements?)` reading the payload's lines; English source strings are the keys |
| `useModel(key, options?)` | subscribed model kept in step with Echo; options: `onUpdate`, `refetch`; exposes `data`, `isDeleted` |
| `useEcho()` | the raw Echo connection (`privateChannel`, …) — every channel is left on unmount |
| `useSortableTiles({ container, list, attribute?, onReorder, enabled? })` | pointer-event dragging for a strip or grid |
| `useViewWidth({ baseWidth? })` | **the root element of every island** — `root` + `rootStyle` carry the view's maximum width and publish `--table-toolbar-h`. Never write a `max-w-*` class instead; a view that needs more room passes `baseWidth` |
| `startVueIslands(registry, { setup? })` | the runtime; called once at boot with the component registry |
| `delegateNavigate(root, { within, isEnabled? })` (core entry) | one listener that turns the plain links an island renders into in-place page swaps — a row pointing at a record no longer loads a document. Called once at boot, never per view; leaves a new tab, a modifier click, `download`, a foreign host and anything under `data-no-spa` to the browser |

## `@aaix/laravel-islands/vue/helpers`

Generic UI primitives. All Tailwind-styled — register the package with your build
(`@source '../vendor/aaix/laravel-islands/resources/js/**/*';`) or classes are dropped.

### Buttons & actions

| Import | Purpose |
| --- | --- |
| `Button` | tone/size/shape/loading + `icon`/default/`iconRight`/`menu` slots — the split-button lives here |
| `ButtonGroup` | joins the outlined Buttons placed directly inside it into one strip with shared seams; keep hidden inputs and wrappers outside |
| `startVueIslands(registry, { theme })` / `provideTheme(theme)` / `useTheme(section)` | the look of every helper from one object: per family (`button`, `iconButton`, `field`, `select`, `badge`, `card`, `modal`, `toast`, `optionStrip`, `switch`, `fieldGroup`, `listItem`, `menuItem`, `editButton`) the defaults an unset prop falls back to and the class tables (`tones`, `sizes`, `shapes`, `skins`) it picks from; merged key by key over the package defaults. `provideButtonDefaults` / `BUTTON_DEFAULTS_KEY` still work. Tokens (`--color-il-*`, `--radius-il-*`, `--spacing-il-control*`) and `il-<component>__<part>` class hooks with `data-state` are documented in docs/theming.md |
| `IconButton` | icon-only, `label` doubles as aria-label and tooltip; `tone` shares `Button`'s `secondary`/`primary`/`outlined` surfaces so it can sit in a row of them; `href` makes it a link |
| `EditButton` | the quiet pencil beside an editable value |

### Fields & inline editing

| Import | Purpose |
| --- | --- |
| `TextField`, `NumberField`, `TextArea`, `SelectField`, `FileField` | standard form fields on the shared 36 px control-height frame — `NumberField` takes `prefix`/`suffix` for a unit and `stepper` for a minus/plus pair |
| `DateTimeField` | a moment, a day or a time (`mode`) — typed input in the common shapes plus a calendar with two clock columns (`minuteStep`, default 5); `min`/`max`, `shortcuts` rows the application supplies, 24-hour clock, browser local time; model is ISO · `YYYY-MM-DD` · `HH:MM`; wording via `labels`. Never the browser's native date input |
| `DateRangeField` | a span of days — two months side by side, first click starts, second ends, footer applies; `shortcuts` rows the application supplies apply at once; `variant` = `field` (typed input) · `filter` · `filter-card` · `filter-pill` (toolbar trigger); model `{ from, to }` as `YYYY-MM-DD`. Replaces two native date inputs for from/until |
| `ColorPicker` | hex field plus swatch that opens a picker — saturation/value plane, hue rail, optional `alpha` rail, `presets` palette (a curated default ships), format switch hex · rgb · hsl · hsv with a copy button; model is a hex string; wording via `labels` |
| `Checkbox`, `Switch`, `Radio`, `RadioGroup`, `Slider` | boolean and choice controls — `Slider` takes `options` for named stops or `min`/`max`/`step` for a number, and emits `commit` when the pointer lets go |
| `ChoiceSegment`, `EditSegment` | segmented controls for inline choice / edit rows |
| `InlineEdit` | the in-place edit contract — Enter saves, Esc cancels, spinner in the value's place |
| `FieldGroup`, `FieldSegment`, `FieldCaption` | dividers, segments and the 10 px uppercase caption |
| `fieldClasses`, `textareaClasses`, `FIELD_SHAPES`, `FIELD_SIZES` | the shared style tokens, for custom fields that must match |

### Status & typography

| Import | Purpose |
| --- | --- |
| `Badge` | one-line status word — `tone`, `icon`, `numeric` |
| `Skeleton` | the shape of content not yet loaded — `variant` = `block` · `text` (`lines`) · `circle`; sized with `width`/`height`; a loading view is the finished view in grey, never a spinner on an empty page |
| `PersonChip` | avatar with a name-based fallback |
| `RemovableChip` | a value the user can take out of a list — takes `removeLabel`, emits `remove` |
| `List`, `ListItem` | hairline-divided list; item takes `label`, `description`, `descriptionTone` |
| `Table` | thin frame over a native `<table>` — uniform header typography, no silent truncation |
| `Tabs` | tab strip with icon/count/mark per item; `variant` = `underline` · `pills`, the theme decides when unset |

### Overlays

| Import | Purpose |
| --- | --- |
| `Tooltip` | hover label, fixed positioning, auto-flip; **never** use the native `title` attribute |
| `Popover` | anchored layer with `anchor`, `open`, `width`, `offset`, `margin` |
| `Modal` | teleported dialog with focus-trap, backdrop and Escape handling; controlled — only emits `close`, callsite owns `:open` |
| `FormModal` | `Modal` wrapper for Save-forms: content sits in a `<form>`, footer has Cancel + primary in one row, wording as props, `#title` / `#footer` as escape hatches |
| `WysiwygEditor` | for the one field that needs rich text |

### Hosts (mount once per island root)

| Host | Provide function | Access from anywhere below |
| --- | --- | --- |
| `<ConfirmHost />` | `provideConfirm()` | `useConfirm()` — `await confirm({ title, message, tone })` |
| `<ToastHost />` | `provideToasts()` | `useToast()` — `info` · `success` · `warning` · `danger` |
| — | `provideIcons(ICONS)` / `ICONS_KEY` | `useIcons()`; used by `<Icon>` and every SortButton |

### Icons

`Icon` + `provideIcons(ICONS)`. The package ships **no** icon set — hand it your own,
so the bundle only carries the glyphs you use. See `docs/helpers/index.md` (Icons) for the
icon-set shape and naming conventions.

## `@aaix/laravel-islands-datagrid/vue`

Everything a list view needs beyond the generic helpers above. If a table shape here
does not fit, extend the component before forking it in an island — a control in one
table but not the other is a bug in waiting.

### The table itself

| Import | Purpose |
| --- | --- |
| `DataTable` | the card, sticky toolbar, error banner, `<table>` scaffold, skeletons, empty state and pagination |
| `Pagination` | the pagination bar `DataTable` already renders — standalone only outside a table |
| `useDataTable(dataUrl, options)` | state, fetching, race guard, URL sync — the single owner of table state |

### Toolbar & filtering

| Import | Purpose |
| --- | --- |
| `SearchInput` | the search field bound to `state.q` via `onSearchInput` / `clearSearch` |
| `Combobox` | single-value select with search; `variant` = `field` · `filter` · `filter-card`; supports async `fetchOptions`; an option with `depth` nests under the row above, one with `disabled: true` is a heading that cannot be picked; `:searchable="false"` drops the search box |
| `SelectMenu` | the `Combobox` without its search box, for a list short enough to read — the same props, arrow keys and typing jump through the list; prefer it over `SelectField`, which is the native control |
| `MultiCombobox` | several values from a searchable list — the `Combobox`'s search, `fetchOptions`, `depth` and headings, with checkboxes; the model is an array, the trigger names the first `previewLimit` picks |
| `MultiSelect` | several values from one short list, no search field |
| `useOptionSearch(props, query)` / `selectSkin(variant)` | what the selects are built from — the filtered, ranked, server-fetched option list and the trigger skins — for a picker of your own |
| `TreeSelect` | a hierarchy with a searchable path, cached per URL; `variant` = `field` · `filter` · `filter-card`, the same skins as Combobox |
| `OptionStrip` | micro switcher — `variant` = `pills` (row of switches) or `segmented` (one question, n answers); `size` = `md` beside fields, `sm` in a dense toolbar beside `Button size="sm"` |
| `SwipeRow` / `SwipeSlide` | anything a thumb should swipe through on a phone: one slide per view, snapping, a dot per slide, no scrollbar. Below `sm` by default, `always` at every width; `trackClass` dresses the rail (a frame above the breakpoint), the slides bring their own look. Never hand-roll a snap row again |
| `FilterPanel` | the panel beside the table, docking with the toolbar |
| `useFilterPanelDock(storageKey, { baseWidth })` | `useViewWidth` plus a docking panel: the same `root` + `rootStyle`, widened while the panel is docked, overlaid when there is no room |

### Sorting & columns

| Import | Purpose |
| --- | --- |
| `SortButton` | header-cell sort control — same emit contract as `SortMenu`, so both share one handler |
| `SortMenu` | compact sort dropdown for cards mode or dense toolbars |
| `ColumnPicker` | which columns are visible, plus a reset |

### View modes & saved views

| Import | Purpose |
| --- | --- |
| `ViewProfileMenu` + `useViewProfiles({ state, defaults, keys, storeUrl, profileUrl, initial, shared, plain, apply })` | saved views with sharing, per-user default and dirty tracking |
| `useAutoMobileMode({ state, key, breakpoint, narrow, wide, remember })` | switches to a narrow-viewport mode below `breakpoint` the first time; a `?mode=…` in the URL counts as a manual choice |

### Cards mode

| Import | Purpose |
| --- | --- |
| `GridCard`, `GridCardMedia` | building blocks for the `#cards` slot — `GridCardMedia` defaults to a 3∶2 frame |

### Row selection

| Import | Purpose |
| --- | --- |
| `SelectionBox` + `useSelection(rows, { key: 'id' })` | selection across pages and bulk actions |

### Fetching layer

| Import | Purpose |
| --- | --- |
| `httpClient` / `createHttpClient({ headers })` | the shared fetch layer with a non-2xx throw — pass a custom client as `useDataTable(url, { http })` |
| `sendJson(url, payload)` | one-shot JSON POST used for preferences, view mutations and the like |

**Never call `fetch` directly.** The composable owns the race guard, the query
serialisation and the non-2xx throw that bare `fetch` lacks.

### Icons

Shipped as named Vue components so tables read from one set:

`IconViews`, `IconSort`, `IconColumns`, `IconModeTable`, `IconModeCards`,
`IconModeList`, `IconFilter`, `IconSearch`, `IconStar`, `IconChevronRight`.

Every one is solid-mini 20 — the sanctioned style for the toolbar strip.

### Context

`provideDatagrid({ t, locale })` — optional; hands the package your translator and
locale. Without it, English source strings and locale `en`.

For the PHP side of the datagrid (`HandlesViewProfiles`, `ViewProfileStore`,
`ViewProfileSchema`), see the datagrid SKILL and `base-datagrid-recipe.md`.
