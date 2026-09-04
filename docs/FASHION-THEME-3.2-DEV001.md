# FASHION-THEME-3.2-DEV001

## 任务编号
P3-T002

## 任务名称
Botiga Free 正式路线固化与生产级前台基础闭环

## 前置状态

- P2 Hostinger 部署链路已完成并通过真实白名单部署验证。
- P3-T001-ALT001 已通过 GPT 独立验收并合并：`BOTIGA_ROUTE_PASS`。
- 正式路线已决定为：**WordPress + WooCommerce + Botiga Free + `fashion-child` + 轻量 `fashion-core`**。
- WoodMart / Rey / Blocksy Pro 的商业主题选型 PR #5 已关闭且未合并，仅保留历史研究价值。
- 当前 `fashion-child` 已有可运行的本地原型，但其中仍包含“原型阶段”的职责混合，需要正式化后才能作为后续商城开发基础。

## 本任务目标

把 P3-T001-ALT001 的可行性原型整理成项目正式前台基础，明确“主题负责展示、插件负责业务规则”的边界，并完成纯展示商城的生产级基础闭环。

本任务完成后，应形成稳定、可继续开发首页 / 分类 / 商品详情 / 搜索 / 评价的正式基础，但**仍不部署 Hostinger**。

核心目标：

1. 正式确认 Botiga Free 为父主题，并更新项目事实源；
2. 将 `fashion-child` 从 PoC 角色整理为正式视觉层；
3. 建立最小 `fashion-core` 插件，把目录模式与商品身份等业务规则从主题中抽离；
4. 生产级阻止顾客进入 Cart / Checkout / My Account 页面流程；
5. 保持 WooCommerce 标准商品、SKU、价格、促销时间、分类等数据结构；
6. 正式确定品牌维度的数据结构，不继续依赖 PoC 临时品牌 meta；
7. 保留 Kakao CTA 所需的“当前商品唯一 SKU + 当前商品 URL”能力，但本任务不配置真实 Kakao 密钥/账号；
8. 完整回归 360 / 390 / 430 / 1440px 页面与核心契约。

## 必须先读取

1. `README.md`
2. `docs/FASHION-SELECTION-8.1-GEN001.md`
3. `docs/FASHION-THEME-3.1-ALT001.md`
4. `docs/FASHION-THEME-3.1-ALT001-REPORT.md`
5. `docs/FASHION-DEPLOY-2.6-REPORT001.md`

不得重新定义已确认的商城业务范围。

---

## A. 项目事实源更新

更新 `README.md`，使其反映已经完成的路线决策。

至少修改：

1. 不再写“优先购买商业主题”作为当前主路线；
2. 明确父主题：**Botiga Free**；
3. 明确：
   - `fashion-child` = 项目自有视觉/模板展示层；
   - `fashion-core` = 项目自有轻量业务规则层；
4. WoodMart 付费路线标记为“已评估但被 Botiga Free 原型路线取代，当前不采购”；
5. 当前阶段改为“已进入正式前台基础开发”；
6. 仍坚持 WooCommerce 原生数据优先，不重做商品后台。

不得删除历史任务报告。

---

## B. `fashion-child` 正式化

### B1. 角色边界

`fashion-child` 只负责：

- 页面结构与视觉；
- Header / Footer / Mobile Nav 的展示；
- 商品卡展示；
- 首页 B-Lite 展示；
- 商品详情 UI；
- 韩文排版；
- 促销倒计时的前端显示；
- Kakao CTA / 复制链接的前端交互；
- 必要 WooCommerce hooks 的展示层适配。

不得继续承担应属于业务规则的职责，例如：

- “商品不可购买”核心规则；
- Cart / Checkout / Account 路由处理；
- 品牌数据结构注册；
- 产品身份/咨询 payload 的核心数据规则。

### B2. 清理 PoC 痕迹

正式主题中不得出现面向顾客的：

- `Local-only`；
- `로컬 프로토타입`；
- `FPOC-*` 固定业务假设；
- “未连接真实 상담 계정”等 PoC 提示文案。

PoC 合成数据、测试环境、测试截图可以继续保留在 `dev/botiga-poc/` 与 `docs/evidence/`，不得混入正式主题默认内容。

### B3. 不修改父主题

- Botiga 父主题仍不进入仓库；
- 不 fork Botiga；
- 不新增 WooCommerce template override，除非有明确不可通过 hook 解决的证据；本任务默认期望仍为 0。

---

## C. 建立 `fashion-core` 最小正式插件

目录：

`wordpress/plugins/fashion-core/`

当前 `.gitkeep` 可替换为正式插件文件。

建议最小结构：

