# FASHION-THEME-3.1-REPORT001

## 1. 报告状态与边界

- 任务编号：`P3-T001`
- 证据截点：2026-09-04（Asia/Shanghai）
- 评估对象：WoodMart、Rey、Blocksy Pro
- 商城基线：韩文、手机优先、高端鞋服包展示；WooCommerce Simple Product；数千商品；纯目录展示 + Kakao 咨询；不保留购物车、结账、付款、订单或顾客账户。
- 本报告只做选型和下一步方案，不代表 GPT 独立验收，不授权购买、安装、激活、部署或开始 `P3-T002`。
- 本任务没有登录或写入 Hostinger / WordPress / WooCommerce，没有购买、安装或激活主题/插件，没有写商城 PHP / JS / CSS，也没有修改 Workflow。

## 2. 当前运行基线与兼容性判定方法

| 基线 | 当前官方事实 | 对本任务的含义 |
|---|---|---|
| WordPress | 官方版本 API 于证据截点返回 `7.1`；最低 PHP 7.4 | 以 WordPress 7.1 作为“当前版本”比较基线，而不是推定线上已经是 7.1。 |
| WooCommerce | WordPress 官方插件 API 于证据截点返回 `11.1.0`，要求 WordPress 7.0+、PHP 7.4+，最后更新 2026-09-03 | 以 WooCommerce 11.1.0 作为当前比较基线；先前 11.1 延期信息已被正式发布状态取代。 |
| Hostinger | 仓库已确认 PHP 8.3；仓库没有记录线上 WordPress / WooCommerce 精确版本 | 本任务禁止登录补查；线上精确版本列为 `P3-T002` 安装前只读核验项。 |

“最近发布”“Demo 正在运行”只能证明持续维护或一个已观察组合可运行，不能替代对线上精确版本、PHP 8.3、韩文、插件组合和数千商品数据量的原型测试。未找到明确官方声明的组合一律标为“待实测”，不推定兼容。

## 3. 三候选当前版本与维护状态

| 候选 | 当前版本 / 日期 | 当前维护证据 | WordPress / WooCommerce / PHP 8.3 风险结论 |
|---|---|---|---|
| WoodMart | `8.5.7`，2026-07-29 | 官方 changelog 持续更新；8.5.0 明确加入 WordPress 7 官方支持，8.5.6 更新 WooCommerce templates | 持续维护，且有 WordPress 7 与近期 WooCommerce 模板证据。公开证据未给出“WoodMart 8.5.7 + WooCommerce 11.1.0 + PHP 8.3”的完整组合声明，安装前仍须在隔离原型实测。 |
| Rey | `3.3.0`，2026-07-31 | 官方 changelog 同时要求 Rey Theme 与 Rey Core 保持同版；3.3.0 新增 Mobile Bottom Bar 与移动导航搜索框。Oslo 官方 Demo 观察到 Rey 3.3.0、WordPress 7.1、WooCommerce 11.0.1、Elementor 4.2.3 | WordPress 7.1 组合有当前 Demo 证据，但 WooCommerce 11.1.0 比 Demo 更新；公开资料也未给出完整 PHP 8.3 组合声明，须实测。 |
| Blocksy Pro | Blocksy Theme / Companion 公开发布线均为 `2.1.56`，2026-09-03 | 官方 changelog 持续更新；WordPress 官方 Theme / Plugin API 交叉确认 2.1.56、要求 WordPress 6.7+ / PHP 7.0+ | 更新最接近证据截点，但最低版本声明不等于 PHP 8.3 与 WooCommerce 11.1.0 的组合验收；Pro 扩展、主题和 Companion 必须同轮升级并实测。 |

三者均未因停止维护被淘汰。淘汰结论来自与本项目“成熟模板直接套用、少插件、少开发、最快落地”目标的相对适配度，而不是安全或维护红线。

## 4. 官方来源清单

### 4.1 平台基线

- WordPress 当前版本 API：<https://api.wordpress.org/core/version-check/1.7/>
- WooCommerce 当前版本 API：<https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request%5Bslug%5D=woocommerce>
- WooCommerce 服务器要求：<https://woocommerce.com/document/server-requirements/>

