# FASHION-THEME-3.2-REPORT001

## 1. 执行状态

- 任务：`P3-T002`
- 分支：`codex/fashion-theme-3.2-foundation`
- 基线：执行前同步后的 `origin/main`，起始 SHA `1cd2b35266ef7591286c97e4426d4c55d24c8a0f`
- Draft PR：待创建
- 当前结论：本地正式化与自动验收完成，等待 GPT 独立验收；不代表已合并或已部署。

## 2. README 路线更新

`README.md` 已完成以下事实源更新：

1. 正式父主题改为 WordPress.org 的 Botiga Free；
2. `fashion-child` 明确为项目自有视觉/模板展示层；
3. `fashion-core` 明确为项目自有轻量业务规则层；
4. WoodMart 标记为已评估但被通过验收的 Botiga Free 路线取代，当前不采购；
5. 当前阶段更新为 Botiga Free 正式前台基础开发；
6. 商品、分类、品牌、价格与促销继续优先使用 WooCommerce 原生结构，不重做商品后台。

历史任务书和报告均保留。

## 3. 正式职责边界

### `fashion-child`

只负责前台展示：

- 页面结构、Header/Footer/Mobile Nav；
- 首页 B-Lite、商品集合和商品卡；
- 商品归档/详情视觉；
- 韩文排版与响应式 CSS；
- 倒计时、Kakao CTA、复制链接的浏览器交互；
- Botiga 专属组件与 theme mod 的展示适配；
- 标准 WooCommerce 评价的首页展示。

主题不再定义商品可购买规则，不处理 Cart/Checkout/My Account 路由，不注册品牌结构，也不自行读取第二套 SKU、URL、品牌或促销截止字段。

### `fashion-core`

只负责轻量业务规则：

- WooCommerce 商品不可购买过滤；
- 标准 loop/single Add to Cart 流程移除；
- Cart/Checkout/My Account 前台路由跳转；
- 当前商品 ID、SKU、canonical URL、Regular Price、Sale Price、Sale End 的统一接口；
- 官方品牌 taxonomy 选择与品牌 term 读取；
- 官方品牌 taxonomy 缺失时的单一 `fashion_brand` fallback。

未引入 Composer、框架或复杂 OOP。

## 4. `fashion-core` 文件结构

```text
wordpress/plugins/fashion-core/
├── fashion-core.php
└── inc/
    ├── brand.php
    ├── catalog-mode.php
    └── product-identity.php
```

- 插件版本：`0.1.0`
- 声明依赖：WooCommerce
- 本地 Web/CLI 均以只读仓库挂载；
- 初始化脚本在 WooCommerce 激活后幂等激活 `fashion-core`；
- 所有 4 个插件 PHP 文件均通过 `php -l`。

## 5. Catalog Mode 与路由行为

`fashion-core` 通过 WooCommerce 公共 hooks/filters 将商品标记为不可购买并移除标准 Add to Cart 输出。Botiga header/cart/account 的外观组件由 `fashion-child` 的展示适配关闭。

直接 HTTP 验证：

| 路由 | 首次响应 | Location | 最终响应 |
|---|---:|---|---:|
| `/cart/` | 302 | `/shop/` | 200 |
| `/checkout/` | 302 | `/shop/` | 200 |
| `/my-account/` | 302 | `/shop/` | 200 |

目标选择规则：已发布 Shop 页面可用时跳转 Shop；不可用时返回首页。处理仅作用于前台页面请求，跳过后台、AJAX 与 REST 请求；没有修改或删除 WooCommerce 页面与数据库数据。WooCommerce 后台商品管理路径未被前台路由规则接管。

首页、Shop、分类和 Product 均保持 HTTP 正常，自动浏览器验收未发现主要 Add to Cart/Cart/Checkout/Account 顾客入口。

## 6. 商品身份实现

`fashion_core_get_product_identity()` 以当前 `WC_Product` 为唯一来源并返回：

- `id`：WooCommerce Product ID；
- `sku`：`WC_Product::get_sku()`；
- `url`：当前 product permalink；
- `regular_price`：`WC_Product::get_regular_price()`；
- `sale_price`：`WC_Product::get_sale_price()`；
- `sale_end`：WooCommerce Sale End Unix timestamp 或 `null`。

`fashion-child` 的自有商品卡与商品详情支持区均消费该接口。稳定合成商品的 CTA SKU、URL、页面 permalink 与复制到 Clipboard 的 URL 已由运行时和 Playwright 交叉验证一致。未新增第二套 SKU 或 URL 字段，未配置或保存真实 Kakao Secret。

## 7. 最终品牌 taxonomy

最终采用：**WooCommerce `product_brand`**。

选择依据：在 WooCommerce 11.1.0 本地运行时检查得到 `taxonomy_exists( 'product_brand' ) = true`；taxonomy 绑定对象为 `product`，`public = true`，`show_ui = true`，`query_var = product_brand`。因此直接复用官方结构，没有注册并行 `fashion_brand`。

