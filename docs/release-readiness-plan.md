# Docszine Classic Theme 升级与 WordPress.org 发布计划

状态：R3 0.2.0 候选 / 不提交 WordPress.org 或 GitHub

适用范围：`docszine/theme/` 主题本体，以及它与 Docszine Editorial companion plugin 的边界

目标：将 Docszine 从“已在 wordpress.im 子站运行的 Issue-first 主题”升级为“可以提交 WordPress.org Theme Directory 审核的、视觉完成度稳定的、可独立运行的 Classic Theme”。

本文件是执行方案和验收标准，不代表当前主题已经通过 WordPress.org 审核。所有“通过”都必须以实际检查、实际页面和实际发布包为证据。

## 1. 先锁定判断标准

### 1.1 三层事实来源

Docszine 的开发不采用“当前线上看起来像什么就继续补什么”的方式，而采用三层事实来源：

| 层级 | 资料 | 用途 | 权威程度 |
| --- | --- | --- | --- |
| S0 视觉与交互 | `reference/static-html/humanities-docs-home.html`、`humanities-docs-section.html`、`humanities-docs-article.html` | 定义页面家族、版式、节奏、组件关系和响应式意图 | 最高 |
| S1 产品与内容模型 | `README.md`、`PLAN.md`、Docszine issue/section/topic 约定 | 定义“杂志 / 期号 / 栏目 / 文章”的内容语义 | 高 |
| S2 当前实现 | `theme/` PHP、CSS、JS、插件运行结果 | 审查对象；只保留经过 S0/S1 验证的实现 | 待修正 |

结论：静态三页是设计起点和验收基准；当前 theme 不是视觉标准，而是待推敲、待重构和待验证的对象。线上站点只用于验证 WordPress 数据、插件边界和真实浏览器行为。

### 1.2 产品定位

Docszine 不是普通博客皮肤，也不是只展示封面和文章卡片的杂志首页。它的核心是“文档化出版物的阅读系统”：

- Home 是文档入口和出版物总览；
- Section 是一个可解释、可浏览、可继续深入的栏目页；
- Article 是连续阅读、定位、注释和相关阅读的阅读表面；
- Issue 是出版组织轴，可以承载当前期、编辑按语、期号目录和跨期归档；
- Docs Tree 是全站导航轴，不能被某一期号目录替代；
- WordPress 原生内容在插件停用后仍然可以作为普通主题继续使用。

因此最终主题必须同时处理两条互不混淆的轴：

```text
站点导航轴：Docs Root → Section → Article → Topic / Related Reading
出版组织轴：Current Issue → Issue Contents → Issue Archive → Across Issues
```

## 2. 当前状态与发布判断

### 2.1 已有基础

- 已有完整 Classic Theme 入口：`style.css`、`functions.php`、`front-page.php`、`home.php`、`single.php`、`page.php`、`archive.php`、`category.php`、`tag.php`、`author.php`、`search.php`、`404.php`。
- 已有标准模板入口：`wp_head()`、`wp_body_open()`、`body_class()`、`wp_footer()`、跳过链接、`title-tag`、自动 Feed、HTML5 支持、菜单、搜索模板。
- 已有 `template-parts/`，且 Issue 相关功能主要放在 companion plugin，而不是主题中。
- `style.css`、`readme.txt`、`LICENSE`、主题截图均已存在。
- 当前 CSS 已经具有静态基准中的主要几何值：64px header、248px 左 rail、208px 右 TOC、1500px shell、article rail/prose 结构。
- 当前文章页是最接近 S0 的页面，已有文章 rail、TOC、Issue drawer、相关内容、评论和分页。

### 2.2 当前尚不能声明“可提交”的原因

下列问题在没有实际证据前，不能标记为已完成：

