<?php

namespace App\Filament\Actions\Table;

use Filament\Actions\Action;
use App\Enums\Verifications\VerificationStatus;
use App\Models\Customer;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class VerifyAction extends Action
{
    use CanCustomizeProcess;
    public static function getDefaultName(): ?string
    {
        return 'verify';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Verified'));

        $this->color('success');

        $this->icon('heroicon-o-check');

        $this->form([
            Textarea::make('note'),
        ]);

        $this->visible(fn (Model $record) => $record->isNotVerified());

        $this->action(function () {
            $this->process(function (array $data, Model $record, Table $table) {
                $record->verifications()->create([
                    'user_id' => auth()->user()->id,
                    'status' => VerificationStatus::Pass,
                    'note' => $data['note'] ?? null,
                ]);
            });

            $this->success();
        });
    }
}
