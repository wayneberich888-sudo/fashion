#!/usr/bin/env sh
set -eu

poc_dir="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd -P)"
repo_root="$(CDPATH= cd -- "$poc_dir/../.." && pwd -P)"
compose="docker compose --project-directory $poc_dir -f $poc_dir/docker-compose.yml"
playwright_python="${PLAYWRIGHT_PYTHON:-$poc_dir/.runtime/playwright-venv/bin/python}"

cd "$repo_root"

if [ ! -x "$playwright_python" ]; then
  printf 'PLAYWRIGHT_RUNTIME_MISSING: create .runtime/playwright-venv and install playwright locally.\n' >&2
  exit 1
fi

bash dev/botiga-poc/tests/source-contracts.sh

$compose run --rm cli wp eval-file /workspace/tests/runtime-contract.php --allow-root
$compose run --rm cli sh -lc 'find /var/www/html/wp-content/themes/fashion-child -type f -name "*.php" -exec php -l {} \;'
$compose run --rm cli sh -lc 'find /var/www/html/wp-content/plugins/fashion-core -type f -name "*.php" -exec php -l {} \;'

node --check wordpress/themes/fashion-child/assets/js/catalog.js
PYTHONPYCACHEPREFIX="$poc_dir/.runtime/pycache" "$playwright_python" -m py_compile dev/botiga-poc/tests/catalog_test.py

curl -fsS -o /dev/null http://127.0.0.1:8097/
curl -fsS -o /dev/null http://127.0.0.1:8097/shop/
curl -fsS -o /dev/null http://127.0.0.1:8097/product-category/shoes/
curl -fsS -o /dev/null "http://127.0.0.1:8097/product/fpoc-001-%ed%81%b4%eb%9d%bc%ec%9a%b0%eb%93%9c-%eb%9f%ac%eb%84%88-%ec%8a%a4%ed%86%a4/"

if $compose logs --no-color wordpress 2>&1 | grep -Eiq 'PHP Fatal error|Uncaught Error'; then
  printf 'WORDPRESS_LOG_FAIL: PHP fatal found in WordPress service logs.\n' >&2
  exit 1
fi

PYTHONDONTWRITEBYTECODE=1 "$playwright_python" dev/botiga-poc/tests/catalog_test.py

printf 'FULL_VALIDATION_PASS\n'
