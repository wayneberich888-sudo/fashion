#!/usr/bin/env sh
set -eu

cd /var/www/html

attempt=0
until [ -f wp-config.php ] && wp db check --allow-root >/dev/null 2>&1; do
  attempt=$((attempt + 1))
  if [ "$attempt" -ge 60 ]; then
    printf 'WordPress files or database did not become ready.\n' >&2
    exit 1
  fi
  sleep 2
done

if ! wp core is-installed --allow-root >/dev/null 2>&1; then
  wp core install \
    --allow-root \
    --url='http://127.0.0.1:8097' \
    --title='FASHION POC' \
    --admin_user='poc_admin' \
    --admin_password="$(cat /run/secrets/wp_admin_password)" \
    --admin_email='poc@example.invalid' \
    --skip-email
fi

install_plugin_version() {
  slug="$1"
  version="$2"
  package_url="$3"
  installed_version="$(wp plugin get "$slug" --field=version --allow-root 2>/dev/null || true)"
  if [ "$installed_version" != "$version" ]; then
    wp plugin install "$package_url" --force --allow-root
  fi
}

install_theme_version() {
  slug="$1"
  version="$2"
  package_url="$3"
  installed_version="$(wp theme get "$slug" --field=version --allow-root 2>/dev/null || true)"
  if [ "$installed_version" != "$version" ]; then
    wp theme install "$package_url" --force --allow-root
  fi
}

install_plugin_version \
  woocommerce \
  11.1.0 \
  'https://downloads.wordpress.org/plugin/woocommerce.11.1.0.zip'

install_theme_version \
  botiga \
  2.4.8 \
  'https://downloads.wordpress.org/theme/botiga.2.4.8.zip'

if ! wp plugin is-active woocommerce --allow-root; then
  wp plugin activate woocommerce --allow-root
fi

if [ "$(wp option get stylesheet --allow-root)" != 'fashion-child' ]; then
  wp theme activate fashion-child --allow-root
fi

if [ "$(wp option get WPLANG --allow-root 2>/dev/null || true)" != 'ko_KR' ]; then
  if wp language core install ko_KR --allow-root >/dev/null 2>&1; then
    wp site switch-language ko_KR --allow-root >/dev/null
  fi
fi

wp option update timezone_string 'Asia/Seoul' --allow-root >/dev/null
wp option update blogdescription '한국 모바일 패션 카탈로그 프로토타입' --allow-root >/dev/null
wp rewrite structure '/%postname%/' --allow-root >/dev/null

if [ -f /workspace/seed/catalog.php ]; then
  wp eval-file /workspace/seed/catalog.php --allow-root
fi

wp rewrite flush --allow-root >/dev/null

printf 'WORDPRESS_VERSION=%s\n' "$(wp core version --allow-root)"
printf 'WOOCOMMERCE_VERSION=%s\n' "$(wp plugin get woocommerce --field=version --allow-root)"
printf 'BOTIGA_VERSION=%s\n' "$(wp theme get botiga --field=version --allow-root)"
printf 'ACTIVE_STYLESHEET=%s\n' "$(wp option get stylesheet --allow-root)"
