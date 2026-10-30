# Docszine Design System and Theme Review

状态：规范 v1.0（视觉基准已锁定，R3 QA 进行中；核心几何对照已修正）  
更新时间：2026-09-17  
适用范围：Docszine Classic Theme、Docszine Editorial companion plugin、Docszine 自身示例内容

这不是一份只描述颜色和字号的 style guide。Docszine 的核心是一个“让内容可以被阅读、定位、返回和继续阅读”的出版界面，因此本文件同时规定：页面家族、信息层级、Docs Tree、文章内部轨道、响应式行为、WordPress 内容边界、CSS 组织方法、无障碍和验收方法。

当前实施证据：`docs/r0-audit-2026-09-16.md`。当前代码已经加入原生 Pages-based Docs Tree、可选 Docs Root、Docs-first Home 外壳、Section directory、Issue archive 的 Docs Tree 组合、共享侧栏搜索和无 JS 子树可见 fallback；PHP/WPCS/JS/CSS 静态检查、官方 Theme Check（无 ERROR/WARNING）、隔离 WordPress 运行时、六类页面固定视口矩阵、静态三页关键几何对照、抽屉交互 smoke、原生媒体/短代码/分页 fixture、核心空状态 fixture、空 Category/Tag/Author archive、无菜单空 landmark fallback、Chromium 可访问性树预检、axe-core WCAG 2A/2AA（默认与抽屉展开状态均为 0 violation）和缩放等效回归已通过，仍需屏幕阅读器、真实浏览器 200%/400% zoom、更多空状态组合和最终手工验收。

## 0. 先锁定基准：哪些文件可以决定设计

### 0.1 来源优先级

Docszine 后续开发采用以下不可混淆的来源层级：

| 优先级 | 来源 | 作用 | 是否可以直接改变视觉规则 |
| --- | --- | --- | --- |
| S0 | `reference/static-html/humanities-docs-home.html` | Home 页面基准：站点入口、全局导航、Docs Tree、目录列表和首页节奏 | 是，最高优先级 |
| S0 | `reference/static-html/humanities-docs-section.html` | Section 页面基准：章节说明、分组索引、连续编号和下一章节关系 | 是，最高优先级 |
| S0 | `reference/static-html/humanities-docs-article.html` | Article 页面基准：长文阅读表面、内部 rail、边注、媒体等级、TOC 和抽屉 | 是，最高优先级 |
| S1 | 本 `design.md` | 将静态样本中隐含的判断变成可执行规则，并记录审查结论 | 是；如果发生设计变化，先更新本文件 |
| S2 | `theme/` | 当前 WordPress 实现，是本文件的审查对象和后续实施对象 | 不能反过来覆盖 S0 |
| S2 | `https://wordpress.im/docszine/` | 运行时验证环境，用于检查 WordPress 查询、模板、内容和交互是否正确 | 不能作为视觉真相 |
| S3 | `docs/design-archive.md` | 历史来源说明、文件校验和早期约束 | 供追溯，不替代本文件 |
| S3 | `reference/field-notes-magazine/` | 较早的杂志主题参考包 | 仅作技术/历史参考 |
| S3 | `theme/screenshot.png` | 当前主题的 WordPress 主题预览图 | 仅作主题预览，不是设计基准 |

### 0.2 这三份静态页面各自规定什么

三份页面使用相同的视觉语言，但职责不同，不能把一个页面的局部布局机械复制到另一个页面。

1. **Home 是总目录和入口。** 它回答“这个系统是什么、从哪里开始、有哪些主要方向”。它不是当前 Issue 的广告封面，也不应该首先把读者送进一个营销式 hero。
2. **Section 是章节索引。** 它回答“这一组内容如何组织、阅读顺序是什么、哪些条目属于同一个子组”。列表行的编号、描述、箭头和分组边界都具有结构意义。
3. **Article 是阅读表面。** 它回答“我现在读到哪里、这段文字属于哪个章节、页面还有哪些段落、读完后去哪里”。它是三者中最严格、最需要保护的版式。

静态文件保留 `humanities-docs-*` 文件名是历史来源的可追溯性要求，不代表 Docszine 要在生产内容中使用 `Humanities` 或 `FIELD NOTES` 品牌。静态 HTML 不应被直接放进主题发布 ZIP，也不应把其中的演示 URL、示例文案或固定目录当成 WordPress 数据模型。

### 0.3 规范用词

- **MUST / 必须**：违反后即不符合 Docszine 设计契约，不能用“看起来差不多”解释。
- **SHOULD / 应当**：默认采用；若确有内容或兼容性理由偏离，必须记录原因。
- **MAY / 可以**：实现可选择，但不能破坏上层页面职责和视觉层级。
- **基准尺寸**：以 1440px 桌面和 390px 手机为主要对照尺寸。
- **页面家族**：Home、Section、Article 三种核心页面，以及 Issue、Generic archive、Search、Page、404 等 WordPress 边界页面。

## 1. 设计命题

### 1.1 Docszine 是文档导向的杂志表面

Docszine 可以发布 Issue、Essay、Methods、Field Notes 和 Reviews，但它的视觉骨架不是传统杂志首页的“封面—卡片—推荐”，而是：

```text
站点入口 Home
  └── 全局 Docs Tree
        └── Section 索引
              └── Article 阅读表面
                    └── 章节、边注、媒体、TOC、下一篇
```

Issue 是内容组织和编辑出版层；Docs Tree 是读者的空间导航层。两者可以互相链接，但不得让 Issue 列表悄悄取代全局文档层级。

### 1.2 五条视觉原则

1. **结构优先于装饰。** 读者首先应看懂当前页面的层级、位置和下一步；颜色、图像和动效只能帮助结构，不可制造新的竞争层级。
2. **白色是阅读面，浅灰是辅助面。** 正文所在区域尽量保持白色；侧栏、context、搜索控件和轻量插入层使用 `--soft`，不把每个内容单元都包装成卡片。
3. **关系保持在附近。** Issue、Section、作者、更新时间、阅读时间、当前章节和下一篇应当在正文附近可见；不要让读者依靠页面顶部或浏览器返回按钮重建关系。
4. **留白是节奏，不是空洞。** H2 前的间距、分隔线、文章开头和正文段落间距用于控制长文速度。不要因为“页面看起来空”而随意添加背景、阴影或更多模块。
5. **同一对象在不同屏幕保持相同身份。** 边注在移动端可以移动位置，但不能突然变成警告框；比较图在移动端可以缩小，但不能失去比较关系；Docs Tree 可以进入抽屉，但仍然是同一个全局目录。

### 1.3 明确反对的方向

以下做法与基准设计冲突：

