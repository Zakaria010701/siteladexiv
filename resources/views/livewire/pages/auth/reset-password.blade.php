<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, Locked};
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;

new
#[Layout('components.layouts.guest')]
#[Title('ForgotPassword')]
class extends Component implements HasForms
{
    use InteractsWithForms;

    //#[Locked]
    public $token;

    public $email;
    public $password;
    public $password_confirmation;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->same('password_confirmation')
                    ->autocomplete('new-password')
                    ->required(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->required(),
            ]);
    }

    public function resetPassword()
    {
        $data = $this->form->getState();

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $this->token,
            ],
            function ($user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}

?>

<div>
    <form wire:submit="resetPassword">
        {{ $this->form }}

        <div class="flex items-center justify-end mt-4">
            <x-filament::button type="submit">
                {{ __('Reset Password') }}
            </x-filament::button>
        </div>
    </form>
</div>
