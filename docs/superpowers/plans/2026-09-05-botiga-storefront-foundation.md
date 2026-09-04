# Botiga Storefront Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Formalize the accepted Botiga Free prototype into a display-only storefront foundation with business rules owned by a minimal `fashion-core` plugin.

**Architecture:** Botiga Free remains an unmodified, untracked parent theme. `fashion-child` renders presentation and consumes stable product/brand data from `fashion-core`; `fashion-core` owns catalog mode, transaction-page redirects, product identity, and the selected WooCommerce brand taxonomy. The existing loopback-only Docker runtime provides all behavioral and responsive evidence.

**Tech Stack:** WordPress 7.1, WooCommerce 11.1.0, Botiga Free 2.4.8, PHP 8.3, MariaDB 11.8.3, Docker Compose, Python Playwright 1.60.0.

**Spec:** `docs/FASHION-THEME-3.2-DEV001.md`

## Global Constraints

- Work only on `codex/fashion-theme-3.2-foundation`, created from current `origin/main`.
- Do not connect to or deploy Hostinger and do not run `dry_run=false`.
- Do not modify `.github/workflows/deploy-hostinger.yml`.
- Do not buy or install paid themes or plugins.
- Do not start search or image-review plugin selection.
- Do not use real Kakao credentials or real customer/order data.
- Keep Botiga, WooCommerce, WordPress Core, runtime data, and credentials out of Git.
- Keep WooCommerce template overrides at zero.
- Create a Draft PR to `main`; never merge it.

---

### Task 1: Lock the formal contracts with failing tests

**Files:**
- Modify: `dev/botiga-poc/tests/source-contracts.sh`
- Modify: `dev/botiga-poc/tests/runtime-contract.php`
- Modify: `dev/botiga-poc/tests/catalog_test.py`

**Interfaces:**
- Consumes: the P3-T002 acceptance criteria and the existing PoC runtime.
- Produces: executable contracts for plugin activation, role separation, routes, identity, brand taxonomy, Sale End, and four responsive widths.

- [ ] **Step 1: Add source contracts**

Require the `fashion-core` entry point and three focused includes, read-only plugin mounts, idempotent activation, plugin PHP linting, no Botiga dependency inside the plugin, no tracked runtime/parent/core packages, no theme-owned purchase or route rules, no parallel Sale End field, and zero WooCommerce template overrides.

- [ ] **Step 2: Run the source contract and verify RED**

Run: `bash dev/botiga-poc/tests/source-contracts.sh`

Expected: `SOURCE_CONTRACTS_FAIL` because `wordpress/plugins/fashion-core/fashion-core.php` is absent.

- [ ] **Step 3: Add runtime contracts**

Assert that `fashion-core` is active; `fashion_core_get_product_identity()` returns literal keys `id`, `sku`, `url`, `regular_price`, `sale_price`, and `sale_end`; the first product's identity matches WooCommerce; Sale End equals `get_date_on_sale_to()->getTimestamp()`; `product_brand` is the only formal brand taxonomy; all 25 products have brand terms and no `_fashion_brand` value; two brand queries return products; and catalog purchase filters return false.

- [ ] **Step 4: Run the runtime contract and verify RED**

Run: `docker compose --project-directory dev/botiga-poc -f dev/botiga-poc/docker-compose.yml run --rm cli wp eval-file /workspace/tests/runtime-contract.php --allow-root`

Expected: `RUNTIME_DATA_FAIL` because the plugin API is not loaded.

- [ ] **Step 5: Add browser route and presentation contracts**

Require `/cart/`, `/checkout/`, and `/my-account/` to finish at Shop or home; require home, Shop, category, and product to stay healthy; require brand, price, SKU, countdown, Kakao CTA, and copy-link UI; reject customer-facing PoC copy; retain 360/390/430/1440 overflow, error, and interaction checks; and write new evidence under `docs/evidence/p3-t002/`.