| 优先级 | 阻塞点 | 当前迹象 | 处理结果 |
| --- | --- | --- | --- |
| P0 | 官方 Theme Check、PHP 运行时和真实浏览器审查尚未形成完整证据 | 官方 Theme Check 已在隔离站点运行且无 ERROR/WARNING；全新站点路由、WP_DEBUG、active/inactive smoke、六类页面固定视口矩阵，以及 global menu/Docs/Issue/TOC 抽屉交互 smoke 已通过 | 补屏幕阅读器、键盘语义、真实 200%/400% zoom 和最终手工可访问性证据 |
| P0 | `readme.txt` 曾把未确认托管路径的 companion plugin 作为推荐项 | 当前公共 readme 已移除该推荐，不再把插件作为主题依赖 | 插件说明只留项目内部/子站部署文档；若未来推荐，先确认 WordPress.org 托管 |
| P0 | 静态 Home 的 Docs-first 结构与当前 Issue-first 首页不一致 | `front-page.php` 已重排为 Docs entry first，Current Issue 保留为编辑内容块 | 用真实 WordPress 内容完成三页家族视觉回归 |
| P0 | Issue contents sidebar 不能代替全站 Docs Tree | 已有统一 `docs-tree.php` renderer；Issue archive 桌面侧栏已组合 Docs Tree 与 Issue 导航，移动端由同一 local bar 分开打开；无 JS 时相关目录回退为静态内容 | 完成屏幕阅读器、键盘语义和插件停用的最终手工验收 |
| P0 | 真实浏览器中的键盘、焦点、drawer、窄屏行为未形成完整证据 | Home/Section/Article/Issue 已完成 390/640/1024/1440 固定视口几何与视觉矩阵，Search/404 已完成 390/1440 复核；抽屉开启、Escape、焦点返回、Tab 循环、reduced-motion、axe WCAG 2A/2AA 默认态与展开态、无 JS 和 360/320/195/98px CSS 视口等效回归已通过 | 补屏幕阅读器、键盘语义、真实 200%/400% zoom 和最终手工验收 |
| P0 | 发布包中资源、版权、隐藏文件、参考文件尚未逐项清单核对 | `docs/resources.md`、公共 Resources 段和干净 ZIP 已建立 | 送审前复核作者身份、每个新增资源和最终包清单 |
| P1 | 文章 heading 管线和原生正文兼容性仍需完整内容 fixture | H2 提取、ID 注入和 rail class 已优先使用 WordPress HTML API；API 不可用时使用跳过注释/引号属性的保守扫描器；嵌套 H2、重复标题、H3、blockquote、table、pre/code、列表、长 URL、Media Library 图片、caption、gallery、embed、Docszine shortcode 和 `<!--nextpage-->` 分页 fixture 已通过；无结果 Search、无文章 Issue、当前 Issue 无文章首页、空 Category/Tag/Author archive，以及两种无 Issue 文章组合已通过 | 补更多无媒体、无 Issue 关联、无摘要、无评论及多媒体组合的空状态 fixture |
| P1 | CSS 规则需要保持可维护 | `magazine.css` 已重写为 tokens、base、layout、components、state、responsive 六层约束；仍需随内容增长保持通用 | 继续禁止页面 ID、slug 和单页补丁，新增视觉只进入通用组件或 token |
| P1 | `:has()`、`100dvh` 和旧内容包装兼容性 | `:has()` 仍仅作为增强；`100vh` fallback 已存在，旧 `.margin-note` 已清理 | 补直接 class 基础路径、复杂内容 fixture，并确认旧浏览器仍可读 |
| P1 | 国际化目录和 POT 资产未确认 | `languages/docszine.pot` 已生成，WPCS 与 text domain 检查通过 | 每次文案/模板变化后重生成并检查 POT |
| P2 | Issue 查询、排序、关联内容的性能边界未固化 | 目前有最多 100 篇后 PHP 排序等逻辑 | 明确上限、缓存、分页和插件接口，不影响首轮审核 |

### 2.3 当前决策

当前 `0.2.0` 已作为本地 R3 候选包完成结构、视觉和运行时首轮验收，但不应被描述为已通过 WordPress.org 审核。线上只部署到 `wordpress.im/docszine`，并保留当前 `0.1.2` 回滚点。版本可采用：

- `0.2.0`：完成结构与视觉迁移；当前候选包；
- `0.9.0`：所有本地/测试站验收完成，作为 WordPress.org candidate；
- `1.0.0`：审核通过后发布的首个正式稳定版本。

版本号只是项目策略，提交前还必须按照当时 WordPress.org 的实际规则刷新 `Requires at least`、`Tested up to` 和 `Requires PHP`。

### 2.4 2026-09-16 实施检查点

已完成 Docs Tree 的首轮实现：原生 Page hierarchy、可选 Docs Root、首页 Docs-first 外壳、Article/Issue archive 左 rail、侧栏搜索和共享窄屏 drawer 均已有代码入口。PHP/JS 语法、WPCS、Stylelint、Docs Tree 数据回归、heading ID 回归、主题与内容种子 contract 回归、POT 生成、基线 ZIP 检查、隔离 WordPress 运行时、官方 Theme Check、Plugin Check（companion plugin 无错误）、最终关键路由 smoke、无菜单空 landmark fallback、六类页面固定视口视觉矩阵、360/320/195/98px 缩放等效回归、抽屉交互 smoke、完整首轮复杂正文 fixture、核心空状态 fixture 和 Chromium 可访问性树预检均已有证据；屏幕阅读器/真实 200%/400% zoom 的完整手工验收、更多空状态组合和 contributor 身份仍未收口。

本检查点仍不是发布候选版。具体命令、SHA-256、工具缺口和未验证项见 [`docs/r0-audit-2026-09-16.md`](r0-audit-2026-09-16.md)。

### 2.5 2026-09-18 0.2.0 实施与线上复核

0.2.0 已完成本地隔离站点安装、官方 Theme Check/Plugin Check、公共路由、Widgets、多期排序、文章 TOC、移动抽屉、Primary Menu 和 320–1440px 几何矩阵，并通过当前已登录浏览器部署到 `wordpress.im/docszine`。Network Admin 显示主题 0.2.0 且已 Network Enable，子站后台显示 Active: Docszine。最终包和逐项证据见 [`docs/r1-audit-2026-09-18.md`](r1-audit-2026-09-18.md)。

线上复核中曾发现并修复 Primary Menu 的通用 `<ul>` reset 缺失；修订包重新上传后确认横向菜单、桌面三栏和移动菜单均正常。真实屏幕阅读器、真实 200%/400% zoom、contributor 身份及 WordPress.org 送审仍然是未完成的正式门槛。

