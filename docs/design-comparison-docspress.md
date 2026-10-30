# Docszine × Docspress 设计规范对照

更新时间：2026-09-16  
对照对象：

- `docszine/docs/design.md`
- `docspress/docs/design.md`
- `docspress/docs/theme-review.md`
- 两个子项目各自的静态 HTML、Classic Theme 源码和当前设计存档

本次通过 `分析并下载 Docspress 模板` 工作线程完成交叉读取。两个子项目位于同一个共享工作区，因此“交换”采用共享路径互读和线程间传递文件路径的方式，没有复制、覆盖或合并任何一个主题的正式设计文档。

## 1. 结论先行

两套主题共享同一个**阅读系统基础**，但不共享同一个**产品和内容模型**。

```text
共享基础层
  白色阅读面 · 浅灰侧栏 · 细规则线 · 单一橙色强调色
  Header · Docs Tree · Article rail · Page TOC · drawers
  H1/H2/H3 · 原生 WordPress 内容 · Classic Theme fallback
  CSS 级联纪律 · 无障碍 · 响应式 · 视口/内容 QA

Docspress 产品层                    Docszine 产品层
Docs Root + Native Pages             Docs Tree + Posts/Pages
Section Index                        Issue taxonomy
Depth-first Page order               Current Issue / Issue order
Content Page                         Cover / editor note / related stories
纯文档站点                           杂志与出版系统
```

因此：

1. 不把两个 `design.md` 合并成一份总文档。
2. 各自保留产品层章节，并在各自文档中维护一份 shared foundation。
3. 暂不抽出物理上的共享 `design-shared.md`；只有当两个主题开始共享 CSS/测试代码时，再抽取实现层资产。
4. Docszine 必须采用 Docspress 已经明确的 Docs-first 基础，但不能删除或掩盖 Issue-first 的编辑出版能力。

## 2. 共同契约：可以共享的内容

### 2.1 设计来源和页面家族

三份静态样本是两边共同的视觉 S0：

- Home：站点入口、整体构图、Docs Tree、目录行。
- Section：章节介绍、分组、编号、连续阅读关系。
- Article：文章 rail、正文、边注、媒体、TOC 和上下篇。

两边的 `design.md` 都正确地把静态样本视为参考来源，而不是部署代码或固定内容。`humanities-docs-*` 文件名、`FIELD NOTES` 品牌和演示 URL 都不能进入 Docspress 或 Docszine 的正式身份。

### 2.2 共同视觉基础

以下规则可以作为两个主题的 shared foundation：

| 项目 | 共同规则 |
| --- | --- |
| Surface | 白色主阅读面，浅灰辅助/侧栏表面 |
| Type | 本地系统无衬线字体，body 约 `16px / 1.68` |
| Color | 深灰正文、低竞争度 metadata、单一橙色 accent |
| Border | `1px` 细线；橙色线只作为章节或当前状态信号 |
| Shell | 最大宽度约 `1500px`，桌面左 rail / 内容 / 可选右 TOC |
| Geometry | header `64px`、local bar `44px`、左 `248px`、右 `208px`、padding `28px` |
| Article | 内部 rail + prose，文章最大宽度约 `924px`，正文约 `620px` |
| Density | 连续列表和规则线，不使用 dashboard 式卡片墙 |
| Motion | 只用于 drawer/menu 等状态，支持 reduced motion |

### 2.3 共同响应式状态

两边的断点意图一致：

- `>1280px`：完整桌面，Docs Tree 常驻，Article 可有右 TOC。
- `981–1280px`：右 TOC 先消失，避免压缩正文。
- `901–980px`：左 rail 转为 local navigation/drawer，Article 内部 rail 可保留。
- `641–900px`：header search 收起，正文继续保持可读宽度。
- `<=640px`：文章单列，global menu、Docs Tree/Issue drawer、TOC drawer 分离。

这部分共享的是“什么时候改变信息架构”的方法，不是要求两个主题的每个 breakpoint 数字永远完全相同。

### 2.4 共同语义和可访问性

两边应共同遵守：

- H1/H2/H3 使用真实标题。
- H2/H3 与当前页 TOC 使用同一套稳定 ID 逻辑。
- 链接负责导航，button 负责 disclosure。
- Drawer 需要 `aria-expanded`、`aria-hidden`、`inert`、Escape、focus return 和 scroll lock。
- 当前页面与当前 Section/祖先状态要能区分，不能只靠橙色。
- 图片自然比例、caption、表格容器和长 URL 不能导致 viewport 横向溢出。
- 对比度、focus-visible、200% zoom、reduced motion、键盘访问必须进入 QA。

### 2.5 共同 Classic Theme 边界

两个主题都应：

