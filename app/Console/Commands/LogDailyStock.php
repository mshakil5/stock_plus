<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\DailyStockLog;
use Carbon\Carbon;

class LogDailyStock extends Command
{
    protected $signature = 'stock:log-daily';
    protected $description = 'Log daily stock quantities';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::today();
        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            DailyStockLog::create([
                'product_id' => $stock->product_id,
                'quantity' => $stock->quantity,
                'log_date' => $today,
            ]);
        }

        $this->info('Daily stock log has been recorded.');
    }
}