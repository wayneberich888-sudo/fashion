# FASHION-THEME-3.1-ALT001-REPORT

## 1. 结论

**`BOTIGA_ROUTE_PASS`**

Botiga Free 2.4.8 + `fashion-child` 已在本机隔离 WordPress/WooCommerce 环境完成手机首页、商品列表/分类页、商品详情页原型。360 / 390 / 430 px 的韩文页面、WooCommerce 原价/促销价、直接读取 WooCommerce Sale End 的倒计时、当前唯一 SKU、Kakao 咨询原型、复制当前商品链接，以及主要 Cart / Add to Cart 顾客流程隐藏均有真实运行证据。

判定依据：

- 三个核心页面均达到“可继续打磨成正式商城”的程度；
- WooCommerce template override 为 **0**；
- 未引入 Elementor、重型 Builder 或额外功能插件；
- 商品、分类、标签、SKU、Regular Price、Sale Price、Sale Start / End、评价继续使用 WordPress/WooCommerce 标准结构；
- 未出现必须购买 Botiga Pro 才能完成本原型核心页面的阻塞；
- Child Theme 的工作量明显低于从零开发完整主题，现有实现集中在 5 个 PHP、2 个 CSS、1 个 JavaScript 文件。

本结论是开发原型路线判定，不代表 GPT 独立验收、正式上线或生产级安全验收。

## 2. 隔离环境与本地运行

运行边界：

- Compose project：`fashion_botiga_poc`；
- Web：仅 `127.0.0.1:8097`；
- MariaDB：无 Host 端口；
- 独立网络与命名卷；
- 本地随机数据库/管理员凭据仅保存在 Git 忽略的 `dev/botiga-poc/.runtime/`；
- Botiga、WooCommerce、WordPress Core、数据库、uploads 与缓存均不提交 Git。

首次复现：

```bash
cd dev/botiga-poc
make prepare
python3 -m venv .runtime/playwright-venv
.runtime/playwright-venv/bin/python -m pip install playwright==1.60.0
make init
make test
make evidence
```

Playwright 复用本机已有 Chrome-family 浏览器，不要求下载浏览器或安装系统级依赖。停止并删除容器可运行 `make down`；命名卷保留以便复验。

## 3. 实际版本与官方证据

核验日期：2026-09-04。