## 3. 目标架构：先清楚分工，再写代码

### 3.1 主题本体允许做什么

主题负责 presentation：

- 模板层级和 HTML 结构；
- Docs Tree、文章 rail、TOC、drawer 等展示导航；
- 视觉 tokens、排版、布局、响应式；
- 菜单、Logo、特色图、摘要、评论、Feed 等原生主题能力；
- 对已存在的原生 Post、Page、Category、Tag、User 和 Media 的展示适配；
- 对插件提供的数据做安全、可选、无依赖崩溃的展示。

### 3.2 主题本体不能承担什么

不能在主题中新增或隐藏为主题功能的内容管理机制：

- Custom Post Type；
- Custom Taxonomy；
- Shortcode；
- Custom Block；
- Custom Role、用户资料字段或非展示性用户功能；
- 自定义数据迁移、演示内容导入、后台远程安装；
- 埋点、默认开启的 tracking、外部 API 拉取；
- 把 companion plugin 的业务逻辑复制到主题里。

Issue metadata、Issue order、Issue archive 数据仍应属于 companion plugin。主题只通过存在性检查、原生查询和已约定的展示过滤器读取它们。

### 3.3 companion plugin 的发布边界

这是发布前必须做出的明确决策：

1. 主题必须在插件停用时仍然可以显示 Home、Pages、Posts、Categories、Tags、Search、Archives、Comments、404 和普通文章。
2. 如果插件要在主题 `readme.txt` 中被推荐，必须具有 WordPress.org 允许的托管与安装路径；主题不能自动安装一个未确认托管在 WordPress.org 的 ZIP 或插件。
3. 若插件暂时只服务于 wordpress.im，应将其说明放在项目文档或站点部署文档中，不在公共主题 readme 中做不符合审核规则的安装推荐。
4. 主题发布包不能包含 plugin 源码、plugin ZIP、演示导入文件或依赖插件才能工作的关键 PHP。

## 4. 页面家族升级方案

### 4.1 Home：从 Issue-first 改为 Docs-first

目标顺序：

```text
全局 header
→ Docs entry / publication statement
→ Docs Tree 或文档目录入口
→ Section directory rows
→ Current Issue editorial block
→ latest / across issues / related reading
→ footer
```

具体要求：

- 首屏必须让用户知道“这里是什么文档系统”，不能先要求用户理解 Issue 号。
- 保留 Current Issue、cover、editor note 和 feature story，但它们变成首页中的出版编辑模块。
- Section 目录采用静态基准的编号行、标题、说明、箭头和细线节奏，不改造成密集 card wall。
- 首页在插件停用时仍显示可用的原生栏目/文章目录；插件开启时再增加 Issue 内容。
- 用 `get_template_part()` 拆分 Docs entry、section directory、current issue、editorial list、cross-issue 等页面部件，避免 `front-page.php` 成为单一大模板。
- 使用 `front-page.php` 处理静态首页，使用 `home.php` 保持 posts index 语义，不能用首页内容去污染博客索引。

### 4.2 Section：建立真正的栏目页家族

Section 不是 Issue archive 的别名，也不是简单的 Category loop。它要表达“这个栏目是什么、包含哪些内容、如何继续读”：

- 读取栏目标题、描述、可选特色图；
- 顶部呈现 eyebrow、H1、deck/summary；
- 下方按确定顺序显示编号目录行；
- 行内显示文章标题、摘要、日期/作者等必要元信息；
- 保持 `category.php` 作为原生回退，必要时使用命名 page template 或 category template part；
- 不能把 Issue cover、Issue editor note 强行套到 Section 页面上；
- Section 页面必须能从 Docs Tree 和首页进入，并能回到本栏目上下文。

### 4.3 Article：保留现有优点，修正语义与细节

文章页的核心方向是正确的，应保留并完善：

- 全站 Docs Tree 是左侧全局导航；
- Issue contents 是当前文章的出版上下文，作为独立可选区块；
- 文章 rail 用于 H2/section marker，不与全站导航混用；
- 右侧 TOC 只在足够宽的 viewport 出现；
- H2 生成稳定的可链接 ID，H3 进入 TOC 但不能造成过度拥挤；
- 文章正文保持 620px 左右的阅读宽度，图片、表格、代码和引用允许按规则扩展；
- 相关内容放在文章语义结束之后，不插入正文中间破坏阅读连续性；
- 评论、分页和上一篇/下一篇使用明确的 WordPress 语义和翻译字符串；
- 文章缺少 cover、Issue 或 TOC 时必须自然降级，而不是留下空 rail、空 drawer 或空白块。

### 4.4 原生 Classic Theme surfaces

必须建立一组真实内容 fixture，逐一验证：

- `page.php` 普通页面；
- `single.php` 普通文章；
- `home.php` 文章索引；
- `category.php`、`tag.php`、`author.php`；
- `archive.php` 日期/通用归档；
- `search.php` 有结果和无结果；
- `404.php`；
- 评论开启、关闭、嵌套、无评论；
- 特色图存在、缺失、超宽、窄图；
- 菜单存在、未设置、深层级、键盘打开；
- Logo 存在和缺失；
- Feed、分页、`<!--more-->`、`wp_link_pages()`；
- Gallery、caption、blockquote、table、code、pre、embed、audio、video、列表和链接。