### 4.2 WoodMart

- Changelog：<https://xtemos.com/woodmart-changelog.php>
- ThemeForest 正版购买页：<https://themeforest.net/item/woodmart-woocommerce-wordpress-theme/20264492>
- 安装流程：<https://xtemos.com/docs-topic/installation-process-3/>
- Catalog Mode：<https://xtemos.com/blog/woocommerce-catalog-mode-in-woodmart/>
- Layouts Builder：<https://xtemos.com/docs-topic/layouts-builder/>
- Product Sale Countdown：<https://xtemos.com/docs-topic/product-sale-countdown/>
- Product Reviews：<https://xtemos.com/docs-topic/product-reviews/>
- Fashion 2 构建说明：<https://xtemos.com/blog/how-we-built-the-fashion-2-demo-website-with-woodmart/>
- Fashion 2 Demo：首页 <https://woodmart.xtemos.com/fashion-2/>；分类 <https://woodmart.xtemos.com/fashion-2/product-category/women/>；商品 <https://woodmart.xtemos.com/fashion-2/product/thick-sole-sneakers/>；Blog <https://woodmart.xtemos.com/fashion-2/blog/>

### 4.3 Rey

- Changelog：<https://support.reytheme.com/changelog/>
- 官方定价：<https://reytheme.com/pricing/>
- Cart / Checkout / Catalog 设置：<https://support.reytheme.com/kb/theme-settings-woocommerce-cart-checkout/>
- Product / Archive Custom Templates：<https://support.reytheme.com/kb/custom-templates/>
- Mobile Bottom Bar：<https://support.reytheme.com/kb/mobile-bottom-bar/>
- Oslo Demo：首页 <https://demos.reytheme.com/oslo/>；分类 <https://demos.reytheme.com/oslo/product-category/women/>；商品 <https://demos.reytheme.com/oslo/product/drawstring-crossbody-bag/>；Journal <https://demos.reytheme.com/oslo/journal/>

### 4.4 Blocksy Pro

- Changelog：<https://creativethemes.com/blocksy/changelog/>
- 官方定价：<https://creativethemes.com/blocksy/pricing/>
- WordPress 官方 Theme API：<https://api.wordpress.org/themes/info/1.2/?action=theme_information&request%5Bslug%5D=blocksy>
- WordPress 官方 Companion API：<https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request%5Bslug%5D=blocksy-companion>
- Product Archives：<https://creativethemes.com/blocksy/docs/woocommerce/product-archives/>
- Single Product：<https://creativethemes.com/blocksy/docs/woocommerce/single-product/>
- Product Sale Countdown：<https://creativethemes.com/blocksy/docs/woocommerce/product-sale-countdown/>
- Advanced Product Reviews：<https://creativethemes.com/blocksy/docs/woocommerce/advanced-woocommerce-product-reviews/>
- Garderobe 说明：<https://creativethemes.com/blocksy/starter-site/garderobe/>
- Garderobe Demo：首页 <https://startersites.io/blocksy/garderobe/>；分类 <https://startersites.io/blocksy/garderobe/product-category/accessories/>；商品 <https://startersites.io/blocksy/garderobe/product/snapback-cap/>；文章 <https://startersites.io/blocksy/garderobe/lobortis-elementum-nibhtellus-molestie-mauris/>

### 4.5 FiboSearch 官方集成证据

- WoodMart：<https://fibosearch.com/documentation/themes-integrations/woodmart-theme/>
- Rey：<https://fibosearch.com/documentation/themes-integrations/rey-theme/>
- Blocksy：<https://fibosearch.com/documentation/themes-integrations/blocksy-theme/>

## 5. 核心能力兼容矩阵

图例：`原生` = 官方主题/官方 Pro 能力；`配置` = 依赖主题设置或标准 WooCommerce；`插件` = 需要独立插件；`待实测` = 官方资料不足以验收本项目精确流程。

