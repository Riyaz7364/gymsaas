<?php

namespace App\Livewire;

use Livewire\Component;

class VerifyOtp extends Component
{
    public string $email         = '';
    public string $phone         = '';
    public bool   $emailVerified = false;
    public bool   $phoneVerified = false;
    public string $emailError    = '';
    public string $phoneError    = '';

    public function mount(): void
    {
        if (! session()->has('signup.step1')) {
            $this->redirect(route('signup'));
            return;
        }

        $step1 = session('signup.step1', []);
        $this->email         = $step1['email'] ?? '';
        $this->phone         = '+91 ' . ($step1['phone'] ?? '');
        $this->emailVerified = (bool) session('signup.otp_verified_email', false);
        $this->phoneVerified = (bool) session('signup.otp_verified_phone', false);
    }

    public function verifyEmail(string $otp): void
    {
        $this->emailError = '';

        if (trim($otp) !== '123456') {
            $this->emailError = 'Invalid code. Please try again.';
            return;
        }

        session(['signup.otp_verified_email' => true]);
        $this->emailVerified = true;
    }

    public function verifyPhone(string $otp): void
    {
        $this->phoneError = '';

        if (! $this->emailVerified) {
            $this->phoneError = 'Please verify your email first.';
            return;
        }

        if (trim($otp) !== '123456') {
            $this->phoneError = 'Invalid code. Please try again.';
            return;
        }

        session(['signup.otp_verified_phone' => true]);
        $this->phoneVerified = true;
        // User sees both panels as verified, then clicks Continue themselves
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.verify-otp');
    }
}
