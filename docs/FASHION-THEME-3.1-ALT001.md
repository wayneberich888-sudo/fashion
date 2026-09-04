# FASHION-THEME-3.1-ALT001

## 任务编号
P3-T001-ALT001

## 任务名称
Botiga Free + KREAM/得物方向的免费主题原型验证

## 背景

P3-T001 商业主题评估已形成 WoodMart 首选结论，但用户决定在购买付费主题前先验证“免费成熟主题 + 项目自有 Child Theme”的路线是否足以达到目标视觉与核心功能。

当前 PR #5（WoodMart/Rey/Blocksy 选型报告）保持 Draft、冻结，不合并、不继续修改，直到本替代原型完成并由 GPT 独立验收后再决定关闭、修订或继续。

本任务不是正式上线，不允许碰生产 Hostinger。

## 必须先读取

1. `README.md`
2. `docs/FASHION-SELECTION-8.1-GEN001.md`
3. `docs/FASHION-THEME-3.1-SELECT001.md`
4. `docs/FASHION-DEPLOY-2.6-REPORT001.md`

不得重新定义已经确认的商城业务范围。

## 本任务目标

用 **Botiga Free** 作为优先父主题，在本机隔离 WordPress/WooCommerce 环境中做一个可运行的手机端原型，验证以下问题：

1. 不购买 WoodMart，能否只依靠 Botiga Free + `fashion-child` 少量定制达到接近 KREAM / 得物 / 29CM 的简洁高端商品流视觉；
2. 首页、商品列表、商品详情三类核心页面能否在不重写 WooCommerce 后台的前提下完成；
3. 韩文手机端、Simple Product、原价/促销价、促销倒计时、SKU、Kakao CTA 等项目核心要求能否落地；
4. 如果原型效果达到要求，是否可以正式改为“免费父主题 + 自有 Child Theme”路线；
5. 如果 Botiga 明显受限，必须明确指出限制，再决定是否回到 WoodMart，而不是边做边堆插件。

## 技术路线

### 父主题

优先使用 WordPress.org 官方来源的 **Botiga Free**。

- 不购买 Botiga Pro；
- 不修改 Botiga 父主题源码；
- 父主题只安装在本地隔离环境，不提交其完整源码到本项目仓库；
- 项目自有代码全部进入 `wordpress/themes/fashion-child/`；
- `fashion-child` 的 `Template` 必须正确指向 Botiga 父主题目录。

如果发现 Botiga Free 在技术上根本无法完成本原型的核心页面，不允许擅自换主题。先标记 BLOCKED，并给出具体限制证据，等待 GPT/用户决定是否切换 Blocksy Free、Kadence Free 或其他候选。

## 开源参考使用规则

KREAM、得物、29CM、MUSINSA 仅作为视觉和交互逻辑参考：

- 禁止复制其 Logo、品牌名称、商品图、文案、图标包、专有素材或整页受版权保护的代码；
- 禁止从“无明确 LICENSE”的 KREAM Clone 仓库复制生产代码；
- GitHub 上如需参考开源项目，只能在确认明确许可证后使用；
- `sadmann7/skateshop` 可作为 MIT License 的现代商城 UI/组件思路参考，但技术栈不同，优先只参考结构与视觉，不直接搬 Next.js 代码；
- 如实际复制任何允许复用的第三方代码片段，必须记录来源、许可证和修改说明到 `THIRD_PARTY_NOTICES.md`；
- 最优方案是自己实现 PHP/CSS/JS，不直接复制 KREAM Clone 代码。

## 本地隔离环境

本任务只允许本机隔离环境。

优先原则：

1. 复用机器上已经存在的 Docker / WordPress 本地工具；
2. 如果仓库没有本地 WordPress 环境，可建立最小、可删除的开发环境；
3. 不得要求用户为了本原型注册新的第三方账号；
4. 如果需要安装新的全局系统软件、需要管理员密码、需要手动登录或可能影响其他项目，先停止并询问用户；
5. 不读取或使用 Hostinger SSH Secret，不连接生产服务器。

本地环境允许安装：

- WordPress；
- WooCommerce；
- Botiga Free；
- 仅为原型确有必要的免费插件。

首轮不要安装 Elementor、WPBakery 或付费插件。

## 原型数据

只使用虚构/合成数据，不使用真实顾客、真实订单或敏感业务数据。

建议建立 20–30 个 WooCommerce Simple Product，覆盖：

- 鞋履；
- 包袋；
- 服饰；
- 香水；
- 配饰。

至少包含：

- 韩文商品名；
- 英文品牌名；
- 唯一 SKU；
- Regular Price；
- Sale Price；
- 部分商品设置真实 WooCommerce Sale Start / Sale End；
- 占位商品图；
- NEW / BEST / SALE 示例标签或等价测试数据。

## 必做页面

### A. 手机首页

目标不是复制 Botiga Demo，而是用 Child Theme 做成项目自己的商品发现首页。

至少包含：

