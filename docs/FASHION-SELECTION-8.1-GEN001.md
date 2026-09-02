# FASHION-SELECTION-8.1-GEN001

## 文档状态

- 状态：DRAFT / 选型进行中
- 日期：2026-09-02
- 仓库：`wayneberich888-sudo/fashion`
- 当前阶段：成熟主题与插件选型
- 本阶段不写正式业务代码，不购买未完成评估的产品。

---

## 1. 选型目标

为韩国鞋服包高端纯展示商城选择：

1. 1 个正版成熟 WooCommerce 商业主题；
2. 1～3 个真正必要的成熟插件；
3. 尽量以安装、配置、导入 Demo、统一模板和少量 CSS 完成首版；
4. 在满足核心展示与运营需求的前提下，允许简化或舍弃非核心功能；
5. 禁止重新开发商品后台、搜索、评价、批量编辑或完整主题。

---

## 2. 已确认的硬性筛选条件

### 2.1 前台与视觉

- 手机端优先；前台第一版只显示韩文。
- 整体气质：简洁、高端、以商品图片为主，参考得物、29CM、KREAM、MUSINSA 等成熟商城的设计逻辑。
- 首页采用 B-Lite：商品流为主，穿搭和真实评价为辅助；栏目尽量自动读取后台内容。
- 支持统一商品列表、分类页、品牌页、搜索结果页和商品详情模板。
- 页面编辑器只用于首页、穿搭频道、活动页和普通说明页；不得逐个商品拖拽设计。
- 顶部导航、首页栏目、显示隐藏和排序应尽量由后台自由配置。

### 2.2 商品与目录

- WooCommerce Simple Product；每个颜色或明显不同版本作为独立商品。
- 不使用复杂变体，不在网站选择尺码。
- 大类采用扁平结构：鞋履、包袋、服饰、香水、配饰。
- 品牌为独立维度；NEW / BEST / SALE / 编辑精选为专题入口。
- 显示原价与促销价；支持真实促销起止时间与倒计时。
- 纯目录模式：不显示购物车、结账、付款、网站订单和顾客账户。
- 商品详情页支持复制当前商品链接及打开 Kakao 咨询；页面链接与唯一后台 SKU 一一对应。

### 2.3 内容

- 穿搭精选复用 WordPress 标准文章或主题自带 Blog / Journal / Editorial / Portfolio 模板。
- 发布穿搭时可手动搜索并选择一个或多个关联商品。
- 图片评价由运营人员后台创建，支持韩文文字、星级、日期、脱敏昵称、关联商品及 1～3 张真实顾客图片。
- 顾客不能在前台自行投稿评价。

### 2.4 大目录后台

- 面向数千商品。
- 日常几十到几百件商品：网页表格式批量编辑。
- 首次导入或大范围更新：WooCommerce CSV / Excel。
- 支持批量价格、促销价、促销日期、分类、品牌、标签、状态和图片管理。
- 不自研后台。

---

## 3. 实现优先级规则

每个需求必须按以下顺序判断：

1. 商业主题原生；
2. WordPress / WooCommerce 原生；
3. 长期维护的成熟插件；
4. 后台配置、模板设置或少量 CSS；
5. 少量可隔离的子主题 / 小插件代码；
6. 需要明显定制或复杂联动时，优先简化或取消。

不得修改 WordPress Core、WooCommerce Core 或商业主题原始文件。

---

## 4. 第一轮主题候选池

> 当前仅为候选池，不代表购买决定。最终只保留 2～3 个进入原型对比。

### 4.1 Rey

- 定位：视觉优先候选。
- 主要理由：官方提供 London、Amsterdam、Tokyo、Milano、New York、Paris、Oslo 等多套简洁时尚 Demo；深度集成 Elementor、WooCommerce，支持 Demo 导入和模块化启停。
- 适配优势：默认审美最接近高端时尚展示；适合鞋履、服装、包袋及 Editorial 风格。
- 需要验证：目录模式完整度、数千商品后台体验、韩文排版、第三方搜索/评价插件兼容性、主题专属模块的数据锁定程度。
- 官方来源：
  - https://reytheme.com/themeforest-landing-page/
  - https://support.reytheme.com/kb/theme-settings-woocommerce-catalog/
  - https://support.reytheme.com/kb/ajax-filter-widgets/

### 4.2 WoodMart

- 定位：成熟功能与最快落地候选。
- 主要理由：支持 Elementor、Gutenberg 和 WPBakery；官方提供大量预建站、目录模式、布局构建器、AJAX 商品搜索、移动端及商品目录相关组件。
- 适配优势：可能减少搜索、倒计时、导航、商品布局等额外插件数量；后台配置成熟。
- 主要风险：功能很多，默认视觉更偏通用电商；需主动关闭无关模块并做视觉减法，防止页面臃肿和配置过多。
- 官方来源：
  - https://woodmart.xtemos.com/
  - https://xtemos.com/blog/woocommerce-catalog-mode-in-woodmart/
  - https://xtemos.com/docs-topic/layouts-builder/
  - https://xtemos.com/docs-topic/product-category-navigation/

