# UniMart — Unified POS & E-Commerce Platform

A full-stack Laravel MVP where a single database drives three synchronized
storefronts: a public **online shop**, an **admin dashboard**, and a
tablet-optimized **POS terminal** — all updating each other in real time,
with zero page refreshes.

> Sell an item at the register, and the website's stock count changes
> instantly. Edit a price in the admin panel, and the cashier's screen
> updates mid-sale. That's the whole point of this project.

**Live demo:** https://uni-mart.onrender.com
(Free-tier host — the first request after inactivity can take ~50s to
wake the server. Test accounts below.)

<!--
  Add 2-3 screenshots or a short GIF here before publishing — e.g. the
  Storefront cart, the Admin table with a live stock update, the POS
  register, and the Reports calendar with the profit chart. A GIF showing
  a POS sale updating the Admin tab live is the single most convincing
  thing you can put at the top of this README.
-->

## Why this exists

This started as a self-directed exercise in building the kind of system a
retail business actually needs: one inventory, multiple sales channels,
no overselling, no manual reconciliation — plus the reporting a business
owner would actually ask for. It deliberately avoids the "todo app with a
database" trap — the interesting parts are the concurrency/real-time sync
and the profit math, not the CRUD.

## Features

- **Live inventory sync** — a sale anywhere (online, POS, or an admin
  edit) broadcasts instantly to every other open screen via WebSockets.
- **Overselling is structurally impossible** — every stock-changing action
  funnels through one row-locked database transaction
  (`Product::sell()`), regardless of which channel triggered it.
- **Per-user live availability** — the storefront and POS both show stock
  minus whatever's already in *that* customer's/cashier's current
  cart, without ever touching real inventory until the sale actually
  completes.
- **Admin Dashboard** — searchable, inline-editable product table with
  cost tracking and soft-delete/restore.
- **Sales Reports** — a calendar view of every day's revenue, profit,
  order count, and online/POS split; a revenue-vs-profit trend line for
  the month; and a top-sellers chart by units moved. Profit is computed
  from a dedicated per-product cost field, not guessed.
- **Storefront** — product grid with real photography, individual product
  pages with descriptions and "you might also like" recommendations
  (ranked by actual sales history, same-category fallback to random),
  session-backed cart, checkout, and a queued receipt email.
- **POS Terminal** — category browsing, product thumbnails, a
  calculator-style numeric keypad, a barcode-scanner input parser, a
  "Quick Product Lookup" modal, and a live toast + sound when an online
  order comes in mid-shift.
- **Daily Sales Summary** — combined online + POS revenue, updating live
  as sales happen on either channel.
- **Role-based auth** — admin vs. cashier accounts; the storefront stays
  fully public.
- **A real design system** — a custom "ledger/receipt" visual language
  (ink/ledger/amber/stamp palette, Barlow Semi Condensed + IBM Plex type,
  rotating stamp-style badges) applied consistently across all three
  surfaces, not just default framework styling.

## How the sync actually worksThe key design decision: **every** stock-changing code path — an online
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
| Charts | Chart.js | Reports trend line and top-products bar chart |
| Ephemeral UI state | Alpine.js | Modal open/close, numeric keypad buffer — ships with Livewire |
| Legacy input parsing | jQuery | Barcode scanner keystroke buffering, used narrowly and deliberately |
| Styling | Tailwind CSS | Utility-first, fast to iterate; custom design tokens on top |
| Database | SQLite (dev) / MySQL-ready | Zero-config for local development |
| Deployment | Docker + Caddy on Render | Single container: Caddy reverse-proxies to `serve`, `reverb:start`, and `queue:work` |

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

### Run it locally — 4 terminals, all left running:

```bash
php artisan reverb:start   # start this FIRST — model observers broadcast
                            # on stock changes, including during seeding
php artisan serve
npm run dev
php artisan queue:work
```

