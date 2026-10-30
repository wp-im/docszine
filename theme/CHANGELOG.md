# Changelog

## 0.2.0

- Added one shared Classic Theme reading shell to all public routes.
- Added `docszine-leftbar` and `docszine-rightbar` widget areas.
- Added descending multi-Issue navigation and contextual Rightbar fallback content.
- Unified Page and Article prose measure at approximately 620px.
- Moved the article TOC to the desktop Rightbar and kept it inline above mobile article content.
- Replaced route-specific rail markup with reusable template parts and one responsive drawer system.

## Unreleased

- Aligned Docs-first Home and Section content columns with the static 900px index measure at wide and mid desktop widths.
- Matched the static article heading rhythm by protecting the article kicker from the generic paragraph margin rule and restoring its explicit line-height and spacing.
- Raised the accessible text accent and muted metadata palette to keep small text readable on the white surface and soft rail.
- Added a regression contract for pale text colors in front-end and editor styles.
- Added a native Pages-based Docs Tree with an optional Docs Root Customizer setting.
- Reworked the homepage toward the static Docs-first visual baseline.
- Shared Docs Tree navigation across pages, archives, and article desktop/mobile surfaces.
- Added the Docs Tree and sidebar search to the Issue archive without merging it with Issue contents.
- Improved global menu ARIA state, focus entry/trapping, media-query compatibility, and shared sidebar background geometry.
- Aligned the Docs-first homepage rhythm with the static baseline: 38px content start, intro metadata, functional lead search, and editorial content before the Issue block.
- Added dependency-free Docs Tree regression coverage and generated the theme POT file.
- Hardened the pre-HTML-Processor heading fallback so quoted attributes and nested heading markup cannot be mistaken for real H2 tags.
- Made release ZIP generation deterministic by normalizing timestamps, sorting archive entries, and stripping variable ZIP metadata.
- Fixed the generated POT creation header during release validation so localization assets remain reproducible.
- Clarified that the public theme is standalone and does not require a companion plugin.

## 0.1.2

- Avoids a PHP warning on single article requests when the main loop is rewound after the comments template.

## 0.1.1

- Maintenance release for the Docszine Theme and Editorial plugin.
- Clarified the recommended companion plugin version.

## 0.1.0

- First Docszine Classic Theme prototype.
- Added issue-led homepage, Issue archive, long-form article layout, responsive navigation, article TOC, and Classic Editor styles.
- Added ordinary WordPress archive, page, search, comments, and 404 fallbacks.
- Separated Issue metadata and editorial controls into the Docszine Editorial companion plugin.
