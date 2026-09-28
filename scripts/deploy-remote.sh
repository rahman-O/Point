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

"$PHP" artisan down --retry=15 || true
trap '"$PHP" artisan up || true' EXIT

# Ignored files (storage/app/public, .env, vendor) are untouched by reset --hard.
git reset --hard "$SHA"

# Hostinger disables proc_open, so composer can't run its artisan hooks; run them directly.
"$PHP" "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --no-scripts
"$PHP" artisan package:discover --ansi
"$PHP" artisan filament:upgrade

rm -rf public/build.new
mkdir -p public/build.new
tar xzf "$BUILD_ARCHIVE" -C public/build.new
rm -rf public/build
mv public/build.new public/build

# public_html is the Hostinger docroot; mirror public/ into it without deleting anything else.
rsync -a --exclude=/storage --exclude=/hot --exclude=/build public/ "$WEB_DIR/"
rsync -a --delete public/build/ "$WEB_DIR/build/"

if [ "$RUN_MIGRATIONS" = "true" ]; then
    "$PHP" artisan migrate --force
else
    echo "Skipping migrations (run the workflow manually with run_migrations=true to apply them)."
fi

"$PHP" artisan optimize:clear
"$PHP" artisan optimize

IMAGES_AFTER=$(count_images)
if [ "$IMAGES_AFTER" -lt "$IMAGES_BEFORE" ]; then
    echo "ERROR: production image count dropped from $IMAGES_BEFORE to $IMAGES_AFTER"
    exit 1
fi

rm -f "$BUILD_ARCHIVE"
echo "Deployed $SHA. Production images: $IMAGES_AFTER. Previous commit: $PREVIOUS_SHA"
