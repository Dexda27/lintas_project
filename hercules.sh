#!/usr/bin/env bash
# ==========================================================
#  HERCULES — one-punch setup for your Laravel project ⚔️🏛️
#  (Interactive • Multilingual • Smart-Skip • Dev Output Live)
#  samaelzco x Alicia
# ==========================================================
set -Eeuo pipefail

# ---------- styling ----------
C_RESET="\033[0m"; C_BOLD="\033[1m"; C_DIM="\033[2m"
C_CYAN="\033[36m"; C_GREEN="\033[32m"; C_RED="\033[31m"; C_YELLOW="\033[33m"; C_MAGENTA="\033[35m"; C_BLUE="\033[34m"; C_GRAY="\033[90m"

line() { printf "${C_GRAY}%s${C_RESET}\n" "──────────────────────────────────────────────────────────────────────────────"; }
box()  { local t="${1:-}"; local s="${2:-}"; line; printf "${C_CYAN}${C_BOLD}🏛️  %s${C_RESET}\n" "$t"; [ -n "$s" ] && printf "${C_GRAY}%s${C_RESET}\n" "$s"; line; }
ok()   { printf "${C_GREEN}${C_BOLD}✓ %s${C_RESET}\n" "$*"; }
skip() { printf "${C_GRAY}${C_BOLD}↷ skipped${C_RESET}  %s\n" "$*"; }
warn() { printf "${C_YELLOW}⚠ %s${C_RESET}\n" "$*"; }
err()  { printf "${C_RED}${C_BOLD}✘ %s${C_RESET}\n" "$*" >&2; }

run_silent() { # run_silent "label" cmd...
  local label="$1"; shift
  printf "${C_BLUE}${C_BOLD}▸ %s...${C_RESET} " "$label"
  if "$@" >> hercules.log 2>&1; then
    printf "${C_GREEN}${C_BOLD}done${C_RESET}\n"; return 0
  else
    printf "${C_RED}${C_BOLD}failed${C_RESET}\n"; warn "See hercules.log for details."; return 1
  fi
}

need() { command -v "$1" >/dev/null 2>&1 || { err "Command '$1' not found. Please install it first."; exit 1; }; }

# GNU/BSD sed -i compat
sedi() {
  if sed --version >/dev/null 2>&1; then sed -i "$@"
  else local file="${@: -1}"; local args=("${@:1:$#-1}"); sed -i '' "${args[@]}" "$file"; fi
}
trim() { awk '{$1=$1;print}'; }

ensure_env_line() { # ensure_env_line KEY DEFAULT
  local key="$1" default="$2"
  if grep -Eq "^[#;[:space:]]*${key}=" .env; then
    sedi "s/^[#;[:space:]]*${key}=/${key}=/" .env
  else
    printf "%s=%s\n" "$key" "$default" >> .env
  fi
}
set_env() { # set_env KEY VALUE (ke .env)
  local key="$1" val="$2"
  if grep -Eq "^[#;[:space:]]*${key}=" .env; then
    sedi "s|^[#;[:space:]]*${key}=.*|${key}=${val}|" .env
  else
    printf "%s=%s\n" "$key" "$val" >> .env
  fi
}

# slugify APP_NAME → mysql-safe db (snake_case, <=63)
to_db_name() {
  local s; s="$(printf "%s" "$1" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9]/_/g; s/_\+/_/g; s/^_//; s/_$//')"
  [ -z "$s" ] && s="app"
  printf "%s" "$s" | grep -qE '^[0-9]' && s="app_${s}"
  printf "%.63s" "$s"
}

# baca APP_URL dari .env → fallback http://127.0.0.1:8000, normalisasi jika tanpa protokol
get_app_url() {
  local url
  url="$(grep -E '^APP_URL=' .env 2>/dev/null | head -n1 | cut -d= -f2- | sed -e 's/^"//' -e "s/'$//" -e 's/"$//')"
  if [ -z "$url" ]; then url="http://127.0.0.1:8000"; fi
  case "$url" in http://*|https://*) ;; *) url="http://$url";; esac
  printf "%s" "$url"
}

cleanup() { echo; err "Aborted. Check hercules.log for details."; }
trap cleanup ERR

: > hercules.log

# ---------- Step 1: choose language ----------
LANG_CHOICE="id"
box "HERCULES — Setup Wizard" "Select language / Pilih bahasa"
echo "  [0] Indonesia (default)"
echo "  [1] English"
printf "\n"
read -r -p "Choice [0/1]: " _choice
case "${_choice:-0}" in 1) LANG_CHOICE="en";; *) LANG_CHOICE="id";; esac
printf "\n"