```text
wordpress/plugins/fashion-core/
├── fashion-core.php
├── inc/
│   ├── catalog-mode.php
│   ├── product-identity.php
│   └── brand.php
└── [仅确有需要时增加的文件]
```

不要建立大型框架，不引入 Composer，不做复杂 OOP 架构。

### C1. Catalog Mode 业务规则

由 `fashion-core` 负责：

1. WooCommerce 商品前台不可购买；
2. 移除主要 Add to Cart 顾客流程；
3. Cart / Checkout / My Account 不作为可访问顾客流程；
4. 直接访问这些页面时统一跳转到 Shop 页面；若 Shop URL 不可用，则回首页；
5. 不修改 WooCommerce Core；
6. 不删除 WooCommerce 页面或数据库数据；
7. 后台管理员仍可正常进入 WooCommerce 后台管理商品。

路由处理只针对前台页面请求，不做企业级 API 安全工程；本项目首版重点是“顾客不能进入交易流程”。

### C2. 商品身份能力

由 `fashion-core` 提供可复用函数，至少返回：

- 当前商品 ID；
- 当前唯一 SKU；
- 当前 canonical product URL；
- 当前 Regular Price / Sale Price（如调用方需要）；
- 当前 WooCommerce Sale End（如存在）。

`fashion-child` 不应自行重复实现同一套身份来源。

Kakao CTA 仍可以是本地/无发送状态，但必须由正式 `fashion-core` 提供当前商品 SKU + URL 数据。

不得：

- 硬编码 Kakao 密码、Token、Cookie、账号凭据；
- 在 Git 中保存真实 Kakao Secret；
- 为每个商品单独维护第二套 SKU 或 URL 字段。

### C3. 促销倒计时数据源

继续强制：

`WC_Product::get_date_on_sale_to()`

为唯一促销结束数据源。

禁止新增：

- `_fashion_sale_end`；
- 独立倒计时日期字段；
- 与 WooCommerce Sale End 不一致的第二套截止时间。

---

## D. 品牌维度正式化

项目已确认“品牌是独立维度”，生产代码不得继续使用 PoC `_fashion_brand` 字符串 meta 作为正式品牌模型。

执行时先在 WooCommerce 11.1.0 本地运行时检查：

1. WooCommerce 当前是否已经注册可用的标准品牌 taxonomy（优先复用官方/标准 `product_brand`，若确实存在）；
2. 如果当前运行环境不存在官方品牌 taxonomy，则在 `fashion-core` 注册一个项目级 taxonomy，命名必须稳定且明确，例如 `fashion_brand`；
3. 不允许同时存在两套正式品牌维度；
4. 商品列表、详情、搜索后续必须可以基于正式品牌 taxonomy 工作。

PoC seed 可以迁移为 taxonomy 测试数据，但不得在正式模板继续依赖 `_fashion_brand`。

报告必须明确最终采用了哪个 taxonomy，以及为什么。

---

## E. 本地 PoC / 验收环境升级

继续复用 `dev/botiga-poc/`，不要另造第二套本地 WordPress。

要求：

1. 本地初始化时同时挂载/启用 `fashion-child` 与 `fashion-core`；
2. `fashion-core` 同样使用只读仓库挂载；
3. 25 个合成 Simple Product 可以继续用于回归；
4. seed 需改为正式品牌 taxonomy；
5. 0 订单保持不变；
6. 所有随机本地密码继续留在 `.runtime/` 且不进 Git。

---

## F. 必须新增的自动验收

在现有测试基础上，新增/修改断言覆盖：

### F1. Plugin 契约

- `fashion-core` 插件可正常激活；
- PHP 无 syntax error；
- 不依赖 Botiga 父主题内部 PHP 文件；
- 不修改 WooCommerce / WordPress Core。

### F2. Catalog 路由

浏览器或 HTTP 验证：

- `/cart/` 不停留在 Cart 页面；
- `/checkout/` 不停留在 Checkout 页面；
- `/my-account/` 不停留在 Account 页面；
- 三者最终进入 Shop 或首页；
- 首页 / Shop / 分类 / Product 仍 HTTP 正常；
- Header、商品卡、商品详情没有 Add to Cart / Cart / Checkout / Account 主要顾客入口。

### F3. 商品身份

对稳定测试商品验证：

- `fashion-core` 返回唯一 SKU；
- 返回 URL 与当前 product permalink 一致；
- theme CTA 使用的 SKU / URL 与 plugin 返回值一致；
- 复制链接仍得到当前商品 URL。

### F4. Sale End

- plugin/theme 最终使用的 countdown timestamp = `get_date_on_sale_to()->getTimestamp()`；
- 不存在平行截止时间 meta。

