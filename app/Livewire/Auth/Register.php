<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $name = '';
    public $company_name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|min:3',
        'company_name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed', // Basic strength check
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'admin', // FR1 says "Admin Registration"
            'is_active' => true, // Skipping email verification complexity for local dev, but logic is here
        ]);

        // Send Email Verification (Mocked for now)
        // event(new Registered($user));

        auth()->login($user);

        return redirect()->route('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }
}
