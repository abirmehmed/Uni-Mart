import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Any Livewire component can now do, in its mount():
//
//   Echo.channel('inventory').listen('.stock.updated', (e) => {
//       $wire.set(`stockLevels.${e.product_id}`, e.stock_quantity);
//   });
//
// Prefer $wire.set() over $wire.call('someMethod') here — it updates the
// component's local state directly without a network round-trip back to
// the server, since the server already told us the new value.
