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
