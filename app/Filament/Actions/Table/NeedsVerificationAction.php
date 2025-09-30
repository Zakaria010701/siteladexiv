<?php

namespace App\Filament\Actions\Table;

use Filament\Actions\Action;
use App\Enums\Verifications\VerificationStatus;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class NeedsVerificationAction extends Action
{
    use CanCustomizeProcess;
    public static function getDefaultName(): ?string
    {
        return 'needs-verification';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Needs verification'));

        $this->color('warning');

        $this->icon('heroicon-o-x-mark');

        $this->form([
            Textarea::make('note'),
        ]);

        $this->action(function () {
            $this->process(function (array $data, Model $record, Table $table) {
                $record->verifications()->create([
                    'user_id' => auth()->user()->id,
                    'status' => VerificationStatus::Failure,
                    'note' => $data['note'] ?? null,
                ]);
            });

            $this->success();
        });
    }
}
