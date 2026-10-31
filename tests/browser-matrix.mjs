// Run inside cua_repl with documented tab/viewport handles. No standalone driver.
import fs from 'node:fs/promises';

export function measure() {
  const rect = element => {
    if (!element) return null;
    const r = element.getBoundingClientRect();
    return { top: r.top, bottom: r.bottom, left: r.left, right: r.right, width: r.width };
  };
  const main = document.querySelector('main');
  const shell = rect(document.querySelector('.docszine-shell'));
  const footer = rect(document.querySelector('footer.site-footer'));
  const toolbar = rect(document.getElementById('wpadminbar'));
  const header = rect(document.getElementById('masthead'));
  const heading = rect(main?.querySelector('h1'));
  const surface = rect(document.querySelector('main>.docszine-home-content,main>.post-list,main>.docszine-section-index,main>.page-content,main>article,main>.issue-hero'));
  return {
    width: innerWidth, height: innerHeight,
    overflow: Math.max(0, document.documentElement.scrollWidth - innerWidth),
    main: document.querySelectorAll('main').length,
    left: document.querySelectorAll('#docszine-leftbar').length,
    right: document.querySelectorAll('#docszine-rightbar').length,
    h1: main?.querySelectorAll('h1').length,
    asideToc: document.querySelectorAll('aside .docszine-toc').length,
    footerOverlap: Boolean(shell && footer && footer.top < shell.bottom - 1),
    leak: /PRIVATE_TREE_DO_NOT_LEAK|PROTECTED_(?:HEADING|BODY|EXCERPT)_DO_NOT_LEAK/.test(document.body.innerText),
    warning: /open_basedir|Fatal error:|Warning:/.test(document.body.innerText),
    toolbar, header,
    surface, heading,
    axisMismatch: Boolean(surface && heading && (Math.abs(surface.left - heading.left) > 1 || surface.width > 621)),
    toolbarOverlap: Boolean(toolbar && header && header.top < Math.max(0, toolbar.bottom) - 1),
    local: rect(document.querySelector('.docszine-local-bar')),
    prose: rect(document.querySelector('.docszine-prose')),
  };
}

export const routes = [
  ['home', '/'], ['page', '/documentation/about-docszine/'],
  ['section', '/documentation/methods/'], ['issue', '/issues/reading-surface/'],
  ['en', '/2026/10/31/reading-en/'], ['zh', '/2026/10/31/reading-zh/'],
  ['ja', '/2026/10/31/reading-ja/'], ['notes', '/notes/'],
  ['category', '/category/essays/'], ['tag', '/tag/reading-systems/'],
  ['author', '/author/admin/'], ['date', '/2026/10/'],
  ['search', '/?s=reading'], ['empty-search', '/?s=qa-no-results-8916'],
  ['404', '/qa-not-found-8916/'], ['protected', '/2026/10/31/protected-fixture/'],
];

export const failed = row => row.width !== (row.targetWidth ?? row.requested) || row.overflow > 1 || row.main !== 1 || row.left !== 1 || row.right !== 1 || row.h1 !== 1 || row.asideToc || row.footerOverlap || row.leak || row.warning || row.toolbarOverlap || row.axisMismatch;

export async function runBatch({ tab, viewport, directory, state, base, width, targetWidth = width, selection, screenshots = false }) {
  await fs.mkdir(directory, { recursive: true });
  const report = directory + '/matrix.json';
  let rows = [];
  try { rows = JSON.parse(await fs.readFile(report, 'utf8')); }
  catch (error) { if (error.code !== 'ENOENT') throw error; }
  await viewport.set({ width, height: 844 });
  const batch = [];
  for (const [name, path] of selection) {
    await tab.goto(base + path);
    const row = { state, requested: width, targetWidth, route: name, ...await tab.playwright.evaluate(measure) };
    rows = rows.filter(old => !(old.state === state && old.requested === width && old.route === name));
    rows.push(row);
    await fs.writeFile(report, JSON.stringify(rows, null, 2));
    if (screenshots) await fs.writeFile(`${directory}/${state}-${width}-${name}.jpg`, await tab.screenshot({ fullPage: false }));
    batch.push(row);
  }
  return { checked: batch.length, total: rows.length, widths: [...new Set(batch.map(row => row.width))], failures: batch.filter(failed) };
}