## 5. Docs Tree 设计与实现要求

### 5.1 语义

Docs Tree 是站点导航，不是文章目录。它应当回答：

- 我现在位于哪一个 Section；
- 这个 Section 下有哪些文档入口；
- 当前文章如何被定位；
- 如何回到 Docs Root；
- 在窄屏下如何打开和关闭同一棵树。

### 5.2 数据来源与 fallback

当前实现的来源优先级：

1. 用户明确指定的 Docs Root 页面及其 Page hierarchy/order；
2. 未指定 Docs Root 时使用有序的 published top-level Pages；
3. 没有可用 Page 时不生成虚假目录，保留普通主题导航和内容 fallback。

菜单驱动的 Docs Tree 和插件过滤器不属于当前 candidate 的已实现契约；若未来加入，必须先补数据模型、设置说明、无配置 fallback 和视觉回归，不能只在文档中宣称支持。

实现规则：

- 不在主题中创建新的内容类型来保存目录；
- 每一个节点必须有真实链接；
- 当前节点有 `aria-current="page"` 或 `aria-current="location"`；
- 子树展开状态不能只依靠颜色或图标表达；
- 桌面、移动 drawer、文章页 local bar 必须调用同一个 renderer；
- 不能出现桌面一套内容、移动端另一套内容导致状态漂移；
- 无 JavaScript 时应回退为可见、可点击、可滚动的普通导航。

建议的文件分工：

```text
theme/inc/docs-tree.php
theme/template-parts/navigation/docs-tree.php
theme/template-parts/navigation/local-bar.php
theme/template-parts/navigation/issue-contents.php
```

命名空间统一使用 `docszine_` 前缀；CSS 使用 `docszine-` 或 `dz-` 前缀，避免 `.section`、`.source`、`.meta`、`.margin` 等泛名与插件或文章内容冲突。

## 6. 视觉系统：严格以静态 S0 为准

### 6.1 不可随意改变的基础 tokens

首轮迁移先锁定下列值，任何变化都必须在设计文档中说明原因：

```css
--dz-bg: #fff;
--dz-soft: #f6f6f7;
--dz-sidebar: #f6f6f7;
--dz-ink: #3c3c43;
--dz-faint: #6b6c72;
--dz-line: #e2e2e3;
--dz-accent: #e87917;
--dz-header-h: 64px;
--dz-local-h: 44px;
--dz-left-w: 248px;
--dz-right-w: 208px;
--dz-page-pad: 28px;
--dz-shell-max: 1500px;
```

实际 CSS 可以使用不同变量名，但要有唯一 Docszine 前缀，并且所有颜色、间距、尺寸、阴影、动效优先引用 tokens，禁止在后续局部修复中不断产生孤立魔法数字。

### 6.2 布局规则

- 全局 shell 最大宽度约 1500px；页面两侧空白是视觉节奏的一部分，不要把内容无限拉宽。
- 左 Docs Tree 约 248px；右 TOC 约 208px；中心内容负责主要阅读。
- Article inner grid 最大约 924px；内部 rail 约 170–210px；正文约 620px。
- header 高度 64px；local bar 高度 44px；二者的垂直关系必须在滚动和 drawer 开关时稳定。
- 内容边界使用线、字重、空白和橙色短规则表达，不用渐变、重阴影和无语义装饰制造层级。
- 页面各段的上/下间距必须形成一致的节奏：标题、deck、meta、rule、正文、相关内容分别有固定角色。
- 目录行的箭头、编号、说明和点击区域必须整体可点击或至少拥有明确的 link hit area。

### 6.3 颜色、对比度与状态

- `--dz-ink` 用于正文和主要标题；`--dz-faint` 只用于次要信息，不能承担唯一可读内容。
- 静态稿的浅灰 `#929299` 仅作为视觉来源记录；主题文本 token 使用 `#6b6c72`，以满足白色阅读面和浅灰 rail 上的小字号对比度要求。
- accent 橙色用于规则、当前状态、链接和局部强调，不能大面积填充造成视觉噪声。
- 所有文字、链接、placeholder、focus ring、禁用/已访问状态都要在浅灰 rail 和白色正文上分别检查。
- hover 不能是唯一状态；键盘 focus 必须清晰、连续、可见。
- focus ring 不能被 `outline: none` 或 overflow 裁掉；drawer 打开时焦点必须进入 drawer，关闭后返回触发按钮。
- `prefers-reduced-motion: reduce` 下取消滑动、缩放和非必要过渡。

### 6.4 字体与排版

- 主题不依赖远程字体；优先使用系统字体栈，保证离线、隐私和 WordPress.org 审核安全。
- H1、H2、H3 的字号、行高、字重和上下间距按页面家族统一定义，不能在 `single.php` 单独变形。
- 英文大写 eyebrow 要控制 tracking；中文标题不能机械套用过大的 letter-spacing。
- 正文行长保持可读，长单词、URL、代码和表格必须有溢出策略。
- 摘要 deck 与正文的颜色、字号、行高要有明确差异，但不能把 deck 降成低对比度注释。
- 日期、作者、Issue number 的元信息用统一的语义顺序和视觉权重。

### 6.5 响应式状态

