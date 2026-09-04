#!/usr/bin/env sh
set -eu

umask 077
script_dir="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd -P)"
runtime_dir="$(dirname "$script_dir")/.runtime"

mkdir -p "$runtime_dir"
chmod 700 "$runtime_dir"

create_secret() {
  target="$1"
  if [ ! -s "$target" ]; then
    temporary="$(mktemp "$runtime_dir/.secret.XXXXXX")"
    openssl rand -hex 24 >"$temporary"
    chmod 600 "$temporary"
    mv "$temporary" "$target"
  fi
}

create_secret "$runtime_dir/db_password"
create_secret "$runtime_dir/db_root_password"
create_secret "$runtime_dir/wp_admin_password"

printf 'Local runtime files are ready.\n'