| 核验项 | WoodMart | Rey | Blocksy Pro |
|---|---|---|---|
| Builder 依赖 | Gutenberg / Elementor / WPBakery 均支持；Fashion 2 的 Product Loop Builder 官方说明固定用 Gutenberg | Elementor + Rey Core 是 Oslo 与自定义模板主路径；普通文章可用 Gutenberg | Gutenberg 优先，也支持 Elementor；Pro 能力由 Blocksy Companion 承载 |
| 纯目录 / 隐藏交易 UI | 原生 Catalog Mode，可隐藏价格/加入购物车并做目录展示；Header、购物车、账户还需统一关闭 | 原生 Cart / Checkout / Catalog 设置，可隐藏交易入口 | 可从 Header / 商品设置分别关闭多项入口，但未找到与前两者等价的“一键完整目录模式 + 直接路由关闭”官方证据；需额外配置或轻量方案 |
| 商品归档、分类、品牌、搜索结果统一模板 | Layouts Builder + Product Loop Builder 覆盖最完整；适合统一大量商品列表 | Custom Templates 覆盖 Product / Archive，灵活但更依赖 Elementor / Rey Core | Product Archives / Content Blocks / Customizer 可统一控制，标准结构较强 |
| 商品详情模板 | Layouts Builder 原生，组件完整 | Custom Product Template 原生，视觉能力强 | Single Product 设置与 Pro Content Blocks 可统一控制 |
| 原价 + Sale Price | WooCommerce 原生数据，三者均可显示 | WooCommerce 原生数据，三者均可显示 | WooCommerce 原生数据，三者均可显示 |
| 真实促销倒计时 | 原生 Product Sale Countdown，读取 WooCommerce sale schedule | 原生 scheduled sale countdown 能力 | Shop Extra / Pro 原生 Product Sale Countdown |
| 手机 Header / Mega Menu / 搜索 | Header Builder、移动菜单与搜索入口成熟；功能覆盖最完整 | Header、自定义 Mega Menu；3.3.0 新增 Mobile Bottom Bar 和移动导航搜索框 | Header Builder 与 Pro Mega Menu；移动结构清晰，但时尚成品需更多配置 |
| Blog / Editorial / Lookbook | Fashion 2 Blog、Layouts、Shop the Look 可复用；完成度高 | Oslo Journal / Global Sections / Elementor 模板最接近高端 Editorial | Garderobe 标准 Blog 可用，但现成 Editorial 气质最弱 |
| FiboSearch | FiboSearch 官方有 WoodMart 专用集成文档；启用后应关闭重复的主题 AJAX 建议层 | FiboSearch 官方有 Rey 专用集成文档 | FiboSearch 官方有 Blocksy 专用集成文档 |
| 图片评价 | WoodMart 原生扩展 WooCommerce Reviews 并支持图片；后台主动录入 1–3 图、日期、昵称的完整运营流程待实测 | 未找到等价的完整原生图片评价后台流程，预计需一个成熟图片评价插件；须关闭/避免重叠展示 | Advanced Product Reviews 扩展原生评价并支持图片；后台主动录入精确流程待实测 |
| 数千 Simple Product | AJAX 列表、统一 Loop、筛选和模块开关有利；功能包大，需关停无关模块并做真实数据压测 | 目录体验好；Elementor + Rey Core +动态模板链更长，须重点测 TTFB、筛选和移动端脚本 | 结构轻、条件加载、标准数据路径最有利；但达到目标视觉可能增加内容块与配置 |
| 主题锁定 | WooCommerce 商品数据标准；Layouts、Header 与部分元素依赖 WoodMart Core，锁定中等偏高 | WooCommerce 数据标准；Global Sections / Custom Templates / Elementor 与 Rey Core 依赖强，锁定最高 | 商品和文章最接近 WordPress / WooCommerce 标准结构，锁定最低 |

### 图片评价冲突规则

WoodMart 与 Blocksy Pro 都会扩展 WooCommerce 原生评价；Rey 若使用第三方图片评价也会进入同一显示区域。任何原型只能启用一套高级图片评价方案，禁止同时启用主题高级评价和第三方图片评价插件，否则可能产生重复 schema、重复字段、重复模板、重复前端脚本或评价图片不一致。运营人员后台创建韩文评价、指定日期/脱敏昵称、绑定商品并上传 1–3 图的完整路径，三者都尚未以本项目角色和数据实测；这不是本报告可推定 PASS 的事项。

