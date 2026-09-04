# Botiga Free Mobile Catalog Prototype Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build and verify a local-only Botiga Free plus `fashion-child` Korean mobile catalog prototype and deliver evidence supporting one binary route decision.

**Architecture:** A unique Docker Compose project runs WordPress, WooCommerce, MariaDB, and WP-CLI entirely locally, with only the web service exposed on loopback. Botiga stays an uncommitted runtime dependency; the repository owns a small child theme that uses WooCommerce data and hooks, deterministic synthetic fixtures, and Playwright acceptance evidence.

**Tech Stack:** WordPress 7.1 / PHP 8.3 Apache, WooCommerce 11.1.0, Botiga Free 2.4.8, MariaDB, WP-CLI, Bash, vanilla PHP/CSS/JavaScript, Playwright.

**Spec:** `docs/superpowers/specs/2026-09-04-botiga-free-prototype-design.md`

## Global Constraints

- Work only on `codex/fashion-theme-3.1-botiga-poc`, based on `origin/main@523e8a3f438ba2760f6c667d68a9eaba174d97f3`.
- Do not connect to, inspect, modify, or deploy Hostinger or any remote WordPress instance.
- Do not modify `.github/workflows/deploy-hostinger.yml` or PR #5.
- Do not buy a theme/plugin, require Botiga Pro, install a heavy builder, or modify/copy Botiga parent files into Git.
- Use only deterministic synthetic products/reviews and project-created placeholder imagery; create no customer or order records.
- Keep WordPress Core, WooCommerce, Botiga, database data, uploads, caches, runtime credentials, and Node dependencies out of Git.
- Implement the home, shop/category, and product-detail flows at 360, 390, and 430 px with a Korean-first monochrome mobile UI.
- Read sale end only from `WC_Product::get_date_on_sale_to()`; do not create an alternate sale-deadline field.
- Remove primary Cart, Checkout, My Account, quantity, and Add to Cart customer affordances without claiming production-level route blocking.
- End with exactly one `BOTIGA_ROUTE_PASS` or `BOTIGA_ROUTE_FAIL`, a new Draft PR to `main`, no merge, and no start of P3-T002.

---

## File map

- `.gitignore`: excludes local runtime secrets, Python caches, Playwright results, and disposable evidence intermediates.
- `dev/botiga-poc/docker-compose.yml`: defines the isolated database, WordPress, and WP-CLI services.
- `dev/botiga-poc/Makefile`: exposes reproducible `prepare`, `up`, `init`, `test`, `evidence`, and `down` commands.
- `dev/botiga-poc/bin/prepare-runtime.sh`: creates untracked random local secret files without printing them.
- `dev/botiga-poc/bin/init-wordpress.sh`: installs exact public versions, activates the child theme, configures permalinks/pages, and invokes fixtures.
- `dev/botiga-poc/bin/run-validation.sh`: runs source, PHP, JavaScript, runtime, HTTP, and browser checks in order.
- `dev/botiga-poc/seed/catalog.php`: idempotently creates taxonomy, 25 Simple Products, sale schedules, generated placeholder media, and synthetic reviews.
- `dev/botiga-poc/tests/source-contracts.sh`: enforces repository, parent-theme, workflow, and source-level requirements.
- `dev/botiga-poc/tests/runtime-contract.php`: validates WooCommerce runtime data and front-end contract with WordPress loaded.
- `dev/botiga-poc/tests/catalog_test.py`: uses native Python Playwright to test pages, responsive layout, commerce-data provenance, interactions, and deterministic screenshots.
- `wordpress/themes/fashion-child/style.css`: WordPress child-theme metadata and minimal root import.
- `wordpress/themes/fashion-child/functions.php`: theme bootstrap and focused include loading.
- `wordpress/themes/fashion-child/inc/catalog-mode.php`: removes visible transaction affordances.
- `wordpress/themes/fashion-child/inc/collections.php`: retrieves and renders standard WooCommerce product collections.
- `wordpress/themes/fashion-child/inc/product-detail.php`: outputs SKU, sale-end-backed countdown, and consultation/copy controls.
- `wordpress/themes/fashion-child/front-page.php`: renders the B-Lite discovery home.
- `wordpress/themes/fashion-child/assets/css/catalog.css`: complete responsive visual system.
- `wordpress/themes/fashion-child/assets/js/catalog.js`: countdown, consultation prototype, and clipboard behavior.
- `docs/evidence/p3-t001-alt001/*.png`: actual Playwright screenshots from the local runtime.
- `docs/FASHION-THEME-3.1-ALT001-REPORT.md`: versions, evidence, limits, decision, and stop state.