### 4.3 XStore

- 定位：综合商业主题备选。
- 主要理由：原生 Catalog Mode；支持 Elementor Header / Single Product Builder，并提供较多 WooCommerce 模板与配置。
- 适配优势：纯目录模式切换直接；页面模板灵活。
- 主要风险：功能面较宽，需验证性能、后台复杂度和插件冲突；不因“功能多”自动优先。
- 官方来源：
  - https://www.8theme.com/documentation/xstore/xstore-features/catalog-mode/
  - https://www.8theme.com/documentation/xstore/xstore-builders/xstore-single-product-builder-with-elementor/

### 4.4 Blocksy Pro

- 定位：轻量、低锁定、长期维护候选。
- 主要理由：WooCommerce 商品归档可通过 Customizer 统一控制；Shop Extra 提供品牌、筛选、评价等模块；支持 Gutenberg，也可配合 Elementor。
- 适配优势：数据与 WordPress 标准结构结合较紧，换主题风险相对较低；配置界面清晰。
- 主要风险：现成时尚 Demo 与得物式商品流的接近程度可能不如 Rey / WoodMart，需要更多一次性视觉配置。
- 官方来源：
  - https://creativethemes.com/blocksy/woocommerce/
  - https://creativethemes.com/blocksy/docs/woocommerce/product-archives/
  - https://creativethemes.com/blocksy/docs/woocommerce/product-brands/
  - https://creativethemes.com/blocksy/docs/woocommerce/product-filters/

### 4.5 Flatsome

- 定位：成熟度对照候选。
- 主要理由：长期销售量和用户量大；具备 WooCommerce 目录模式、Demo、Header Designer、商品和分类模板、UX Builder。
- 适配优势：成熟、资料多、快速搭建。
- 主要风险：UX Builder 基于主题私有短代码；与“降低主题锁定、尽量使用标准内容”的原则存在冲突。默认不作为首选，除非真实 Demo 与运营便利明显领先。
- 官方来源：
  - https://flatsome3.uxthemes.com/
  - https://docs.uxthemes.com/

### 4.6 Savoy

- 定位：极简视觉对照候选。
- 主要理由：官方定位为现代极简 WooCommerce 主题，商品聚焦明显，内置 AJAX 体验。
- 适配优势：视觉克制、商品图优先。
- 主要风险：页面构建与扩展方式需要重点验证；相比新一代 Elementor / Gutenberg 方案，长期灵活性可能较弱。
- 官方来源：
  - https://www.nordicmade.com/
  - https://docs.nordicmade.com/savoy/

---

## 5. 第一轮插件候选池

### 5.1 即时商品搜索

#### 首选候选：FiboSearch Pro

- 输入时显示商品图、名称、品牌和价格。
- 可搜索名称、描述、SKU、分类、标签、品牌和自定义字段。
- 可把韩文名、英文品牌、型号、简称和搜索别名纳入索引。
- 必须验证：韩文分词实际效果、数千商品索引性能、最终主题搜索框集成。
- 官方来源：
  - https://fibosearch.com/feature/search-engine/
  - https://fibosearch.com/pro-vs-free/
  - https://fibosearch.com/documentation/features/search-in-custom-fields/

### 5.2 网页表格式批量商品管理

#### 首选候选：WP Sheet Editor – WooCommerce Products

- Spreadsheet 方式编辑价格、促销价、图片、分类、标签、描述和自定义字段。
- 支持按 SKU 匹配图片、CSV 导入、公式批量调价和大规模更新。
- 与当前“价格 + 图片 + CSV 混合运营”需求最贴合。
- 官方来源：
  - https://wpsheeteditor.com/extensions/woocommerce-spreadsheet/
  - https://wpsheeteditor.com/woocommerce-bulk-upload-images-products/

#### 备选：Smart Manager

- Excel-like 后台；支持大量商品、批量价格、分类及自定义字段更新。
- 优点是后台操作集中；需要与 WP Sheet Editor 对比图片批量能力、字段覆盖和成本。
- 官方来源：
  - https://www.storeapps.org/product/smart-manager/
  - https://www.storeapps.org/smart-manager-pricing/

### 5.3 运营人员后台上传图片评价

#### 候选 A：ReviewX 付费版

- 官方明确支持管理员从后台手动新增 WooCommerce 评价，并支持图片评价功能。
- 必须在试用或预售咨询中进一步确认：管理员手动新增评价时，能否同时直接上传 1～3 张图片、修改日期、脱敏昵称及精选状态。
- 官方来源：
  - https://reviewx.io/docs/how-to-add-manual-review-using-reviewx/
  - https://reviewx.io/docs/photo-video-review/

#### 候选 B：WooCommerce Photo Reviews / VillaTheme Photo Reviews

