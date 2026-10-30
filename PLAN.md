# Docszine / Plan

状态：Docszine 0.2.0 已完成本地与线上部署复核；wordpress.im/docszine 已网络启用并在子站激活，0.1.2 仍作为可回滚基线保留。项目不提交 WordPress.org 或 GitHub。

目标：把当前的 Field Notes Magazine 设计底稿发展为一个可独立安装、可维护、可发布的 WordPress Classic Theme，并在 `wordpress.im/docszine` 建立它自己的展示站。

发布就绪阶段的执行基线见 [`docs/release-readiness-plan.md`](docs/release-readiness-plan.md)。该文件优先于本文件中早期的“核心验收完成”表述：线上可用不等于已经满足 WordPress.org 送审条件。

## 0. 当前判断

当前参考包不是 Block Theme，也不是普通博客皮肤，而是一个以长文阅读和期刊导航为中心的 Classic Theme 原型：

`Current Issue → Issue → Article → Section / Topic / Author`

它的价值在于阅读结构已经成立：桌面端左侧期刊目录、中间正文、右侧文章目录；移动端把这些关系收缩为本地导航和抽屉。首页也不是固定营销页，而是从当前 Issue、文章顺序、分类、标签和最新文章组成编辑流。

Docszine 应该保留这套“出版物作为阅读系统”的核心，但不做 Field Notes 的机械改名。

## 1. 参考包审计结果

### 已通过的基础检查

- 31 个 PHP 文件：`php -l` 全部通过。
- 2 个 JavaScript 文件：`node --check` 全部通过。
- `style.css` 位于主题根目录，`index.php`、`comments.php`、`header.php`、`footer.php` 和主要 Template Hierarchy 文件齐全。
- 已正确使用 `wp_head()`、`wp_footer()`、`wp_body_open()`、`body_class()`、`language_attributes()`、`post_class()`、导航菜单和 Classic Editor styles。
- 输出层大部分使用了 `esc_html()`、`esc_attr()`、`esc_url()`、`wp_kses_post()`；未发现 `eval()`、`exec()`、`shell_exec()`、`unserialize()`、直接 `$wpdb` SQL、AJAX/REST 自定义写入口或远程下载逻辑。
- 主题在 Issue taxonomy 不存在时具备普通 Posts/Pages 的降级路径。

### 发布前必须处理的问题

#### P1：发布包尚不完整

- 已补齐标准 `readme.txt`、`screenshot.png`、独立 `LICENSE`、`CHANGELOG.md` 和可重复打包脚本。
- `style.css`、text domain、函数前缀和包名已经切换为 Docszine。
- 已加入无 WordPress 依赖的 heading ID 回归测试；WPCS/PHPCS、Stylelint、PHP/JS、主题包检查、隔离 WordPress 运行时、官方 Theme Check、Plugin Check、固定视口视觉矩阵、缩放等效视口、抽屉交互 smoke、axe WCAG 2A/2AA 默认态与展开态扫描、首轮完整复杂正文 fixture、核心空状态 fixture 和 Chromium 可访问性树预检已有证据。屏幕阅读器/真实 200%/400% zoom 的完整手工验收、空状态组合 fixture 和 contributor 身份仍未完成，因此这些正式门槛未伪装成已通过。

这里的 P1 是“发布阻塞项”，不是已经确认的远程漏洞。

#### 已完成：身份与命名迁移

已完成一次完整的命名迁移，而不是只改 `style.css`：

- `field-notes-magazine` → `docszine`
- `Field_Notes_Magazine` → `Docszine`
- `fnm_` → `docszine_`
- `field-notes-magazine` text domain → `docszine`
- `fn_issue`、`_fn_issue_*`、`fn_current_issue`、`fn_featured_*` 等数据契约已改为 Docszine 命名并写入文档。

迁移后已扫描主题、插件和测试源树，没有发现旧身份残留；参考包保留在 `reference/`，不进入安装包。

#### P2：HTML 正文处理需要重构

