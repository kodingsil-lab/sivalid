#!/usr/bin/env bash
set -Eeuo pipefail

REPO_URL="${REPO_URL:-https://github.com/kodingsil-lab/sivalid.git}"
BRANCH="${BRANCH:-main}"

HOME_DIR="${HOME_DIR:-$HOME}"
APP_DIR="${APP_DIR:-$HOME_DIR/sivalid-app}"
WEB_DIR="${WEB_DIR:-$HOME_DIR/domains/sivalid.disertasi.web.id/public_html}"
ENV_SOURCE="${ENV_SOURCE:-.env.production}"

PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

log() {
  printf '\n[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

fail() {
  printf '\nERROR: %s\n' "$*" >&2
  exit 1
}

command_exists() {
  command -v "$1" >/dev/null 2>&1
}

require_command() {
  command_exists "$1" || fail "Command '$1' tidak ditemukan di server."
}

ensure_repo() {
  if [ ! -d "$APP_DIR/.git" ]; then
    log "Clone repository ke $APP_DIR"
    git clone --branch "$BRANCH" "$REPO_URL" "$APP_DIR"
    return
  fi

  log "Update repository di $APP_DIR"
  git -C "$APP_DIR" fetch origin "$BRANCH"
  git -C "$APP_DIR" checkout "$BRANCH"
  git -C "$APP_DIR" pull --ff-only origin "$BRANCH"
}

ensure_env() {
  if [ -f "$APP_DIR/.env" ]; then
    log ".env sudah ada, tidak ditimpa."
    return
  fi

  if [ ! -f "$APP_DIR/$ENV_SOURCE" ]; then
    fail "$APP_DIR/$ENV_SOURCE tidak ditemukan. Upload file env production terlebih dahulu."
  fi

  log "Membuat .env dari $ENV_SOURCE"
  cp "$APP_DIR/$ENV_SOURCE" "$APP_DIR/.env"

  if grep -q "GANTI_DENGAN_PASSWORD_DATABASE_HOSTINGER" "$APP_DIR/.env"; then
    printf '\nPERLU DIEDIT: isi password database di %s/.env\n' "$APP_DIR"
    printf 'Jalankan: nano %s/.env\n\n' "$APP_DIR"
    fail "Password database production belum diisi."
  fi
}

install_dependencies() {
  log "Install dependency composer"
  cd "$APP_DIR"

  if command_exists "$COMPOSER_BIN"; then
    "$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction
    return
  fi

  if [ -f composer.phar ]; then
    "$PHP_BIN" composer.phar install --no-dev --optimize-autoloader --no-interaction
    return
  fi

  fail "Composer tidak ditemukan. Install composer atau upload vendor dari lokal."
}

sync_public() {
  [ -d "$APP_DIR/public" ] || fail "Folder $APP_DIR/public tidak ditemukan."
  mkdir -p "$WEB_DIR"

  log "Copy isi public/ ke $WEB_DIR"
  if command_exists rsync; then
    rsync -a --delete \
      --exclude='.user.ini' \
      "$APP_DIR/public/" "$WEB_DIR/"
  else
    cp -R "$APP_DIR/public/." "$WEB_DIR/"
  fi

  log "Patch public_html/index.php agar memakai app di luar public_html"
  "$PHP_BIN" <<'PHP'
<?php
$index = getenv('WEB_DIR') . '/index.php';
$app = getenv('APP_DIR') . '/app/Config/Paths.php';

$contents = file_get_contents($index);

if ($contents === false) {
    fwrite(STDERR, "Gagal membaca index.php\n");
    exit(1);
}

$contents = preg_replace(
    "/require\s+FCPATH\s*\.\s*'\.\.\/app\/Config\/Paths\.php';/",
    'require ' . var_export($app, true) . ';',
    $contents
);

if ($contents === null) {
    fwrite(STDERR, "Gagal patch index.php\n");
    exit(1);
}

file_put_contents($index, $contents);
PHP
}

run_migrations() {
  log "Jalankan migration"
  cd "$APP_DIR"
  "$PHP_BIN" spark migrate --all
}

clear_cache() {
  log "Clear cache"
  cd "$APP_DIR"
  "$PHP_BIN" spark cache:clear || true
}

fix_permissions() {
  log "Rapikan permission writable"
  mkdir -p "$APP_DIR/writable/cache" "$APP_DIR/writable/logs" "$APP_DIR/writable/session" "$APP_DIR/writable/uploads"
  chmod -R 775 "$APP_DIR/writable" || true
}

main() {
  require_command git
  require_command "$PHP_BIN"

  export APP_DIR WEB_DIR

  log "Deploy SIVALID dimulai"
  log "Repo    : $REPO_URL ($BRANCH)"
  log "App dir : $APP_DIR"
  log "Web dir : $WEB_DIR"

  ensure_repo
  ensure_env
  install_dependencies
  sync_public
  fix_permissions
  run_migrations
  clear_cache

  log "Deploy selesai."
  printf '\nBuka: https://sivalid.disertasi.web.id/login\n'
  printf 'Jika muncul error database, edit password di: %s/.env\n\n' "$APP_DIR"
}

main "$@"
