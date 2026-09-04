# Botiga Free Mobile Catalog Prototype Design

## Status and scope

- Task: `P3-T001-ALT001`
- Approved direction: Botiga Free plus the repository-owned `fashion-child` theme
- Baseline: `origin/main` at `523e8a3f438ba2760f6c667d68a9eaba174d97f3`
- Target branch: `codex/fashion-theme-3.1-botiga-poc`
- Outcome: a local-only, evidence-backed prototype and an explicit `BOTIGA_ROUTE_PASS` or `BOTIGA_ROUTE_FAIL`

This task evaluates whether a free, maintained parent theme plus a small child theme can support the confirmed Korean mobile catalog experience. It does not authorize production deployment, purchases, customer/order data, or a production-grade catalog-routing policy. PR #5 remains unchanged.

## Confirmed product boundary

The prototype is a Korean-first, mobile-first product-discovery catalog. WooCommerce remains the source of product, category, price, sale schedule, and SKU data. Products are independent Simple Products; there is no size-selection flow. The visible customer journey ends at product discovery, copying a product link, or starting a Kakao-oriented consultation interaction. Cart, checkout, payment, orders, and customer accounts are outside the customer journey.

The visual target borrows only general principles from KREAM, Dewu, 29CM, and MUSINSA: dense image-led discovery, restrained monochrome styling, useful whitespace, and editorial rhythm. No third-party brand assets, copy, proprietary icons, or unlicensed clone code will be used.

## Approaches considered

### 1. Recommended: child template plus WooCommerce hooks

Use one child-owned front-page template for the editorial home page, while keeping WooCommerce archive and single-product rendering on standard WooCommerce structures. Add and remove presentation through supported hooks, filters, enqueued CSS, and small JavaScript modules. Avoid WooCommerce template overrides unless an executable acceptance requirement cannot be met otherwise.

This route best tests the stated hypothesis: a mature free parent theme remains responsible for compatibility and base layout, while the repository owns only the distinctive catalog layer. It minimizes theme lock-in and makes the amount of custom code measurable.

### 2. Gutenberg page plus shortcodes

Build the home page from editor blocks and product shortcodes. This improves routine page editing but makes deterministic test-data initialization and pixel-stable evidence more complicated. It also does not materially reduce the archive and product-page customization required for this prototype.

### 3. Broad WooCommerce template overrides

Copy archive, loop, and single-product templates into the child theme for full markup control. This would achieve visual control quickly, but it weakens the experiment by increasing upgrade burden and maintenance risk. It is rejected unless the recommended route encounters a documented technical blocker; such a blocker must be reported rather than silently expanding the override surface.

## Local isolation architecture

The repository will contain a minimal `dev/botiga-poc/` environment. Docker Compose will use a unique project name, private network, and named database/content volumes. MariaDB will expose no host port. WordPress will bind only to `127.0.0.1` on a non-conflicting port. A WP-CLI service will install and initialize software inside the isolated volumes.

The target runtime is:

- WordPress 7.1 with PHP 8.3 Apache;
- WooCommerce 11.1.0 from WordPress.org;
- Botiga Free 2.4.8 from WordPress.org;
- MariaDB in a private Compose network;
- no Elementor, heavy builder, paid theme, or paid plugin.

Runtime downloads, WordPress Core, WooCommerce, Botiga, uploads, cache, and the database will not be committed. Only reproducible environment definitions, initialization code, child-theme source, tests, screenshots, and the report belong in Git.

All local credentials will be synthetic development values. No Hostinger hostname, IP address, port, username, key, account, absolute server path, cookie, token, API key, or customer data will enter the environment or repository.

## Repository components

### `dev/botiga-poc/`

Contains the Compose definition, environment example, deterministic initialization script, local test helpers, and Playwright acceptance tests. Initialization is idempotent: it waits for WordPress, installs exact public versions, activates Botiga and `fashion-child`, activates WooCommerce, creates the required pages and taxonomies, and inserts only synthetic products and reviews.

### `wordpress/themes/fashion-child/`

Contains `style.css`, `functions.php`, the home template, and focused CSS/JavaScript assets. The `Template` declaration is `botiga`. Parent theme source is neither copied nor modified.

Responsibilities are separated as follows:

- PHP registers assets, presentation hooks, deterministic product collections, product metadata output, and transaction-entry removal.
- CSS owns the monochrome visual system, mobile grids, truncation, price hierarchy, touch targets, responsive spacing, and bottom-navigation safe area.
- JavaScript owns sale countdown rendering and the two user interactions: Kakao consultation prototype and copy-current-link feedback.
- WooCommerce continues to own product identity, permalink, SKU, regular price, sale price, and sale end timestamp.

### `docs/evidence/p3-t001-alt001/`

Contains committed screenshots from the real local runtime. Required captures cover the 390 px home, shop, and product pages; at least one 360 px or 430 px boundary; one desktop view; and a product view that shows SKU, regular/sale pricing, countdown, Kakao CTA, and copy-link control together.

### `docs/FASHION-THEME-3.1-ALT001-REPORT.md`

Records exact versions, run instructions, changed files, page status, viewport results, executable checks, screenshots, limitations, plugin inventory, parent-theme integrity, Hostinger non-contact, and the binary route decision.

## Page design

### Mobile home

