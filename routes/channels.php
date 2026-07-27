<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Both channels are deliberately public (Channel, not PrivateChannel):
| stock counts and "an order was placed" are not sensitive data, and every
| surface — public storefront, POS terminal, admin dashboard — needs to
| subscribe without an authenticated session. If you later add e.g. a
| "cashier chat" feature, that would warrant a PrivateChannel with auth
| below this file.
|
*/

// No Broadcast::channel() calls needed for public channels — they're open
// by default. This file exists so the structure is here when you add a
// private channel later (e.g. per-cashier order queues).