## 6. 官方 Demo 与手机端视觉对比

评估覆盖每个候选的首页、分类页、商品详情、移动导航/搜索和内容页。WoodMart 与 Rey 四类官方 Demo URL 在证据截点由只读客户端返回 HTTP 200；Blocksy Starter Site 对自动客户端返回 403 反爬响应，因此 Blocksy 视觉判断以官方 Garderobe 说明页和普通浏览器可见 Demo 为边界，不把自动化 403 当作主题故障。

| 候选 / 官方 Demo | 首页与商品发现 | 分类 / 商品详情 | 移动导航 / 搜索 | Blog / Editorial | 与本项目距离 |
|---|---|---|---|---|---|
| WoodMart / Fashion 2 | 大图与商品流平衡，B-Lite 可通过删减模块直接形成 | Product Loop、Archive、Single Product 结构完整，价格、标签、图集和信息密度适合大量商品 | Header Builder、移动菜单、搜索入口与筛选路径成熟；需要关闭购物车/账户等交易入口 | Blog + Shop the Look 可承载穿搭精选；视觉需做适度减法 | 功能和交付距离最近；不是三者中最克制，但配置即可达到，不需要另造模板系统 |
| Rey / Oslo | 三者中最接近高端时装编辑感，留白、图片比例、版式节奏最佳 | 分类和商品页精致；自定义模板能力强 | 官方称 Oslo 有 pleasant mobile experience；3.3.0 又补齐 Bottom Bar 与移动导航搜索 | Journal 最适合穿搭内容与品牌叙事 | 视觉距离最近，但为此承担 Elementor + Rey Core 的维护、性能和锁定成本 |
| Blocksy Pro / Garderobe | 清晰、轻量、可用，但首页更像传统商店 Starter Site | 标准归档/商品页易维护，视觉成品感不及前两者 | Header Builder 路径清楚，FiboSearch 有官方集成；移动性能潜力最好 | 标准文章页可用，现成 Editorial / Lookbook 完成度最低 | 技术底座轻，但要达到目标气质需更多布局与视觉重做，偏离“直接套成熟方案”优先级 |

三个 Demo 都不是韩文基线。韩文字体加载、长商品名、英文品牌 + 韩文混排、`word-break`、两行截断和 360/390 px 窄屏导航必须在 P3-T002 原型中实测；本报告只评价潜力，不宣称韩文排版已经验收。

## 7. 100 分评分矩阵

| 维度 | 权重 | WoodMart | Rey | Blocksy Pro |
|---|---:|---:|---:|---:|
| 手机端高端视觉及韩文排版潜力 | 25 | 22 | 24 | 18 |
| 商品网格与大量商品浏览效率 | 15 | 15 | 13 | 12 |
| 商品详情模板 | 10 | 10 | 10 | 8 |
| Blog / Editorial 穿搭内容模板 | 10 | 8 | 9 | 7 |
| 纯目录模式与交易功能隐藏 | 8 | 8 | 8 | 3 |
| 导航与第三方即时搜索集成 | 8 | 8 | 8 | 8 |
| 后台配置和日常维护难度 | 8 | 6 | 5 | 8 |
| 性能及数千商品适配潜力 | 8 | 6 | 5 | 8 |
| 更新、文档和支持状态 | 5 | 5 | 4 | 5 |
| 数据锁定与换主题风险 | 3 | 1 | 1 | 3 |
| **总分** | **100** | **89** | **87** | **80** |

### 7.1 逐项扣分理由