必须对以下状态逐个截屏，不以“CSS 看起来能缩”代替验收：

| 状态 | 宽度 | 规则 |
| --- | ---: | --- |
| Wide | >1280 | 左 Docs Tree、中心内容、右 TOC 同时存在 |
| Mid | 981–1280 | 右 TOC 隐藏或转为按钮；中心阅读宽度保持 |
| Compact | 901–980 | 左 Docs Tree 转 drawer/local nav；文章 rail 保留 |
| Tablet | 641–900 | 文章 rail 逐步收缩；导航、meta 和媒体重新排列 |
| Mobile | ≤640 | 文章线性化；global menu、local Docs Tree、TOC 均为可访问 drawer |

每个状态必须验证：无横向滚动、没有被 sticky header 遮挡的锚点、没有截断标题、没有空白侧栏、没有重复焦点顺序、没有被 `100dvh` 锁死的页面。

### 6.6 组件级抠细节清单

- header：Logo 缺失时的 fallback、主菜单深度、菜单间距、搜索按钮和输入框、移动 menu 的开关与关闭路径；
- local bar：文章所在 Issue/Section 的定位、长标题省略、按钮 hit area、滚动后层级；
- Docs Tree：active、expanded、hover、focus、无子节点、空树、长标题、深层级；
- directory row：编号宽度、标题与说明基线、箭头位置、分隔线起止点、整行点击范围；
- Issue block：cover 比例、cover 缺失、期号层级、editor note、feature story 与 story list 的节奏；
- Article header：kicker、H1、deck、meta、featured image 以及首段之间的垂直间距；
- Article rail：H2 标记与正文起点对齐，窄屏不产生孤立空列；
- TOC：当前 heading、H2/H3 缩进、长标题换行、无 heading、锚点滚动偏移；
- notes/media：注释在桌面为 margin note，移动端回到正文语序且不丢失关联；
- prose：列表、引用、表格、图片说明、代码、链接、分隔线、嵌入内容；
- footer：菜单、版权/自定义文本、空值、长文本、上下边界和窄屏换行；
- comments：输入框、错误/验证、嵌套评论、头像缺失、分页与可访问标签；
- empty states：搜索无结果、无文章、无 Issue、无封面、无目录、404 都要具有完整视觉结构。

## 7. CSS / HTML / JS 工程升级

### 7.1 CSS 重组

把 `assets/css/magazine.css` 从“不断追加的整站样式”整理为以下逻辑顺序；文件是否拆分可在实施时决定，但顺序和责任必须保持：

```text
01 tokens
02 reset + base elements
03 accessibility utilities
04 site header + global navigation
05 shell + Docs Tree + local navigation
06 page-family: home / section / issue / article
07 article prose + media + notes + TOC
08 WordPress integrations: comments / widgets / pagination
09 responsive states
10 reduced motion + print
```

要求：

- 组件选择器全部带 Docszine 前缀或位于明确的 `.docszine-*` scope；
- 删除重复声明和 breakpoint 反复覆盖；
- 任何 `:has()` 都有不依赖它的基础路径；
- `100vh` 后再提供 `100dvh` 增强，不让不支持 `dvh` 的浏览器失去可用高度；
- 旧的 `.margin-note` 已清理；继续禁止新内容引入与 `.docszine-note` 不一致的注释类名；
- 不用 `!important` 解决主题与插件的长期冲突；需要覆盖时修正层级或 scope；
- 文章正文内容内部 class 不直接使用泛名，避免用户文章 HTML 与主题布局相互污染；
- 增加 print 样式：隐藏导航和 drawer，保留标题、正文、链接和必要出处。

### 7.2 HTML 与模板

- 使用语义化的 `header`、`nav`、`main`、`aside`、`article`、`footer`，每页只保留正确的主 `main`；
- 每一个 `nav` 有可区分的 accessible name；
- 标题层级从页面 H1 开始连续，不用字号代替语义；
- 图像始终有符合用途的 alt；装饰图为空 alt；封面缺失有自然 fallback；
- buttons 用于改变状态，links 用于导航，不用 `<a href="#">` 假装按钮；
- 输出全部经过上下文正确的 escaping；URL 经过允许的 URL 处理；输入经过合适的 sanitize/validate；
- 分离 presentation 和 content data，不在主题里硬编码产品数据、演示文章或站点私密信息；
- `wp_nav_menu()`、`get_search_form()`、`get_template_part()` 等既有 WordPress 入口继续作为统一调用点。

### 7.3 JS 交互状态机

导航脚本需要明确实现并测试这些状态：

```text
closed
→ opening: 写入 aria-expanded、显示 drawer、移动焦点
→ open: trap focus、锁定背景、允许 Escape
→ closing: 恢复 aria/inert/body scroll、焦点返回触发按钮
→ closed
```

要求：

- 不依赖 hover 才能打开；
- Tab/Shift+Tab 不越过打开的 drawer；
- Escape、点击关闭按钮、点击遮罩、选择内部 link 都有明确结果；
- resize 后不会留下不可见 drawer、锁死 body 或错误 `aria-expanded`；
- 同一页面多个 drawer 的 ID、label、focus target 唯一；
- no-JS 时导航仍然可读可用；
- 搜索在桌面和移动端都能访问，不用无意义的 Unicode 图形代替可读 label；
- JS 仅承担交互，不承担必须存在的文章内容和导航链接。

