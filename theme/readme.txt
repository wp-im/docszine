=== Docszine ===
Contributors: docszine
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 0.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, grid-layout, one-column, two-columns, three-columns, custom-logo, custom-menu, editor-style, featured-images, theme-options, threaded-comments, translation-ready

A document-led Classic Theme for long-form reading, multi-issue navigation, and editorial archives.

Copyright 2026 Docszine contributors.

== Description ==

Docszine presents a publication as a reading system: long-form articles, sections, topics, authors, archives, and related reading.

The theme uses native WordPress Posts, Pages, Users, Categories, Tags, Media Library, menus, excerpts, featured images, and widget areas. It works as a complete Classic Theme without an additional plugin; the optional Docszine Editorial plugin adds Issue metadata and editorial order.

== Frequently Asked Questions ==

= Does Docszine require a plugin? =

No. The theme works with native WordPress content and remains usable when no additional plugin is active.

= How do I create the documentation navigation? =

Create a hierarchy of published Pages, then select an optional Docs Root page in Appearance > Customize > Documentation Navigation. If no root is selected, the theme uses published top-level Pages as its fallback tree.

= Does the theme load remote fonts or tracking services? =

No. The theme does not load remote fonts, analytics, advertising, or tracking services by default.

== Changelog ==

= 0.3.1 =

* Follow the native archive query page size, including no-paging mode, so complete Issue navigation agrees with WordPress URL validation and Reading settings.

= 0.3.0 =

* Kept both widget-ready rails on every route; moved all article and Page TOCs into their own content.
* Unified toolbar, header, menu, drawer and anchor geometry, including mobile scroll-away toolbar behavior.
* Improved modal focus, inert background, viewport changes, mobile prose, native search widgets and password forms.
* Aligned indexes, Issue pages and 404 content to the reading measure; added a native Docs Section Index template.
* Corrected complete Issue ordering, pagination and previous/next navigation; guarded large or cyclic Docs Trees.
* Added content-based asset versions, child asset overrides and package version validation.

= 0.2.0 =

* Added a shared reading shell with left and right widget-ready rails on every public route.
* Added descending multi-Issue navigation and contextual fallback content.
* Constrained Page and Article prose to the same reading measure and moved the Article TOC into the Rightbar on desktop.
* Added mobile left/right rails with accessible drawers and inline mobile TOC.

= 0.1.2 =

* Initial Docszine Classic Theme release with native archives, long-form article reading, and optional publication context.

== Upgrade Notice ==

= 0.3.1 =

Includes the 0.3 reading-system upgrade and honors WordPress archive page-size settings for complete Issue pagination. Existing content and widget IDs are preserved.

== Resources ==

* Docszine includes original PHP, CSS, JavaScript, SVG, and PNG artwork licensed under GPLv2 or later.
* The theme uses system font stacks and includes no third-party fonts, icon libraries, remote assets, analytics, advertising, or tracking services.

== License ==

Docszine is licensed under the GNU General Public License, version 2 or later.
