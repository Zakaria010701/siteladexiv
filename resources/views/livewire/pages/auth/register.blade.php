<?php

use App\Enums\Gender;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;


new
#[Layout('components.layouts.guest')]
#[Title('Register')]
class extends Component implements HasForms {
    use InteractsWithForms;

    public $gender;
    public $firstname;
    public $lastname;
    public $email;
    public $phone_number;
    public $password;
    public $password_confirmation;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('gender')
                    ->options(Gender::class)
                    ->required(),
                Forms\Components\TextInput::make('firstname')
                    ->required(),
                Forms\Components\TextInput::make('lastname')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required(),
                PhoneInput::make('phone_number'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->same('password_confirmation')
                    ->required(),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(),
            ]);
    }

    public function register()
    {
        $validated = $this->form->getState();

        event(new Registered($customer = Customer::create($validated)));

        Auth::login($customer);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}

?>

<div>
    <form wire:submit="register">
        {{ $this->form }}

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