Seed 已将 25 个合成商品迁移到 `product_brand` term，并删除每个商品的旧 `_fashion_brand` meta。运行时结果：

- 25/25 商品均有且仅有 1 个正式品牌；
- 共 6 个品牌；
- 至少两个品牌分别通过 taxonomy query 返回商品；
- 商品卡、归档与详情均通过 `fashion-core` 的 term 接口渲染品牌；
- `fashion_brand` 未注册；
- 正式主题与插件不读取 `_fashion_brand`。

插件仅在未来运行时确实没有 `product_brand` 时注册单一 `fashion_brand` fallback，避免两套正式品牌维度并存。

## 8. WooCommerce Sale End

唯一数据源继续为：

```php
WC_Product::get_date_on_sale_to()
```

`fashion-core` 将其原始 Unix timestamp 放入商品身份数组；`fashion-child` 仅将该值乘以 1000 供浏览器倒计时显示。运行时断言确认插件 timestamp 与 `get_date_on_sale_to()->getTimestamp()` 精确相等。仓库中未新增 `_fashion_sale_end` 或其他平行截止字段。

## 9. 本地运行版本与边界

| 组件 | 版本/状态 |
|---|---|
| WordPress | 7.1 |
| PHP | 8.3.33 |
| WooCommerce | 11.1.0 |
| Botiga Free | 2.4.8 |
| `fashion-child` | 0.2.0 |
| `fashion-core` | 0.1.0，active |
| MariaDB | 11.8.3 |
| Playwright for Python | 1.60.0，仅在 Git 忽略的本地虚拟环境 |

运行环境继续使用 `dev/botiga-poc/`：Web 仅绑定 `127.0.0.1:8097`，数据库无 Host 端口，随机本地凭据仅在 `.runtime/`，25 个合成 Simple Product、25 个唯一 SKU、0 订单。

复现命令：

```bash
make -C dev/botiga-poc prepare
make -C dev/botiga-poc init
make -C dev/botiga-poc test
make -C dev/botiga-poc evidence
```

## 10. 自动测试结果

完整入口：

```bash
make -C dev/botiga-poc test
```

最近一次完整结果：

```text
SOURCE_CONTRACTS_PASS
RUNTIME_DATA_PASS products=25 unique_skus=25 brands=6 orders=0
No syntax errors detected in 5 child-theme PHP files
No syntax errors detected in 4 fashion-core PHP files
BROWSER_ACCEPTANCE_PASS viewports=360,390,430,1440
FULL_VALIDATION_PASS
```

Playwright 对首页、Shop、分类、Product 以及三个禁用交易路由执行了：韩文页面、无横向溢出、触控尺寸、商品网格、价格、品牌、SKU、Sale End 倒计时、Kakao payload、Clipboard URL、无主要交易入口、无客户可见 PoC 文案、无 console error/pageerror/document failure/HTTP 5xx 检查。

## 11. 截图证据

| 证据 | 路径 | 实际尺寸 |
|---|---|---:|
| 390px 首页 | `docs/evidence/p3-t002/mobile-home-390.png` | 390×6090 |
| 390px Shop | `docs/evidence/p3-t002/mobile-shop-390.png` | 390×3970 |
| 390px 商品详情 | `docs/evidence/p3-t002/mobile-product-390.png` | 390×2745 |
| 430px 商品详情 | `docs/evidence/p3-t002/mobile-product-430.png` | 430×2821 |
| 1440px 首页 | `docs/evidence/p3-t002/desktop-home-1440.png` | 1440×4984 |
| 360px 补充首页 | `docs/evidence/p3-t002/mobile-home-360.png` | 360×5964 |

商品详情截图可见正式 taxonomy 品牌、韩文商品名、Regular/Sale Price、SKU、倒计时、Kakao CTA 与复制链接。截图只使用合成数据。

## 12. 核心/父主题/Override 检查

- Botiga 父主题修改：**NO**；父主题源码未进入 Git。
- WooCommerce Core 修改：**NO**；WooCommerce 插件包未进入 Git。
- WordPress Core 修改：**NO**；Core 未进入 Git。
- WooCommerce template override：**0**。
- `fashion-core` 未引用父主题内部 PHP 文件。
- `.github/workflows/deploy-hostinger.yml` 修改：**NO**。

## 13. 安全、禁止事项与停止状态

- Hostinger 连接、读取、修改或部署：**NO**。
- `dry_run=false`：**未运行**。
- 付费主题/插件购买、安装或激活：**NO**。
- FiboSearch 最终选型：**未开始**。
- 图片评价插件最终选型：**未开始**。
- 真实 Kakao Secret：**未使用**。
- 真实商品、顾客、订单数据：**未使用**。
- 3000 商品性能压测：**未执行**。
- 自行 merge：**NO**。
- 当前是否 BLOCKED：**NO**。

## 14. 下一任务建议

先等待 P3-T002 的 GPT 独立验收与用户决定。只有后续另行下发任务后，再分别处理搜索、图片评价、规模性能或部署；本任务不进入这些工作。
