<div class="mx-auto mt-16 max-w-sm">
    <p class="mb-1 text-center font-mono text-xs uppercase tracking-widest text-amber-dark">Staff access</p>
    <h1 class="mb-8 text-center font-display text-3xl font-bold uppercase tracking-tight text-ink">Sign in</h1>

    <div class="border border-ink/10 bg-white">
        <div class="flex items-center justify-between border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
            <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">UniMart</span>
            <span class="font-mono text-[11px] uppercase tracking-widest text-amber">Admin &middot; Cashier</span>
        </div>

        <form wire:submit="authenticate" class="space-y-4 p-6">
            <div>
                <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Email</label>
                <input type="email" wire:model="email" autofocus class="mt-1 w-full rounded-sm border-ink/15 text-sm">
                @error('email') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Password</label>
                <input type="password" wire:model="password" class="mt-1 w-full rounded-sm border-ink/15 text-sm">
                @error('password') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 font-mono text-[11px] uppercase tracking-wide text-ink/40">
                <input type="checkbox" wire:model="remember" class="rounded-sm border-ink/20">
                Remember me
            </label>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-sm bg-ink px-4 py-2.5 font-mono text-xs uppercase tracking-wide text-white hover:bg-amber-dark disabled:opacity-60"
            >
                <span wire:loading.remove>Sign in</span>
                <span wire:loading>Signing in&hellip;</span>
            </button>
        </form>
    </div>
</div>