# messages
if [ "$LANG_CHOICE" = "en" ]; then
  MSG_WELCOME="Welcome to HERCULES — may your setup be blessed by the gods. 🧿"
  MSG_NEED_ROOT="Run this from your Laravel project root (artisan & composer.json required)."
  MSG_APP_NAME_PROMPT="Enter your APP_NAME (e.g., Gudangku): "
  MSG_APP_NAME_EMPTY="APP_NAME cannot be empty."
  MSG_APP_SET="APP_NAME set to"
  MSG_DB_FROM_APP="DB_DATABASE derived from APP_NAME"
  MSG_ENV_STAGE="Step 3 · Create .env from .env.example"
  MSG_ENV_EXISTS="Existing .env detected — keeping it"
  MSG_ENV_CREATE="Creating .env from .env.example"
  MSG_ENV_NOEX="Missing .env.example — cannot continue."
  MSG_ENV_FIX="Ensure DB keys, set mysql/database/password"
  MSG_DEPS_STAGE="Step 5 · Dependencies (npm & composer)"
  MSG_NPM_SKIP="Skipping npm (npm or package.json missing)."
  MSG_RITES_STAGE="Step 6–7 · Artisan rituals"
  MSG_STORAGE_LINK="Link storage"
  MSG_KEYGEN="Generate app key"
  MSG_KEY_EXISTS="APP_KEY already present — skipping"
  MSG_MIGRATE="Migrate database"
  MSG_MIGRATE_FRESH="Recreate & seed"
  MSG_DEV_STAGE="Step 8 · Dev server"
  MSG_DEV_FG="Streaming dev output (press Ctrl+C to stop)"
  MSG_DEV_NOTFOUND="composer run dev not found — trying npm run dev"
  MSG_VISIT="Open your app at"
  MSG_DONE="All done! Your project is battle-ready. Go forth, hero."
else
  MSG_WELCOME="Selamat datang di HERCULES — semoga setup-mu diberkahi para dewa. 🧿"
  MSG_NEED_ROOT="Jalankan dari root proyek Laravel (butuh artisan & composer.json)."
  MSG_APP_NAME_PROMPT="Masukkan APP_NAME (contoh: Gudangku): "
  MSG_APP_NAME_EMPTY="APP_NAME tidak boleh kosong."
  MSG_APP_SET="APP_NAME diset menjadi"
  MSG_DB_FROM_APP="DB_DATABASE dibuat dari APP_NAME"
  MSG_ENV_STAGE="Langkah 3 · Buat .env dari .env.example"
  MSG_ENV_EXISTS=".env sudah ada — dibiarkan"
  MSG_ENV_CREATE="Membuat .env dari .env.example"
  MSG_ENV_NOEX="Tidak ada .env.example — tidak dapat melanjutkan."
  MSG_ENV_FIX="Pastikan kunci DB & set mysql/database/password"
  MSG_DEPS_STAGE="Langkah 5 · Dependensi (npm & composer)"
  MSG_NPM_SKIP="Lewati npm (npm atau package.json tidak ada)."
  MSG_RITES_STAGE="Langkah 6–7 · Ritus artisan"
  MSG_STORAGE_LINK="Buat storage link"
  MSG_KEYGEN="Generate app key"
  MSG_KEY_EXISTS="APP_KEY sudah ada — dilewati"
  MSG_MIGRATE="Migrasi database"
  MSG_MIGRATE_FRESH="Bersihkan & seed"
  MSG_DEV_STAGE="Langkah 8 · Dev server"
  MSG_DEV_FG="Menayangkan output dev (tekan Ctrl+C untuk berhenti)"
  MSG_DEV_NOTFOUND="composer run dev tidak ada — coba npm run dev"
  MSG_VISIT="Buka aplikasimu di"
  MSG_DONE="Selesai! Proyek siap tempur. Maju, pahlawan."
fi

box "HERCULES" "$MSG_WELCOME"
printf "${C_DIM}%s${C_RESET}\n\n" "$MSG_NEED_ROOT"

# preflight
need php; need composer; need sed; need grep; need cp
command -v npm >/dev/null 2>&1 || true
[ -f artisan ] || { err "Missing 'artisan'."; exit 1; }
[ -f composer.json ] || { err "Missing 'composer.json'."; exit 1; }

# ---------- Step 2: app name ----------
box "APP_NAME"
APP_NAME_INPUT=""
while [ -z "${APP_NAME_INPUT}" ]; do
  read -r -p "$MSG_APP_NAME_PROMPT" APP_NAME_INPUT
  APP_NAME_INPUT="$(printf "%s" "$APP_NAME_INPUT" | sed 's/^[[:space:]]\+//; s/[[:space:]]\+$//')"
  [ -z "$APP_NAME_INPUT" ] && warn "$MSG_APP_NAME_EMPTY"
