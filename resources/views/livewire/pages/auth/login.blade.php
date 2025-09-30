<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\form;

new
#[Layout('components.layouts.guest')]
#[Title('Login')]
class extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $email;
    public $password;
    public $remember;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->hint(
                        Route::has('password.request')
                        ? new HtmlString(Blade::render('<x-filament::link :href="route(\'password.request\')" >{{ __(\'Forgot your password?\') }}</x-filament::link>'))
                        : null
                    )
                    ->extraInputAttributes(['tabindex' => 2])
                    ->required(),
                Forms\Components\Checkbox::make('remember')
                    ->label(__('Remember me')),
            ]);
    }

    public function login()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}

?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        {{ $this->form }}

        <div class="flex items-center justify-end mt-4">
            <x-filament::button type="submit">
                {{ __('Log in') }}
            </x-filament::button>
        </div>
    </form>
</div>
