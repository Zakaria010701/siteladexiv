<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state(['email' => '']);

new
#[Layout('components.layouts.guest')]
#[Title('ForgotPassword')]
class extends Component implements HasForms
{
    use InteractsWithForms;

    public $email;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),
            ]);
    }

    public function sendPasswordResetLink()
    {
        $data = $this->form->getState();

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink([
            'email' => $data['email'],
        ]);

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        Session::flash('status', __($status));
    }
}

?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        {{ $this->form }}

        <div class="flex items-center justify-end mt-4">
            <x-filament::button type="submit">
                {{ __('Email Password Reset Link') }}
            </x-filament::button>
        </div>
    </form>
</div>
