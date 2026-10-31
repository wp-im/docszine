# Library regression and release progress

## Changes verified in native WordPress

The baseline selected the earliest 100 stories before applying editorial metadata order. Issue queries now read and cache a complete single Issue, sort by explicit editorial order / date / ID, then apply the requested display limit. The Issue archive displays 30 entries per page. Sidebar contents show 100 entries plus the current article when outside that range, with a link to the complete paginated Issue. Previous/next navigation reads the full sequence.

Per-request Issue, grouping and Docs Tree caches include site/locale context; Docs Tree also keys the configured root. The root traversal uses an indexed stack and a visited set rather than repeatedly scanning the full page collection. Rendering guards against cyclic parent relationships.

Tradeoff: complete Issue ordering still loads all published stories in that Issue into memory once per request. This removes silent truncation; it does not establish unlimited archive scalability. The native regression uses 121 stories. The 1,000-page deep/cyclic tree checks use dependency-free fixture objects, not a database load benchmark.

## Evidence

- Native Studio PHP 8.4.22 / WordPress 7.1.4: 121 stories, explicit order outside the old SQL cap, ID tie-break, shared query across limits, all navigation boundaries, bounded sidebar with current item, rendered archive pages 1/2/5 with 30/30/1 entries, modern heading IDs. Test transaction rollback verified.
- Minimum runtime PHP 8.0.30 / WordPress 6.6.11: same native library/parser regression passed via official Playground CLI 3.1.60. Raw result retained in ignored `build/minimum-runtime-result.json`.
- Fresh minimum-runtime request after deactivating Editorial: home, article and ordinary Page rendered with both sidebars and one main landmark; native Docs navigation remained available. Passed output retained in ignored `build/minimum-runtime-plugin-off.txt`.
- Native Theme Check 20260901: exit 0; no REQUIRED or WARNING messages. Recommendations: block patterns, block styles, optional custom header/background. No filler features added solely to remove these recommendations.
- WordPress Coding Standards: passed; PHP syntax and existing Docs Tree/heading/theme/content/readme contracts passed; `git diff --check` passed.
- Packaging now derives versions from source headers and rejects mismatched filenames/readme tags before creating or replacing archives. Deliberately mismatched version 9.9.9 was rejected.

## Not yet accepted

The final browser matrix, authenticated toolbar checks, package and production deployment are recorded in [release-0.3.0.md](release-0.3.0.md). Native plugin-off assertions and a minimum-runtime Blueprint are included for repeatable verification.

Opening the persistent site in the Studio app injected its normal global MU-plugin loader outside the chosen site-directory PHP boundary, causing an open_basedir warning. The user explicitly approved changing only Docszine QA Preview to Studio's all-files access mode. `studio config set --path /Users/zhanbin/Studio/docszine-qa --file-access all-files` completed and restarted this QA server; reading the specific config key confirmed all-files. Fresh HTTP and the user's original browser tab after reload both showed no PHP warning. Evidence: `studio-warning-resolved.jpg`. No generated loader was patched and no sibling site was changed.

This intermediate regression record is superseded by the final 0.3.0 release record.