- [ ] **Step 6: Run browser acceptance and verify RED**

Run: `dev/botiga-poc/.runtime/playwright-venv/bin/python dev/botiga-poc/tests/catalog_test.py`

Expected: FAIL because transaction routes remain accessible or the formal plugin-backed page contract is absent.

### Task 2: Add the minimal `fashion-core` plugin and runtime wiring

**Files:**
- Delete: `wordpress/plugins/fashion-core/.gitkeep`
- Create: `wordpress/plugins/fashion-core/fashion-core.php`
- Create: `wordpress/plugins/fashion-core/inc/catalog-mode.php`
- Create: `wordpress/plugins/fashion-core/inc/product-identity.php`
- Create: `wordpress/plugins/fashion-core/inc/brand.php`
- Modify: `dev/botiga-poc/docker-compose.yml`
- Modify: `dev/botiga-poc/bin/init-wordpress.sh`
- Modify: `dev/botiga-poc/bin/run-validation.sh`

**Interfaces:**
- Consumes: WooCommerce public functions, hooks, and `WC_Product` APIs only.
- Produces: `fashion_core_catalog_destination_url(): string`, `fashion_core_get_product_identity($product): ?array`, `fashion_core_brand_taxonomy(): string`, and `fashion_core_get_product_brand($product): ?WP_Term`.

- [ ] **Step 1: Add plugin bootstrap**

Create a standard plugin header with `Requires Plugins: woocommerce`, guard `ABSPATH`, define `FASHION_CORE_VERSION`, and require only the three project-owned include files.

- [ ] **Step 2: Implement catalog mode**

Return false from WooCommerce simple/variation purchasability filters, remove standard loop/single Add to Cart actions on `wp`, and redirect only front-end Cart/Checkout/My Account requests with `wp_safe_redirect()` to Shop or home.

- [ ] **Step 3: Implement identity API**

Resolve a product object or ID and return exactly the current product ID, WooCommerce SKU, canonical permalink, Regular Price, Sale Price, and the timestamp obtained directly from `get_date_on_sale_to()`.

- [ ] **Step 4: Implement brand API**

At late `init`, select existing `product_brand`; only if it is absent, register stable `fashion_brand`. Expose the selected taxonomy and first assigned `WP_Term` without reading product meta.

- [ ] **Step 5: Wire the isolated runtime**

Mount `wordpress/plugins/fashion-core` read-only in Web and CLI, activate it idempotently after WooCommerce, print its version, and lint every plugin PHP file during full validation.

- [ ] **Step 6: Run source/runtime checks**

Run the source contract, reinitialize the local runtime, then run the runtime contract. Expected: `SOURCE_CONTRACTS_PASS` and `RUNTIME_DATA_PASS products=25 unique_skus=25 brands>=2 orders=0`.

### Task 3: Make the child theme presentation-only and migrate fixture brands

**Files:**
- Delete: `wordpress/themes/fashion-child/inc/catalog-mode.php`
- Create: `wordpress/themes/fashion-child/inc/storefront-presentation.php`
- Modify: `wordpress/themes/fashion-child/functions.php`
- Modify: `wordpress/themes/fashion-child/inc/product-detail.php`
- Modify: `wordpress/themes/fashion-child/inc/collections.php`
- Modify: `wordpress/themes/fashion-child/front-page.php`
- Modify: `wordpress/themes/fashion-child/style.css`
- Modify: `wordpress/themes/fashion-child/assets/css/catalog.css`
- Modify: `dev/botiga-poc/seed/catalog.php`

**Interfaces:**
- Consumes: `fashion_core_get_product_identity()` and `fashion_core_get_product_brand()`.
- Produces: Botiga-specific header/presentation adapters, taxonomy-backed brand rendering, plugin-backed SKU/URL/Sale End markup, and standard WooCommerce review presentation.

- [ ] **Step 1: Move only Botiga presentation adapters**

