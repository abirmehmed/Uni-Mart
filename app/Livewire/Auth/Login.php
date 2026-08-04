<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Those credentials don\'t match our records.');

            return;
        }

        request()->session()->regenerate();

        $destination = Auth::user()->role === 'admin'
            ? route('admin.products')
            : route('pos.terminal');

        $this->redirect($destination, navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