- 使用原生 Pages、Posts、菜单、媒体、摘要、评论、搜索和标准模板层级。
- 将特殊编辑语义放入可选 companion plugin，而不是让主题拥有不可迁移的数据。
- plugin 停用时仍能显示普通 WordPress 内容。
- 不注册 Docs custom post type，不引入 page builder、SPA、远程字体或运行时远程依赖。
- 将内容查询集中在 `inc/` 查询函数，模板只做语义结构和安全输出。
- 保留 `front-page.php`、`page.php`、`single.php`、`home.php`、`archive.php`、`search.php`、`404.php` 等标准入口。

## 3. 产品层差异：不能直接复制的内容

### 3.1 Docspress：纯文档主题

Docspress 的核心假设是：

```text
一个可配置 Docs Root
  → Native Pages 的 parent/order
    → Docs Tree
      → Section Index
        → Content Page
```

Docspress 的 `design.md` 和源码已经围绕这个模型建立了：

- `inc/docs.php` 负责 root、树、开放路径和 depth-first 顺序。
- `page-templates/docs-section.php` 负责显式 Section Index。
- `page.php` 根据是否属于 Docs subtree 在文档页面和普通 Page 之间切换。
- Previous/Next 是 Page tree 的深度优先顺序。
- 主题不需要 Issue、封面、编辑按期号组织或跨 Issue 推荐。

这些是 Docspress 的产品层，不应直接复制为 Docszine 的全部模型。

### 3.2 Docszine：杂志与文档双轴

Docszine 必须保留现有的出版轴：

- Issue taxonomy。
- Current Issue 和 Issue number/label/order。
- cover、editor note。
- Posts 的 Essay、Methods、Field Notes、Reviews 等栏目。
- Topics、跨 Issue related articles、Issue archive 和 Issue pagination。
- 用杂志自身作为示例内容发布主题的文案。

Docszine 应采用双轴模型，而不是在两者之间二选一：

| 轴 | 主要数据 | 主要回答的问题 | 主要界面 |
| --- | --- | --- | --- |
| Docs axis | Pages 的 parent/order，必要时关联文章 leaf | 这个内容在知识/文档树的哪个位置？ | 全局 Docs Tree、Home、Section、Article 左 rail |
| Publication axis | Posts、Issue、Section/category、Topic、order、cover | 这篇文章属于哪一期、栏目和编辑关系？ | Issue landing、Article kicker、Issue contents、related |

同一篇文章可以同时存在于两个轴上。Docs Tree 不应被 Issue contents 取代，Issue contents 也不应被 Docs Tree 假装成不存在。

### 3.3 Docszine Home 的特殊处理

静态 Home 是 Docszine 的 S0，因此 Home 必须先呈现文档入口：

1. Docs Tree。
2. Docszine 的站点说明和 H1。
3. Start here / directory rows。
4. 使用说明或设计原则。
5. Current Issue 作为入口后的 editorial block。

Docspress 的纯文档 Home 规则可以直接作为结构参考；但 Docszine 不能因此删除 Current Issue，只能改变它在首页中的位置和竞争层级。

### 3.4 Docszine Section 与 Issue archive 的区分

Docspress 的 Section Index 是 Docs Tree 的一个分支索引。Docszine 当前的 `taxonomy-docszine_issue.php` 是 Issue landing page。两者都可能使用连续列表和分组，但不能混为一套模板：

- Docs Section：展示文档树中的子页面/入口和阅读顺序。
- Issue archive：展示一期出版物、栏目分组、文章元数据、封面、editor note 和上下期。

这正是当前 Docszine Theme 的 P0：它已经有 Issue archive，却还没有真正对应静态 Section 的页面家族。

## 4. 当前实现的相互位置

### 4.1 Docspress 已经领先的部分

从源码和文档看，Docspress 已经有：

- 可配置 Docs Root。
- Native Pages 的递归树。
- Section template。
- depth-first Previous/Next。
- H2/H3 TOC 和 heading ID 处理。
- 文档页面和普通 WordPress 页面 fallback。

所以 Docspress 当前主要是 hardening 问题：CSS 级联、generic selector、focus、drawer modality、mobile search、`:has()` fallback、editor.css 和标准表面分离。

### 4.2 Docszine 当前值得保留的部分

Docszine 已经有：

- Issue/Section/Topic 的编辑出版模型。
- Current Issue 和 Issue archive 页面。
- Article grid、section rail、TOC、drawer、related、上下篇。
- `front-page-editorial` 的普通 WordPress 内容入口。
- plugin 可选边界和生产运行验证基础。

因此 Docszine 不是要把 Docspress 代码整包复制过来，而是要把 Docspress 的 Docs axis 和 shared foundation 补到自己的出版轴之上。

### 4.3 当前 Docszine 的主要差距