### F5. 品牌

- 所有 25 个测试商品均能通过正式 brand taxonomy 取得品牌；
- 不再依赖 `_fashion_brand` 才能渲染商品卡与详情；
- 至少两个品牌可用于查询/过滤测试。

### F6. 响应式回归

继续：

- 360px；
- 390px；
- 430px；
- 1440px。

至少首页 / Shop / 商品详情无明显视觉退化、无横向溢出、无 PHP fatal / pageerror / 5xx。

---

## G. 截图证据

重新生成至少：

- 390px 首页；
- 390px Shop；
- 390px 商品详情；
- 430px 商品详情；
- 1440px 首页。

商品详情截图必须能够看到：

- 品牌；
- 韩文商品名；
- Regular / Sale Price；
- SKU；
- 倒计时；
- Kakao CTA；
- 复制链接。

截图使用合成数据，不使用真实顾客数据。

---

## H. 禁止事项

本任务禁止：

- 连接、写入、部署 Hostinger；
- 运行 GitHub Actions `dry_run=false`；
- 修改 `.github/workflows/deploy-hostinger.yml`；
- 安装/购买 WoodMart、Botiga Pro 或其他付费主题；
- 开始 FiboSearch 最终选型；
- 开始图片评价插件最终选型；
- 使用真实 Kakao 凭据；
- 导入真实商品/顾客/订单数据；
- 做 3000 商品性能压测（另开任务）；
- merge 自己的 PR；
- 开始 Hostinger 正式上线。

---

## I. 分支

从最新 `main` 新建：

`codex/fashion-theme-3.2-foundation`

不得直接在 `main` 开发。

---

## J. 开发过程报告

新增：

`docs/FASHION-THEME-3.2-REPORT001.md`

至少记录：

1. 分支与 PR；
2. README 路线更新摘要；
3. `fashion-child` 与 `fashion-core` 最终职责边界；
4. `fashion-core` 文件结构；
5. Catalog Mode 路由行为；
6. 商品身份实现；
7. 最终品牌 taxonomy；
8. Sale End 数据源验证；
9. 本地运行版本；
10. 自动测试结果；
11. 截图路径；
12. 是否修改 Botiga 父主题；
13. 是否使用 WooCommerce template override；
14. 是否触碰 Hostinger；
15. 是否 BLOCKED；
16. 下一任务建议。

报告不得记录任何 Secret、密码、SSH 信息或真实 Kakao 凭据。

---

## K. PR 要求

创建 Draft PR 到 `main`：

`[P3-T002] formalize Botiga storefront foundation`

PR 中明确：

- 本任务只做本地正式化；
- 未触碰 Hostinger；
- 未修改部署 Workflow；
- 未开始搜索/评价插件选型；
- 未做生产部署；
- 等待 GPT 独立验收；
- 不自行 merge。

---

## L. 验收标准

全部满足才算 PASS：

1. README 已正式记录 Botiga Free 路线；
2. `fashion-child` 成为纯展示/视觉层，PoC 顾客文案清理；
3. `fashion-core` 正式插件建立并可激活；
4. Catalog Mode 核心业务规则进入 `fashion-core`；
5. Cart / Checkout / My Account 前台直接访问不能进入交易流程；
6. 当前商品唯一 SKU / URL 由 `fashion-core` 提供；
7. 倒计时继续唯一读取 WooCommerce Sale End；
8. 正式品牌 taxonomy 已确定并用于渲染/查询；
9. 25 个 Simple Product / 25 唯一 SKU / 0 订单回归通过；
10. 360 / 390 / 430 / 1440 响应式回归通过；
11. 无 PHP fatal / browser pageerror / HTTP 5xx；
12. Botiga 父主题未修改、未提交；
13. WooCommerce Core 未修改；
14. 默认 WooCommerce template override 仍为 0；若新增必须有不可替代证据，否则 FAIL；
15. 未触碰 Hostinger；
16. 未修改部署 Workflow；
17. 完成报告与截图；
18. 创建 Draft PR 且不自行 merge。

---

## Codex 最终只回复

- 分支名
- PR 编号/链接
- README 是否更新
- `fashion-core` 是否建立并激活
- Catalog / Checkout / Account 路由验证结果
- 最终品牌 taxonomy
- 倒计时是否仍读取 WooCommerce Sale End
- SKU + URL 是否由 `fashion-core` 提供
- 360/390/430/1440 测试结果
- WooCommerce template override 数量
- Botiga 父主题是否修改（必须 NO）
- Hostinger 是否触碰（必须 NO）
- 是否 BLOCKED

完成后停止，等待 GPT 独立验收，不自行 merge。