| 组件 | 本地实际版本 | 官方当前证据 |
|---|---:|---|
| WordPress | 7.1 | [WordPress 7.1 “Mary Lou”](https://wordpress.org/news/2026/08/mary-lou/)、[WordPress.org Core Version Check API](https://api.wordpress.org/core/version-check/1.7/) |
| PHP | 8.3.33 | 本地容器 `php -r 'echo PHP_VERSION;'` |
| WooCommerce | 11.1.0 | [WordPress.org WooCommerce](https://wordpress.org/plugins/woocommerce/)、[WordPress.org Plugin API](https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request%5Bslug%5D=woocommerce) |
| Botiga Free | 2.4.8 | [WordPress.org Botiga](https://wordpress.org/themes/botiga/)、[WordPress.org Theme API](https://api.wordpress.org/themes/info/1.2/?action=theme_information&request%5Bslug%5D=botiga)、[aThemes Botiga Changelog](https://athemes.com/changelog/botiga/) |
| fashion-child | 0.1.0 | 本仓库 Child Theme |
| Playwright for Python | 1.60.0 | 仅位于被忽略的本地虚拟环境 |

当前官方 API 返回：Botiga 2.4.8，最后更新 2026-08-20，最低 PHP 7.0；WooCommerce 11.1.0，最后更新 2026-09-03，最低 WordPress 7.0 / PHP 7.4，并标记 tested up to WordPress 7.1。aThemes 官方 changelog 包含 Botiga 2.4.8 条目。

WooCommerce `ko_KR` 官方语言包已在本地幂等安装并处于 active。

## 4. 原型数据

- 25 个已发布 WooCommerce Simple Product；
- SKU `FPOC-001` 至 `FPOC-025`，25 个值均唯一且非空；
- 5 个扁平分类：신발、가방、의류、향수、액세서리；
- NEW / BEST 商品标签与 WooCommerce Sale 商品集合；
- 5 张项目自制合成 4:5 PNG，占位素材在运行时生成；
- 2 条明确虚构的 WooCommerce 评价；
- 0 顾客、0 订单，不含真实业务数据。

稳定验收商品为 `FPOC-001`，具备 Regular Price、Sale Price、未来 Sale End、主图与 2 张辅助画廊图。

## 5. 三类核心页面完成情况

| 页面 | 状态 | 实际实现 |
|---|---|---|
| 手机首页 | PASS | 韩文 B-Lite 首页；简洁 Header/搜索；NEW/BEST/SALE 与 5 分类入口；Editorial 主视觉；手机双列商品流；穿搭 Editorial；合成评价；移动底部快速导航；无交易入口。 |
| 商品列表/分类页 | PASS | 保留 Botiga/WooCommerce 标准归档与排序语义；Child Theme hooks 注入品牌和 NEW/BEST/SALE；手机双列 4:5 商品卡；两行商品名；标准原价/促销价；隐藏“大按钮商城感”的 loop button。 |
| 商品详情页 | PASS | 标准画廊 3 图；品牌、韩文名、评价、Regular/Sale Price、SKU、Sale End 倒计时、Kakao CTA、复制链接、说明、评价区、相关商品；无数量/Add to Cart/结账入口。 |

## 6. 视口与截图证据

Playwright 对每个视口均实际打开首页、Shop、`shoes` 分类和 `FPOC-001` 商品详情，并验证 `html[lang]` 为韩文、无横向溢出、指定触控目标至少 44×44 px、商品标题最多两行、无主要交易入口、无 console error / pageerror / document request failure / HTTP 5xx。

| 视口 | 首页 | Shop | 分类 | 商品详情 | 截图 |
|---:|---|---|---|---|---|
| 360×800 | PASS | PASS | PASS | PASS | [mobile-home-360.png](evidence/p3-t001-alt001/mobile-home-360.png) |
| 390×844 | PASS | PASS | PASS | PASS | [mobile-home-390.png](evidence/p3-t001-alt001/mobile-home-390.png)、[mobile-shop-390.png](evidence/p3-t001-alt001/mobile-shop-390.png)、[mobile-product-390.png](evidence/p3-t001-alt001/mobile-product-390.png) |
| 430×932 | PASS | PASS | PASS | PASS | [mobile-product-430.png](evidence/p3-t001-alt001/mobile-product-430.png) |
| 1440×1000 | PASS | PASS | PASS | PASS | [desktop-home-1440.png](evidence/p3-t001-alt001/desktop-home-1440.png) |

提交的 PNG 实际宽度分别为 360、390、430、1440 px；均由通过断言的本地运行时自动截图。390px 商品详情证据中 Regular/Sale Price、倒计时、SKU、Kakao CTA 与复制链接同屏。

## 7. WooCommerce 价格与倒计时

价格：

- 商品数据使用 `WC_Product_Simple::set_regular_price()` / `set_sale_price()`；
- 前台继续输出 WooCommerce 自身的 `WC_Product::get_price_html()`；
- 浏览器验收确认详情页标准 `del` 原价与 `ins` 促销价均可见。

倒计时：

- `fashion_child_sale_end_ms( WC_Product $product )` 只调用 `WC_Product::get_date_on_sale_to()`；
- 返回值严格等于 WooCommerce Sale End Unix timestamp × 1000；
- 不存在 `_fashion_sale_end` 或其他平行截止时间字段；
- JavaScript 使用 `data-sale-end - Date.now()` 每秒更新，过期后隐藏；
- 运行时契约验证时间源精确相等，浏览器验收验证倒计时为未来正值且会递减。

## 8. SKU、Kakao CTA 与复制链接

商品详情支持区从当前 `WC_Product` 读取唯一 SKU，并从 `get_permalink()` 读取当前 canonical product URL，输出到同一容器的 `data-product-sku` / `data-product-url`。

- Kakao CTA：本地原型把当前 SKU + 当前商品 URL 组合为咨询 payload，写入可验证的数据属性并在 `aria-live` 状态中显示；不连接真实 Kakao 账号，不发送数据；
- 复制链接：优先使用 Clipboard API，失败时使用临时 textarea fallback；
- Playwright 实际点击两项控制，确认 Kakao 状态/payload 包含 `FPOC-001` 与当前 URL，并从浏览器 Clipboard 读回与当前商品 URL 相同的值。

## 9. 纯展示模式

已完成的原型前台行为：

- 移除 WooCommerce loop/single Add to Cart hooks；
- 前台把商品过滤为不可购买；
- 移除/关闭 Botiga desktop/mobile cart、account 与 off-canvas 相关组件；
- 安全 primary 菜单仅包含 NEW/BEST/SALE、Shop 和 5 个分类；
- 不把 Cart、Checkout、My Account 当作顾客导航流程；
- 浏览器验收在四个页面和四种宽度检查主要交易文字/控件均不存在。

这只是前台原型展示控制，不宣称已完成生产级 URL/REST/API/权限封锁。正式化时应由独立 `fashion-core` 或后续正式任务完成路由级策略。

## 10. Child Theme 与插件范围

- Child Theme 自有文件：5 PHP、2 CSS、1 JavaScript；
- WooCommerce template override：**0**；
- Botiga 父主题修改：**NO**；
- 提交 Botiga 父主题源码：**NO**；
- 激活插件：WooCommerce 11.1.0（项目基础依赖）；
- 为原型新增的额外功能插件：**无**；
- Elementor / WPBakery / 其他 Builder：**无**；
- Botiga Pro / 付费主题或插件：**无**；
- 第三方 KREAM Clone 代码：**未使用**；所有 Child Theme PHP/CSS/JS 均为本任务实现，因此未新增 `THIRD_PARTY_NOTICES.md`。

## 11. 新增/修改内容

- `.gitignore`：本地运行时、Python cache、测试中间文件忽略规则；
- `dev/botiga-poc/`：Compose、Make、随机本地凭据准备、WordPress 初始化、合成数据、源码/运行时/浏览器测试；
- `wordpress/themes/fashion-child/`：Child Theme metadata、首页、hooks、目录模式、商品详情、CSS、JavaScript；
- `docs/superpowers/specs/` 与 `docs/superpowers/plans/`：经批准的设计规格与执行计划；
- `docs/evidence/p3-t001-alt001/`：6 张真实运行截图；
- 本报告。

未提交 WordPress Core、WooCommerce 插件包、Botiga 父主题、数据库、uploads、cache、`.runtime` 或凭据。

## 12. 验证结果

完整入口：

```bash
make -C dev/botiga-poc test
make -C dev/botiga-poc evidence
```

最近一次完整结果：

```text
SOURCE_CONTRACTS_PASS
RUNTIME_DATA_PASS products=25 unique_skus=25 orders=0
No syntax errors detected in 5 child-theme PHP files
BROWSER_ACCEPTANCE_PASS viewports=360,390,430,1440
FULL_VALIDATION_PASS
```

另行检查：

- Child Theme JavaScript：`node --check` PASS；
- 首页 / Shop / 分类 / 商品详情：HTTP 200；
- WooCommerce template override 数量：0；
- `.github/workflows/deploy-hostinger.yml` 相对 `origin/main`：无差异；
- WordPress Web 容器日志：无 PHP fatal；
- 25 个唯一 SKU、0 订单：PASS。

## 13. 已知限制与正式化建议

已知限制：

1. Kakao CTA 是本地无发送原型，尚未配置真实官方 Kakao 渠道；
2. 搜索只验证 WordPress/WooCommerce 基础入口，未选择或安装 FiboSearch；
3. 评价只验证 WooCommerce 标准评价和虚构展示，未决定最终后台图片评价插件；
4. 纯展示仅完成主要前台入口/控件隐藏，尚未完成生产级 Cart/Checkout/Account 路由与 API 策略；
5. 25 商品证明页面路线，不代表数千商品性能验收；
6. Botiga Free 没有 Pro template builder，但本原型依靠标准 WooCommerce hooks 已完成核心页面，因此当前不构成关键阻塞。

PASS 后建议的正式化范围：

1. 在独立正式任务中整理 `fashion-child`，把原型文案/数据和正式内容配置分离；
2. 用轻量 `fashion-core` 完成目录模式的生产级路由/API/账户策略；
3. 经用户确认后配置真实 Kakao 入口，但不把账号或认证信息写入 Git；
4. 分别验证韩文即时搜索与后台图片评价的成熟插件，避免堆叠重复插件；
5. 使用数千条纯合成商品做查询、归档分页、搜索和缓存性能验收；
6. 继续进行无障碍、真实内容、SEO、缓存和回归验收，再决定是否进入部署任务。

## 14. 安全与停止状态

- Hostinger 是否连接、读取、修改或部署：**NO**；
- 真实 WordPress/WooCommerce 是否连接或修改：**NO**；
- `.github/workflows/deploy-hostinger.yml` 是否修改：**NO**；
- PR #5 是否修改、关闭或合并：**NO**；
- 是否购买、安装或激活付费主题/插件：**NO**；
- 是否使用真实顾客/订单数据：**NO**；
- 是否开始 P3-T002：**NO**；
- 是否自行 merge：**NO**；
- 当前是否 BLOCKED：**NO**。

最终状态：`BOTIGA_ROUTE_PASS`，等待 GPT 独立验收。