---

### Task 1: Add repository and isolation contracts

**Files:**
- Create: `.gitignore`
- Create: `dev/botiga-poc/tests/source-contracts.sh`
- Test: `dev/botiga-poc/tests/source-contracts.sh`

**Interfaces:**
- Consumes: clean branch and `origin/main`.
- Produces: executable source gate used by every later task.

- [ ] **Step 1: Write the failing source-contract test**

Create an executable shell test with strict mode. It must fail until the child theme and environment exist and include these assertions:

```bash
#!/usr/bin/env bash
set -euo pipefail
repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd -P)"
cd "$repo_root"

test "$(git branch --show-current)" = "codex/fashion-theme-3.1-botiga-poc"
test -f wordpress/themes/fashion-child/style.css
grep -Eq '^Template:[[:space:]]+botiga$' wordpress/themes/fashion-child/style.css
test ! -d wordpress/themes/botiga
test ! -d wordpress/plugins/woocommerce
test -z "$(git ls-files dev/botiga-poc/.runtime)"
git diff --exit-code origin/main -- .github/workflows/deploy-hostinger.yml
! git grep -En 'BEGIN ([A-Z ]+ )?PRIVATE KEY|github_pat_|ghp_[A-Za-z0-9]|/Users/[^/]+/' -- ':!docs/FASHION-DEPLOY-*'
grep -Rqs 'get_date_on_sale_to' wordpress/themes/fashion-child
! grep -Rqs '_fashion_sale_end' wordpress/themes/fashion-child
echo 'SOURCE_CONTRACTS_PASS'
```

- [ ] **Step 2: Run the test and verify RED**

Run: `bash dev/botiga-poc/tests/source-contracts.sh`

Expected: non-zero exit because `wordpress/themes/fashion-child/style.css` does not exist.

- [ ] **Step 3: Add ignore rules**

Add only runtime-derived paths:

```gitignore
dev/botiga-poc/.runtime/
dev/botiga-poc/tests/__pycache__/
dev/botiga-poc/tests/test-results/
```

- [ ] **Step 4: Verify the workflow is unchanged and commit the contracts**

Run:

```bash
git diff --check
git diff --exit-code origin/main -- .github/workflows/deploy-hostinger.yml
```

Expected: both commands succeed.

Commit: `test: add Botiga prototype source contracts`

---

### Task 2: Build the disposable WordPress runtime and child-theme skeleton

**Files:**
- Create: `dev/botiga-poc/docker-compose.yml`
- Create: `dev/botiga-poc/Makefile`
- Create: `dev/botiga-poc/bin/prepare-runtime.sh`
- Create: `dev/botiga-poc/bin/init-wordpress.sh`
- Create: `wordpress/themes/fashion-child/style.css`
- Create: `wordpress/themes/fashion-child/functions.php`
- Test: `dev/botiga-poc/tests/source-contracts.sh`

**Interfaces:**
- Consumes: Task 1 source gate.
- Produces: loopback WordPress URL, WP-CLI service, exact active public dependencies, and a loadable `fashion-child` theme.

- [ ] **Step 1: Extend the source test for isolation and exact dependencies**

Require the Compose source to contain `127.0.0.1`, `wordpress:7.1`, `php8.3`, a private database with no `ports:` entry, and a read-only mount of `wordpress/themes/fashion-child`. Require initialization to use WooCommerce `11.1.0` and Botiga download URL `https://downloads.wordpress.org/theme/botiga.2.4.8.zip`.

- [ ] **Step 2: Run the source test and verify RED**

Run: `bash dev/botiga-poc/tests/source-contracts.sh`

Expected: non-zero exit because Compose and child-theme files are absent.

- [ ] **Step 3: Implement untracked runtime-secret preparation**

`prepare-runtime.sh` must use `umask 077`, create `dev/botiga-poc/.runtime`, and write random database and local WordPress-admin values only if missing. Use `openssl rand -hex 24`; never echo generated values. Write a Compose `.env` containing only non-sensitive loopback/project configuration, while secret values remain individual ignored files mounted through Compose secrets.

- [ ] **Step 4: Define the Compose stack**

Use project name `fashion_botiga_poc`, a private `poc_internal` network, named `db_data` and `wp_data` volumes, MariaDB without host ports, and WordPress bound as `127.0.0.1:8097:80`. Mount the child theme into `/var/www/html/wp-content/themes/fashion-child` and use Compose secrets for database credentials. The WP-CLI service must share WordPress content and the seed directory but publish no port.

