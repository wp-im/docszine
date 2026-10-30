# Docszine

Docszine is a WordPress Classic Theme for a document-led editorial magazine: long-form reading, issue navigation, and structured archives without turning the content into a page-builder canvas.

## Reference captured

The local reference is the `Field Notes Magazine 1.0.1` Classic Theme package from the supplied ChatGPT project conversation.

- Archive: `reference/field-notes-magazine-1.0.1.zip`
- Extracted reference: `reference/field-notes-magazine/`
- SHA-256: `9efba99e990d4f6f3ac2662d923a929c153dcbfd7f55214974ce085f5f415327`
- Package contents: 37 files, including PHP templates, CSS, JavaScript, and documentation

按当前项目约定，Docszine 的设计、文案和实现属于我们自己的项目成果。这里保留 Field Notes 压缩包只是为了保存我们在 ChatGPT 对话中形成的设计/开发底稿；主题身份、text domain、函数前缀、资源、许可证声明和实现已整理为独立的 Docszine 发布包。

## Design analysis

### Core idea

The template treats a publication as a reading system rather than a blog feed. Its main unit is the relationship between:

`Current Issue → Issue archive → Article → Section / Topic / Author`

The page hierarchy is therefore editorial and navigational. A reader can enter through the current issue, move through the issue's ordered contents, use a page table of contents inside an article, and continue through related sections or topics.

### Visual characteristics

- Quiet, light editorial surface: white background, soft gray panels, thin rules, dark gray text, and a restrained orange accent.
- Wide desktop composition: fixed left issue rail, central reading column, and optional right-side article TOC.
- Strong typographic hierarchy: oversized issue/article titles, small uppercase-like metadata labels, deck text, reading time, and compact section headers.
- Information density comes from alignment and rules rather than cards, shadows, gradients, or decorative effects.
- The home page is a sequence of editorial modules: current issue hero, lead story, ordered story list, cross-issue section/topic columns, and archive fallback.
- Responsive behavior changes the navigation model at breakpoints: rails become local bars and drawers, the TOC becomes a drawer, and multi-column article layouts collapse into a single reading flow.

### Structural strengths

- Uses WordPress-native Posts, Users, Categories, Tags, Media Library, menus, and template hierarchy.
- Keeps the custom Issue taxonomy optional: the theme still renders ordinary posts/pages if the companion plugin is disabled.
- Computes issue navigation from post relationships and editorial order instead of asking editors to maintain sidebar widgets manually.
- Builds the article TOC from headings in the rendered content, with stable IDs and keyboard-accessible drawer controls.
- Uses native Classic Editor semantics for headings, paragraphs, images, galleries, captions, tables, and blockquotes.
- Keeps visual presentation in the theme and proposes editorial data/editing tools for a small companion plugin.

### Core implementation pattern to retain

1. Theme controls presentation: templates, responsive layout, typography, navigation, issue rail, article TOC, related content, and editor styles.
2. WordPress core controls ordinary content: Posts, Users, Categories, Tags, Media Library, menus, excerpts, and featured images.
3. Optional plugin controls publication-specific data: Issue taxonomy, issue order, issue metadata, current issue, and editorial insertion tools.
4. Every special layout should have a semantic editor representation and a safe fallback when the plugin is absent.

## Docszine direction

Docszine keeps this editorial logic while developing its own visual identity and code. The implementation is a small Classic Theme and a visual/content contract, not a blind rename of Field Notes files. The current milestone includes:

- `style.css`, `functions.php`, setup/enqueue files, header/footer, and the standard Classic Theme hierarchy;
- a homepage, issue archive, and single-article reading layout;
- native category/tag/author/search/archive/404 fallbacks;
- responsive left rail / local navigation / TOC behavior;
- editor styles aligned with the front-end reading measure;
- no GitHub commit or push; the WordPress subsite remains the verified production baseline while the current local R3 candidate has completed isolated runtime smoke, official Theme Check, Plugin Check for the companion plugin, the fixed-viewport visual matrix, the 320/360px zoom-equivalent reflow check, drawer interaction smoke, the first full complex-content fixture, and core empty-state fixtures; screen-reader, real-browser zoom, empty-state combinations, and contributor identity gates remain.

The companion plugin remains separate and is installed only on the Docszine subsite for Issue metadata, editorial order, homepage selections, and reading shortcodes. The current local candidate has also passed a Chromium accessibility-tree preflight for Home, Article, empty Search, and 404, empty Section/Topic/Author archives, no-menu navigation fallback, and a final isolated route smoke; real screen-reader and 200%/400% browser-zoom review remain release gates.