| 维度 | WoodMart 扣分 | Rey 扣分 | Blocksy Pro 扣分 |
|---|---|---|---|
| 手机端高端视觉及韩文排版潜力 | `-3`：Fashion 2 仍带通用商业主题气质；韩文无官方 Demo | `-1`：视觉最优，但韩文仍无官方 Demo | `-7`：Garderobe 偏传统，达到目标需更多排版和内容结构重做 |
| 商品网格与大量商品浏览效率 | `0`：Loop / Archive / AJAX / 筛选覆盖完整 | `-2`：目录优秀，但 Elementor / Rey Core 链更长 | `-3`：标准目录清晰，现成商品发现层级与商品流密度弱于 WoodMart |
| 商品详情模板 | `0`：Layouts Builder 覆盖完整 | `0`：Custom Template 覆盖完整 | `-2`：可统一配置，但现成高级布局与模块组合弱于前两者 |
| Blog / Editorial | `-2`：有 Blog / Shop the Look，但视觉叙事不及 Oslo | `-1`：Oslo Journal 最接近目标；仍依赖 Elementor 模板体系 | `-3`：标准 Blog 可用，Editorial 成品距离最大 |
| 纯目录模式 | `0`：原生 Catalog Mode 最接近任务需求 | `0`：原生设置覆盖目录展示 | `-5`：未找到等价的一键完整目录与直接路由禁用证据，需要多处分散配置/补充方案 |
| 导航与 FiboSearch | `0`：原生 Header / Mega Menu + FiboSearch 官方集成 | `0`：移动搜索/导航 + FiboSearch 官方集成 | `0`：Header / Mega Menu + FiboSearch 官方集成 |
| 后台维护 | `-2`：能力多、设置面大，需建立模块白名单 | `-3`：Rey Theme、Rey Core、Elementor 与模板条件共同维护 | `0`：Customizer / Content Blocks 路径相对清晰 |
| 性能及数千商品 | `-2`：功能包大，必须关模块并以数千商品测量 | `-3`：Elementor 与动态模板依赖最重，性能不确定性最高 | `0`：轻量和条件加载最有优势，但仍需实测而非免测 |
| 更新、文档和支持 | `0`：近期版本、完整文档、ThemeForest 支持路径 | `-1`：近期更新，但 3.2 刚切换定价/设置体系，迁移与支持模式需观察 | `0`：更新最接近证据截点，文档与年付/终身支持路径清晰 |
| 数据锁定 | `-2`：Layouts / Header / 元素依赖 WoodMart Core | `-2`：Elementor / Rey Core / Global Sections 依赖最高 | `0`：标准 WordPress / WooCommerce 结构占比最高 |

评分没有把“轻量”或“视觉好看”单独当作最终目标。项目最重要的是：在不开发新商城框架的前提下，用最少插件和最少定制完成手机商品发现、统一目录、详情、穿搭和非交易目录模式。

## 8. 最终结论

### 8.1 首选主题：WoodMart（89 / 100）

唯一首选为 **WoodMart**。

关键理由：它比 Rey 少 2 分视觉优势，但在目录模式、Product Loop / Archive / Single Product 统一模板、Gutenberg 路径、倒计时、Header / Mega Menu、FiboSearch 官方集成和图片评价覆盖上最完整。它最符合“主题原生 > 标准能力 > 少量插件 > 少量定制”和“最快落地”的既定原则。主要代价是设置面大、必须主动关闭无关商业模块，并接受 WoodMart Core 的中等偏高锁定。

### 8.2 备选主题：Rey（87 / 100）

唯一备选为 **Rey**。

关键理由：Oslo 的手机视觉、时尚编辑感和 Journal 是三者最佳；如果 P3-T002 发现 WoodMart 无法在少量配置下达到视觉目标，Rey 是明确替代方案。未列为首选的原因是 Elementor + Rey Core + Custom Templates 的运行和维护链更长，数千商品性能与换主题成本风险更高。

### 8.3 淘汰主题：Blocksy Pro（80 / 100）

本轮淘汰 **Blocksy Pro**，不进入首选主题的 P3-T002 原型主线。

关键理由：它在轻量、日常维护和低锁定上最好，但官方现成 Garderobe 的高端 Editorial 完成度明显落后；同时没有找到与 WoodMart / Rey 等价的完整一键目录模式及交易路由关闭证据。补足这两项会增加视觉重做和功能拼装，违背本项目“成熟方案直接套用、少开发、快速落地”的优先级。淘汰是本项目适配结论，不代表主题质量低或停止维护。

## 9. 采购成本与购买门

