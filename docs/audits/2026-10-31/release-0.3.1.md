# Docszine 0.3.1 pagination compatibility patch

0.3.1 supersedes 0.3.0 with one runtime correction: the Issue template uses the native archive query's `posts_per_page` value, including no-paging mode. A fixed 30-entry display could otherwise disagree with WordPress's main-query page count when Reading settings requested more than 30 posts, making a displayed later-page link resolve to 404. No new hook, setting or query mechanism was added.

The 121-story native regression now renders page sizes 10, 30, 100 and -1, including final-page counts of 1 and 21. Each rendered page must also have native query results; pagination must disappear in no-paging mode. These checks, transaction rollback, library ordering and modern heading checks passed on PHP 8.0.30 / WordPress 6.6.11. The complete local validation script, WPCS, Stylelint, POT, official native Theme Check and package checks also passed for 0.3.1.

The 0.3.0 responsive and keyboard acceptance remains applicable: CSS and JavaScript bytes are unchanged. The affected production Issue page was additionally checked at actual 1440 and 390px after the patch: `production/patch-0.3.1/matrix.json`, no failures.

The authenticated native WordPress uploader compared 0.3.0 with 0.3.1 and reported a successful theme update. Site content, the optional Editorial 0.1.1 plugin, menus, widgets and permalinks were preserved. Actual enqueued production asset bytes and content-version tokens matched local source: `production-assets-0.3.1.json`.

- Live site: <https://wordpress.im/docszine/>
- Repository: <https://github.com/wp-im/docszine>
- Theme package: `docszine-theme-0.3.1.zip`, 52 source-identical theme files.
- Theme SHA-256: `74e74dc76ec59d8d71550b6e2be6d350a7fbb625b08bd5883d02d822deed2b9e`.
- 0.3.0 remains an immutable earlier release, with all three GitHub installation/source assets downloaded and compared successfully against local packages.
- The persistent Studio preview also has the 0.3.1 source. Physical-device and human assistive-technology certification remain outside this technical/browser acceptance; no WordPress.org submission was performed.
