<?php

namespace App\Jobs\Invoices;

use App\Events\Invoices\InvoiceDueEvent;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class DispatchInvoiceDueEvents implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Invoice::where('due_date', today())
            ->chunk(100, function (Collection $invoices) {
                $invoices->each(fn (Invoice $invoice) => InvoiceDueEvent::dispatch($invoice));
            });
    }
}
