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

This evidence establishes a local preview only. Full responsive route matrix, authenticated toolbar behavior, large-library behavior, official Theme Check, final package version, production deployment and Docszine GitHub publication still require completion under the active upgrade goal.

To start this registered site later without printing credentials:

```sh
studio start --path /Users/zhanbin/Studio/docszine-qa --skip-log-details --skip-browser
```