文章 heading 处理应优先使用结构化 HTML 处理方案；如果保留现有解析逻辑，必须限制处理边界、覆盖嵌套/属性/编码/重复标题 fixture，并证明不会破坏正文 HTML。

## 8. WordPress.org 合规检查矩阵

以下是发布前的“必须有证据”清单。`未检查` 不等于 `通过`。

### 8.1 必需文件与 metadata

- `style.css` 位于主题根目录，headers 完整且与实际版本一致；
- `index.php` 存在，作为 Classic Theme catch-all；
- `functions.php` 可安全加载，所有 include 文件存在；
- `readme.txt` 使用 WordPress.org 识别的格式；
- `LICENSE` 与 readme license 一致；
- `screenshot.png` 不超过 1200×900，比例为 4:3，内容是主题展示而非广告；
- `Theme Name`、作者、slug、贡献者和公开描述全部统一为 Docszine/docszine；
- `Tested up to`、`Requires at least`、`Requires PHP` 在提交前根据当前稳定环境复核；
- root ZIP 只包含主题文件，不包含仓库目录、`.git`、编辑器设置、系统隐藏文件、ZIP、脚本、临时构建物和私密资料。

### 8.2 许可证、原创性和资源来源

- PHP、JS、CSS、SVG、PNG、字体、图标、截图和任何第三方资源逐项列出来源与许可证；
- 没有不能以 GPL-compatible 方式再分发的资源；
- 不把外部模板、主题或网站做成未经许可的复制品；
- 自有设计、文案和示例内容的版权说明保持一致；
- 主题包不含未授权的远程资源调用；
- 若以后引入图标库/字体库，必须把许可证和必要源文件一起纳入审计，不只在网页上能加载就算完成。

建议在项目内部维护 `docs/resources.md`，在公共 `readme.txt` 的 Resources/License 部分只写适合公开发布的信息。

### 8.3 隐私与外部请求

- 主题默认不 tracking、不上报、不收集个人数据；
- 不调用外部字体、统计、广告、推荐、远程图片或未经用户明确同意的 API；
- 搜索、评论、表单和嵌入内容使用 WordPress 原生机制；
- readme 明确说明主题本身不会收集或发送用户数据；
- 如果 companion plugin 有数据处理，必须由 plugin 自己提供隐私说明，不能把它隐含在主题中。

### 8.4 安全、代码与命名

- 所有 PHP 文件执行 `php -l`；
- `WP_DEBUG=true` 下无 PHP warning、notice、deprecated、JS console error；
- 输入 sanitize/validate，输出 escape，nonce/capability 在涉及后台写入时完整存在；
- 主题公开函数、变量、hooks、CSS class、JS global、option key 均采用 `docszine_` / `docszine-` 前缀；
- 不使用已弃用函数、不抑制错误、不留下调试输出；
- 不读取或打印密码、token、数据库凭据；
- 删除不必要的全局状态，避免主题与插件用相同 function/class 名称；
- 运行 WordPress Coding Standards，并对每一个保留的例外写出局部原因。

### 8.5 可访问性

当前已具备 skip link 是好起点，但不能据此声明 `accessibility-ready`。发布策略：

1. 首先完成键盘、焦点、语义、对比度、响应式和 reduced-motion 修复；
2. 在真实页面和真实内容上进行手工键盘审查；
3. 运行自动检查作为辅助，不把自动工具零错误当成完整通过；
4. 只有确认符合 WordPress.org 该标签的人工审查要求后，才考虑保留 `accessibility-ready`；
5. 若首轮不能达到该标签要求，就不要为了营销把标签写入 `style.css`。

重点验收：skip link、focus visibility、drawer focus trap、键盘菜单、TOC 锚点、表单错误、图片 alt、标题层级、link/button 语义、色彩对比、无 JS 可用性、缩放到 200%/400% 后的布局。

### 8.6 国际化

- 所有主题可见文案使用 `__()`、`_x()`、`_n()` 等正确 gettext API；
- text domain 始终为 `docszine`，并与 slug/style.css 一致；
- translator comment 在上下文容易歧义的字符串旁提供；
- 日期、数字、作者和分页使用 WordPress 本地化 API；
- 生成并检查 `languages/docszine.pot`；如果提交包不含翻译文件，也要确认构建/提交路径符合 WordPress.org 当前规则；
- 不在 CSS content、JS alert、SVG 文本或 HTML 属性中遗留未翻译的用户可见字符串。

### 8.7 功能边界与审核安全

- 不修改 admin bar，不改变 WordPress 核心的非展示性行为；
- 激活主题不自动重定向，不自动导入 demo，不自动写核心设置；
- 主题设置只使用合适的 Customizer/Settings API 和 `edit_theme_options` capability；
- 多项主题展示设置集中、带前缀并可安全 sanitize；
- 不在主题内放 plugin、ZIP 或自动安装机制；
- 不使用主题做 spam、affiliate、强制 credit 或遮挡内容的 upsell；
- 主题 preview、后台、登录和前台不被主题逻辑误伤；
- 不在模板中保存业务数据，业务数据由 WordPress 原生内容或 companion plugin 管理。

