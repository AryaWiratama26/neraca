<?php

namespace App\Console\Commands;

use App\Http\Controllers\RecurringController;
use Illuminate\Console\Command;

class ProcessRecurring extends Command
{
    protected $signature = 'recurring:process';
    protected $description = 'Process due recurring transactions';

    public function handle()
    {
        $controller = new RecurringController();
        $count = $controller->processRecurring();
        $this->info("Processed {$count} recurring transaction(s).");
        return 0;
    }
}