- 将首页第一视觉固定为大面积营销 hero、订阅 CTA 或封面图。
- 将所有条目做成有圆角、有阴影、有背景的独立卡片。
- 用橙色填满按钮、标题、背景和所有链接，造成“强调色疲劳”。
- 为了桌面三栏而在手机上保留极窄的文字列、横向滚动正文或被截断的标题。
- 用视觉上的 `div` 标题代替真实的 `h1`、`h2`、`h3`。
- 让箭头同时负责展开目录和页面跳转；展开控制与链接必须是两个可区分的交互对象。
- 把旧静态样本中的固定内容、URL、品牌字符串直接写进主题模板。

## 2. 页面家族与 DOM 契约

### 2.1 共同外壳

核心页面应共享以下语义结构。类名可以根据 WordPress 模板调整，但职责和层级必须保持。

```html
<header class="site-header">全局品牌、全局导航、搜索、手机全局菜单</header>
<div class="local-bar">移动端的章节/Issue/TOC 入口</div>
<div class="doc-frame|mag-shell">
  <aside class="left-sidebar">Docs Tree 或当前 Issue 上下文</aside>
  <main id="primary">页面主内容</main>
  <aside class="page-toc">当前页面目录，仅 Article 使用</aside>
</div>
<div class="drawer-backdrop">抽屉遮罩</div>
<aside class="chapter-drawer|issue-drawer">移动端左侧目录</aside>
<aside class="toc-drawer">移动端右侧本页目录</aside>
<footer class="site-footer">站点辅助信息和菜单</footer>
```

必须满足：

- 页面只有一个主要 `h1`。
- 主内容有稳定的 `id="primary"`，供 skip link 使用。
- 侧栏和 TOC 使用 `aside`，并带有描述性 `aria-label`。
- 可交互的目录条目是链接；展开/收起是单独的 `button`。
- CSS 不得依赖某个固定文案来识别页面；页面状态使用类名、body class、模板上下文或数据属性表达。

### 2.2 Home 页面契约

Home 的参考结构是静态首页的 `doc-frame index-frame`：

1. 固定/黏性的全局 header。
2. 桌面端左侧 Docs Tree；手机端为章节目录抽屉。
3. 主内容 `content-wrap > index-page`。
4. `page-eyebrow`：站点或文档系统的短标签。
5. `h1`：站点的核心名称或定位。
6. `index-deck`：一段明确说明内容范围和阅读方式的导语。
7. `index-meta`：补充入口说明，不与导语争夺层级。
8. `lead-search`：搜索入口；若 WordPress 搜索实际在 header 已可用，这里可以做为显著的内部搜索入口，但不能伪装成不可用的装饰框。
9. 一个或多个 `directory-block`：每个 block 有分组标题和列表。
10. `directory-row`：编号、标题、描述、箭头；整行可点击，但标题与箭头仍要有清楚的视觉区分。
11. 底部 `home-columns`：使用说明、设计原则或其他低竞争度说明。

Home 的内容顺序规则：

- 第一组必须是“开始阅读”或同等意义的入门入口。
- 第二组可以是“最近整理”“精选”或跨 Section 的入口。
- Issue 展示可以存在，但应作为一个清晰命名的 editorial block，放在文档入口之后；不得默认覆盖文档入口。
- 没有内容时要有可理解的空状态，不能输出空的列表边框。

### 2.3 Section 页面契约

Section 复用 Home 的 `index-page` 排版骨架，但主内容职责不同：

1. `page-eyebrow` 显示 Section 编号、栏目或上级上下文。
2. `h1` 是章节名称。
3. `index-deck` 解释章节主题和阅读方式。
4. `section-summary` 用轻量浅灰面说明章节内部结构；它是地图，不是正文。
5. 以真实子组划分的 `directory-block`。
6. `subgroup` 可作为分组之间的短标签或锚点，但不能替代语义标题。
7. 每个 `directory-row` 需要稳定编号或顺序标记、标题、摘要/描述和方向箭头。
8. Section 末尾可出现 `Next section` 或上一/下一 Section 导航。

Section 不应把所有文章按发布日期混成一个线性列表。顺序来自编辑结构：页面层级、菜单顺序、Issue 顺序或明确的 companion plugin 元数据，必须在数据层可解释。

### 2.4 Article 页面契约

Article 是核心阅读表面，静态文章页定义的关键关系如下：

```text
左侧 Docs Tree       文章内部主网格                    右侧本页 TOC
                       ┌── title/deck/meta ──────────┐
                       ├──正文/边注/插入层───────────┤
                       ├── H2 在左侧 section rail ───┤
                       └── prev/next/related─────────┘
```

文章开头必须包含：

- `article-kicker`：Issue、Section、Topic 等上下文，短、可扫描。
- `h1`：文章标题，不能用全大写或过度压缩破坏长标题阅读。
- `article-deck`：文章摘要/导语，若无摘要则不要制造空白占位。
- `article-meta`：作者、日期、阅读时间等次级信息。

正文必须满足：

- H2/H3 是真实 HTML 标题；H2 是主要 Section rail，H3 是正文内部小节。
- H2 前插入分隔线，H2 本身进入左侧 rail；分隔线和标题要作为一个视觉事件处理。
- 普通段落的阅读宽度保持稳定，不因边注或图像随机变化。
- 文章结束后依次考虑分页、上一/下一篇、相关 Section、相关 Topic、评论；每一层之间使用规则线和间距分开。
- 右侧 TOC 只列当前文章的 H2/H3，不列 Issue、站点菜单或相关内容。

当前静态样本用 H2 生成了 `#street`、`#facade`、`#memory`、`#method` 等稳定锚点。WordPress 实现可以使用自动 slug，但必须处理空标题、重复标题、显式 ID 和多语言标题的冲突。

## 3. 视觉令牌

令牌是设计契约。实现可以把它们拆到多个 CSS 文件，但不能在组件中重新发明同义的颜色和尺寸。

### 3.1 颜色

```css
:root {
  --bg: #fff;
  --soft: #f6f6f7;
  --soft-2: #fafafa;
  --sidebar: #f6f6f7;
  --ink: #3c3c43;
  --muted: #67676c;
  --faint: #6b6c72;
  --line: #e2e2e3;
  --accent: #e87917;
  --accent-text: #a85a0a;
}
```

用法规则：

- `--bg` 是正文和主阅读面的默认背景。
- `--soft` 是侧栏、浅色 context、搜索字段和轻量插入层背景。
- `--sidebar` 默认与 `--soft` 相同；如果未来分开，必须仍然保持低对比度、无卡片感。
- `--ink` 用于正文、主要标题和品牌。
- `--muted` 用于导语、描述和可阅读的次级信息。
- `--faint` 只用于小标签、日期、辅助编号等短文本；不能承载重要说明或长段落。静态稿的 `#929299` 是视觉来源值，但在真实文本上不足以满足 WCAG AA，因此主题使用同一明度方向中更深的 `#6b6c72`。
- `--line` 用于普通分隔线、表格行和输入框边界。
- `--accent` 只用于品牌 mark、图形箭头、少量规则线和装饰性边框；它不直接承担小字号文字。
- `--accent-text` 用于 eyebrow、当前状态、文字链接 hover、可见焦点环和其他需要在白色背景上阅读的强调文字。`#a85a0a` 在白色背景上的对比度约为 5.08:1，在 `#f6f6f7` 上约为 4.71:1；若文字尺寸或背景发生变化，必须重新检查。