1. 简洁 Header：Logo/文字标识、搜索入口；
2. 主导航：NEW / BEST / SALE + 主要分类入口；
3. 一张简洁主视觉或 Editorial 区，不做复杂轮播；
4. 手机双列商品流；
5. NEW / BEST / SALE 至少三种商品集合示例；
6. 一小段穿搭/Editorial 示例；
7. 一小段评价展示示例；
8. 手机底部导航或等价快速入口原型；
9. 不显示 Cart、Checkout、My Account 等交易入口。

### B. 商品列表 / 分类页

至少包含：

1. 手机双列商品卡；
2. 商品图；
3. 品牌；
4. 韩文商品名，两行以内可控；
5. 原价划线；
6. 促销价突出；
7. NEW / BEST / SALE 标签的基础视觉；
8. 分类标题与基础排序/筛选入口；
9. 列表密度要接近 KREAM/得物方向，避免传统 WooCommerce“大按钮商城感”。

### C. 商品详情页

至少包含：

1. 图片画廊；
2. 品牌；
3. 韩文商品名；
4. SKU；
5. Regular Price + Sale Price；
6. 若设置 WooCommerce Sale End，则显示倒计时；
7. 倒计时必须直接读取 WooCommerce 促销结束时间，不允许另建独立截止时间字段；
8. Kakao 咨询 CTA 原型；
9. 复制当前商品链接；
10. CTA 必须能取得当前商品唯一 SKU / 当前 URL；
11. 简洁商品说明；
12. 评价区展示原型；
13. 相关商品；
14. 不显示 Add to Cart、数量、结账入口。

## 纯展示模式原型

原型必须验证：

- 前台不显示 Add to Cart；
- Header 不显示购物车；
- 不需要顾客账户；
- Cart / Checkout / My Account 页面不作为顾客流程的一部分；
- 不改 WooCommerce Core；
- 尽量通过 Child Theme / hooks / filters 处理。

本任务不要求完成最终生产级路由封锁，但报告必须明确哪些已完成、哪些需要后续 `fashion-core` 或正式任务处理。

## 搜索与评价边界

本任务重点是验证主题/视觉路线，不要把任务扩展成插件选型大会。

搜索：

- 首轮可使用 WordPress/WooCommerce 基础搜索入口完成 UI；
- 若时间允许，可本地安装 FiboSearch Free 验证 Header 集成；
- 不购买 FiboSearch Pro。

评价：

- 只需做展示原型；
- 可以用 WooCommerce 标准评价 + 本地虚构图片结构做视觉验证；
- 不在本任务决定最终图片评价插件。

## 韩文与移动端必须验收

至少验证以下 viewport：

- 360 px；
- 390 px；
- 430 px。

重点检查：

- 韩文字体栈；
- 英文品牌 + 韩文商品名混排；
- 商品名两行截断；
- 搜索/Header 是否拥挤；
- 商品卡图片比例；
- 价格换行；
- CTA 可点击区域；
- 底部导航不遮挡内容。

## 视觉方向

关键词：

- KREAM / 得物式高商品发现效率；
- 29CM 式留白与 Editorial 感；
- 黑白灰为主；
- 商品图优先；
- 少边框、少阴影、少渐变；
- 不做“传统 WooCommerce 模板感”；
- 不使用 KREAM/得物的 Logo 或品牌元素。

如果 Botiga 自带样式影响目标，可以通过 Child Theme CSS / WooCommerce template hooks 做适度改造；禁止为了原型直接 fork 整个 Botiga 父主题。

## 建议仓库结构

允许新增/修改：

```text
wordpress/themes/fashion-child/
├── style.css
├── functions.php
├── assets/
│   ├── css/
│   └── js/
└── [只有确有必要时才增加的 WooCommerce template override]

prototype/ 或 dev/
└── [最小本地隔离环境文件，如确有必要]

docs/
├── FASHION-THEME-3.1-ALT001-REPORT.md
└── evidence/
    └── p3-t001-alt001/
        ├── mobile-home-390.png
        ├── mobile-shop-390.png
        ├── mobile-product-390.png
        └── [其他必要截图]
```

不允许把 WordPress Core、WooCommerce 插件目录、Botiga 父主题完整源码、数据库文件、uploads、cache 提交到 Git。

## 截图与证据

必须提供可供 GPT 独立验收的实际运行截图，不接受只写“已完成”。

最低证据：

1. 390px 首页；
2. 390px 商品列表；
3. 390px 商品详情；
4. 至少 1 张 360px 或 430px 边界截图；
5. 一张桌面版截图用于确认响应式没有完全失控；
6. 商品详情促销倒计时与促销价格同屏证据；
7. Kakao CTA / 复制链接 / SKU 同屏或可验证证据。

可使用 Playwright、浏览器截图或其他本地自动化方式。

## 验证项目

至少执行：

1. PHP 语法检查（对所有新增 PHP）；
2. JS 基础语法/静态检查（如新增 JS）；
3. WordPress 页面无 PHP fatal；
4. WooCommerce 首页/Shop/Product 可打开；
5. 手机端 360/390/430px 无明显横向溢出；
6. 促销倒计时读取 WooCommerce Sale End；
7. 当前商品 SKU 可在 Kakao CTA 数据中取得；
8. 复制链接得到当前商品 URL；
9. 没有 Cart/Add to Cart 主要顾客入口；
10. 不修改父主题；
11. 不连接 Hostinger；
12. 不修改 `.github/workflows/deploy-hostinger.yml`。

