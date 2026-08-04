# UniMart — Unified POS & E-Commerce Platform

A full-stack Laravel MVP where a single database drives three synchronized
storefronts: a public **online shop**, an **admin dashboard**, and a
tablet-optimized **POS terminal** — all updating each other in real time,
with zero page refreshes.

> Sell an item at the register, and the website's stock count changes
> instantly. Edit a price in the admin panel, and the cashier's screen
> updates mid-sale. That's the whole point of this project.

<!--
  Add 2-3 screenshots or a short GIF here before publishing — e.g. the
  Storefront cart, the Admin table with a live stock update, and the POS
  register. A GIF showing a POS sale updating the Admin tab live is the
  single most convincing thing you can put at the top of this README.
-->

## Why this exists

This started as a self-directed exercise in building the kind of system a
retail business actually needs: one inventory, multiple sales channels,
no overselling, no manual reconciliation. It deliberately avoids the
"todo app with a database" trap — the interesting part is the concurrency
and real-time sync, not the CRUD.

## Features

- **Live inventory sync** — a sale anywhere (online, POS, or an admin
  edit) broadcasts instantly to every other open screen via WebSockets.
- **Overselling is structurally impossible** — every stock-changing action
  funnels through one row-locked database transaction
  (`Product::sell()`), regardless of which channel triggered it.
- **Admin Dashboard** — searchable, inline-editable product table with
  soft-delete/restore.
- **Storefront** — product grid, session-backed cart, checkout, and a
  queued receipt email.
- **POS Terminal** — category browsing, a numeric keypad for quantities, a
  barcode-scanner input parser, a "Quick Product Lookup" modal, and a live
  toast + sound when an online order comes in mid-shift.
- **Daily Sales Summary** — combined online + POS revenue, updating live
  as sales happen on either channel.
- **Role-based auth** — admin vs. cashier accounts; the storefront stays
  fully public.

## How the sync actually works

Product::sell() / restock() / admin edit
│
▼
ProductObserver detects the change
│
▼
event(new StockUpdated($product))
│
▼
Broadcast on Reverb (WebSocket server)
│
┌──────────┼──────────┐
▼ ▼ ▼
Storefront Admin POS
(Livewire) (Livewire) (Livewire)


The key design decision: **every** stock-changing code path — an online
checkout, a POS sale, or a plain admin edit — goes through the same
`Product::sell()` / `Product::restock()` methods, which wrap a
`lockForUpdate()` row lock inside a database transaction. That's what
makes the "no overselling" guarantee real, not just a happy-path demo: if
an online sale and a POS sale hit the last unit of the same product at the
same instant, one of them will cleanly fail with an
`InsufficientStockException` instead of both succeeding.

Because a Model Observer — not the controllers — is what fires the
broadcast event, any future code path that touches stock gets the live-sync
behavior automatically, with no risk of a developer adding a new "sell"
path and forgetting to wire up the event.

## Tech stack

| Layer | Choice | Why |
|---|---|---|
| Backend | Laravel | Eloquent, migrations, queues, broadcasting all built in |
| Real-time UI | Livewire | Server-driven reactivity without a separate JS framework |
| WebSockets | Laravel Reverb | First-party, self-hosted, no third-party dependency for the core demo |
| Ephemeral UI state | Alpine.js | Modal open/close, numeric keypad buffer — ships with Livewire |
| Legacy input parsing | jQuery | Barcode scanner keystroke buffering, used narrowly and deliberately |
| Styling | Tailwind CSS | Utility-first, fast to iterate |
| Database | SQLite (dev) / MySQL-ready | Zero-config for local development |

## Getting started

```bash
git clone https://github.com/abirmehmed/Uni-Mart
cd unimart
composer install
npm install
cp .env.example .env
php artisan key:generate
composer require laravel/reverb
php artisan reverb:install
```

Add to `.env` (or confirm `reverb:install` already added these):
```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

```bash
php artisan migrate
php artisan db:seed
```

### Run it — 4 terminals, all left running:

```bash
php artisan serve
```
```bash
php artisan reverb:start
```
```bash
npm run dev
```
```bash
php artisan queue:work
```

### Pages

| Page | URL |
|---|---|
| Storefront | `/` |
| Admin Dashboard | `/admin/products` (admin only) |
| POS Terminal | `/pos` (admin or cashier) |
| Login | `/login` |

### Test accounts (from the seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@unimart.test | password |
| Cashier | cashier@unimart.test | password |

> These are seed credentials for local evaluation only — rotate them
> before deploying anywhere public.

## Notable design decisions

- **Money stored as integer cents** (`price_cents`), not a decimal column —
  avoids float-rounding bugs some drivers still hit with `decimal` casts.
  A `price` accessor formats it back to `"12.99"` everywhere above the
  model layer.
- **`order_product.product_id` restricts on delete**, combined with
  soft-deletes on `products` — a deactivated product's historical sales
  are permanently safe even against an accidental hard delete.
- **Cashier "Current Sale" cart is in-memory, not session-persisted** —
  a POS transaction is meant to be short-lived; a reload starting fresh
  matches how a real register behaves.

## Deliberately out of scope (for now)

- Real payment gateway (orders are marked `paid` immediately — Stripe
  test mode would be the natural next step)
- Mock shipping-rate API
- Camera-based barcode scanning (`BarcodeDetector` API — the SKU-lookup
  backend already exists, this would just be a new input source)
- Self-service account registration (staff accounts are provisioned by
  whoever runs the business, not signed up)

## License

MIT — do whatever you want with it.