- [ ] **Step 5: Add the child-theme skeleton**

`style.css` must declare:

```css
/*
Theme Name: Fashion Child
Template: botiga
Version: 0.1.0
Text Domain: fashion-child
*/
```

`functions.php` must guard direct access and register `after_setup_theme` and `wp_enqueue_scripts` callbacks without implementing page behavior yet.

- [ ] **Step 6: Add exact, idempotent WordPress initialization**

`init-wordpress.sh` must wait for the database, run `wp core install` only if needed, install WooCommerce 11.1.0, install Botiga 2.4.8 from the official zip, activate WooCommerce and `fashion-child`, set Korean locale/timezone and pretty permalinks, create/resolve the Shop page, and flush rewrite rules. It must not contact any production URL.

- [ ] **Step 7: Add Make targets and verify GREEN**

Expose these commands:

```make
prepare:
	./bin/prepare-runtime.sh
up: prepare
	docker compose up -d db wordpress
init: up
	docker compose run --rm cli sh /workspace/bin/init-wordpress.sh
down:
	docker compose down
```

Run: `bash dev/botiga-poc/tests/source-contracts.sh`

Expected: `SOURCE_CONTRACTS_PASS`.

- [ ] **Step 8: Start and verify the isolated runtime**

Run: `make -C dev/botiga-poc init`

Then run WP-CLI version assertions for WordPress 7.1, WooCommerce 11.1.0, Botiga 2.4.8, and active stylesheet `fashion-child`. Expected: exact matches and HTTP 200 from `http://127.0.0.1:8097/`.

- [ ] **Step 9: Commit**

Commit: `build: add isolated Botiga WordPress runtime`

---

### Task 3: Create deterministic synthetic catalog fixtures

**Files:**
- Create: `dev/botiga-poc/seed/catalog.php`
- Create: `dev/botiga-poc/tests/runtime-contract.php`
- Modify: `dev/botiga-poc/bin/init-wordpress.sh`
- Test: `dev/botiga-poc/tests/runtime-contract.php`

**Interfaces:**
- Consumes: loaded WordPress/WooCommerce runtime from Task 2.
- Produces: 25 Simple Products, five flat categories, NEW/BEST tags, sale metadata, generated images, reviews, and stable acceptance-product options.

- [ ] **Step 1: Write the failing runtime contract**

The WP-loaded PHP test must assert:

```php
$products = wc_get_products(['limit' => -1, 'status' => 'publish']);
assert(count($products) === 25);
$skus = array_map(static fn(WC_Product $p): string => $p->get_sku(), $products);
assert(count(array_unique($skus)) === 25);
assert(count(array_filter($products, static fn(WC_Product $p): bool => $p->is_type('simple'))) === 25);
$featured_id = (int) get_option('fashion_poc_featured_product_id');
$featured = wc_get_product($featured_id);
assert($featured instanceof WC_Product_Simple);
assert($featured->get_regular_price() !== '');
assert($featured->get_sale_price() !== '');
assert($featured->get_date_on_sale_to() instanceof WC_DateTime);
assert($featured->get_date_on_sale_to()->getTimestamp() > time());
assert(wc_get_orders(['limit' => 1, 'return' => 'ids']) === []);
echo "RUNTIME_DATA_PASS\n";
```

- [ ] **Step 2: Run the contract and verify RED**

Run: `docker compose run --rm cli wp eval-file /workspace/tests/runtime-contract.php --allow-root`

Expected: assertion failure because there are no products.

- [ ] **Step 3: Implement idempotent categories and products**

Create the five Korean categories `신발`, `가방`, `의류`, `향수`, `액세서리`; tags `NEW` and `BEST`; and 25 products keyed by deterministic SKUs `FPOC-001` through `FPOC-025`. Use fictional English brands and Korean names, regular prices in KRW, and no stock/order/customer records. `FPOC-001` is the stable featured sale product used by runtime and browser tests.

- [ ] **Step 4: Add real WooCommerce sales and local imagery**

For selected products call `set_sale_price()`, `set_date_on_sale_from()`, and `set_date_on_sale_to()` with a future timestamp. Generate five neutral 4:5 PNG compositions with PHP GD inside the isolated runtime, attach them as synthetic product media, and reuse them across products. Store no external image or brand asset.