- 支持多图片评价、评价图片编辑、前台网格等。
- 当前官方说明更偏顾客前台提交；必须重点验证后台主动创建带图片评价的流程，不满足则淘汰。
- 官方来源：
  - https://woocommerce.com/products/photo-reviews/
  - https://villatheme.com/extensions/woocommerce-photo-reviews/

#### 评价插件硬性淘汰条件

以下任一项无法满足则淘汰：

1. 管理员可主动新增评价；
2. 新增时可精确选择商品；
3. 管理员可上传 1～3 张图片；
4. 可设置星级、昵称、日期和正文；
5. 可关闭顾客前台提交；
6. 无评价商品可隐藏空模块；
7. 可在首页展示精选评价，或能通过标准区块/短代码完成。

### 5.4 倒计时、目录模式与 Kakao

- 倒计时：最终主题原生优先；主题没有时再评估轻量插件。
- 目录模式：最终主题原生优先；不得为隐藏购物车引入重型插件。
- Kakao 按钮与复制商品链接：优先使用主题按钮、标准链接或极少量独立代码，不占用主要付费插件名额。

---

## 6. 评分矩阵

主题总分 100 分：

| 维度 | 权重 |
|---|---:|
| 手机端高端视觉及韩文排版潜力 | 25 |
| 商品网格与大量商品浏览效率 | 15 |
| 商品详情模板 | 10 |
| Blog / Editorial 穿搭内容模板 | 10 |
| 纯目录模式与交易功能隐藏 | 8 |
| 导航与第三方即时搜索集成 | 8 |
| 后台配置和日常维护难度 | 8 |
| 性能及数千商品适配潜力 | 8 |
| 更新、文档和支持状态 | 5 |
| 数据锁定与换主题风险 | 3 |

插件评分重点：

- 是否精确满足已确认后台流程；
- 是否可关闭无关功能；
- 是否兼容最终主题和当前 WooCommerce；
- 是否有持续更新与文档；
- 是否会与主题重复加载相同功能；
- 是否需要额外定制代码；
- 是否能在数千商品规模下稳定运行。

---

## 7. 实际选型流程

### STEP 1：Demo 与官方能力审查

- 对候选主题逐个查看手机首页、商品列表、商品详情、搜索、Blog/Editorial 和导航。
- 保存关键页面截图，并标注“直接采用 / 配置可达 / 需要少量 CSS / 不适合”。
- 对照硬性条件淘汰明显不适合的主题。

### STEP 2：缩减到 3 个候选

预期保留：

- 1 个视觉最优候选；
- 1 个成熟功能最优候选；
- 1 个轻量低锁定候选。

### STEP 3：插件兼容矩阵

对 3 个主题分别验证：

- FiboSearch Pro；
- 图片评价候选；
- WP Sheet Editor 或 Smart Manager；
- 目录模式；
- 倒计时；
- Kakao 按钮与复制链接。

### STEP 4：20～30 件代表商品原型

样本应覆盖：鞋履、包袋、服饰、香水、配饰、普通价、促销价、倒计时、NEW、BEST、SALE、有评价、无评价、有穿搭和无穿搭。

原型必须展示：

- 首页 B-Lite；
- 横向可配置导航；
- 即时搜索；
- 扁平分类和品牌入口；
- 商品列表与详情；
- 图片评价；
- 穿搭文章及关联商品；
- 复制商品链接与 Kakao 咨询；
- 后台批量调价和换图样例。

### STEP 5：最终购买决定

只有真实 Demo / 原型通过后才购买最终主题和插件。不得因为宣传功能数量多直接购买。

---

## 8. 当前初步优先级（非最终结论）

1. WoodMart：最快落地与原生功能覆盖优先审查；
2. Rey：高端时尚视觉优先审查；
3. Blocksy Pro：轻量、低锁定和长期维护优先审查；
4. XStore：综合备选；
5. Flatsome：成熟度对照，但重点审查短代码锁定；
6. Savoy：极简视觉参考，重点审查现代编辑与扩展能力。

最终排序必须以手机 Demo 截图、插件兼容和原型证据为准。

---

## 9. 本阶段交付物

1. 主题候选登记与官方证据；
2. 每个候选的手机端关键页面截图；
3. 主题评分矩阵；
4. 插件评分与兼容矩阵；
5. 预计正版采购成本；
6. 最终 2～3 个原型候选；
7. 20～30 商品原型任务书；
8. 最终推荐、备选方案和明确淘汰理由。

---

## 10. 当前禁止事项

- 不开始重新开发完整 WordPress 主题；
- 不创建自定义商品后台；
- 不为了复制得物而逐像素重做所有页面；
- 不安装多个功能重复的主题插件包；
- 不购买盗版、破解或停止维护的主题/插件；
- 不把购物车、付款、订单、账户、复杂变体重新带回首版；
- 不在正式主题确定前导入数千真实商品。
