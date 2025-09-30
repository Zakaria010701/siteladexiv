<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class Deploy extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform deployment steps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->confirm('Do you really want to do this?', true)) {
            return;
        }

        $this->call('migrate');
        $this->call('db:seed');
        $this->call('optimize');
        $this->call('icons:cache');
        $this->call('filament:cache-components');
    }
}