The home page uses a compact wordmark/search header, NEW/BEST/SALE and category navigation, one static editorial hero, and dense two-column product discovery. NEW, BEST, and SALE collections are backed by synthetic taxonomy/price state rather than hard-coded product cards. A short styling editorial and synthetic review section break up the product stream. A fixed bottom navigation provides discovery shortcuts and preserves enough bottom padding not to cover content. No cart, checkout, or account link appears.

### Shop and category archive

The archive keeps WooCommerce's product query and ordering semantics. Child-theme hooks add a compact category heading and filter/sort affordance, brand line, Korean two-line product name, price hierarchy, and NEW/BEST/SALE badges. CSS converts the conventional shop layout into a dense two-column mobile grid without adding large purchase buttons.

### Product detail

The single-product page keeps the WooCommerce gallery and product object. It displays the synthetic brand, Korean name, unique SKU, regular and sale prices, description, review prototype, and related products. When `WC_Product::get_date_on_sale_to()` returns a future date, PHP emits that WooCommerce timestamp for the countdown module; there is no second deadline field. Expired or absent sale dates render no countdown.

The Kakao CTA is explicitly a non-production prototype: it receives the current SKU and canonical product URL as data, prepares a consultation payload locally, and provides visible feedback without requiring a real Kakao account. The copy control copies the current canonical product URL with a safe fallback. Both controls use at least 44 px touch targets.

## Display-only transaction handling

Supported WooCommerce hooks and filters remove loop and single-product add-to-cart controls. Theme header cart/account affordances are removed through supported child-theme integration where available, with narrowly scoped CSS only as a presentation fallback. No WooCommerce Core or Botiga file is edited.

The acceptance suite checks that home, archive, and product journeys contain no visible primary Cart, Checkout, My Account, quantity, or Add to Cart control. Direct platform routes are not claimed to be production-blocked. The report will list that routing/policy enforcement as future `fashion-core` work if the route passes.

## Synthetic data model

Initialization creates 25 published Simple Products across shoes, bags, apparel, fragrance, and accessories. Every product has:

- an English synthetic brand;
- a Korean product name;
- a unique deterministic SKU;
- a regular price;
- a category and product imagery generated specifically for the prototype.

A representative subset has a lower sale price and real WooCommerce `_sale_price_dates_from` / `_sale_price_dates_to` values. Product tags supply NEW and BEST examples; WooCommerce sale state supplies SALE. Synthetic reviews include Korean text, rating, date, and non-identifying nickname. No real customer or order record is created.

## Error and fallback behavior

- Initialization fails loudly if exact WordPress, WooCommerce, or Botiga installation/activation fails.
- Re-running initialization updates or reuses deterministic objects rather than duplicating products.
- Missing SKU renders a visible unavailable marker and disables the consultation payload; seeded acceptance products must never enter this state.
- Missing or expired sale end hides the countdown while preserving WooCommerce price output.
- Clipboard API failure uses a selection-based fallback and returns visible success/failure status.
- JavaScript-disabled pages retain readable product identity, prices, and links; only live countdown decrement and enhanced copy feedback are lost.
- PHP or HTTP failure, console errors, horizontal overflow, paid-feature dependency, or broad template override triggers investigation and may force `BOTIGA_ROUTE_FAIL`.

## Verification strategy

Development follows test-first cycles. Before child-theme implementation, acceptance checks will describe the required product-data and rendered-page behavior and will be run to demonstrate failure. Implementation then proceeds in the smallest increments until the same checks pass.

Executable verification includes:

1. PHP syntax checks for every committed PHP file.
2. JavaScript syntax/static checks for every committed JavaScript file.
3. WP-CLI assertions for exact runtime versions, active theme/plugin, 25 Simple Products, unique SKUs, and WooCommerce sale-end metadata.
4. HTTP checks for home, shop/category, and product pages with no PHP fatal or HTTP 5xx.
5. Browser checks at 360, 390, and 430 px plus desktop for horizontal overflow, Korean wrapping, two-column cards, touch targets, unobscured bottom content, and visible transaction-entry absence.
6. Browser interaction checks that the countdown originates from the current product's WooCommerce sale end, the Kakao control exposes the current SKU/URL, and copy-link returns the current product URL.
7. Repository checks proving no Botiga parent source, WordPress Core, WooCommerce package, database, uploads, secrets, production configuration, or deployment-workflow diff is committed.
8. A final public-safety scan before push and Draft PR creation.

## Route decision

`BOTIGA_ROUTE_PASS` is permitted only when all three page types are credible foundations for continued polishing, all mobile widths pass, core data stays in WooCommerce, no paid capability or heavy builder is required, and the customization remains materially smaller than a complete custom theme.

`BOTIGA_ROUTE_FAIL` is required if the prototype needs broad parent/template replacement, paid functionality, multiple overlapping plugins, or still cannot reach the target mobile experience after reasonable child-theme work. A fail result completes the experiment; it does not authorize switching themes.

## Delivery and stop state

Only ALT001 environment files, child-theme source, tests, evidence, design/plan documents, and the ALT001 report may be committed. `.github/workflows/deploy-hostinger.yml` must remain byte-identical to `origin/main`. The completed branch will be pushed and opened as a new Draft PR titled `[P3-T001-ALT001] Botiga Free KREAM-style prototype` against `main`.

The PR will not be merged. PR #5 will not be modified, closed, or merged. Hostinger will not be connected to, inspected, or changed. Work stops after the required final report for independent GPT acceptance.