橙色不是背景主题色。默认状态下，一个视口内不应出现大量相同强度的橙色文本；当前项、链接 hover、当前 TOC 项和焦点状态应有明确而有限的使用边界。

可读性要求：`--faint` 与白色的对比度不应被用于正文级文字；如果小字承载实际信息，使用 `--muted`、`--accent-text` 或更深的颜色，并在实施后执行自动对比度检查。装饰性的原橙 `--accent` 不得被误当作小字号正文颜色。

### 3.2 字体

静态基准使用系统无衬线字体栈，而不是依赖远程字体：

```css
--font: -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial,
  "Noto Sans CJK SC", "PingFang SC", "Hiragino Sans GB", sans-serif;
```

规则：

- body 默认 `16px / 1.68`。
- 标题使用同一字体家族，通过字号、字重、字距和行高形成层级；不得为了“杂志感”突然切换不受控的装饰字体。
- 英文 eyebrow 使用大写、`0.12em–0.13em` 字距、`700` 字重；中文不强制大写转换。
- 正文不使用 `letter-spacing` 压缩；标题的负字距只能在大标题上使用，并必须在中英文混排和长标题下验证。
- 文章的主要阅读宽度建议控制在约 `620px`；一行过长会削弱静态样本的“阅读表面”特征。

建议层级：

| 角色 | 基准 | 说明 |
| --- | --- | --- |
| Brand | `0.95rem` 左右，`600` | 站点标识，不与 H1 竞争 |
| Eyebrow | `0.72rem`，`700` | 橙色、全大写或栏目标签 |
| Page H1 | `clamp(2.15rem, 4vw, 4rem)` | Home/Section 的页面识别 |
| Article H1 | `clamp(2.05rem, 3.05vw, 2.75rem)` | 长文阅读入口，避免过度巨大 |
| Issue H1 | 可到 `4.6rem–5.6rem` | 仅在 Issue/编辑 hero 中使用，不能强行成为所有页面的 H1 |
| Page deck | `1.0625rem–1.08rem` | 说明范围和阅读路径 |
| Article deck | `1.0625rem / 1.62` | 文章摘要 |
| Section rail H2 | `1.125rem–1.1875rem` | 右对齐、短标题、章节标记 |
| Body | `16px / 1.68` | 主阅读文字 |
| Metadata | `0.72rem–0.76rem` | 作者、日期、阅读时间 |
| List title | `0.98rem–1.16rem` | 索引和 Issue 列表条目 |

### 3.3 间距和规则线

以 `4px` 为基本单位，优先使用以下离散值：`4, 7, 8, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 24, 28, 30, 32, 34, 38, 40, 42, 46, 48, 60, 64, 80`。

这不是要求每个值都建立变量，而是限制任意的 magic number。组件中应优先使用同一组间距：

- 标题与导语：`18–24px`。
- 普通段落：约 `1.15rem` 下边距。
- H2 分隔事件：上方 `32px`、规则线下方 `22px`。
- 索引行：上下约 `15–18px`，用细线形成连续列表。
- 主要区块：约 `42–48px` 上下节奏。
- 页面底部：至少 `60–90px`，避免最后一个模块贴住 footer。

线条规则：

- 普通线：`1px solid var(--line)`。
- 编辑强调线：`1px solid rgba(232,121,23,.32)`，只用于 H2 分隔或入口结束。
- 边注短线：约 `28–30px`，放在边注标题上方，不能变成长装饰线。
- 线条是分组信号，不是每个卡片的边框；避免同时使用边框、阴影和背景。

### 3.4 页面几何

桌面基准变量：

```css
--header-h: 64px;
--local-h: 44px;
--left-w: 248px;
--right-w: 208px;
--page-pad: 28px;
```

核心 shell：

- 最大宽度：`1500px`。
- 桌面列：`248px minmax(0, 1fr) 208px`。
- 列间距：约 `40px`。
- Article 内部最大宽度：`924px`。
- Article 内部 rail：`clamp(170px, 23%, 210px)`。
- Article rail 与正文间距：`clamp(28px, 3vw, 34px)`。
- 主正文列由 `minmax(0, 1fr)` 承担，禁止因为长 URL 或未处理的图片撑破 shell。

## 4. 布局规则和断点

断点是版式状态切换点，不是为了给每个组件添加一组独立的临时覆盖。

| 宽度 | 布局状态 | 必须行为 |
| --- | --- | --- |
| `>= 1281px` | 完整桌面 | Docs Tree 常驻；Article 可显示右侧 TOC；三栏 shell |
| `981–1280px` | 窄桌面 | 左 rail 缩至约 `220px`；右 TOC 隐藏；Article rail 仍保留 |
| `901–980px` | 平板过渡 | 左 Docs Tree 进入抽屉；主文档仍可保持 rail；外侧约 `18px` |
| `641–900px` | 紧凑平板 | header 搜索收起；文章 rail 约 `138px`；正文不被压窄 |
| `<= 640px` | 手机单列 | 主体 `display:block`；左 rail/TOC 变抽屉；图片按编辑语义重排 |

### 4.1 1440px

- header 高度 64px，内容最大宽度 1500px，左右内边距 28px。
- 左侧背景从 viewport 左侧延伸到 Docs Tree 右边界，形成稳定的浅灰层；不能只给侧栏内容本身一个小块背景。
- Article 三栏同时可见，右 TOC 不应压缩正文到窄于约 620px 的阅读宽度。
- Home/Section 的主内容应该从 shell 的中间列开始，而不是覆盖整个 viewport。

### 4.2 1180px

- 右 TOC 可以保留到内容仍舒适的临界点，但若隐藏，必须是整个组件一致隐藏，不留下空列。
- 左 Docs Tree 约 220px，列间距约 28px。
- 标题、索引行和正文仍保持静态样本的呼吸感；不能因为宽度下降而把所有字号同时缩小。

### 4.3 1024px / 900px / 768px

- 1024px 主要验证窄桌面到平板切换的连续性。
- 900px 左侧全局 Docs Tree 收入抽屉；`local-bar` 固定在 header 下方，提供“章节目录”和 Article 的“On this page”。
- 768px 验证文章 rail 是否仍然帮助定位。只要正文仍能保持稳定宽度，rail 可以保留；不能在 768px 被压成无意义的 60px 标签列。
- 进入抽屉状态后，页面主内容不得与左侧目录同时形成两个可滚动的主要区域，除非抽屉已明确获得焦点。