| 候选 | 官方公开价格（证据截点） | 授权 / 更新 / 支持摘要 |
|---|---:|---|
| WoodMart | **USD 59**（税费另计） | ThemeForest Regular License，1 个 end product；含未来更新与 6 个月作者支持。购买前在结账页复核最终税费与条款。 |
| Rey | Personal **USD 59 / 年**；官方也列出 1 站点的一次性 USD 69 / 89 / 139 选项 | 方案按更新/支持周期区分；购买时必须选择与 1 个正式站点相符的官方授权并复核当日条款。 |
| Blocksy Pro | Personal **USD 69 / 年** 或 **USD 199 lifetime**（税费另计） | 1 站点授权；按年或终身方案。 |

本项目按首选主题计算的预计首笔主题成本为 **USD 59 + 适用税费**。FiboSearch、批量编辑和图片评价尚未通过原型，不计入本任务采购承诺。

**不建议在 P3-T001 内立即购买。** 阻塞购买的流程门为：

1. 本报告完成 GPT 独立验收；
2. 用户明确授权进入 P3-T002 和采购；
3. 购买前复核 ThemeForest 当前价格、Regular License、单站点/最终产品范围和支持期；
4. P3-T002 安装前只读确认线上精确 WordPress / WooCommerce 版本，并在隔离原型验证 WordPress 7.1 / WooCommerce 11.1.0 / PHP 8.3 风险。

当前没有购买动作，也不以此流程门将 P3-T001 判为 BLOCKED。

## 10. WoodMart 下一步安装方案（仅方案，未执行）

### 10.1 来源、核心插件与 Builder

1. 只从 ThemeForest 正版页购买 WoodMart Regular License，并按 XTemos 官方安装流程取得父主题包；不使用二手、共享或破解包。
2. 父主题为 `woodmart`；必装并同版维护官方 **WoodMart Core** companion 插件。
3. Builder 选择 **Gutenberg**。Fashion 2 的 Product Loop 官方路径本身使用 Gutenberg；不安装 Elementor 或 WPBakery，不为 Demo 同时保留第二套 Builder。
4. 推荐 Starter / Demo 为 **Fashion 2**，但只允许导入隔离本地原型。不得直接导入 Hostinger；导入前记录会创建的页面、媒体、菜单、模板和设置，原型验收后只迁移确认内容。

### 10.2 可选插件与重复功能禁令

| 类型 | 方案 |
|---|---|
| 即时搜索 | 可选 FiboSearch Pro；先按其 WoodMart 官方集成文档做原型。启用后关闭 WoodMart 重复 AJAX suggestions / 搜索层，只保留一个索引与一个建议 UI。 |
| 图片评价 | 先实测 WoodMart 原生 Product Reviews 是否满足“管理员后台、韩文、星级、日期、脱敏昵称、指定商品、1–3 图、关闭前台投稿”；只要有一项不满足，再评估且只启用一个第三方图片评价插件。 |
| 批量管理 | WP Sheet Editor / Smart Manager 属于后续独立插件选型，不是主题必装项，不在本任务决定或安装。 |
| 禁止重复插件 | 不另装倒计时、Mega Menu、Catalog Mode、Wishlist、Compare、Quick View、第二套图片评价或第二套搜索插件；不因 Demo 提示安装 Elementor、WPBakery、Slider Revolution 或交易营销套件。确有缺口须另行立项和授权。 |

### 10.3 Child Theme 与 Git / 数据库边界

- 不修改 WoodMart 父主题。后续在 `wordpress/themes/fashion-child/` 建立项目自有 Child Theme，`Template` 指向 `woodmart`；本任务只定义角色，没有创建 PHP / CSS / 模板文件。
- Git 管理：`fashion-child` 中项目自有的 `style.css`、`functions.php`、必要模板覆盖、静态资源；以及未来经独立任务批准的 `fashion-core` 项目小插件代码。
- 不进入 Git：商业父主题 ZIP、WoodMart Core 商业包、许可证/激活数据、上传媒体、缓存、数据库导出、Secret。
- 数据库 / 后台管理：Theme Settings、Header Builder、菜单、Widgets、Customizer/Settings、Layouts Builder 条件、Gutenberg 页面与 Pattern、Demo 内容、产品/分类/品牌/评价及媒体。
- 当前 GitHub Actions 白名单只部署 `fashion-child` 与 `fashion-core`；父主题、Core 和数据库配置不应伪装成该白名单中的项目代码。