- [ ] **Step 5: Add synthetic reviews and stable test pointers**

Insert only clearly synthetic Korean review comments with ratings and fictional nicknames. Save the featured sale product ID and representative category term ID in WordPress options for runtime and browser tests.

- [ ] **Step 6: Invoke fixtures and verify GREEN**

Append `wp eval-file /workspace/seed/catalog.php --allow-root` to initialization, rerun it twice, and prove the second run still has exactly 25 products.

Expected: `RUNTIME_DATA_PASS` on both runs.

- [ ] **Step 7: Commit**

Commit: `test: add synthetic WooCommerce catalog fixtures`

---

### Task 4: Implement display-only commerce behavior and product data controls

**Files:**
- Create: `wordpress/themes/fashion-child/inc/catalog-mode.php`
- Create: `wordpress/themes/fashion-child/inc/product-detail.php`
- Create: `wordpress/themes/fashion-child/assets/js/catalog.js`
- Modify: `wordpress/themes/fashion-child/functions.php`
- Modify: `dev/botiga-poc/tests/runtime-contract.php`
- Test: `dev/botiga-poc/tests/source-contracts.sh`
- Test: `dev/botiga-poc/tests/runtime-contract.php`

**Interfaces:**
- Consumes: WooCommerce global/current `WC_Product` and canonical permalink.
- Produces: `fashion_child_sale_end_ms(WC_Product): ?int`, product support markup with `data-product-sku` and `data-product-url`, and no primary purchase controls.

- [ ] **Step 1: Extend tests and verify RED**

Add runtime assertions that `fashion_child_sale_end_ms($featured)` equals `$featured->get_date_on_sale_to()->getTimestamp() * 1000`, that rendered support markup contains the featured SKU/permalink, and that no `_fashion_sale_end` metadata exists. The source contract must require removal of loop and single add-to-cart hooks.

Expected: failure because the functions do not exist.

- [ ] **Step 2: Remove primary transaction affordances**

In `catalog-mode.php`, remove `woocommerce_template_loop_add_to_cart` and `woocommerce_template_single_add_to_cart` from their standard hooks, filter products as non-purchasable on the front end, and remove Botiga cart/account/header affordances using supported filters/actions discovered in Botiga 2.4.8. Use narrowly scoped CSS hiding only if Botiga exposes no supported removal hook; document any fallback.

- [ ] **Step 3: Render SKU, countdown source, Kakao CTA, and copy control**

`fashion_child_sale_end_ms()` must call only `WC_Product::get_date_on_sale_to()` and return a future millisecond timestamp or `null`. Render escaped current SKU and canonical permalink into explicit `data-product-sku` / `data-product-url` attributes. The consultation button opens an in-page prototype status containing the SKU and copied consultation payload; it must not require a real Kakao account. The copy button uses the current canonical product URL.

- [ ] **Step 4: Implement resilient JavaScript**

Use `Date.now()` against `data-sale-end`, update once per second, and hide at expiry. Use `navigator.clipboard.writeText()` with a temporary-textarea fallback. Return visible Korean success/failure status through an `aria-live` region. Do not embed an external account identifier.

- [ ] **Step 5: Verify GREEN and commit**

Run source contracts, `php -l` in the WordPress container for every child-theme PHP file, `node --check` for `catalog.js`, and the WP-loaded runtime contract.

Expected: all pass.

Commit: `feat: add catalog-only product interactions`

---

### Task 5: Build the B-Lite mobile home and reusable product collections

**Files:**
- Create: `wordpress/themes/fashion-child/inc/collections.php`
- Create: `wordpress/themes/fashion-child/front-page.php`
- Modify: `wordpress/themes/fashion-child/functions.php`
- Create: `wordpress/themes/fashion-child/assets/css/catalog.css`
- Test: `dev/botiga-poc/tests/runtime-contract.php`

**Interfaces:**
- Consumes: WooCommerce product/category/tag queries and Task 3 fixtures.
- Produces: `fashion_child_get_collection(string, int): array` and `fashion_child_render_product_card(WC_Product): void`, reused by home and related content.

- [ ] **Step 1: Add home/collection assertions and verify RED**

Assert NEW and BEST queries return seeded tagged products, SALE returns products from WooCommerce's sale IDs, rendered cards include brand, Korean title, image, SKU-link permalink, and standard WooCommerce price HTML, and the front page contains NEW/BEST/SALE headings with no cart/account link.

- [ ] **Step 2: Implement standard-data collection queries**