### 4.4 390px

- 页面只有一个主阅读列，左右外边距约 `18px`。
- header 中显示菜单按钮，不显示桌面 global nav 和 header search。
- local bar 显示页面上下文；Article 同时提供 Issue/Docs Tree 入口和本页 TOC 入口，两个按钮不可互相覆盖。
- H1 建议约 `1.8rem`，line-height 约 `1.1`；标题可自然换行，不能通过固定高度裁切。
- 边注回到正文流中、左对齐；其“边注”身份通过字号、短线和浅色建立，不用大面积色块替代。
- 普通横图保持容器宽度；margin 图、portrait 图保持较小比例；pair 图默认保留双列；carousel 保留横向 snap。
- 表格若无法重排，可以在明确的 `.table-wrap` 内部横向滚动，但页面 viewport 本身不得出现横向 overflow。

## 5. 导航、Docs Tree 和抽屉

### 5.1 Docs Tree 是全局结构，不是装饰

静态样本的左侧树有 2–3 层：

- 有子项的节点：`button.tree-toggle + a.tree-link + ul.tree-children`。
- 无子项的节点：`tree-spacer + a.tree-link`。
- 箭头只改变 `aria-expanded` 和子树显示状态。
- 标题链接始终负责导航，即使该节点有子项。
- 当前文章使用 `.active`；当前 Section/祖先使用 `.section-current` 或等效状态，二者不能仅靠颜色混淆。

WordPress 生成树时：

- 使用 Pages 的 `post_parent`、菜单顺序和一个可配置的 Docs Root Page。
- 首版支持 2–3 层，但实现不能写死为两层；未来可递归渲染。
- 根页面 ID 不得写死在 PHP；通过 Customizer、主题选项或可过滤设置提供。
- 如果树中需要链接到 Post 文章，必须有明确的映射规则；不能用发布日期猜测树位置。
- 主题提供普通 Page 的结构呈现；若需要“Article belongs to Section/Issue”关系，使用现有 plugin 元数据，但不把 plugin 失效时的普通页面变成空白。

### 5.2 当前状态

至少区分以下状态：

- `is-current-page`：当前链接就是当前页面。
- `is-current-section`：当前页面的父 Section 或当前文章所属 Section。
- `is-open`：节点子树当前展开。
- `is-collapsed`：节点子树当前收起。
- `is-disabled`：只有在确有理由不能导航时使用；不能把普通链接做成灰色不可用。

状态表达优先顺序：语义属性和 DOM 结构 > 字重/位置 > 颜色。不能只通过橙色表示当前项，因为对比度和色觉差异会让状态丢失。

### 5.3 抽屉行为

全局菜单、Docs Tree/Issue drawer 和 Article TOC 共享一个状态控制器，但每个面板保留自己的语义标签。

打开面板时必须：

1. 设置触发按钮 `aria-expanded="true"` 和 `aria-controls`。
2. 对面板设置 `aria-hidden="false"`，移除 `inert`。
3. 显示 backdrop，锁定 body 主滚动。
4. 把焦点移动到面板标题或关闭按钮。
5. 在面板内部形成可循环的焦点范围。

关闭面板时必须：

1. 通过关闭按钮、backdrop、Escape 或选择导航链接关闭。
2. 恢复 `aria-expanded="false"`、`aria-hidden="true"` 和 `inert`。
3. 解除滚动锁定。
4. 将焦点返回原触发按钮；触发按钮已不存在时才选择合理的 fallback。

层级规则：

- 普通 sticky header：`1000` 左右。
- local bar：低于抽屉，但高于普通内容，约 `900`。
- backdrop：约 `1090`。
- drawer：约 `1110`。
- skip link focus：高于以上层级。

`100dvh` 可以作为现代浏览器优先值，但必须先提供 `100vh` fallback；不能让旧浏览器出现高度为 0 的抽屉。

### 5.4 动效

- 面板滑入约 `0.2–0.24s`，使用简单 ease，不使用弹跳。
- hover 只做颜色、边线或极轻微状态变化；不改变布局尺寸。
- `@media (prefers-reduced-motion: reduce)` 下关闭 smooth scroll、过渡和不必要的 transform 动画。
- 动效是状态反馈，不得成为页面主要视觉元素。

## 6. Article 阅读语法

### 6.1 内容层级

推荐的 Classic Editor 内容顺序：

```html
<p>导语或正文</p>
<aside class="docszine-note">边注</aside>
<div class="docszine-context">等宽补充层</div>
<div class="docszine-definition">术语定义</div>
<hr class="section-rule">
<h2 class="section-title" id="stable-id">章节标题</h2>
<p>正文</p>
<h3>小节标题</h3>
<p>正文</p>
```

语义边界：

- `docszine-note`：退到左侧 rail 的注释、阅读提示、来源补充；桌面右对齐，手机回流左对齐。
- `docszine-context`：与正文同宽的平静插入层，适合设计背景、编辑说明、中段总结；不表达警告。
- `docszine-definition`：橙色左线的术语/概念定义；保持短小。
- `blockquote`：引用，不能用定义框替代。
- `section-rule`：章节切换，不是单纯的装饰 hr。
- `figure`：真实媒体对象，必须配合可选 `figcaption`，不要用背景图隐藏内容。

### 6.2 媒体等级

静态文章页定义至少五种媒体意图：

| 类型 | 桌面位置 | 手机行为 | 适用场景 |
| --- | --- | --- | --- |
| 标准正文图 | 正文列宽 | 满正文宽度 | 主要说明图 |
| Margin 图 | 左侧 rail | 小比例回流正文 | 视觉注释、对象识别 |
| Portrait/Margin portrait | 左侧较小宽度 | 保持中等小图 | 人物、书封、立面 |
| Float right | 正文右侧浮动 | 取消 float、居中中等宽度 | 案例或物件图 |
| Breakout | 横跨 rail+正文 | 满内容宽度 | 需要横向空间的图 |
| Pair | 正文双列 | 默认仍双列 | 比较关系 |
| Carousel | 正文内横向轨道 | `78%` 左右卡片宽度、snap | 同组多图 |

规则：

- 不以“图片重要性”决定宽度，而以阅读任务决定宽度。
- 图片必须 `max-width:100%; height:auto`，不得因原始尺寸撑破网格。
- `figure` 的边框和底色只服务于占位或媒体边界；真实图片仍应保留自然比例。
- caption 是媒体语义的一部分，字号可以小，但不能使用不可读的极淡颜色。
- 移动端取消 float 时，必须保留媒体在原文中的邻接关系。