done
APP_NAME_VAL="\"${APP_NAME_INPUT}\""
ok "$MSG_APP_SET: ${APP_NAME_INPUT}"

# ---------- Step 3: .env from example ----------
box "$MSG_ENV_STAGE"
if [ -f .env ]; then
  skip "$MSG_ENV_EXISTS"
else
  [ -f .env.example ] || { err "$MSG_ENV_NOEX"; exit 1; }
  run_silent "$MSG_ENV_CREATE" cp .env.example .env
fi

# ---------- Step 4: ensure DB_* + set values ----------
ok "$MSG_ENV_FIX"
ensure_env_line "DB_CONNECTION" "mysql"
ensure_env_line "DB_HOST" "127.0.0.1"
ensure_env_line "DB_PORT" "3306"
ensure_env_line "DB_DATABASE" "laravel"
ensure_env_line "DB_USERNAME" "root"
ensure_env_line "DB_PASSWORD" "root"

set_env "DB_CONNECTION" "mysql"
DB_NAME="$(to_db_name "$APP_NAME_INPUT")"
set_env "DB_DATABASE" "$DB_NAME"; ok "$MSG_DB_FROM_APP: ${DB_NAME}"
set_env "DB_PASSWORD" "root"
set_env "APP_NAME" "${APP_NAME_VAL}"

# ---------- Step 5: dependencies (sequential + smart) ----------
box "$MSG_DEPS_STAGE"

DID_NPM_INSTALL=false
if command -v npm >/dev/null 2>&1 && [ -f package.json ]; then
  if [ -d node_modules ]; then
    skip "node_modules exists — npm install"
  else
    if [ -f package-lock.json ]; then
      run_silent "npm ci" npm ci --no-audit --fund=false && DID_NPM_INSTALL=true
    else
      run_silent "npm install" npm install --no-audit --fund=false && DID_NPM_INSTALL=true
    fi
  fi
  if [ "$DID_NPM_INSTALL" = true ]; then
    run_silent "npm update" npm update --no-audit --fund=false
  else
    skip "npm update (skipped because install/ci not run now)"
  fi
else
  warn "$MSG_NPM_SKIP"
fi

DID_COMPOSER_INSTALL=false
if [ -d vendor ] && [ -f vendor/autoload.php ]; then
  skip "vendor exists — composer install"
else
  run_silent "composer install" composer install --no-interaction --no-progress --prefer-dist && DID_COMPOSER_INSTALL=true
fi
if [ "$DID_COMPOSER_INSTALL" = true ]; then
  run_silent "composer update" composer update --no-interaction --no-progress
else
  skip "composer update (skipped because install not run now)"
fi

# ---------- Step 6–7: artisan ----------
box "$MSG_RITES_STAGE"

# storage:link
if [ -L "public/storage" ] || [ -e "public/storage" ]; then
  skip "$MSG_STORAGE_LINK"
else
  run_silent "$MSG_STORAGE_LINK" php artisan storage:link || warn "storage:link may already exist"
fi

# key:generate (skip jika sudah ada)
if grep -Eq '^APP_KEY=.+$' .env && [ -n "$(grep -E '^APP_KEY=.+$' .env | cut -d= -f2-)" ]; then
  skip "$MSG_KEY_EXISTS"
else
  run_silent "$MSG_KEYGEN" php artisan key:generate --force
fi

# migrate, lalu migrate:fresh --seed
run_silent "$MSG_MIGRATE" php artisan migrate --force --no-interaction || warn "migrate failed (check DB)"
run_silent "$MSG_MIGRATE_FRESH" php artisan migrate:fresh --seed --force --no-interaction || warn "migrate:fresh failed"

# ---------- Step 8: dev server (FOREGROUND, show links) ----------
box "$MSG_DEV_STAGE"
APP_URL="$(get_app_url)"
printf "${C_GREEN}${C_BOLD}→ ${MSG_VISIT}: ${APP_URL}${C_RESET}\n"
printf "${C_BLUE}${C_BOLD}▸ %s${C_RESET}\n" "$MSG_DEV_FG"
echo    "  (Ctrl+C untuk menghentikan / to stop)"

# Penting: jalankan di FOREGROUND agar seluruh output (Vite, Laravel) tampil.
# Jika ada script "dev" di composer.json, pakai itu; jika tidak, fallback ke npm run dev.
if grep -q '"dev"' composer.json 2>/dev/null; then
  exec bash -lc "composer run dev"
elif [ -f package.json ] && grep -q '"dev"' package.json 2>/dev/null; then
  exec bash -lc "npm run dev"
else
  warn "$MSG_DEV_NOTFOUND"
  echo "Tidak ada script 'dev'. Jalankan manual: npm run dev"
fi
