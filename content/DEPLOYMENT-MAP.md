# Docszine deployment map

本文件是上线时的逐项核对表。它不执行任何 WordPress 写入。

状态：基线已执行；`https://wordpress.im/docszine/` 当前仍运行 Theme 0.1.2 与 Editorial 0.1.1。R3 Docs-first 升级已在本地完成首轮实施，尚未同步到线上。

当前线上页面是 Issue-first 基线，不是本地 R1 视觉候选版。下一次部署前必须以本地发布包、全新站点验收和视觉回归结果为准，并重新获得当前任务的线上变更确认。

## Site

- Network：`wordpress.im`
- Site address：`docszine`
- Public URL：`https://wordpress.im/docszine/`
- Site title：`Docszine`
- Site language：English (United States)
- Admin email：沿用网络管理员当前邮箱 `info@wordpress.im`

## Install order for the next explicit deployment

1. Create the path-based site.
2. Upload and install `build/docszine-theme-0.1.2.zip`.
3. Upload and install `build/docszine-editorial-0.1.1.zip`.
4. Switch to the Docszine site dashboard and activate the Docszine Theme.
5. Activate Docszine Editorial for this site only.
6. Create the native Categories and Tags listed in `content/README.md`.
7. Create the Issue and assign its metadata and post order.
8. Create the five Pages and six Posts from `content/`.
9. Set the static front page to Home, then assign Primary and Footer menus.
10. Verify anonymous front-end routes before considering the site built.

## Acceptance routes

- `/docszine/`
- `/docszine/about-docszine/`
- `/docszine/editorial-principles/`
- `/docszine/archive/`
- `/docszine/contact/`
- `/docszine/issues/issue-01-reading-surface/`
- One single Post with Issue rail and table of contents
- One Section archive, one Topic archive, search, comments, and 404

## Public-write gate

The site creation, package upload, theme/plugin activation, menu settings, and content publication were external changes and were executed only after explicit user confirmation in the active task.
