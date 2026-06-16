# Footer logos — Organizers (المنظمين) & Partners (الشركاء)

Drop your real logo image files into these folders, then run the seeder.

## Where to put files

- Organizer logos -> `database/seeders/assets/organizers/`
- Partner logos -> `database/seeders/assets/partners/`

## Rules

- Supported types: **png, jpg, jpeg, svg, webp, gif**.
- The **file name becomes the display name**. So name files like:
  - `POINT Foundation.png`
  - `Tech Community Hub.svg`
  - Dashes/underscores are turned into spaces: `point_foundation.png` -> "point foundation".
- Transparent **PNG or SVG** look best in the footer (logos sit on a colored background).

## Run it

```bash
php artisan db:seed --class=DemoContentSeeder
```

or a full refresh:

```bash
php artisan migrate:fresh --seed
```

## Notes

- If a folder has **at least one** image, ONLY your files are used (the generated
  placeholder badges are skipped for that section).
- If a folder is **empty**, the seeder falls back to generated initials badges.
- Re-running the seeder overwrites the same stored files (no duplicates).
- These source files are copied into `storage/app/public` and served via
  `/api/images/{filename}`.