Use `wc_get_products()` and WooCommerce/taxonomy query arguments. Do not copy product data into page-builder content or create a parallel catalog data structure.

- [ ] **Step 3: Implement semantic home markup**

Create the compact wordmark/search header integration, NEW/BEST/SALE/category navigation, static editorial hero, three product streams, one synthetic styling editorial, one synthetic review block, and mobile bottom navigation. Use Korean copy written for the prototype and no referenced-brand copy/assets.

- [ ] **Step 4: Implement the base responsive visual system**

Define a monochrome token set, Korean system sans stack, 4:5 media ratio, two-column mobile grid, two-line title clamp, clear regular/sale price hierarchy, restrained borders/shadows, and at least 44 px interactive hit areas. Add bottom safe-area padding and desktop max-width rules.

- [ ] **Step 5: Verify and commit**

Run source, PHP, JavaScript, runtime, and HTTP checks. Inspect the real 390 px home before committing.

Commit: `feat: build Korean mobile discovery home`

---

### Task 6: Adapt Botiga shop/category and product-detail layouts

**Files:**
- Modify: `wordpress/themes/fashion-child/inc/collections.php`
- Modify: `wordpress/themes/fashion-child/inc/product-detail.php`
- Modify: `wordpress/themes/fashion-child/assets/css/catalog.css`
- Modify: `dev/botiga-poc/tests/runtime-contract.php`
- Test: local home/shop/product HTTP responses.

**Interfaces:**
- Consumes: Botiga 2.4.8 markup and standard WooCommerce archive/single hooks.
- Produces: compact mobile archive and information-first product detail without a WooCommerce template override unless an evidence-backed blocker is recorded.

- [ ] **Step 1: Add rendered archive/single assertions and verify RED**

Require brand text, two-line title class, NEW/BEST/SALE badge, regular/sale price markup, SKU, future countdown element, Kakao CTA, copy control, review section, related products, and absence of quantity/add-to-cart elements.

- [ ] **Step 2: Add archive hooks**

Use WooCommerce loop hooks to inject synthetic brand and badges while retaining the product link, image, title, and standard price HTML. Keep native ordering semantics and add a compact Korean category/filter label without installing a filter plugin.

- [ ] **Step 3: Add product-detail hooks**

Reorder standard summary elements, add brand/SKU/support controls after price, preserve gallery/description/reviews/related products, and remove quantity/add-to-cart output. Confirm sale and regular prices remain standard WooCommerce output.

- [ ] **Step 4: Style archive and single pages across widths**

At 360/390/430 px enforce a two-column archive, stable media ratio, non-overlapping prices, readable mixed Korean/English text, gallery fit, full-width support controls, and unobscured bottom content. At desktop constrain content and allow a balanced gallery/summary split.

- [ ] **Step 5: Verify no broad overrides and commit**

Run `find wordpress/themes/fashion-child -path '*/woocommerce/*' -type f`. Expected: no files. If any override became technically necessary, stop and document the exact blocker before continuing.

Commit: `feat: tailor Botiga catalog and product views`

---

### Task 7: Add browser acceptance tests and capture real evidence

**Files:**
- Create: `dev/botiga-poc/tests/catalog_test.py`
- Modify: `dev/botiga-poc/bin/run-validation.sh`
- Create: `docs/evidence/p3-t001-alt001/mobile-home-390.png`
- Create: `docs/evidence/p3-t001-alt001/mobile-shop-390.png`
- Create: `docs/evidence/p3-t001-alt001/mobile-product-390.png`
- Create: `docs/evidence/p3-t001-alt001/mobile-home-360.png`
- Create: `docs/evidence/p3-t001-alt001/mobile-product-430.png`
- Create: `docs/evidence/p3-t001-alt001/desktop-home-1440.png`
- Test: `dev/botiga-poc/tests/catalog_test.py`

**Interfaces:**
- Consumes: loopback runtime and stable fixture product/category URLs.
- Produces: deterministic browser assertions and committed screenshots.

- [ ] **Step 1: Write browser tests and verify RED where UI gaps remain**

Use synchronous Python Playwright tests that, for each of 360, 390, and 430 px, navigate to home, Shop, a category, and the featured sale product and assert:

```python
page.goto(url, wait_until="networkidle")
assert page.locator("html").get_attribute("lang").startswith("ko")
assert page.evaluate("document.documentElement.scrollWidth <= window.innerWidth")
assert page.locator("a, button").filter(has_text=re.compile(r"장바구니|Cart|Add to cart", re.I)).count() == 0
assert page.locator('[data-product-sku="FPOC-001"]').is_visible()
assert page.locator("[data-sale-end]").is_visible()
```