### 10.4 升级不覆盖定制的策略

1. 所有项目定制只放 Child Theme / 项目小插件，不直接改父主题或 WoodMart Core。
2. 每次升级先在隔离原型克隆验证，并备份数据库、`wp-content` 中相关文件和当前版本清单。
3. WoodMart 与 WoodMart Core 按官方要求同轮升级；检查 WooCommerce Status 中模板覆盖差异，逐一重放必要 Child Theme override。
4. 回归手机首页、分类/品牌/搜索结果、商品详情、促销价/倒计时、韩文排版、FiboSearch、图片评价、Kakao 链接，以及购物车/结账/账户入口和直接 URL 均不可进入交易流程。
5. 只有独立验证通过且获得明确部署授权，才进入线上；本报告不授权升级或部署。

### 10.5 模块白名单

预计保留：WooCommerce 基础集成、Gutenberg Product Loop / Layouts Builder、Header Builder / Mega Menu、分类/品牌/筛选/分页、Sale Price 与倒计时、Blog / Shop the Look、必要的图片优化/按需资源、最终选定的一套搜索和一套评价展示。

预计关闭：Cart / Checkout / My Account、Add to Cart / Quick Shop、Wishlist / Compare、Waitlist、Abandoned Cart、Free Gifts、Dynamic Discounts、Social Login、多供应商、复杂变体/Size Guide，以及与项目范围无关的交易营销模块。实际开关清单须由 P3-T002 原型证据确认。

## 11. 已知风险、BLOCKED 与 P3-T002 建议范围

### 11.1 已知风险

1. 仓库没有线上 WordPress / WooCommerce 精确版本；本任务没有为补证登录线上。
2. 三者的公开资料都不足以证明“当前主题 + WordPress 7.1 + WooCommerce 11.1.0 + PHP 8.3 + 本项目插件组合”完整兼容。
3. 三个官方 Demo 都不能替代韩文字体、混排、长标题和 360/390 px 窄屏验收。
4. 数千商品性能只能通过代表数据和测量确认；Demo 流畅或“轻量”宣传不是压测证据。
5. 纯目录不仅是隐藏按钮，还必须处理直接访问 cart / checkout / my-account、结构化数据、Header、搜索结果和缓存后的入口一致性。
6. 图片评价后台录入与主题/第三方插件冲突尚未实测，必须坚持“一套高级评价实现”。
7. Demo 导入会写数据库、页面、媒体和设置，只能在后续获批的隔离原型中进行。

### 11.2 是否 BLOCKED

**P3-T001：否。** 三候选已有足够的当前官方版本、文档、Demo、定价和相对能力证据，可形成唯一首选、唯一备选和明确淘汰结论。

**购买 / 安装 / P3-T002：流程上等待 GPT 独立验收与用户明确授权。** 该等待不影响本报告交付，但在授权前禁止前进。

### 11.3 P3-T002 建议范围（未开始）

如后续获明确授权，建议 P3-T002 只做：

1. 安装前只读记录线上 WordPress / WooCommerce / PHP 精确版本，不改配置；
2. 在隔离本地原型安装正版 WoodMart + WoodMart Core + Gutenberg Fashion 2；不导入生产数据、不部署 Hostinger；
3. 用 20–30 件无真实业务数据的代表 Simple Product 验证手机首页、分类/品牌/搜索结果、商品详情、Sale Price / schedule / countdown、Blog / Shop the Look；
4. 验证韩文排版、FiboSearch、图片评价后台完整流程、Kakao/复制链接，以及购物车/结账/账户的 UI 和直接路由关闭；
5. 记录插件/模块白名单，测量数千商品规模的查询、分页/筛选与移动端性能风险；
6. 形成新的独立报告和验收门，不把本报告视为 P3-T002 PASS，不触碰生产。

本任务在报告和 PR 创建后停止，等待 GPT 独立验收；不自行 merge，不开始 P3-T002。
