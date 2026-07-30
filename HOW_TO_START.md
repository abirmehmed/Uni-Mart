# UniMart — How to Start Everything

Every time you sit down to work on this project, open 4 separate terminals,
cd into the project folder in each one, and run one command per terminal.
Leave all 4 running the whole time you're working.

```bash
cd ~/Desktop/unimart
```
(run this in all 4 terminals first)

---

### Terminal 1 — the web server
```bash
php artisan serve
```

### Terminal 2 — the real-time WebSocket server
```bash
php artisan reverb:start --debug
```

### Terminal 3 — the frontend build tool
```bash
npm run dev
```

### Terminal 4 — the background job processor
```bash
php artisan queue:work
```

---

## Once all 4 are running

Open your browser to:

- Storefront: http://127.0.0.1:8000/
- Admin Dashboard: http://127.0.0.1:8000/admin/products
- POS Terminal: http://127.0.0.1:8000/pos

## Shutting down

Ctrl+C in each of the 4 terminals, any order.

## If something looks broken after restarting

```bash
php artisan config:clear
php artisan optimize:clear
```