当前 H2 的提取、ID 注入和 rail class 已优先使用 WordPress HTML API；跨列规则线改为 `H2.section-title` 的 CSS 伪元素，不再插入 HTML。

显式 ID、重复标题和非拉丁标题已有统一的去重与 fallback 回归测试；首轮复杂 fixture 已覆盖嵌套 HTML、分页内容、媒体、caption、gallery、embed 和短代码输出，核心空状态、两种无 Issue 文章组合和空正文/无摘要/无特色图/关闭评论组合已通过，剩余是更多多组件组合回归。

#### P2：移动端可访问性还需要实测

已有 skip link、焦点样式、Escape 关闭、Tab 范围控制和 `prefers-reduced-motion`；Chromium smoke 已验证主要状态，但最终人工可访问性验收仍需完成：

- 菜单与抽屉是否具有完整的 dialog/modal 语义；
- 关闭后焦点是否稳定回到触发按钮；
- `inert`、`100dvh`、`matchMedia().addEventListener()` 的浏览器兼容性；
- 屏幕阅读器下的地标、抽屉语义和焦点阅读顺序；
- 真实浏览器 200%/400% zoom 下的内容可达性（360/320/195/98px CSS 视口等效回归已通过，但不替代人工缩放）；
- 390px、768px、1024px、1440px 下是否出现横向溢出或内容不可达（固定视口首轮已通过）。

CSS 中的 `:has()` 选择器也应提供不依赖它的降级路径，或将相关语义类在 PHP 中直接输出。

#### P2：性能与数据契约需要收口

- Issue 文章目前先取最多 100 篇，再在 PHP 中按 `_docszine_issue_order` 排序；第一版可以接受，但需要明确 100 篇上限和未来分页策略。
- Issue 分组、分类、标签、相关文章查询应建立可验证的缓存和查询边界，避免内容量增加后出现明显 N+1 查询。
- Theme 读取的 Issue term meta、post meta 和 options 必须由 Docszine Companion Plugin 统一定义、验证和迁移。
- Theme 不能注册 Issue taxonomy、短代码或编辑功能；这些跨主题仍需存在的能力应放入插件。

## 2. Docszine 目标架构

### Theme：Docszine Classic Theme

Theme 负责：

- `front-page.php`：可编辑的杂志首页；
- `single.php`：长文阅读页、Issue rail、TOC、相关文章；
- `taxonomy-docszine_issue.php`：整期页面；
- Category / Tag / Author / Search / Archive / Page / 404 / Comments；
- header、footer、菜单、响应式 CSS、editor styles；
- 主题自己的设计系统、模板部件和降级表现。

### Companion Plugin：Docszine Editorial

暂不把它混进 Theme。后续插件负责：

- Issue taxonomy；
- Issue number、label、cover、description、editor note；
- 文章在 Issue 内的顺序；
- Current Issue 设置；
- 需要跨主题保留的 editorial shortcodes 或编辑器按钮。

Theme 在插件停用时必须继续作为普通 Classic Theme 工作。

## 3. 分阶段执行计划

### Phase 1：冻结基线与建立 Docszine 源树

- 保留 `reference/field-notes-magazine/` 为不可修改的研究底稿。
- 新建真正的 `docszine/theme/` 源树。
- 完成 Docszine 命名、text domain、函数前缀、包名、许可证和版本策略。
- 增加 `readme.txt`、`CHANGELOG.md`、`LICENSE`、`screenshot.png` 的发布结构。
- 建立可重复的 ZIP 打包和校验脚本。

交付门槛：源树内无旧身份残留，PHP/JS/CSS 基础检查通过，ZIP 可安装。

### Phase 2：先做标准 Classic Theme 核心

- 完成标准 Theme setup、enqueue、menus、custom logo、title tag、HTML5、editor styles、comments 和 fallback。
- H2 heading ID 提取、注入与 rail class 使用 WordPress HTML API；section rule 由 CSS 伪元素表达，不改变正文节点。
- 完成首页、Issue 页、文章页三条主路径。
- 完成普通 WordPress 数据下的 Category、Tag、Author、Search、Archive、Page、404。
- 建立移动端 local navigation、Issue drawer 和 TOC drawer 的可访问行为。

