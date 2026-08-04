<div class="mx-auto mt-16 max-w-sm">
    <div class="rounded-lg border border-slate-200 bg-white p-6">
        <h1 class="mb-1 text-lg font-semibold text-slate-900">Sign in</h1>
        <p class="mb-6 text-sm text-slate-500">Staff access — admin &amp; cashier only.</p>

        <form wire:submit="authenticate" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-500">Email</label>
                <input type="email" wire:model="email" autofocus class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Password</label>
                <input type="password" wire:model="password" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-500">
                <input type="checkbox" wire:model="remember" class="rounded border-slate-300">
                Remember me
            </label>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-60"
            >
                <span wire:loading.remove>Sign in</span>
                <span wire:loading>Signing in...</span>
            </button>
        </form>
    </div>
</div>
