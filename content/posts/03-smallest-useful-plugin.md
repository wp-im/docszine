---
title: The Smallest Useful Plugin
section: Methods
topics: [WordPress, Publishing]
issue: Issue 01 — The Reading Surface
issue_order: 3
excerpt: A companion plugin should preserve editorial meaning across themes, not turn a theme into a private application.
---

WordPress gives a theme a tempting amount of freedom. It is possible to put taxonomies, custom fields, shortcodes, and every editorial rule into `functions.php`. The result can work for one site and become impossible to move.

## Put durable meaning in the durable layer

An Issue is content metadata. Its number, label, cover, editor’s note, and article order should remain available if the visual layer changes. That makes Issue data a plugin responsibility.

The Docszine Editorial plugin is intentionally small. It registers one taxonomy, a few protected fields, three homepage settings, and a handful of reading shortcodes. It does not choose the page layout.

## Keep the theme useful when the plugin sleeps

Without the plugin, Docszine should still render Pages, Posts, Categories, Tags, Authors, Search, Archives, and 404 states. The magazine-specific layer becomes optional enrichment rather than a hidden requirement.

[docszine_definition term="Portability"]The ability to change presentation while keeping the editorial archive understandable and addressable.[/docszine_definition]

## Small is a maintenance feature

Every field creates a future question: who owns it, how is it validated, what happens when it is empty, and what happens when the feature is removed? A small plugin keeps those questions answerable.

