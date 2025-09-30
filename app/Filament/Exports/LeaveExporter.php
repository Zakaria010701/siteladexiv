<?php

namespace App\Filament\Exports;

use App\Enums\TimeRecords\LeaveType;
use App\Models\Leave;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LeaveExporter extends Exporter
{
    protected static ?string $model = Leave::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('user.name'),
            ExportColumn::make('leave_type')
                ->formatStateUsing(fn (LeaveType $state) => $state->value),
            ExportColumn::make('from'),
            ExportColumn::make('till'),
            ExportColumn::make('total_leave_days'),
            ExportColumn::make('processedBy.name'),
            ExportColumn::make('approved_at'),
            ExportColumn::make('denied_at'),
            ExportColumn::make('user_note'),
            ExportColumn::make('admin_note'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your leave export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