### 6.3 TOC 和锚点

- TOC 默认列 H2，若 H3 数量适中可以缩进列出；数量过多时不得让右侧目录变成第二篇文章。
- ID 由明确 ID 优先，其次由标题生成；重复标题追加稳定后缀。
- ID 生成必须与 TOC 读取使用同一个函数，不能出现链接目标和实际标题 ID 不一致。
- 文章分页时，TOC 只针对当前页面内容；不能把未显示的分页标题链接到当前页面不存在的节点。
- 滚动监听只更新当前项，不应持续改写 URL 或阻断用户点击。

## 7. WordPress Classic Theme 责任边界

### 7.1 Theme 负责

- WordPress 标准模板层级：front page、home、single、page、archive、category、tag、author、search、404、comments、footer。
- 页面家族的 DOM、布局、颜色、字号、响应式和可访问性。
- Docs Tree 的递归渲染和当前状态表达。
- Article rail、TOC、上一/下一篇和标准媒体/Classic Editor HTML 的安全显示。
- 自定义 logo、菜单位置、搜索、skip link、feed、评论和标准 fallback。
- 主题未启用 companion plugin 时仍然能正常显示普通 Pages、Posts 和媒体。

### 7.2 Companion plugin 负责

- Issue taxonomy、Issue number/label/order/cover 等编辑元数据。
- 文章 Section/Topic 的关系，如果现有内容模型需要。
- `docszine-note`、`docszine-context`、`docszine-definition` 等编辑语义的可选短代码或编辑器辅助。
- 需要明确数据关系的 carousel 或内容格式；插件只提供内容语义，主题决定安全呈现。
- 设置页、能力检查、数据保存和迁移；不能将主题视觉 CSS 复制到 plugin。

### 7.3 Content 负责

- 页面标题、页面父子关系和菜单顺序。
- Posts 的文章标题、摘要、正文、作者、日期、媒体和编辑顺序。
- 文案的中英文一致性、标题长度、caption、链接和来源信息。
- 演示/自我发布内容应放在 `content/` 导入资产中，而不是散落写死在模板。

### 7.4 安全边界

- 所有 URL 使用 `esc_url()`，属性使用 `esc_attr()`，普通文字使用 `esc_html()`。
- 编辑 HTML 只通过 `wp_kses_post()` 或更窄的白名单输出。
- 不接受事件属性、`javascript:` URL、未过滤的任意 HTML、动态 include、直接 SQL、运行时远程请求或命令执行。
- 主题设置和 plugin 设置使用实际 post type 对应的 capability；页面操作不能只依赖宽泛的 `edit_posts`。
- 不把页面 ID、菜单结构、站点文案或演示文章 ID 写进模板。

## 8. CSS / style 实施方法

### 8.1 建议分层

现有 `theme/assets/css/magazine.css` 已经有较清晰的注释分区，但随着 Docs Tree 和三种页面家族加入，建议整理为以下逻辑层；可以先保持一个文件，再按层次重排，没必要立刻拆成很多网络请求：

1. **Tokens**：颜色、尺寸、字体、z-index、断点。
2. **Base**：box-sizing、body、链接、图片、button、selection、focus、reduced motion。
3. **Layout**：header、shell、columns、main、footer、responsive shell。
4. **Navigation**：Docs Tree、Issue sidebar、local bar、global menu、drawer。
5. **Page family**：Home、Section、Issue、Article、Generic archive。
6. **Content grammar**：prose、headings、notes、context、definition、figures、table、gallery、carousel。
7. **WordPress integration**：菜单、logo、分页、评论、编辑器输出。
8. **Responsive overrides**：每个断点只出现一次，按从宽到窄集中处理。

如果引入 `@layer`，要先验证 WordPress 插件/区块样式的级联关系；在 Classic Theme 首版，清晰的文件内部层次比为了现代语法引入额外兼容风险更重要。

### 8.2 选择器原则

- 优先使用组件根类和直接子级，避免过深选择器。
- `:has()` 可以作为增强，但不能是 margin/breakout 语义正确显示的唯一条件；应从服务器端或编辑器输出直接添加 class。
- 不用 `!important` 解决组件之间的根本层级冲突；仅在 WordPress 生成的宽度属性、标准 utility 或确有边界的兼容规则中使用，并记录原因。
- 同一状态只能有一个主 class/attribute；例如不要同时维护 `.open`、`.is-open`、`aria-expanded` 三套互相可能不一致的状态。
- 不用固定像素 `left: 491px` 等截图坐标定位正文；全部通过 shell grid、rail 和 gap 表达。
- 图像尺寸使用 `aspect-ratio` 和自然高度，不用固定高度裁切正文媒体。

### 8.3 现有 CSS 清理要求

本次审查在现有 `magazine.css` 中发现了以下维护风险，下一次代码改动时应一并清理：

- Home、Issue、Article 和 WordPress integration 的断点规则分散在多个区域；应整理为每个断点一个集中块。
- 文章媒体使用 `.article-grid > p:has(...)` 识别包裹器；需要提供无 `:has()` 的直接 class 路径。
- 旧的 `.margin-note` 残留已清理，当前注释组件统一使用 `.docszine-note`；后续新增内容不得重新引入旧类名。
- `100dvh` 需要 `100vh` fallback。
- 现有文件包含少量重复的声明/覆盖，诸如同一组件多处定义间距、对齐和 grid；清理后应以 token 和单一组件规则为准。
- `.article-grid > *` 的 direct-child 网格契约依赖内容输出结构；应在 Classic Editor、短代码和普通段落三种输入下测试，避免内容一包裹就掉出 rail。

### 8.4 JavaScript 和 CSS 的状态分工

- JS 只负责打开/关闭、焦点、滚动锁定、TOC active、carousel scroll 等状态。
- CSS 负责状态呈现，例如 `[aria-expanded="true"]`、`.open`、`.active`。
- PHP 输出初始状态时必须与 JS 假设一致；面板初始隐藏应同时满足视觉隐藏和辅助技术隐藏。
- 没有 JS 时，普通链接和核心文章仍可用；目录展开可以退化为全部可见或普通链接，不得变成不可访问的空白区域。

## 9. 当前 Docszine Theme 的深度 Review

### 9.1 总结判断

当前主题并不是“做坏了的 CSS”，而是**已经完成一套 Issue-first 的杂志实现，并已把 S0 静态样本的 Docs-first 页面结构迁移到 WordPress 模板；当前重点从结构迁移转为级联收口、内容变化下的视觉稳定性和发布级人工验收**。

因此应采用“保留可用的视觉和数据基础，重排页面职责与导航来源”的路线，不建议从零重写整个主题。

当前成熟度判断：

