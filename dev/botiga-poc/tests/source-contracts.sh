#!/usr/bin/env bash
set -euo pipefail

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd -P)"
cd "$repo_root"

fail() {
  printf 'SOURCE_CONTRACTS_FAIL: %s\n' "$1" >&2
  exit 1
}

test "$(git branch --show-current)" = "codex/fashion-theme-3.1-botiga-poc" \
  || fail "unexpected branch"

test -f dev/botiga-poc/docker-compose.yml \
  || fail "docker-compose.yml is missing"
grep -Fq '127.0.0.1:8097:80' dev/botiga-poc/docker-compose.yml \
  || fail "WordPress is not bound to the expected loopback port"
grep -Eq 'wordpress:7\.1.*php8\.3-apache' dev/botiga-poc/docker-compose.yml \
  || fail "WordPress 7.1 with PHP 8.3 is not pinned"
grep -Fq 'internal: true' dev/botiga-poc/docker-compose.yml \
  || fail "private database network is not declared"
grep -Fq '../../wordpress/themes/fashion-child:/var/www/html/wp-content/themes/fashion-child:ro' dev/botiga-poc/docker-compose.yml \
  || fail "child theme is not mounted read-only"
grep -Fq 'user: "33:33"' dev/botiga-poc/docker-compose.yml \
  || fail "WP-CLI user does not match the WordPress volume owner"
test -f dev/botiga-poc/bin/prepare-runtime.sh \
  || fail "runtime preparation script is missing"
grep -Fq 'umask 077' dev/botiga-poc/bin/prepare-runtime.sh \
  || fail "runtime files are not created with a restrictive umask"
test -f dev/botiga-poc/bin/init-wordpress.sh \
  || fail "WordPress initialization script is missing"
grep -Fq 'woocommerce.11.1.0.zip' dev/botiga-poc/bin/init-wordpress.sh \
  || fail "WooCommerce 11.1.0 official package is not pinned"
grep -Fq 'botiga.2.4.8.zip' dev/botiga-poc/bin/init-wordpress.sh \
  || fail "Botiga 2.4.8 official package is not pinned"
if grep -Fq -- '--hard' dev/botiga-poc/bin/init-wordpress.sh; then
  fail "local initialization must not force Apache .htaccess writes"
fi
grep -Fq 'wp plugin is-active woocommerce' dev/botiga-poc/bin/init-wordpress.sh \
  || fail "WooCommerce activation is not idempotent"
grep -Fq 'wp option get stylesheet' dev/botiga-poc/bin/init-wordpress.sh \
  || fail "child-theme activation is not idempotent"
grep -Fq 'wp option get WPLANG' dev/botiga-poc/bin/init-wordpress.sh \
  || fail "locale activation is not idempotent"

test -f wordpress/themes/fashion-child/style.css \
  || fail "fashion-child/style.css is missing"
grep -Eq '^Template:[[:space:]]+botiga$' wordpress/themes/fashion-child/style.css \
  || fail "fashion-child does not declare Botiga as its parent"
test -f wordpress/themes/fashion-child/functions.php \
  || fail "fashion-child/functions.php is missing"
grep -Fq "add_action( 'after_setup_theme'" wordpress/themes/fashion-child/functions.php \
  || fail "child theme setup hook is missing"
grep -Fq "add_action( 'wp_enqueue_scripts'" wordpress/themes/fashion-child/functions.php \
  || fail "child theme asset hook is missing"

test ! -d wordpress/themes/botiga \
  || fail "Botiga parent source must not be committed"
test ! -d wordpress/plugins/woocommerce \
  || fail "WooCommerce package must not be committed"
test -z "$(git ls-files dev/botiga-poc/.runtime)" \
  || fail "runtime secrets or data are tracked"

git diff --exit-code origin/main -- .github/workflows/deploy-hostinger.yml >/dev/null \
  || fail "deployment workflow differs from origin/main"

if rg -n --hidden \
  -g '!**/source-contracts.sh' \
  -g '!.runtime/**' \
  'BEGIN ([A-Z ]+ )?PRIVATE KEY|github_pat_|ghp_[A-Za-z0-9]|/Users/[A-Za-z0-9._-]+/' \
  dev/botiga-poc wordpress/themes/fashion-child 2>/dev/null; then
  fail "potential credential or local personal path found"
fi

if test -f wordpress/themes/fashion-child/inc/product-detail.php; then
  grep -Rqs 'get_date_on_sale_to' wordpress/themes/fashion-child/inc/product-detail.php \
    || fail "sale countdown is not sourced from WooCommerce Sale End"
  if grep -Rqs '_fashion_sale_end' wordpress/themes/fashion-child; then
    fail "parallel sale-end metadata is forbidden"
  fi
fi

printf 'SOURCE_CONTRACTS_PASS\n'