## 9. 主题设置改造建议

当前 `inc/customizer.php` 只有 Footer text，设置入口很小，方向是可控的；但新增 Docs Root 等设置时必须先固定权限、存储和 fallback：

- 设置能力使用 `edit_theme_options`；
- 设置名带 `docszine_` 前缀；
- 多个相关的展示设置优先集中到一个 option array，避免生成大量散落 options；
- 所有设置都有默认值，站点没有配置时不出现空白结构；
- Docs Root 可以是用户选择的 Page 或 menu，不能让主题在激活时自动生成页面；
- Footer text、Docs Root、是否显示可选 Issue block 等设置都要有明确的 reset/default 行为；
- 设置说明使用翻译字符串，不把 plugin active 状态当成后台错误；
- 不为颜色、字体、间距开放任意设计器，避免破坏 S0 视觉系统和审核可预测性。

## 10. 测试与验收流水线

### 10.1 静态与代码检查

建议增加项目脚本，命令名称可以调整，但必须一条命令重跑：

```text
php -l on every PHP file
node --check on every JavaScript file
WordPress Coding Standards / PHP_CodeSniffer
Theme Check on a clean WordPress install
gettext/POT generation and text-domain scan
package manifest and prohibited-file scan
```

检查结果保存到项目的本地审计目录，不把含环境路径、凭据或临时日志的内容打进主题 ZIP。

### 10.2 WordPress 内容 fixture

准备一个可重复加载的测试站点或手工 fixture，至少覆盖：

- 1 个 Docs Root、3 个 Section、多个层级 Page；
- 10 篇普通文章，包含短文、长文、无摘要、无特色图、超长标题；
- 1 期 Issue、多个 Issue、无 Issue、Issue 缺失封面；
- H2/H3、重复标题、链接、图片、caption、quote、table、code、embed；
- 所有 archive/search/author/tag/category/404/comment 状态；
- 已设置菜单、未设置菜单、深层级菜单、移动端打开状态；
- 插件 active 与 inactive 两套结果。

### 10.3 视觉验收矩阵

每次 CSS/模板改动至少检查：

| 页面 | Wide | Mid | Compact | Mobile |
| --- | --- | --- | --- | --- |
| Home | 必测 | 必测 | 必测 | 必测 |
| Section | 必测 | 必测 | 必测 | 必测 |
| Article | 必测 | 必测 | 必测 | 必测 |
| Issue | 必测 | 必测 | 必测 | 必测 |
| Search/404 | — | — | — | 必测 |

建议固定 viewport：1440×1000、1200×900、1024×900、900×900、768×1024、390×844。截图时隐藏 admin bar、固定字体加载状态、固定内容和滚动位置。

视觉 diff 不只看颜色，还要看：

- shell 列宽和中心线；
- header/local bar 高度；
- H1/H2/正文基线；
- directory row 的间距和分隔线；
- rail、TOC、cover、注释与正文的对齐；
- drawer 打开时的焦点、遮罩和背景滚动；
- 长标题、无图、空内容的降级形态；
- 窄屏是否出现横向滚动和被遮挡的锚点。

### 10.4 手工可访问性验收

不用鼠标完成以下流程：

1. 从页面顶部进入 skip link，直达主内容；
2. 打开 global menu，遍历所有菜单项，关闭并回到触发按钮；
3. 打开 Docs Tree drawer，展开当前 Section，进入 Article；
4. 打开 TOC，进入 H2/H3，确认 sticky header 不遮挡目标；
5. 搜索并提交；
6. 评论表单出现错误时读取错误信息；
7. 在 200% 和 400% zoom 下完成相同操作；
8. 打开 reduced motion 设置，确认没有必要的动效阻碍操作。

## 11. 实施分期与停止条件

### R0 — 基线冻结

只做审查和证据收集：

- 固定三页静态截图和 token 表；
- 固定当前线上/本地主题截图；
- 记录模板层级、插件接口、当前 PHP/JS/CSS 检查结果；
- 不在页面编辑器里用临时 HTML/CSS 解决主题级问题。

停止条件：S0、S1、S2 的差异清单已确认，且没有把线上偶然状态误当成设计要求。

### R1 — 内容架构与模板边界

- 建立统一 Docs Tree renderer；
- 重排 Home 为 Docs-first；
- 建立 Section 页面家族；
- 让 Article 同时表达全站 Docs Tree 和可选 Issue context；
- 为插件 active/inactive 建立稳定 fallback；
- 用 template parts 重新切分大模板。

停止条件：插件停用时仍是一套可用的普通 Classic Theme；插件启用时 Issue 功能只是增强，不改变基本导航语义。

### R2 — 视觉系统重构

- 迁移唯一 tokens；
- 整理 CSS 层次和前缀；
- 修复排版、间距、边界、对齐、颜色、focus 和媒体规则；
- 补齐 responsive、print、reduced motion；
- 逐个页面和 viewport 做截图对照。

停止条件：三张静态基准页的页面家族关系和视觉语法在 WordPress 真实内容中保持一致；不以单一截图好看为通过条件。

### R3 — WordPress.org 合规修复