Also capture `console.error`, `pageerror`, failed document requests, and HTTP 5xx as failures.

- [ ] **Step 2: Test the two product interactions**

Click the Kakao CTA and assert its Korean status contains current SKU `FPOC-001` and current URL. Grant clipboard permission, click copy, read the clipboard, and assert it equals `page.url()` after URL normalization. Verify the countdown text changes or remains a valid positive time during the observation window.

- [ ] **Step 3: Test responsive ergonomics**

Measure visible controls to require width and height of at least 44 px, verify bottom navigation does not intersect the final content element, and confirm product names clamp to at most two rendered lines. Repeat core checks at 1440 px to catch uncontrolled mobile-only styling.

- [ ] **Step 4: Capture screenshots from the passing runtime**

Use `animations: 'disabled'` and the exact filenames above. The 390 px product screenshot must show regular/sale price, countdown, SKU, Kakao CTA, and copy control in one viewport or full-page evidence.

- [ ] **Step 5: Run the complete validation suite and commit**

Run `make -C dev/botiga-poc test` and `make -C dev/botiga-poc evidence`.

Expected: PHP/JS/source/runtime/HTTP/browser checks pass; screenshots are non-empty PNG files with expected viewport dimensions.

Commit: `test: add Botiga prototype browser evidence`

---

### Task 8: Decide the route, document evidence, and create the Draft PR

**Files:**
- Create: `docs/FASHION-THEME-3.1-ALT001-REPORT.md`
- Modify only if necessary for verified instructions: `dev/botiga-poc/Makefile`
- Verify unchanged: `.github/workflows/deploy-hostinger.yml`

**Interfaces:**
- Consumes: all executable results, screenshots, exact runtime versions, and observed customization surface.
- Produces: auditable binary decision, safe public commit, and Draft PR.

- [ ] **Step 1: Measure the result against the route gate**

Count child-theme PHP/CSS/JS files and any WooCommerce overrides. Record paid-plugin/builder count, exact page/viewport results, and remaining production gaps. Select `BOTIGA_ROUTE_PASS` only if every PASS condition in the task document is evidenced; otherwise select `BOTIGA_ROUTE_FAIL` and identify the first failed condition.

- [ ] **Step 2: Write the required report**

Include local run commands, exact Botiga/WooCommerce/WordPress versions and official source URLs, changed files, all three page outcomes, a 360/390/430 matrix, price/countdown/SKU/CTA/copy evidence, plugin list, screenshot links, tests with results, parent-theme non-modification proof, Hostinger/workflow non-contact proof, limitations, and the binary route decision.

- [ ] **Step 3: Run final verification from a clean test start**

Run the full suite with fresh output, inspect every committed screenshot, and run:

```bash
git diff --check origin/main...HEAD
git diff --exit-code origin/main -- .github/workflows/deploy-hostinger.yml
git status --short
git diff --name-only origin/main...HEAD
```

Expected: only ALT001-approved files, no workflow diff, and no uncommitted runtime artifacts.

- [ ] **Step 4: Run the public-safety check**

Scan every file in the branch diff for private-key headers, token prefixes, passwords/secrets with values, cookies, API keys, SSH host/IP/user/port data, full server paths, and local personal paths. Inspect staged binary inventory and Git history for this branch. Allowed generic stack names and version facts must not be mistaken for secrets.

- [ ] **Step 5: Obtain independent code review**

Use the requesting-code-review workflow against `origin/main...HEAD`. Resolve only findings within ALT001 scope, rerun affected tests, and do not declare independent acceptance on the user's behalf.

- [ ] **Step 6: Commit the report and final evidence**

Commit: `docs: report Botiga prototype route decision`

- [ ] **Step 7: Push and create the new Draft PR**

Push only `codex/fashion-theme-3.1-botiga-poc`, then create a Draft PR to `main` titled `[P3-T001-ALT001] Botiga Free KREAM-style prototype`. The PR body must summarize the route decision, runtime versions, validation, evidence paths, limitations, and the explicit statements that Hostinger was untouched, the deployment workflow was unchanged, and PR #5 was not modified.

- [ ] **Step 8: Verify stop state**

Read back the new PR to confirm Draft/Open/base `main`/correct head. Separately read PR #5 and confirm its state/head are unchanged. Do not merge either PR and do not start P3-T002.