| 维度 | 评价 | 说明 |
| --- | --- | --- |
| 颜色、基础字体、规则线 | 高 | 与静态样本使用同一组核心 token |
| Article 阅读网格 | 中高 | `.article-grid`、section rail、TOC、上下篇已经接近基准 |
| Article 内容扩展 | 中 | note/context/definition/media 已有，但输入契约和 fallback 仍需加固 |
| Home 与静态 Home 的一致性 | 中高 | 已改为 Docs-first 入口、目录行和后置 Current Issue；1440/1280/390px 的主栏宽度和 H1 尺度已与静态基准对齐 |
| Section 与静态 Section 的一致性 | 中高 | `category.php` 已使用 summary、编号 directory rows 和原生分页；1440/1280/390px 的主栏宽度和 H1 尺度已与静态基准对齐，分组/下一 Section 关系仍需内容验收 |
| 左侧导航语义 | 中高 | Docs Tree 已从 Issue contents 中分离，并在 Home/Page/Archive/Article 共用 renderer |
| Classic Theme fallback | 中 | 标准模板和 plugin 可选边界已有基础，需要继续验证无 plugin 场景 |
| 响应式基础 | 中高 | 已有 980/900/720/640 处理和移动抽屉，但需按统一断点清理 |
| 维护性 | 中高 | PHP/WPCS、CSS/Stylelint 已有可重复入口；正文局部 H2 处理和旧 CSS 分层仍需继续收口 |

### 9.2 已经做对、应该保留的部分

1. **核心 token 基本正确。** `#fff`、`#f6f6f7`、`#3c3c43`、`#e2e2e3` 和 `#e87917` 已经形成与静态样本一致的安静表面。
2. **Header 结构可复用。** `site-header`、品牌、主菜单、搜索和移动 global menu 已经是标准 Theme 入口，应该继续作为三种页面共同 header。
3. **Article 使用 grid rail，而不是硬编码截图坐标。** `article-grid` 的 `rail + prose` 思路正确，是当前最值得保留的布局骨架。
4. **文章 H2 已经有 section rail 和规则线。** 这直接对应静态 Article 的核心节奏。
5. **Issue drawer、TOC drawer、backdrop、inert、focus-visible 和 skip link 已有实现。** 它们应被统一成全局面板控制机制，而不是拆成互相竞争的局部脚本。
6. **Issue 与 Topic 的查询、上一/下一篇和 related 模板已具备内容产品基础。** 这些是杂志层能力，可以叠加在 Docs-first 入口之后。
7. **模板没有把所有内容写死在主题中。** 现有首页 editorial content 仍通过 WordPress loop 输出，这个方向正确。

### 9.3 P0：必须先处理的结构级问题

#### P0-1：把 Home 从 Issue hero 改为 Docs-first 入口（首轮已实施）

证据：当前 `theme/front-page.php` 先输出 Docs Root/站点说明、`docszine-directory` 和 shared Docs Tree，再输出 `current-issue` 与 editorial modules；原 Issue-first 顺序已被移除。

修改意见：

- `front-page.php` 应优先输出 Docs Tree + 文档入口首页。
- `current-issue` 不必删除，可以作为 Home 中下部的 `editorial-feature` 或明确命名的 Issue block。
- 首页首屏需要能够回答“Docszine 是什么、从哪里读、有哪些入口”，而不是只回答“当前 Issue 是什么”。
- 现有 `front-page-editorial` 可继续保留，放在入口说明或目录块之后，并让其成为普通 WordPress Page 内容。

验收：在 1440px 和 390px 截图中，静态 Home 的三件事必须可辨认：左侧/抽屉 Docs Tree、文档系统 H1/导语、至少一组目录列表。Current Issue 不得遮蔽这三件事。

#### P0-2：建立统一 Docs Tree renderer（首轮已实施）

证据：`theme/inc/docs-tree.php` 现在以原生 published Pages、可选 Docs Root、Page hierarchy 和 `menu_order` 构建树；`issue-sidebar.php` 仍只负责 Issue contents，二者职责已分离。

修改意见：

- 新增一个可复用的 Docs Tree 模板部件和查询/渲染函数，桌面、移动抽屉、Home、Section、Article 共用。
- 主题设置一个 Docs Root Page；默认可以回退到站点首页，但不能写死页面 ID。
- 用 Pages 的父子关系和菜单顺序表示 2–3 层结构；Article leaf 可以通过可解释的映射进入树。
- 当前 Article 的 Issue contents 可以保留为 Issue context，但不能代替 Docs Tree。两者若同一屏出现，应有清楚命名和不同职责。
- 当前树状态要同时输出当前页和当前 Section/祖先，不要只给当前文章一个橙色链接。
- 左栏和 Docs Tree drawer 顶部都保留真实 WordPress 搜索表单；搜索框是可提交的功能入口，不是装饰性输入框。

验收：同一套 Docs Tree 在 Home、Section、Article 桌面端显示一致；手机端只改变容器为 drawer，不改变节点顺序、链接目的和当前状态。

#### P0-3：补齐 Section 页面家族（首轮已实施，待扩展）

证据：`taxonomy-docszine_issue.php` 仍是独立 Issue archive，但已在桌面侧栏组合全站 Docs Tree 与 Issue contents；`category.php` 已成为 native Section index，输出 `docszine-section-summary`、`docszine-section-index`、编号 directory rows 和分页，避免把 Issue archive 误当 Section。

修改意见：

- 不要把 Issue archive 直接改名当作 Section；新增 Section 页面渲染路径。
- 优先使用普通 Page + `post_parent` 表示 Docs Section；若需要从现有 category/Issue 聚合文章，使用查询层提供条目，但模板仍按 Section 的 directory grammar 输出。
- `taxonomy-docszine_issue.php` 保留为 Issue landing page，负责 Issue cover、editor note、按栏目聚合文章和 Issue pagination。
- `page.php` 或专用 page template 根据 Docs Root/子页面上下文输出 Home/Section 的文档索引表面；Generic Page 仍需有安全 fallback。

验收：Section 页首屏有 Section eyebrow、H1、deck、结构说明和分组条目；条目间不是卡片墙，而是连续规则线列表。

### 9.4 P1：在结构对齐后处理的实现问题

#### P1-1：重新定义左 rail 的信息层级

当前 Issue sidebar 的字号、间距和浅灰背景与静态样本接近，说明视觉基础可直接复用。但它展示的是 Issue 内文章清单，无法支持静态样本的“开始—Section—子 Section—Article”关系。

建议顺序：

1. Docs Tree 作为全局左 rail。
2. Article/Issue context 作为 Article header kicker、local bar 或 Issue 页的次级关系。
3. 如果编辑需要快速浏览当前 Issue，在 Issue landing page 及 Article 的移动抽屉中提供 Issue contents，但明确标注 `Issue contents`。

#### P1-2：统一 Article 内容处理