| 差距 | 影响 | 应对 |
| --- | --- | --- |
| Home 首屏是 Current Issue hero | 偏离静态 Home 的入口职责 | P0：Docs-first，Issue block 后置 |
| 左 rail 是 Issue contents | 无法表达全局文档层级 | P0：建立统一 Docs Tree renderer |
| 没有静态 Section 对应的页面家族 | 不能按文档分支进入内容 | P0：补 Docs Section template/path |
| CSS 使用 generic class 和局部覆盖 | 易与 WordPress/plugin/用户 HTML 冲突 | P1：逐步采用 Docszine namespace |
| 媒体布局依赖 `:has()` | 基础排版缺少可靠 fallback | P1：wrapper 直接输出媒体语义 class |
| Article heading/content parser 较脆弱 | 嵌套/重复 ID/包装结构可能回归 | P1：统一内容处理管线 |
| mobile control/search/断点仍需收敛 | 交互和维护成本增加 | P1/P2：按 shared foundation 整理 |

## 5. 设计文档应该如何共存

### 5.1 各自正式文档的职责

`docspress/docs/design.md` 应继续是 Docspress 的规范，重点维护：

- Docs Root 和 Native Pages 数据模型。
- Section Index 和 Content Page 的纯文档路径。
- Docspress namespace、编辑器约定和标准 fallback。

`docszine/docs/design.md` 应继续是 Docszine 的规范，重点维护：

- 静态样本 S0 及 Docszine 视觉契约。
- Docs axis 与 Publication axis 的双轴模型。
- Home/Section/Article 与 Issue landing 的关系。
- Docszine plugin 的 Issue/Topic/editorial boundary。
- 当前 Theme 的 P0/P1/P2 修改顺序。

### 5.2 建议共享但不要物理复制的章节

两份文档可以分别维护内容相同或相近的章节：

1. visual source hierarchy；
2. shared color/geometry/type tokens；
3. responsive state model；
4. Docs Tree/disclosure/accessibility；
5. Article rail/TOC/heading IDs；
6. CSS selector/cascade/motion 方法；
7. Classic Theme security/fallback；
8. viewport/content/interaction QA。

这样做的好处是：每个主题的规范在单独阅读时完整，同时保留各自对差异的解释。当前不抽 `design-shared.md`，避免第三份文件变成真正规范不清楚的中间层。

### 5.3 未来可能抽取的实现层

只有出现真实代码重复时，才考虑抽取：

- 共享 token contract 测试。
- viewport/content/interaction QA fixture。
- focus/drawer 行为测试。
- CSS lint/namespace 检查脚本。

即使以后共享测试，也不能共享：

- Issue 查询和 Docspress Page tree 查询。
- Issue sidebar 与 Docs Tree 的语义组件。
- Current Issue hero 与纯文档 Home。
- 两个主题的 brand、text domain、函数前缀、语言包、发布 README 和截图。

## 6. 不应直接复制的实现细节

- 不将 `docspress` 前缀改成 `docszine` 后就当作完成；要重新确认每个模板的数据来源和 fallback。
- 不把 Docspress 纯 Page depth-first 顺序直接用于 Docszine 的 Issue article order；Docszine 需要同时处理文档顺序和 Issue order。
- 不把 Docspress 的 `docs-sidebar` 直接改名为 `issue-sidebar`；这会继续混淆两个导航轴。
- 不把 Docszine 当前的 `issue-sidebar` 反向复制给 Docspress；它不适合纯 Docs Tree。
- 不将 Docspress 的 `--dp-*` token 原样用于 Docszine；Docszine 后续应采用自己的 `--dz-*` 或清晰的 `--docszine-*` 命名。
- 不把 `Humanities Docs`、`FIELD NOTES`、示例文章、固定页面 ID 或静态 URL 带入任何一个发布主题。
- 不为统一视觉而把两个主题的 Section、Article、Issue 路由强行合并。

## 7. 已确认的下一步

1. 保留两个正式 `design.md`，不做覆盖或合并。
2. 将本对照记录作为 Docszine 侧的协作记录。
3. Docszine 下一轮先实现 Docs Root/Docs Tree 和 Home/Section 结构，不先扩大 Issue 首页。
4. Article 继续复用现有 rail/TOC，但把左侧 Issue contents 调整为 Docs Tree + 明确的 Issue context。
5. 之后按照 Docspress 已发现的 hardening 项目整理 Docszine CSS、drawer、mobile search、media fallback 和 editor.css。
6. 完成代码后再决定是否抽取 shared QA/test 资产；当前不新增共享运行时依赖。

## 最终判断

Docspress 的 `design.md` 可以作为 Docszine 的**共同基础参照**，但不是 Docszine 的产品规范。Docszine 自己的 `design.md` 必须保留 Issue、Current Issue、栏目、Topic 和跨 Issue 编辑关系，并明确说明：这些出版关系与全局 Docs Tree 是两个互补轴。

两边真正应该共享的是“怎样构成一个安静、可读、可定位、可回返的 Classic Theme 阅读表面”；不应该共享的是“内容为什么属于这里、从哪里排序、如何被编辑出版”的产品决定。
