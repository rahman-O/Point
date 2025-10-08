# Development Commands

## Quick Start with Hot Reload

### Option 1: Single Command (Recommended)

```bash
npm run start
```

This will run both Vite dev server (with HMR) and Laravel serve concurrently.

### Option 2: Shell Script

```bash
./dev.sh
```

### Option 3: Individual Commands

If you need to run them separately:

```bash
# Terminal 1 - Vite dev server (hot reload)
npm run dev

# Terminal 2 - Laravel server
npm run serve
```

## What This Does

- **Hot Module Replacement (HMR)**: Your React components will update instantly without page refresh
- **Auto-reload**: Any changes to CSS, JS, or JSX files will be reflected immediately
- **No manual clearing**: No need to run `php artisan optimize:clear` during development

## Access Your Application

- Frontend: http://localhost:8000
- Vite Dev Server: http://localhost:5173

## Production Build

When ready to deploy:

```bash
npm run build
```

## Notes

- Make sure XAMPP's MySQL is running before starting
- The Laravel server runs on port 8000
- Vite dev server runs on port 5173
- Both servers must be running for hot reload to work