当前 `inc/template-tags.php` 的 H2 提取、ID 注入和 rail class 已优先使用 WordPress HTML API；规则线改为 `H2.section-title` 的 CSS 伪元素，因此正文节点不再被主题插入或重排。API 不可用时，主题使用跳过注释和带引号属性的保守扫描器；复杂 HTML fixture 仍是发布前的剩余验证项。

建议：

- heading ID 生成、TOC 提取和 ID 注入已经共享同一套 ID 算法，并优先使用 HTML Processor/Tag Processor。
- 继续为 H2/H3、显式 ID、重复标题、空标题、分页内容和短代码输出建立 fixture；不能解析时保持保守 fallback，不得为属性值、注释或脚本中的 `<h2>` 文字添加 ID。
- 规则线已经转为 CSS 伪元素；继续用 fixture 验证 Tag Processor 只修改 H2 属性，不触碰正文容器、属性内容和短代码输出。
- 对 H2、显式 ID、重复标题、空标题、分页内容和短代码输出补测试。

#### P1-3：让媒体语义脱离 `:has()`

当前 `.article-grid > p:has(.docszine-media--margin)` 和 breakout 选择器很聪明，但把关键版式依赖在 `:has()` 上。Classic Theme 仍应在没有该能力的浏览器/渲染器中保持可读。

建议：

- plugin/内容输出直接给最外层 wrapper 添加 `docszine-media--margin` 或 `docszine-media--breakout`。
- `:has()` 只作为兼容旧内容结构的渐进增强。
- 继续以 `docszine-note` 作为唯一注释组件命名，并为直接 HTML、短代码包装和移动回流分别保留测试。
- 对 WordPress 原生 `.alignleft`、`.alignright`、`.wp-caption`、gallery 和 block editor 生成结构分别写明确的兼容规则。

#### P1-4：把 CSS 断点收敛为一套状态机

当前 CSS 已经覆盖 1280、980、900、720、640，但规则分布在 Home、Issue、Article 和 WordPress integration 多个位置，日后容易出现“桌面规则又把移动规则覆盖”的问题。

建议按本文件第 4 节集中成五个布局状态。每个状态只处理：shell、header、navigation、article rail、media 和 page-specific exceptions；不要在最后追加无注释的补丁块。

本轮已修正两处会破坏 S0 几何的级联问题：Home/Section 内容列在宽桌面收敛为 `900px` 并居中；`article-kicker` 不再被通用 `.article-grid > p` 的 margin 覆盖，且恢复 `1.2` 行高、`12px` 下间距和 H1 的 `10px` 上间距。后续新增规则必须先检查直接子级 selector 的 specificity，再决定是否修改 token 或 DOM。

#### P1-5：提高小字和交互控件的可读性

- `--faint` 不应承载长描述；长描述用 `--muted`。
- 小字号强调文字使用对比度更高的 `--accent-text`；原橙 `--accent` 只保留给图形和装饰。
- header 菜单按钮、关闭按钮和 local trigger 的实际 hit area 已提升到至少 44px。
- 链接默认保留可识别的颜色或下划线策略；不能只依赖 hover。

### 9.5 P2：维护性、扩展性和发布前质量

#### P2-1：查询与模板的边界

现有 `inc/queries.php` 已集中查询，这是正确方向。后续 Docs Tree、Section entries 和 Issue entries 也应继续通过查询函数提供，模板只负责语义标记和输出。

- 控制 Issue/Section 查询的数量和排序，不要先取 100 条再在 PHP 中长期排序。
- 对 related queries 避免同一文章反复查询相同 terms/meta；必要时使用轻量缓存。
- 任何“当前 Issue”“Docs Root”“精选 Section”设置都提供空值 fallback。

#### P2-2：标准 Classic Theme 页面

加入 Docs-first 页面后仍必须保留和验收：

- `home.php`、`archive.php`、`category.php`、`tag.php`、`author.php`、`search.php`、`404.php`。
- 普通 `page.php`、无 plugin 的 `single.php`、评论、分页、Feed。
- 自定义 logo、primary/footer menu、空菜单、无特色图、无摘要、超长标题和无结果搜索。
- `wp_nav_menu()` 输出的嵌套结构不能破坏 header 和 footer 的 CSS。

#### P2-3：视觉回归基线

每次涉及布局的改动都用相同内容和相同 viewport 比较：

```text
Home    1440 / 1180 / 1024 / 900 / 768 / 390
Section 1440 / 1180 / 1024 / 900 / 768 / 390
Article 1440 / 1180 / 1024 / 900 / 768 / 390
```

比较顺序：整体几何 → header/rail → H1/deck → 正文行宽 → 分隔线/列表节奏 → 图片 → drawer 状态 → footer。不要先根据单个文字换行决定是否改字号。

## 10. 无障碍和健壮性验收

### 10.1 键盘和辅助技术

- skip link 能到 `#primary`，且 focus 状态可见。
- 所有链接和按钮均能通过键盘访问，focus ring 使用对比度足够的文字橙 `--accent-text`，且不依赖颜色 alone。
- Docs Tree 的 toggle 有可理解的 `aria-label` 和准确的 `aria-expanded`。
- drawer 打开后焦点不穿透到页面背后的内容；关闭后焦点返回触发器。
- TOC 当前项可用 `aria-current` 或等价状态表达；滚动更新不应让屏幕阅读器不断重复无意义通知。
- H1→H2→H3 顺序不能只为了视觉 rail 而跳级。
- 图片有 alt；装饰性图像显式为空 alt；caption 不重复 alt 的完整内容。

### 10.2 视觉和输入

- 200% 缩放下正文仍可读，不能依赖固定 viewport 宽度隐藏关键内容。
- 390px 下无 body 级横向 overflow；表格横向滚动只发生在自己的容器中。
- 长英文 URL、长中文标题、无空格字符串不会撑破列表和 Article shell。
- `prefers-reduced-motion` 下没有必须依赖动画才能完成的交互。
- 高对比度、键盘 focus、浏览器默认字体放大下，当前状态仍可判断。

### 10.3 无 plugin / 空数据

- plugin 停用时，普通 Home、Page、Post、Archive、Search、404 仍输出完整阅读面。
- 没有当前 Issue 时，Home 输出 Docs-first fallback，不显示空的 Issue hero。
- 没有封面时，Issue layout 自动切换为 no-cover，不留下空白列。
- 没有摘要、特色图、相关内容、评论时，不输出空的占位框或多余规则线。

### 10.4 缩放与极窄视口的测试定义

缩放验收要区分“可读性基准”和“安全性压力测试”，不能把一个极窄视口的自动化结果误称为完整的浏览器缩放审查：

