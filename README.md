# UniMart — Storefront (Pass 3)

Homepage product grid, session-backed Livewire cart, and checkout that sells
through the same `Product::sell()` choke point as the Admin Dashboard —
so an online sale is guaranteed just as oversell-proof as an admin edit.

## Files in this pass

| File | Purpose |
|---|---|
| `app/Livewire/Storefront/Homepage.php` | Product grid + cart, session-backed |
| `app/Livewire/Storefront/Checkout.php` | Form → atomic multi-item sale → order |
| `app/Mail/OrderReceipt.php` | Queued receipt email |
| `resources/views/livewire/storefront/*.blade.php` | The two page views |
| `resources/views/emails/order-receipt.blade.php` | Plain HTML receipt |
| `routes/web.php` | **Replaces** your whole file — see below |

## `routes/web.php` is a full replacement this time

Unlike the Admin pass, this one changes the `/` route itself (welcome page →
storefront homepage), so it's simplest to replace the whole file rather than
patch it. It already includes the admin route from Pass 2.

## Queue + mail setup (new this pass)

The receipt email is queued (`implements ShouldQueue`) so checkout doesn't
sit waiting on SMTP. Two `.env` settings matter:

```env
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

- `QUEUE_CONNECTION=database` — uses the `jobs` table Pass 1 already
  migrated. (`sync` also works for a quick test — it'll just send inline
  instead of queuing, which is fine for a demo but defeats the point of
  Pass 1's queue requirement.)
- `MAIL_MAILER=log` — writes the email to `storage/logs/laravel.log`
  instead of actually sending anything, so you can see the receipt content
  without configuring a real mail provider. Open that log file after
  checkout to read it.

**You need a 4th terminal now** if using `QUEUE_CONNECTION=database`:
```bash
php artisan queue:work
```
Leave it running — this is what actually sends (logs) the email a moment
after checkout completes, not during the request itself.

## Test the full flow

1. `php artisan serve`, `php artisan reverb:start --debug`, `npm run dev`,
   `php artisan queue:work` — four terminals.
2. Visit `http://localhost:8000/` — you should see your product(s) instead
   of the Laravel welcome page now.
3. Add an item to the cart, watch the sidebar update with no page reload.
4. Click Checkout, fill the form, place the order.
5. Confirm: stock dropped by the quantity you bought, `storage/logs/laravel.log`
   has the receipt email, and **your Admin Dashboard tab (if still open)
   updates its stock number live** — proving an online sale syncs to admin
   exactly like the reverse did in Pass 2.

## The oversell race, demonstrated

Open the homepage in two browser tabs (or one tab + Tinker). Add the same
last-remaining item to the cart in both, then checkout in one first. The
second checkout will show the cart error message instead of succeeding —
`Product::sell()`'s row lock is what causes this exact behavior, live.

## What's deliberately deferred

- **No real payment gateway** — orders are marked `status: 'paid'`
  immediately. Wiring in Stripe test mode is a clean follow-up.
- **No mock shipping API** — the address field is collected but not sent
  anywhere yet.
- **Cart survives only as long as the session** — fine for a demo; a real
  build might persist it per logged-in customer instead.

## Next pass

POS terminal — the third leg of the Golden Rule triangle.