交付门槛：不启用插件也能安全运行；启用插件后才出现 Issue 专属内容。

### Phase 3：建立 Docszine Editorial 插件契约

- 先写数据模型和迁移说明，再写后台字段。
- 所有保存入口使用 capability、nonce、sanitize 和 object-level authorization。
- Issue cover 只接受真实图片 Attachment。
- 注册的 post meta/term meta 与保存权限保持一致。
- Classic Editor 扩展只提供编辑体验，不把短代码暴露成必须手写的工作流。

交付门槛：Theme 单独启用、Plugin 单独停用、异常数据和无权限用户状态均有明确结果。

### Phase 4：用 Docszine 自己发布自己（已完成）

第一批内容不写死进模板，而是进入 WordPress 内容层。建议先准备：

- Pages：About Docszine、Editorial Principles、Archive、Contact；
- Categories/Sections：Essays、Methods、Field Notes、Reviews；
- Topics：Classic Themes、Editorial Design、WordPress、Publishing；
- Posts：一篇总宣言、两篇设计/开发说明、两篇长文示例、一篇更新日志；
- 一个首期 Issue 和 4–6 篇属于该 Issue 的文章；
- Primary Menu、Footer Menu、站点标题、tagline、logo 和 footer 文案。

已在用户确认上线后写入生产站基线：5 个页面、4 个 Section、4 个 Topic、1 个 Issue 和 6 篇文章；首页、Issue 目录、文章顺序、菜单和站点基本信息均已配置。该基线仍是线上 Issue-first 版本，不等于本地 R3 候选包已经同步。

### Phase 5：本地与发布 QA（0.2.0 候选完成，正式目录发布门槛仍保留）

- PHP lint、WPCS/PHPCS、CSS 解析、JS syntax。
- WordPress Theme Check/Plugin Check。
- Classic Editor 内容测试：标题、分页、图片、gallery、caption、table、blockquote、embed、短代码降级。
- 断链、空状态、搜索、404、comments、分页、无特色图和无 Issue 状态。
- 键盘、屏幕阅读器基础检查和响应式截图。
- ZIP 完整性、旧版本残留扫描、SHA-256 和解压后内容对比。

已完成的证据包括 PHP/JS 检查、WPCS、Stylelint、heading ID/Docs Tree/主题契约/内容种子/readme metadata contract 回归测试、身份扫描、ZIP 测试、隔离 WordPress 页面路由、WP_DEBUG 前台复测、插件 active/inactive 降级、官方 Theme Check、Plugin Check、抽屉键盘交互 smoke、移动 TOC、媒体/短代码/分页首轮复杂 fixture、无结果 Search/无文章 Issue/当前 Issue 无文章首页/空 Category/Tag/Author archive 空状态 fixture、两种无 Issue 文章组合、浏览器三栏结构检查、临时第二期号降序检查、两侧 Widgets 实际渲染、320–1440px 无横向溢出矩阵，以及 Home/Section/Article/Issue 的桌面与移动视觉复核。屏幕阅读器/真实 200%/400% zoom 的完整手工验收、完整 contributor 身份确认和 WordPress.org 审核仍未完成，因此这些正式门槛不标记为已通过。

Plugin Check 已在隔离站点无错误通过；完整真实浏览器验收、更多空状态组合 fixture、PHP 8.x/WordPress 当前稳定版本全组合和 contributor 身份仍属于待补的正式发布门槛。它们不影响当前已完成的生产基线，但在提交 WordPress.org 或其他公共目录前必须收口。

### Phase 6：wordpress.im/docszine 生产基线与 0.2.0 部署（已完成）

部署前已做只读核实，随后在用户确认后完成：