- 390px 和 1440px 是主要视觉基准；页面需要保持静态三页的比例、留白和阅读宽度。
- 360px、320px CSS 视口用于模拟常见 200%/400% 缩放后的 reflow 边界；重点检查内容重排、标题可读性、local bar、表单和文章媒体。
- 195px 与 98px 只作为更严格的溢出安全压力测试，不是视觉美感目标；此时品牌文字可以收缩，导航入口可以换行，但不能制造页面级横向滚动或使控件不可聚焦。
- 测量必须同时看 `document.documentElement.scrollWidth` 与有效 `clientWidth`、可见主内容的 bounding box、local trigger 的边界，以及每一个打开 drawer 的焦点和内部控件。
- 关闭状态的 off-canvas drawer 不得撑大页面 scroll width；打开状态必须有明确的可见面、关闭按钮和焦点入口。
- `overflow-x: clip` 只是防止隐藏状态污染页面几何，不能用来掩盖正文、表格或媒体本身的溢出；正文媒体应在自己的容器内处理。
- 当前 Chromium 等效结果为 360/320/195/98px 均无页面级横向溢出，320px 下 local bar 能够两行重排；这仍不替代真实浏览器 200%/400% 和屏幕阅读器人工验收。

## 11. 开发顺序

本文件批准后，按以下顺序实施，避免先花时间在局部颜色上而无法验证页面职责：

### Phase 1：页面和数据契约

- 确定 Docs Root Page、Home、Section、Article 的 WordPress 路径。
- 写出 Docs Tree 查询/渲染契约和当前状态规则。
- 设计 Home/Section 专用模板路径，保留 Issue landing 的独立职责。
- 用现有 Docszine 内容建立至少一个 2–3 层 Docs Tree fixture。

### Phase 2：DOM 对齐

- 先让 Home DOM 对齐静态 Home。
- 再让 Section DOM 对齐静态 Section。
- 最后把 Article 左侧从 Issue-only 关系扩展为 Docs Tree + Issue context，同时保留现有文章 rail/TOC。
- 这一步完成前不做大规模视觉微调。

### Phase 3：CSS 整理

- 合并 token，清理重复声明和断点覆盖。
- 统一 `.docszine-note`、media wrapper、TOC、drawer 状态命名。
- 为 `:has()`、`100dvh` 和 WordPress 原生媒体提供 fallback。
- 以 1440/390 两端先完成几何，再补 1180/1024/900/768。

### Phase 4：交互和无障碍

- 统一 global menu、Docs Tree/Issue drawer、TOC drawer 的焦点、inert、backdrop、Escape 和 scroll lock。
- 验证没有 JS、键盘、缩放等效视口、reduced motion 和长标题（当前已有 Chromium smoke，真实浏览器 200%/400% 仍需最终人工流程）。

### Phase 5：内容与自发布站点

- 用 Docszine 自身的 About、Editorial Principles、Archive、Issue 01 和文章填充三种页面家族。
- 内容文案服务于页面结构：入口说明、Section 说明、文章摘要、caption、下一篇关系必须真实。
- 线上子站只作为运行验收和内容验收；不改变 S0 静态设计规则来迁就现有数据。

### Phase 6：发布前 Classic Theme 验收

- PHP lint、主题模板层级、无 plugin fallback、WordPress 标准页面、内容安全输出。
- Theme Check/WPCS 或项目现有静态检查。
- 六类页面的固定 viewport 视觉回归和三种核心交互状态。
- 重新生成 ZIP，检查 ZIP 内只有可发布主题内容；`reference/static-html/`、文档、测试和历史参考不进入主题 ZIP。
- 线上检查缓存后的 CSS/JS、HTTP 200、无 PHP warning、无控制台关键错误和图片自然比例。

## 12. 最终验收清单

### 视觉

- [x] Home 首屏首先呈现文档入口，而不是 Issue hero。
- [x] Section 是分组索引，不是无结构的文章归档。
- [x] Article 保留左侧全局 Docs Tree、中心阅读表面和可选右侧 TOC。
- [x] 白色阅读面、浅灰辅助面、细规则线和单一橙色强调色贯穿页面。
- [x] H1、deck、metadata、H2 rail、正文和边注的层级稳定。
- [x] 图像宽度表达阅读意图，移动端不把所有图粗暴拉成满宽。

### 结构

- [x] Docs Tree 与 Issue contents 不再混为同一组件。
- [x] Docs Tree 的箭头只展开，标题负责跳转。
- [x] Section 顺序来自可解释的数据关系。
- [x] H2 锚点和 TOC 使用同一算法；H3 作为正文层级，不进入当前 TOC。
- [x] 普通 WordPress 内容在 plugin 缺席时仍可读。

### 响应式

- [ ] 1440、1180、1024、900、768、390 均无非预期横向溢出。
- [ ] 980px 左右左 rail 转 drawer，Article rail 在合理宽度内保留。
- [x] 640px 以下为单列，global menu、Docs Tree/Issue drawer、TOC drawer 不互相遮蔽。
- [x] drawer 可关闭、可 Escape、焦点可返回、body scroll lock 正确。
- [ ] `100vh` fallback、reduced motion 和没有 `:has()` 的基本可读性已验证。

### Classic Theme

- [ ] 首页、页面、文章、Issue、category、tag、author、search、404、comments、feed 都有明确入口。
- [ ] 自定义 logo、菜单、摘要、特色图、无图、空状态和长标题都可用。
- [x] 所有输出按上下文转义，未引入直接 SQL、动态 include、远程运行时依赖或写文件机制。
- [x] 主题 ZIP 不含静态参考 HTML，不含开发文档和历史参考包。
- [x] 发布版本号、readme、screenshot、CHANGELOG 和 ZIP 内容一致。

## 结论

当前 Docszine Theme 的基础视觉资产和 Article 阅读骨架值得保留，但必须承认一个核心事实：**静态 HTML 才是设计核心和起点，当前 Theme 是待推敲的实现对象。**

下一步最重要的不是继续给现有 Issue 首页增加模块，而是完成发布前的证据闭环：

1. 保留 Home、Section、Article、Issue、Search、404 的固定视口视觉基线，并在内容变化后重跑。
2. 补屏幕阅读器、键盘语义和真实浏览器 200%/400% 缩放流程；保留现有 Escape、焦点返回、无 JS、reduced motion、对比度和 320/360px 等效回归证据。
3. 补无媒体、无 Issue 关联、无摘要、无评论和更多 Media Library/Caption/Gallery/Embed/Shortcode/`<!--nextpage-->` 组合 fixture，并继续复跑官方 Theme Check、Plugin Check、WP_DEBUG 与插件 active/inactive 验收。

只有这些证据齐全后，现有 Article grid、TOC、drawer、Issue 查询和内容插件才真正构成可提交的 Docszine Classic Theme，而不是一套视觉相近但证据不足的实现。