Keep Botiga component/theme-mod filters, empty wrapper cleanup, and catalog body class in the child theme. Remove purchasability and transaction rules from theme code.

- [ ] **Step 2: Consume plugin identity and brand**

Use plugin output for every custom product card/detail SKU and URL, render the brand term name on custom/archive/detail views, and convert the Sale End seconds to the existing JavaScript millisecond data attribute without another date source.

- [ ] **Step 3: Remove customer-facing PoC defaults**

Replace the theme description, remove the local-only consultation note, remove its CSS, and render seeded standard WooCommerce reviews rather than fixed synthetic-review text from the theme.

- [ ] **Step 4: Migrate the fixture seed**

Resolve `fashion_core_brand_taxonomy()`, upsert stable brand terms, assign one term to every product, and delete legacy `_fashion_brand` values after save.

- [ ] **Step 5: Run runtime and browser checks**

Run the runtime contract and Playwright suite. Expected: taxonomy-backed brands, plugin/theme identity agreement, transaction-route redirects, and four passing viewports.

### Task 4: Record the formal route and produce evidence

**Files:**
- Modify: `README.md`
- Create: `docs/FASHION-THEME-3.2-REPORT001.md`
- Create: `docs/evidence/p3-t002/mobile-home-390.png`
- Create: `docs/evidence/p3-t002/mobile-shop-390.png`
- Create: `docs/evidence/p3-t002/mobile-product-390.png`
- Create: `docs/evidence/p3-t002/mobile-product-430.png`
- Create: `docs/evidence/p3-t002/desktop-home-1440.png`

**Interfaces:**
- Consumes: verified runtime versions, test output, route results, and captured local pages.
- Produces: the current project fact source and independent-review evidence.

- [ ] **Step 1: Update README**

Record Botiga Free as the current parent route, `fashion-child` as presentation, `fashion-core` as minimal business rules, WoodMart as evaluated/replaced and not purchased, WooCommerce-native data priority, and the formal storefront-foundation stage.

- [ ] **Step 2: Capture evidence**

Run: `make -C dev/botiga-poc evidence`

Expected: required PNG files with widths 390, 390, 390, 430, and 1440, using only synthetic runtime data.

- [ ] **Step 3: Write the report**

Record branch/PR status, role boundary, plugin tree, route behavior, product identity, selected `product_brand`, exact Sale End source, runtime versions, test output, screenshots, zero overrides, no parent-theme changes, no Hostinger access, blocker state, and next-task recommendation without secrets.

### Task 5: Full verification and Draft PR

**Files:**
- Verify all changed files only.

**Interfaces:**
- Consumes: complete implementation and evidence.
- Produces: one reviewable Draft PR to `main`.

- [ ] **Step 1: Run complete validation**

Run: `make -C dev/botiga-poc test`

Expected: `SOURCE_CONTRACTS_PASS`, `RUNTIME_DATA_PASS`, `BROWSER_ACCEPTANCE_PASS viewports=360,390,430,1440`, and `FULL_VALIDATION_PASS`.

- [ ] **Step 2: Verify repository safety and scope**

Run `git diff --check`, inspect the complete diff, confirm `.github/workflows/deploy-hostinger.yml` has no diff, confirm no tracked `.runtime`, parent theme, WooCommerce package, WordPress Core, real credential, or WooCommerce template override.

- [ ] **Step 3: Commit and push**

Commit only P3-T002 files, then push `codex/fashion-theme-3.2-foundation` without force.

- [ ] **Step 4: Create the Draft PR**

Create `[P3-T002] formalize Botiga storefront foundation` against `main`, explicitly stating local-only scope, no Hostinger access, no Workflow change, no search/review plugin selection, no production deployment, waiting for GPT independent acceptance, and no self-merge.

- [ ] **Step 5: Record and verify the PR**

Add the actual PR number/link to the report, push the documentation update, and verify the PR remains Draft/Open with the expected Head SHA.
