---
name: archipelago-development
description: Work with the Laravel Islands set installed through aaix/laravel-archipelago — island views, data tables, the global search, image galleries and lightboxes, the file manager. Use whenever an island is created or changed, a list or table is added, something should become findable in the search modal, photos are shown or opened large, or the file manager is touched. Names the package skill to read for the task.
compatibility: Requires aaix/laravel-archipelago to be installed through Composer.
---

# Laravel Islands Set

`aaix/laravel-archipelago` installs five packages. Each ships its own skill; this one only says which
to read. Paths are relative to the application root.

## Workflow

1. Pick every row below that the task touches — a list view inside an island needs both the first
   and the second.
2. Read that skill's `SKILL.md` in full before writing code, then the files it links where the task
   needs them.
3. Follow those skills as if they were loaded directly.

| Task | Skill to read |
| --- | --- |
| A view or widget built as an island: scaffolding, folder layout, props, endpoints, real-time subscriptions, the shipped Vue helpers | `vendor/aaix/laravel-islands/resources/boost/skills/islands-development/SKILL.md` (with `helpers-index.md` and `free-view-recipe.md` beside it) |
| A list or table: the JSON endpoint, `useDataTable`, the `DataTable` shell, search, filters, sorting, grouping, tabs, pagination | `vendor/aaix/laravel-islands-datagrid/resources/boost/skills/islands-datagrid-development/SKILL.md` (with `base-datagrid-recipe.md`) |
| The global search: sources, search tips, leading sources, own result rows, recent hits | `vendor/aaix/laravel-islands-search/resources/boost/skills/islands-search-development/SKILL.md` |
| Photos: the inline `Gallery`, the full-screen `Lightbox`, `openLightbox`, downloads | `vendor/aaix/laravel-islands-gallery/resources/boost/skills/islands-gallery-development/SKILL.md` |
| The file manager: access gate, hosting page, search sources, text extraction, storage, configuration | `vendor/aaix/laravel-islands-filemanager/resources/boost/skills/islands-filemanager-development/SKILL.md` |

A package installed from a Composer path repository may live outside `vendor/`; read the same
relative path inside that working copy then.
