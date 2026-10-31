# Docszine 0.3.0 acceptance and deployment

## Released theme

- Theme: 0.3.0, standard PHP Classic Theme; WordPress 6.6+ / PHP 8.0+.
- Optional Editorial plugin: 0.1.1, unchanged. Issue metadata and shortcodes remain plugin-owned.
- Installation package: `docszine-theme-0.3.0.zip`, 52 files under the single `docszine/` root.
- SHA-256: `dc66ff7d69904d0defae45064a7ab58c6acd6ec5e7f132d231406524c1f3aff7`.
- ZIP contents compared byte-for-byte with `theme/`; ZIP integrity and prohibited-file checks passed.

## Requirement evidence

- **Shared shell and widgets:** one main landmark and both native widget areas on all routes. Existing widget IDs preserved. Article/Page TOC is inside that content; neither sidebar contains it. Primary menu, Docs Tree and Issue navigation remain distinct.
- **Responsive reading:** 16 route types at actual 320, 390, 768, 1024, 1280, 1440 and 1920px; 112 anonymous checks. All 16 routes also checked in a real Studio administrator session at actual 320 and 1440px. Six additional toolbar/layout boundary checks at actual 600, 601, 782, 783, 1179 and 1180px. Final report: `responsive/matrix.json`, 150 checks, no geometry, landmark, privacy or toolbar failures. Initial viewport-control calibration observations are retained separately and are not counted as passing coverage.
- **Visual review:** desktop and phone route captures reviewed. Reading surfaces use the same 620px maximum measure. Corrected Issue, Category and 404 axis differences, search Page-result hierarchy, missing author labels, native search widgets, empty-state heading hierarchy and password-form controls. See the route screenshots under `responsive/`.
- **Real toolbar:** native WordPress 7.1.4 administrator session, both 32px and 46px toolbar states. Header/local bar/drawers follow the visible toolbar; on mobile the toolbar can scroll away while header returns to top 0. TOC target remained below the local controls with a 12px gap.
- **Drawers:** actual 390px short-height viewport, left width 272.92px and right width 343.11px; both begin below the real toolbar and fit a 400px-high viewport. Verified forward/backward focus cycle, Escape return, inert background, scroll lock, height-only resize persistence and desktop-breakpoint cleanup. At the article end, sidebar bottom was 685.81px and footer top 773.81px. Top menu opens independently without a modal drawer or scroll lock. See `responsive/interactions.json`.
- **Long-lived library:** native 121-story regression covers complete editorial sorting before limits, ID tie-breaking, request cache reuse, full previous/next sequence, rendered 30/30/1-entry archive pages and a bounded sidebar that retains the current story. Transaction rollback confirmed. Dependency-free tree tests cover 1,000 deep Pages and cyclic parent data without silent truncation or infinite traversal.
- **Compatibility:** current source ran on PHP 8.0.30 / WordPress 6.6.11 using official Playground CLI 3.1.60. A fresh request after deactivating Editorial rendered Home, article and Page with both rails, one main and native Docs navigation. Studio tests used PHP 8.4.22 / WordPress 7.1.4.
- **Standards:** PHP/JS syntax, existing contract tests, WPCS, Stylelint, POT generation, line endings and package checks passed. Official Theme Check 20260901 returned success with no required items or warnings; optional block-pattern/style and custom-header/background recommendations remain. The system WP-CLI emitted upstream PHP deprecation notices during POT generation; theme runtime and standards checks passed.
- **Cache correctness:** asset versions use SHA-256 content prefixes, preserving cache invalidation despite normalized ZIP timestamps. Native child asset URI/path APIs remain supported.

## Production

The existing authenticated WordPress network uploader compared installed Docszine 0.2.0 with uploaded 0.3.0 and reported “Theme updated successfully” after replacement. No content export was imported; no plugin, menu, widget, permalink or sibling-site configuration was changed.

<https://wordpress.im/docszine/> loads the new theme. Every actual enqueued theme stylesheet/script matched local bytes and the expected content-based version: `production-assets.json`. Authenticated production Home, ordinary Page, Issue and article passed at actual 1440 and 390px: `production/matrix.json`. Both production drawers began at the 46px toolbar boundary, with widths 273px and 343.20px respectively: `production/drawers.json`.

The persistent Studio preview remains available at <http://localhost:8895/>. Its global Studio loader initially conflicted with site-directory-only PHP access; the user explicitly authorized changing only this QA to all-files mode. The original preview tab was refreshed and verified free of that warning. No generated loader was patched.

## Repository and recovery

Independent public repository: <https://github.com/wp-im/docszine>. Source, design guidance, tests and audit evidence are versioned independently of Docspress. Immutable reference downloads, generated builds, dependencies, private administration captures and the production content export are excluded from Git. Source packages include development manifests and public documentation.

Rollback theme: `build/docszine-theme-0.2.0.zip`, SHA-256 `51729be1b1a4a0d8c6de24039c589c854331df60d54c15410ccf5fb97ebeeab3`, integrity verified. The content WXR in ignored `build/` is an additional content snapshot, not a full database/media recovery image. Rollback would use the normal theme upload replacement flow; no database schema migration is involved.

## Limits

Browser and technical simulation replace physical-device acceptance at the user's request. No human screen-reader certification or WordPress.org acceptance/submission is claimed. Complete Issue ordering loads the stories for one Issue once per request; 121-story native correctness and 1,000-page in-memory tree tests do not establish unlimited database scalability.
