<div class="mx-auto mt-20 max-w-sm">
    <span class="mb-3 inline-flex -rotate-2 items-center gap-2 rounded-sm border border-amber-dark/40 bg-white px-2.5 py-1 font-mono text-[11px] font-medium uppercase tracking-widest text-amber-dark">
        <span class="h-1.5 w-1.5 rounded-full border border-current"></span>
        Staff access
    </span>
    <h1 class="mb-8 font-display text-4xl font-bold uppercase tracking-tight text-ink">Sign in</h1>

    <div class="border border-ink/10 bg-white shadow-xl shadow-ink/5">
        <div class="flex items-center justify-between border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
            <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">UniMart</span>
            <span class="font-mono text-[11px] uppercase tracking-widest text-amber">Admin &middot; Cashier</span>
        </div>
        @if (config('demo.show_login'))
            <div class="border-b-2 border-dashed border-ink/15 bg-amber/5 px-7 py-4">
                <p class="mb-3 font-mono text-[10px] uppercase tracking-widest text-amber-dark">Demo accounts &mdash; click to fill</p>
                <div class="space-y-2 font-mono text-xs">
                    <button type="button" x-on:click="$wire.set('email', 'admin@unimart.test'); $wire.set('password', 'password')" class="flex w-full items-center justify-between border border-ink/10 bg-white px-3 py-2 text-left transition-colors hover:border-amber">
                        <span class="text-ink"><span class="block uppercase tracking-wide text-ink/40">Admin</span>admin@unimart.test / password</span>
                        <span class="text-amber-dark">Fill</span>
                    </button>
                    <button type="button" x-on:click="$wire.set('email', 'cashier@unimart.test'); $wire.set('password', 'password')" class="flex w-full items-center justify-between border border-ink/10 bg-white px-3 py-2 text-left transition-colors hover:border-amber">
                        <span class="text-ink"><span class="block uppercase tracking-wide text-ink/40">Cashier</span>cashier@unimart.test / password</span>
                        <span class="text-amber-dark">Fill</span>
                    </button>
                </div>
            </div>
        @endif

        <form wire:submit="authenticate" class="space-y-5 p-7">
            <div>
                <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Email</label>
                <input type="email" wire:model="email" autofocus
                    class="mt-1.5 w-full rounded-sm border-ink/15 py-2.5 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                @error('email') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Password</label>
                <input type="password" wire:model="password"
                    class="mt-1.5 w-full rounded-sm border-ink/15 py-2.5 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                @error('password') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 font-mono text-[11px] uppercase tracking-wide text-ink/40">
                <input type="checkbox" wire:model="remember" class="rounded-sm border-ink/20">
                Remember me
            </label>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-sm bg-ink px-4 py-3 font-mono text-xs uppercase tracking-widest text-white shadow-sm transition-colors hover:bg-amber-dark disabled:opacity-60"
            >
                <span wire:loading.remove>Sign in</span>
                <span wire:loading>Signing in&hellip;</span>
            </button>
        </form>
    </div>
</div>
