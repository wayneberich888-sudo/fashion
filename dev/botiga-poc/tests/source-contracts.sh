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

test -f wordpress/themes/fashion-child/style.css \
  || fail "fashion-child/style.css is missing"
grep -Eq '^Template:[[:space:]]+botiga$' wordpress/themes/fashion-child/style.css \
  || fail "fashion-child does not declare Botiga as its parent"

test ! -d wordpress/themes/botiga \
  || fail "Botiga parent source must not be committed"
test ! -d wordpress/plugins/woocommerce \
  || fail "WooCommerce package must not be committed"
test -z "$(git ls-files dev/botiga-poc/.runtime)" \
  || fail "runtime secrets or data are tracked"

git diff --exit-code origin/main -- .github/workflows/deploy-hostinger.yml >/dev/null \
  || fail "deployment workflow differs from origin/main"

if rg -n --hidden \
  -g '!tests/source-contracts.sh' \
  -g '!.runtime/**' \
  'BEGIN ([A-Z ]+ )?PRIVATE KEY|github_pat_|ghp_[A-Za-z0-9]|/Users/[A-Za-z0-9._-]+/' \
  dev/botiga-poc wordpress/themes/fashion-child 2>/dev/null; then
  fail "potential credential or local personal path found"
fi

grep -Rqs 'get_date_on_sale_to' wordpress/themes/fashion-child \
  || fail "sale countdown is not sourced from WooCommerce Sale End"

if grep -Rqs '_fashion_sale_end' wordpress/themes/fashion-child; then
  fail "parallel sale-end metadata is forbidden"
fi

printf 'SOURCE_CONTRACTS_PASS\n'
