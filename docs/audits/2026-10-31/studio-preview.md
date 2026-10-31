# Persistent Studio preview

User approved rebuilding the incomplete temporary QA registration in a persistent directory.

- Studio name: Docszine QA Preview
- Site directory: `/Users/zhanbin/Studio/docszine-qa`
- URL: <http://localhost:8895/>
- PHP 8.4.22; WordPress 7.1.4; native Studio runtime with site-directory access.
- Current working theme copied to `wp-content/themes/docszine`; Editorial plugin copied and activated. This is not the final release.
- Source/runtime `diff -qr` returned no differences after the navigation correction.
- Existing offline temporary QA registration was preserved. No production settings or content changed.

## Content

Seeded a new local database with documentation hierarchy, a section index template, one Issue, English/Chinese/Japanese articles, and protected/private visibility fixtures. Native default widgets remain available in the right sidebar. The temporary seed PHP file was removed after execution. No production export was imported.

## Verified

- Desktop homepage visually reviewed; native Search block button wrapping corrected.
- Mobile Chinese article: actual CSS viewport 394px (requested override 390px), no document horizontal overflow, prose approximately 358px.
- Left drawer 275.76px (~70%); right drawer 346.66px (~88%). Main content inert while open; closing restored focus to the trigger and removed body scroll lock. Closed drawers remain inert intentionally.
- Article TOC appears within the article, with no sidebar TOC.
- Issue previous/next navigation was reversed in the baseline. Corrected and checked in native WordPress for each seeded story, including null boundaries.
- `git diff --check` passed.

## Evidence and remaining work

Screenshots beside this file: desktop homepage, Chinese mobile article, left drawer, right drawer. Temporary viewport override reset; homepage retained as a browser deliverable.

This file records the initial local preview. The completed responsive/runtime/toolbar checks and production deployment are recorded in [release-0.3.0.md](release-0.3.0.md).

To start this registered site later without printing credentials:

```sh
studio start --path /Users/zhanbin/Studio/docszine-qa --skip-log-details --skip-browser
```
