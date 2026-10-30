# Docszine maturity upgrade — approved execution scope

Owner authorization: 2026-10-30. Upgrade the existing theme, deploy the corresponding wordpress.im/docszine subsite, and create/update the independent wp-im/docszine GitHub repository. WordPress.org submission is excluded.

## Baseline and method

Current local theme: 0.2.0; optional Editorial plugin: 0.1.1. Keep Issue/Section/Topic/Author publishing relationships alongside the existing Docs navigation axis. Preserve native widgets, menus, content, permalinks and optional-plugin degradation. Do not import Docspress wholesale or modify historical reference files.

## Implementation order

1. Freeze the 0.2.0 source/package and establish an independent local Git repository, excluding references, generated artifacts, dependencies and credentials.
2. Reuse shell-start/end: bound sidebars/footer outside article containment; one main reading landmark; default two panels on all templates.
3. Move TOC exclusively into the current article; rightbar owns widgets/context. Retain distinct Primary Menu, Docs Tree and Issue navigation.
4. Unify actual visible WordPress toolbar/header/local-control/anchor geometry. Left drawer outer width <=70vw, right <=90vw; keyboard focus, dialog semantics, inert background, scroll lock, short-height and width-change cleanup.
5. Refine mobile prose, titles, tables/code/images, index/body axes, comments and footer; keep Docszine editorial identity and Issue ordering.
6. Audit native content filtering, stable heading anchors, privacy, bounded queries, tree performance, empty settings and plugin-off behavior.
7. Run existing checks, actual minimum WP/PHP runtime, Theme Check, isolated native widgets/editors, multi-width logged-in/anonymous browser checks and rollback/package/source comparisons.
8. Deploy exact verified package through existing logged-in native administration. Create public wp-im/docszine repository and publish only reviewed project source plus versioned installation packages; verify remote commit and deployed assets.

## Acceptance

Core routes: Home, Docs section, Issue, article, ordinary Page, category/tag/author/date archives, search results/empty, 404, comments/password-protected content. Key widths 320/390/768/1024/1280/1440/1920 and toolbar/breakpoint boundaries. Check actual innerWidth, not only requested viewport. No global horizontal overflow; footer ends outside sidebar background; article TOC never in an aside. Preserve both existing widget IDs. Technical/browser simulation is reported separately from real-device or human assistive-technology certification.

Updates must distinguish planned, locally tested, deployed and pushed states. Keep recoverable artifacts and do not change sibling sites or the parent management repository.
