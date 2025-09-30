<?php

namespace App\Filament\Crm\Resources\TimeReports\Pages;

use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use App\Actions\TimeReport\ControlTimeReport;
use App\Actions\TimeReport\GeneratePayroll;
use App\Actions\TimeReport\GenerateTimeReportOverview;
use App\Actions\TimeReport\RecalculateTimeReport;
use App\Actions\TimeReport\UndoTimeReport;
use App\Enums\TimeRecords\LeaveType;
use App\Filament\Crm\Resources\TimeReports\TimeReportResource;
use App\Models\Payroll;
use App\Models\TimeReport;
use App\Models\TimeReportOverview;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class OverviewTimeReport extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $resource = TimeReportResource::class;

    protected string $view = 'filament.crm.resources.time-report-resource.pages.overview-time-report';

    #[Url]
    public $date;

    #[Url]
    public $user;

    public Collection $records;

    public TimeReportOverview $overview;

    public function mount(): void
    {
        $this->date = $this->date ?? today()->startOfMonth()->format('Y-m-d');
        $this->user = $this->user ?? Auth::id();
        $this->showReport();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return Auth::user()->can('viewAny', TimeReport::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DatePicker::make('date')
                    ->required(),
                Select::make('user')
                    ->searchable()
                    ->options(User::query()->pluck('name', 'id'))
                    ->hidden(fn () => Auth::user()->cannot('admin_time::report'))
                    ->required(),
            ]);
    }

    public function showReport(): void
    {
        $date = Carbon::parse($this->date);
        $overview = TimeReportOverview::query()
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('user_id', $this->user)
            ->first();
        if ($overview === null) {
            $overview = GenerateTimeReportOverview::make($date, User::findOrFail($this->user))->execute();
            $overview->refresh();
        }

        $this->overview = $overview;
        $this->records = $this->overview->timeReports;
    }

    public function editAction(): Action
    {
        return EditAction::make()
            ->record(fn (array $arguments) => TimeReport::findOrFail($arguments['report']))
            ->icon('heroicon-m-pencil')
            ->iconButton()
            ->size(Size::Small)
            ->schema(fn (Schema $schema) => TimeReportResource::form($schema))
            ->mutateDataUsing(function (array $data): array {
                $data['edited_by_id'] = auth()->id();
                $data['edited_at'] = now();
                $data['controlled_by_id'] = auth()->user()->can('admin_time::report') ? auth()->id() : null;
                $data['controlled_at'] = auth()->user()->can('admin_time::report') ? now() : null;

                return $data;
            })
            ->before(function (TimeReport $record, Action $action) {
                if (auth()->user()->cannot('update', $record)) {
                    Notification::make()
                        ->warning()
                        ->title(__('status.permission.denied'))
                        ->color('warning')
                        ->send();
                    $action->cancel(true);
                }
            })
            ->using(function (TimeReport $record, array $data) {
                $record->update($data);

                $this->updateLeave($record, $data);

                RecalculateTimeReport::make($record)->excecute();
            });
    }

    private function updateLeave(TimeReport $report, array $data)
    {
        if(empty($data['leave_type'])) {
            return;
        }

        $leave_type = LeaveType::tryFrom($data['leave_type']);

        if($leave_type === null) {
            return;
        }

        $leave = $report->user->leaves()
            ->approved()
            ->where('from', '<=', $report->date)
            ->where('till', '>=', $report->date)
            ->first();

        if($leave === null) {
            $report->user->leaves()
                ->create([
                    'from' => $report->date,
                    'till' => $report->date,
                    'leave_type' => $leave_type,
                    'approved_by_id' => auth()->id(),
                    'approved_at' => now(),
                ]);
        }

        if($leave->leave_type == $leave_type) {
            return;
        }
    }

    public function controlAction(): Action
    {
        return Action::make('control')
            ->record(fn (array $arguments) => TimeReport::findOrFail($arguments['report']))
            ->icon('heroicon-m-check')
            ->color('success')
            ->iconButton()
            ->size(Size::Small)
            ->requiresConfirmation()
            ->visible(fn (TimeReport $record) => auth()->user()->can('admin_time::report') && $record->needsToBeControlled())
            ->before(function (TimeReport $record, Action $action) {
                if (auth()->user()->cannot('admin_time::report')) {
                    Notification::make()
                        ->warning()
                        ->title(__('status.permission.denied'))
                        ->color('warning')
                        ->send();
                    $action->cancel(true);
                }
            })
            ->action(function (TimeReport $record) {
                ControlTimeReport::make($record, auth()->user())->execute();

                Notification::make()
                    ->success()
                    ->title(__('status.result.success'))
                    ->color('success')
                    ->send();
            });
    }

    public function undoAction(): Action
    {
        return Action::make('undo')
            ->record(fn (array $arguments) => TimeReport::findOrFail($arguments['report']))
            ->icon('heroicon-m-arrow-uturn-left')
            ->color('warning')
            ->size(Size::Small)
            ->requiresConfirmation()
            ->visible(fn (TimeReport $record) => auth()->user()->can('update_time::report') && $record->isEdited())
            ->before(function (TimeReport $record, Action $action) {
                if (auth()->user()->cannot('update', $record)) {
                    Notification::make()
                        ->warning()
                        ->title(__('status.permission.denied'))
                        ->color('warning')
                        ->send();
                    $action->cancel(true);
                }
            })
            ->action(function (TimeReport $record) {
                UndoTimeReport::make($record)->execute();

                Notification::make()
                    ->success()
                    ->title(__('status.result.success'))
                    ->color('success')
                    ->send();
            });
    }

    public function payrollAction()
    {
        return Action::make('payroll')
            ->record(fn (array $arguments) => TimeReport::findOrFail($arguments['report']))
            ->icon('heroicon-m-wallet')
            ->size(Size::Small)
            ->requiresConfirmation()
            ->visible(fn (TimeReport $record) => ! isset($record->payroll) && auth()->user()->can('create_payroll'))
            ->before(function (Action $action) {
                if (auth()->user()->cannot('create', Payroll::class)) {
                    Notification::make()
                        ->warning()
                        ->title(__('status.permission.denied'))
                        ->color('warning')
                        ->send();
                    $action->cancel(true);
                }
            })
            ->action(function (TimeReport $record) {
                GeneratePayroll::make($record, auth()->user())->execute();

                Notification::make()
                    ->success()
                    ->title(__('status.result.success'))
                    ->color('success')
                    ->send();
            });
    }

    public function deletePayrollAction()
    {
        return Action::make('delete_payroll')
            ->record(fn (array $arguments) => TimeReport::findOrFail($arguments['report']))
            ->icon('heroicon-m-wallet')
            ->color('danger')
            ->size(Size::Small)
            ->requiresConfirmation()
            ->visible(fn (TimeReport $record) => isset($record->payroll) && auth()->user()->can('delete', $record->payroll))
            ->before(function (Action $action, TimeReport $record) {
                if (auth()->user()->cannot('delete', $record->payroll)) {
                    Notification::make()
                        ->warning()
                        ->title(__('status.permission.denied'))
                        ->color('warning')
                        ->send();
                    $action->cancel(true);
                }
            })
            ->action(function (TimeReport $record) {
                dump($record->payroll);
                $record->payroll->delete();

                Notification::make()
                    ->success()
                    ->title(__('status.result.success'))
                    ->color('success')
                    ->send();
            });
    }
}
