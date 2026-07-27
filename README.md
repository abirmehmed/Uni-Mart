# UniMart — Foundation Layer

This is pass 1 of the UniMart MVP: the **sync backbone**. Nothing here has a UI —
it's the schema, models, and real-time plumbing that the Admin Dashboard,
Storefront, and POS modules will all sit on top of in later passes.

## How the pieces fit together

```
Admin edits stock  ─┐
Online checkout    ─┼──▶ Product::sell() / restock()  (DB transaction + row lock)
POS "Pay & Complete"─┘              │
                                     ▼
                          Product model saved
                                     │
                                     ▼
                       ProductObserver::updated()
                     (fires only if stock_quantity changed)
                                     │
                                     ▼
                       event(new StockUpdated($product))
                                     │
                                     ▼
                    Broadcast on public channel "inventory"
                          event name: "stock.updated"
                                     │
                     ┌───────────────┼───────────────┐
                     ▼               ▼                ▼
              Storefront        POS terminal    Admin dashboard
           Livewire component  Livewire component  Livewire component
           (Echo.channel       (Echo.channel        (Echo.channel
            .listen)            .listen)              .listen)
```

The key design decision: **every stock-changing action funnels through
`Product::sell()` / `Product::restock()`**, not direct `$product->update()`
calls scattered across the checkout controller and the POS controller. That's
what makes the "no overselling" guarantee real — `lockForUpdate()` inside a
`DB::transaction()` means if an online sale and a POS sale hit the same
product in the same instant, the second one to acquire the row lock sees the
already-decremented stock and fails cleanly with `InsufficientStockException`
instead of both succeeding and taking stock negative.

The Observer, not the controllers, is what fires `StockUpdated`. That means
**any** path that changes stock — including a plain admin inline edit — gets
broadcast automatically, with zero risk of a future developer adding a new
"sell" code path and forgetting to wire up the event.

## Files in this pass

| File | Purpose |
|---|---|
| `database/migrations/*` | products, users (+role), orders, order_product |
| `app/Models/Product.php` | `sell()` / `restock()` — the concurrency-safe stock choke point |
| `app/Models/Order.php`, `User.php` | Relationships + order number generator |
| `app/Exceptions/InsufficientStockException.php` | Thrown by `sell()` on oversell attempt |
| `app/Events/StockUpdated.php` | Broadcasts new stock level to everyone listening |
| `app/Events/OrderPlaced.php` | Broadcasts "a sale happened" (powers the POS new-order sound later) |
| `app/Observers/ProductObserver.php` | Watches Product, fires `StockUpdated` |
| `app/Providers/AppServiceProvider.php` | Registers the observer |
| `routes/channels.php` | Documents that inventory/orders are public channels |
| `resources/js/echo.js` | Frontend Echo client, ready for Livewire components to `Echo.channel(...).listen(...)` |
| `.env.broadcasting.example` | Env vars for Reverb |

## Design choices worth knowing about (small deviations from the brief)

- **Money stored as integer cents** (`price_cents`, not `price` decimal). This
  avoids float rounding bugs that decimal columns *can* still hit depending
  on driver/casting. `Product::price` accessor formats it back to `"12.99"`
  for display, so nothing above the model layer needs to know about cents.
- **`order_product.product_id` restricts on delete**, not cascades — combined
  with soft-deletes on `products`, a "deactivated" product's historical sales
  rows are permanently safe even if someone force-deletes it later.
- **`orders.cashier_id`** added (nullable, only set when `source = pos`) so
  the Daily Sales Summary stretch goal can break down revenue by cashier for
  free later.

## Wiring this into a real Laravel project

```bash
composer create-project laravel/laravel unimart
cd unimart
composer require laravel/reverb
php artisan reverb:install        # generates REVERB_APP_* keys in .env

npm install laravel-echo pusher-js
```

Then copy this pass's files into place (same relative paths), append
`.env.broadcasting.example` into your real `.env`, add
`import './echo';` to `resources/js/app.js`, and:

```bash
php artisan migrate
php artisan reverb:start   # run alongside `php artisan serve`
```

## Next passes

1. **Admin Dashboard** — Livewire CRUD table (search + inline edit) built on
   `Product::sell()`/`restock()` and `#[On('echo:inventory,stock.updated')]`.
2. **Storefront** — homepage grid, cart, checkout → calls `Product::sell()`
   per line item inside one transaction, dispatches `OrderPlaced`.
3. **POS terminal** — category grid, keypad cart, same `sell()` call, tagged
   `source: 'pos'`.
4. Queued receipt emails, mock shipping API, barcode scanning, sales summary.

Say the word when you want the next pass.