## 原型判定门

最终必须给出二选一结论，不允许模糊：

### `BOTIGA_ROUTE_PASS`

只有同时满足以下条件才能给 PASS：

- 三个核心页面在手机端视觉达到“可继续打磨成正式商城”的程度；
- 不需要大量父主题 override；
- 不需要引入 Elementor/重型 Builder；
- 核心 WooCommerce 数据仍走标准结构；
- 预计后续定制量明显低于从零开发完整主题；
- 没出现免费版必须付费解锁的关键阻塞能力。

### `BOTIGA_ROUTE_FAIL`

如果出现以下任一情况，应明确 FAIL：

- 为达到基本布局就必须大规模覆盖 Botiga；
- 免费版关键模板结构无法合理调整；
- 手机端视觉经过合理定制仍明显达不到目标；
- 必须堆多套插件/Builder 才能完成核心页面；
- WooCommerce 兼容或维护风险明显高于购买成熟商业主题。

FAIL 不代表任务失败；它代表原型成功证明“应回到 WoodMart或换另一免费父主题”。

## 分支

从最新 `main` 新建：

`codex/fashion-theme-3.1-botiga-poc`

不得直接在 `main` 开发。

## PR

完成后创建 Draft PR 到 `main`：

`[P3-T001-ALT001] Botiga Free KREAM-style prototype`

PR #5 保持原样冻结；不要 merge、close 或修改 PR #5。

## 报告

新增：

`docs/FASHION-THEME-3.1-ALT001-REPORT.md`

至少记录：

1. 本地运行方式；
2. Botiga Free 实际版本；
3. WooCommerce / WordPress 本地版本；
4. 实际新增/修改文件；
5. 三类核心页面完成情况；
6. 360/390/430px 验证情况；
7. WooCommerce 价格/促销时间/倒计时实现方式；
8. SKU + Kakao CTA / 复制链接实现方式；
9. Botiga 父主题 override 数量与范围；
10. 为实现原型新增了哪些插件；
11. 已知限制；
12. `BOTIGA_ROUTE_PASS` 或 `BOTIGA_ROUTE_FAIL`；
13. 如果 PASS：下一步正式化需要做什么；
14. 如果 FAIL：建议回 WoodMart 还是改测 Blocksy Free，以及原因；
15. 是否触碰 Hostinger（必须为 NO）。

## 禁止事项

本任务禁止：

- 连接、写入或部署 Hostinger；
- 运行 `dry_run=false` GitHub Actions；
- 修改部署 Workflow；
- 购买 WoodMart、Botiga Pro 或任何插件；
- 使用破解/共享商业主题；
- 修改 Botiga 父主题；
- 把完整 WordPress Core / WooCommerce / Botiga 父主题提交到仓库；
- 使用真实顾客/订单/付款数据；
- 直接复制无许可证 KREAM Clone 代码；
- 把 KREAM、得物、29CM 的品牌素材当成自己的素材；
- merge PR；
- 开始正式生产部署。

## BLOCKED 规则

出现以下情况先停止，不要自行绕过：

- Docker/本地 WordPress 环境需要管理员操作且当前无法安全完成；
- 需要真实 Hostinger 信息才能继续；
- 需要购买 Pro 才能完成核心原型；
- 需要使用无许可证第三方代码；
- 需要大面积 fork/修改 Botiga 父主题；
- 任何步骤可能影响当前正式站或其他本地项目。

## 验收标准

全部满足才算任务执行完整：

1. 从最新 main 创建正确分支；
2. Botiga Free 仅在本地隔离环境使用；
3. 有真实可运行的首页、商品列表、商品详情；
4. 有韩文移动端原型；
5. Regular/Sale Price 正确读取 WooCommerce；
6. 倒计时直接读取 WooCommerce Sale End；
7. SKU + Kakao CTA / 当前链接可验证；
8. 不出现主要 Cart/Add to Cart 顾客流程；
9. 有 360/390/430px 证据；
10. 有截图证据提交；
11. 没有修改 Botiga 父主题；
12. 没有连接 Hostinger；
13. 没有购买付费主题/插件；
14. 报告明确给出 BOTIGA_ROUTE_PASS / FAIL；
15. 创建 Draft PR，不自行 merge；
16. 完成后停止，等待 GPT 独立验收。

## Codex 最终只回复

- 分支名
- PR 编号/链接
- Botiga 版本
- 本地运行方式
- 首页 / 列表 / 详情是否完成
- 390px 截图路径
- 倒计时是否读取 WooCommerce Sale End
- SKU + Kakao CTA 是否完成
- 额外插件清单
- Botiga 父主题是否修改（必须 NO）
- Hostinger 是否触碰（必须 NO）
- BOTIGA_ROUTE_PASS / BOTIGA_ROUTE_FAIL
- 是否 BLOCKED

完成后停止，不自行 merge，不修改 PR #5。