- 完善 metadata、readme、LICENSE、资源清单；
- 生成 POT；
- 完成 Theme Check/WPCS/PHP/JS/运行时检查；
- 清理发布包；
- 解决 companion plugin 推荐路径；
- 完成 accessibility 和 security review。

停止条件：所有 P0 规则都有“命令/页面/截图/清单”证据，不能只有口头确认。

### R4 — wordpress.im 子站完整建设

在不提交 GitHub、不创建远程仓库、不发布到 WordPress.org 的前提下：

- 在 docszine 子站导入或创建真实 Docszine 自身的 pages/posts/categories/tags/issues；
- 让主题用自身的文章、页面和文档系统解释自身；
- 验证管理员登录、前台、移动端、搜索、评论、Feed、错误页和插件开关；
- 保留部署前后的可恢复备份和变更记录。

停止条件：子站是完整可浏览的自说明出版物，且所有关键页面经过视觉与交互验收。

2026-09-17 补充：当前内置浏览器存在已登录的 `horse` 会话，已确认目标子站和主题上传入口；主题 ZIP 已选定但未提交安装，配套插件仍未上传。本次浏览器部署不构成 WordPress.org 发布。

当前外部状态：只读 REST 证据确认 `/docszine` 是独立的 Docszine WordPress 运行根，线上仍为旧版 Issue-first 内容；当前本机 SSH agent 没有身份，默认 SSH 私钥也不可用，因此同步前必须恢复已授权部署通道并先生成可恢复备份。

### R5 — 发布候选与送审

- 重新生成干净 ZIP；
- 解压到全新 WordPress 安装并启用；
- 不从工作目录直接复制“看起来能用”的未审计目录；
- 保存 ZIP 文件名、版本、SHA-256、文件清单和测试结果；
- 送审前再次核对官方要求和当前 WordPress 版本；
- 只有用户明确确认后才进行 WordPress.org 提交、Git commit/push 或任何公开发布。

## 12. 发布包清单

候选包必须满足：


- [ ] 主题目录名称为 `docszine`，不混入旧主题名；
- [ ] `style.css` metadata 与版本一致；
- [ ] `readme.txt` 的 `Contributors` 已替换为已确认的 WordPress.org 用户名；
- [ ] `readme.txt` 的描述、截图和功能承诺与实际一致；
- [ ] `index.php`、`functions.php`、全部被 include/template 调用的文件存在；
- [ ] `LICENSE` 存在；
- [ ] `languages/` 与 POT 生成策略已核对；
- [ ] 资源来源和许可证清单完成；
- [ ] 无 `.git`、`.DS_Store`、编辑器文件、ZIP、shell 脚本、测试 fixture、私密文件；
- [ ] 无 plugin 源码、plugin ZIP、demo importer、外部字体或远程跟踪代码；
- [ ] 无 PHP/JS/CSS 构建残留和未引用资源；
- [ ] 解压验证成功；
- [ ] 全新站点启用验证成功；
- [ ] 插件 active/inactive 验证成功；
- [ ] 视觉截图矩阵完成；
- [ ] 键盘、zoom、reduced motion、contrast 检查完成；
- [ ] `WP_DEBUG` 下无错误；
- [ ] ZIP checksum 和最终审计报告保存到项目文档，但不放入主题包。

## 13. WordPress.org 送审后的响应规则

送审不等于完成。审核反馈按三类处理：

- Required：先修复，重新打包、重新运行完整检查；
- Recommended：判断是否影响 Docszine 核心体验和维护成本，再决定是否纳入；
- 视觉偏好：只有在不损害 S0 设计原则、可访问性和主题语义时才采纳。

每轮反馈都要记录：问题、文件/行号、规则依据、修复、回归测试、版本变化。不能为了通过审核而把 Docs-first、阅读宽度、目录语义和 Issue/Docs 双轴结构临时删掉。

## 14. 最终完成定义

Docszine 只有同时满足以下条件，才可以称为“发布准备完成”：

1. Home、Section、Article 三个 S0 页面家族在 WordPress 真实数据中成立；
2. Docs Tree 与 Issue contents 是两套清晰、可独立降级的导航语义；
3. 插件停用后仍是可用的普通 Classic Theme；
4. 所有 PHP/JS/CSS、模板层级、国际化、设置权限和资源许可证都有检查证据；
5. 所有 Wide/Mid/Compact/Mobile 关键页面没有布局、焦点、溢出和锚点缺陷；
6. 发布 ZIP 是干净、可复现、无私密文件和无违规外部依赖的包；
7. WordPress.org 官方要求页面在送审当日重新核对；
8. 用户明确确认后，才执行外部提交、GitHub 发布或其他公开动作。

## 15. 官方依据

- [WordPress.org Theme Review Requirements](https://make.wordpress.org/themes/handbook/review/required/)
- [WordPress.org Theme Review Process](https://make.wordpress.org/themes/handbook/review/)
- [Classic Theme Template Files](https://developer.wordpress.org/themes/classic-themes/basics/template-files/)
- [Classic Theme Template Hierarchy](https://developer.wordpress.org/themes/classic-themes/basics/template-hierarchy/)
- [Accessibility Review Guidelines](https://make.wordpress.org/themes/handbook/review/accessibility/)

这些官方规则会变化；每次提交前必须以当日官方页面和实际审核反馈为准。
