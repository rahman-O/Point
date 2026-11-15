#!/usr/bin/env bash
# Runs ON the Hostinger server (piped over SSH): bash -s -- <commit-sha> <run-migrations:true|false>
# Never touches storage/app/public (production uploads), .env, or anything outside APP_DIR.
set -euo pipefail

SHA="${1:?commit sha required}"
RUN_MIGRATIONS="${2:-false}"

APP_DIR="$HOME/domains/point-iraq.org"
WEB_DIR="$APP_DIR/public_html"
PHP=/opt/alt/php82/usr/bin/php
COMPOSER="$HOME/bin/composer"
BUILD_ARCHIVE="$HOME/.deploy/point-iraq.org/build-$SHA.tar.gz"

cd "$APP_DIR"
[ -f .env ] || { echo "ERROR: $APP_DIR/.env missing"; exit 1; }
[ -f "$BUILD_ARCHIVE" ] || { echo "ERROR: build archive $BUILD_ARCHIVE missing"; exit 1; }

count_images() { find storage/app/public -type f | wc -l; }
IMAGES_BEFORE=$(count_images)
PREVIOUS_SHA=$(git rev-parse HEAD)
echo "Deploying $SHA (previous: $PREVIOUS_SHA). Production images: $IMAGES_BEFORE"

git fetch --prune origin new-design
git cat-file -e "$SHA^{commit}"

# Refuse, before any downtime, a lock file that cannot be installed on this server's PHP
# (e.g. a package needing a newer PHP). Otherwise composer would fail after the code had
# already been switched, leaving new code on old vendor.
PRECHECK=$(mktemp -d)
git show "$SHA:composer.json" > "$PRECHECK/composer.json"
git show "$SHA:composer.lock" > "$PRECHECK/composer.lock"
if ! (cd "$PRECHECK" && "$PHP" "$COMPOSER" check-platform-reqs --no-dev --lock --no-interaction); then
    rm -rf "$PRECHECK"
    echo "ERROR: composer.lock of $SHA cannot be installed with $("$PHP" -r 'echo PHP_VERSION;') on this server; nothing was changed."
    exit 1
fi
rm -rf "$PRECHECK"

# From here on, any failure puts the previous release back (code, vendor, build) so the
# site never stays live on a half-applied deploy. Migrations are never rolled back
# automatically; the message says so when they had already run.
SWITCHED=false
MIGRATED=false
rollback() {
    local status=$?
    trap - ERR
    echo "ERROR: deploy of $SHA failed (exit $status)."
    if [ "$SWITCHED" = true ]; then
        echo "Restoring previous release $PREVIOUS_SHA..."
        git reset --hard "$PREVIOUS_SHA"
        "$PHP" "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --no-scripts || true
        "$PHP" artisan package:discover --ansi || true
        if [ -d public/build.previous ]; then
            rm -rf public/build && mv public/build.previous public/build
            rsync -a --delete public/build/ "$WEB_DIR/build/" || true
        fi
        rsync -a --exclude=/storage --exclude=/hot --exclude=/build public/ "$WEB_DIR/" || true
        "$PHP" artisan optimize:clear || true
        "$PHP" artisan optimize || true
        [ "$MIGRATED" = true ] && echo "WARNING: migrations of $SHA had already run and were NOT rolled back; review them manually."
        echo "Previous release $PREVIOUS_SHA restored."
    fi
    exit "$status"
}
trap rollback ERR

"$PHP" artisan down --retry=15 || true
trap '"$PHP" artisan up || true' EXIT

# Ignored files (storage/app/public, .env, vendor) are untouched by reset --hard.
git reset --hard "$SHA"
SWITCHED=true

# Hostinger disables proc_open, so composer can't run its artisan hooks; run them directly.
"$PHP" "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --no-scripts
"$PHP" artisan package:discover --ansi
"$PHP" artisan filament:upgrade

rm -rf public/build.new public/build.previous
mkdir -p public/build.new
tar xzf "$BUILD_ARCHIVE" -C public/build.new
[ -d public/build ] && mv public/build public/build.previous
mv public/build.new public/build

# public_html is the Hostinger docroot; mirror public/ into it without deleting anything else.
rsync -a --exclude=/storage --exclude=/hot --exclude=/build public/ "$WEB_DIR/"
rsync -a --delete public/build/ "$WEB_DIR/build/"

if [ "$RUN_MIGRATIONS" = "true" ]; then
    "$PHP" artisan migrate --force
    MIGRATED=true
else
    echo "Skipping migrations (run the workflow manually with run_migrations=true to apply them)."
fi

"$PHP" artisan optimize:clear
"$PHP" artisan optimize
"$PHP" artisan search:reindex

IMAGES_AFTER=$(count_images)
if [ "$IMAGES_AFTER" -lt "$IMAGES_BEFORE" ]; then
    echo "ERROR: production image count dropped from $IMAGES_BEFORE to $IMAGES_AFTER"
    exit 1
fi

trap - ERR
rm -rf public/build.previous
rm -f "$BUILD_ARCHIVE"
echo "Deployed $SHA. Production images: $IMAGES_AFTER. Previous commit: $PREVIOUS_SHA"
