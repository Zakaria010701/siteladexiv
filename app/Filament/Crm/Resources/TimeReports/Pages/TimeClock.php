<?php

namespace App\Filament\Crm\Resources\TimeReports\Pages;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use App\Actions\TimeReport\CheckIn;
use App\Actions\TimeReport\CheckOut;
use App\Actions\TimeReport\GenerateTimeReport;
use App\Filament\Admin\Resources\Todos\TodoResource;
use App\Filament\Crm\Resources\TimeReports\TimeReportResource;
use App\Filament\Crm\Widgets\CheckedInUsersWidget;
use App\Models\TimeReport;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TimeClock extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithRateLimiting;

    protected static string $resource = TimeReportResource::class;

    protected string $view = 'filament.crm.resources.time-report-resource.pages.time-clock';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return Auth::user()->can('viewAny', TimeReport::class);
    }

    public function getFooterWidgets(): array
    {
        return [
            CheckedInUsersWidget::class,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('login')
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->required()
                    ->extraInputAttributes(['tabindex' => 2]),
                TextInput::make('note'),
            ])
            ->statePath('data');
    }

    public function check()
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
        }

        $data = $this->form->getState();

        $user = $this->authenticate($data);

        $timeReport = $this->getCurrentTimeReport($user);

        if (is_null($timeReport->time_in)) {
            $this->checkIn($timeReport, $user, $data);
        } else {
            $this->checkOut($timeReport, $user, $data);
        }
    }

    private function checkIn(TimeReport $timeReport, User $user, array $data)
    {
        $timeReport = CheckIn::make($user, $timeReport, $data['note'] ?? '')->execute();
        Notification::make()
            ->title(__('Checked in at :time', ['time' => $timeReport->time_in->format(getTimeFormat())]))
            ->actions([
                Action::make('todos')
                    ->button()
                    ->url(TodoResource::getUrl('index', panel: 'admin'))
            ])
            ->success()
            ->send();
    }

    private function checkOut(TimeReport $timeReport, User $user, array $data)
    {
        $timeReport = CheckOut::make($user, $timeReport, $data['note'] ?? '')->execute();
        Notification::make()
            ->title(__('Checked out at :time', ['time' => $timeReport->time_out->format(getTimeFormat())]))
            ->success()
            ->send();
    }

    private function authenticate(array $data): User
    {
        if (! Filament::auth()->once($this->getCredentialsFromFormData($data))) {
            throw ValidationException::withMessages([
                'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        return Filament::auth()->user();
    }

    private function getCurrentTimeReport(User $user): TimeReport
    {
        if ($user->timeReports()->where('date', today()->format('Y-m-d'))->doesntExist()) {
            return GenerateTimeReport::make(today(), $user)->execute();
        }

        return $user->timeReports()->where('date', today()->format('Y-m-d'))->first();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }
}
