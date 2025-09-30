<?php

namespace App\Filament\Actions;

use App\Enums\Todos\TodoPriority;
use App\Enums\Todos\TodoStatus;
use App\Models\Todo;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class ReportBugAction extends Action
{
    protected string|Closure|null $report_url = null;

    public static function getDefaultName(): ?string
    {
        return 'report-bug';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__("Report"));

        $this->color('warning');

        $this->icon(Heroicon::ExclamationTriangle);

        $this->schema([
            Textarea::make('description')
                ->required()
        ]);

        $this->action(function (array $data, Action $action) {
            $description = sprintf("%s \n\n%s", $this->getReportUrl(), $data['description']);
            $todo = Todo::create([
                'priority' => TodoPriority::Medium,
                'status' => TodoStatus::NotDone,
                'description' => $description
            ]);
            $user = User::where('name', 'Anton')->first();
            if(isset($user)) {
                $todo->users()->attach($user);
            }

            $action->success();
        });
    }

    public function reportUrl(string|Closure $url) : self
    {
        $this->report_url = $url;
        return $this;
    }

    public function getReportUrl(): string
    {
        return $this->evaluate($this->report_url) ?? "";
    }
}