> **Why Reverb goes first:** creating/seeding a product fires a stock
> broadcast synchronously. If Reverb isn't already listening, that
> broadcast throws and can crash a fresh `migrate:fresh --seed`. Learned
> this one from a failed deploy — see Deployment notes below.

### Pages

| Page | URL |
|---|---|
| Storefront | `/` |
| Product detail | `/products/{id}` |
| Admin Dashboard | `/admin/products` (admin only) |
| Sales Reports | `/admin/reports` (admin only) |
| POS Terminal | `/pos` (admin or cashier) |
| Login | `/login` |

### Test accounts (from the seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@unimart.test | password |
| Cashier | cashier@unimart.test | password |

> These are seed credentials for local evaluation only — rotate them
> before deploying anywhere public.

### Demo data

`ProductSeeder` ships a realistic small-shop catalog (mugs, tote bags,
notebooks, groceries) with real photography and a per-product cost, so
Reports shows believable margins out of the box. To populate the Reports
calendar with historical activity for a demo:

```bash
php artisan db:seed --class=DemoOrderSeeder
```

This backfills randomized `paid` orders across the last 60 days. It's
intentionally **not** wired into `DatabaseSeeder`'s default `$this->call()`
list, since `migrate:fresh --seed` runs on every deploy and would keep
piling up fake orders otherwise — run it manually whenever fresh demo data
is wanted.

## Notable design decisions

- **Money stored as integer cents** (`price_cents`, `cost_cents`), not a
  decimal column — avoids float-rounding bugs some drivers still hit with
  `decimal` casts. Accessors format them back to `"12.99"` everywhere
  above the model layer.
- **Cost is tracked separately from price** specifically to make profit
  (not just revenue) a first-class number in Reports. Profit is computed
  against each product's *current* cost, not a snapshot taken at sale
  time — only `price_at_time_cents` is snapshotted on `order_product`. A
  `cost_at_time_cents` column would be the natural next step for
  fully-accurate historical profit if this went to production.
- **`order_product.product_id` restricts on delete**, combined with
  soft-deletes on `products` — a deactivated product's historical sales
  are permanently safe even against an accidental hard delete.
- **Cashier "Current Sale" cart is in-memory, not session-persisted** —
  a POS transaction is meant to be short-lived; a reload starting fresh
  matches how a real register behaves.
- **Product photography is hotlinked from Unsplash** for demo purposes —
  fine for a portfolio project, but a production version would pull
  product images from proper managed storage (S3/Cloudinary) rather than
  a third party.

## Deployment

Runs as a single Docker container on Render: Caddy reverse-proxies to
`php artisan serve`, with `reverb:start` and `queue:work` running
alongside inside the same container. Two things worth knowing if you're
adapting this setup:

- **Boot order matters.** `reverb:start` has to be running *before*
  `migrate:fresh --seed` runs, for the same synchronous-broadcast reason
  noted above in "Getting started."
- **The proxy has to be trusted explicitly, and told the truth.**
  `bootstrap/app.php` trusts Render's proxy (`trustProxies(at: '*')`), but
  Caddy itself also needs `header_up X-Forwarded-Proto https` on its
  `reverse_proxy` blocks — otherwise Laravel builds `http://` asset URLs
  and issues insecure session cookies behind an HTTPS-terminating edge,
  which silently breaks login. This one took a while to track down; worth
  knowing if reusing this Caddyfile elsewhere.

## Deliberately out of scope (for now)

- Real payment gateway (orders are marked `paid` immediately — Stripe
  test mode would be the natural next step)
- Mock shipping-rate API
- Camera-based barcode scanning (`BarcodeDetector` API — the SKU-lookup
  backend already exists, this would just be a new input source)
- Self-service account registration (staff accounts are provisioned by
  whoever runs the business, not signed up)
- Historical cost snapshotting for fully time-accurate profit (see
  "Notable design decisions" above)

## License

MIT — do whatever you want with it.