1. 确认当前 `wordpress.im` 是否已经启用 Multisite。
2. 确认 `/docszine` 是真实的 path-based 子站，还是只能先作为普通页面/目录方案。
3. 确认源站 HTTPS、备份、管理员权限和回滚点。
4. 在不影响主站的前提下建立子站、安装 Theme/Plugin、导入内容、设置菜单和首页。
5. 逐条检查匿名访问、后台编辑、插件停用降级、移动端、搜索、404、comments 和静态资源。
6. 记录 live-to-source 版本、部署时间、包 hash 和回滚方法。

已确认并使用 path-based Multisite 创建 `/docszine` 子站；没有执行 GitHub commit 或 push。

### 6.1 线上交付记录

- Site：`https://wordpress.im/docszine/`
- Theme：Docszine 0.2.0，网络启用并在子站激活；0.1.2 作为 0.2.0 部署前的回滚基线保留。
- Plugin：Docszine Editorial 0.1.1，仅在子站激活。
- Content：Issue 01 / 6 posts / 5 pages / 4 sections / 4 topics。
- QA：线上单篇文章 PHP warning 已修复；默认 Hello world 与 Sample Page 已移入回收站。

### 6.2 2026-09-17 本地候选包与线上部署状态

- 本地候选主题：`build/docszine-theme-0.2.0.zip`，SHA-256：`51729be1b1a4a0d8c6de24039c589c854331df60d54c15410ccf5fb97ebeeab3`。
- 本地配套插件：`build/docszine-editorial-0.1.1.zip`，SHA-256：`fe99e8973b16b120b045f986a5aa0bbb80d2f2c5f4b3fe96db626463fff3bf66`。
- 2026-09-17 已在当前内置浏览器的已登录会话中只读确认目标为 `wordpress.im/docszine`；后台权限将主题上传入口重定向到 Network Admin，说明本次安装是“网络可用、子站激活”的两步流程。
- 0.2.0 主题 ZIP 已在本地隔离站点安装并激活；随后通过当前已登录浏览器在 Network Admin 替换线上主题，确认网络启用、子站激活、首页三栏、Primary Menu、文章 TOC、移动抽屉和公共路由均正常。
- 线上最终包：`build/docszine-theme-0.2.0.zip`，SHA-256：`51729be1b1a4a0d8c6de24039c589c854331df60d54c15410ccf5fb97ebeeab3`。
- 本轮明确不执行 WordPress.org 送审、GitHub commit/push、远程仓库创建或其他公开发布。

## 4. 发布版本策略

- `0.1.0`：主题本地可安装原型，完成首页 / Issue / Article 主路径。
- `0.1.1`：修复 Issue 新增表单的 taxonomy 参数警告，作为当前线上 Theme/Plugin 修订版。
- `0.1.2`：修复单篇文章在评论模板之后触发 WordPress 主循环警告，作为线上 Theme 修订版。
- `0.2.0`：共享阅读 shell、左右 Widgets rail、多期导航、文章正文统一阅读宽度、桌面右栏 TOC 与移动抽屉。
- `0.2.1`：根据线上 0.2.0 浏览器验收发现的问题进行最小修订。
- `0.9.0`：生产候选版，完成完整 QA、内容包和子站验收。
- `1.0.0`：生产稳定版，主题、插件、内容和发布文档一致。

Theme、Plugin、内容种子和 ZIP 必须分别记录版本，不在未验证的情况下直接把参考包改名上传生产。

## 5. 当前需要确认的边界

本计划默认采用：

- WordPress Classic Theme，不转换为 Block Theme；
- Docszine Theme 与 Docszine Editorial Plugin 分离；
- `/docszine` 使用已核实的 path-based Multisite 子站；
- 先完成本地可验证版本，再通过浏览器部署生产；
- 生产内容采用预先审定的页面/文章清单，不把临时文案直接批量写入线上。

后续若准备提交 WordPress.org，再补真实屏幕阅读器、200%/400% zoom、完整 PHP/WordPress 版本矩阵和 contributor 身份等正式审核证据；本项目当前只维护并部署到 `wordpress.im/docszine`。
