<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; 
use App\Models\PurchaseHistoryLog;

class LogPurchaseHistoryDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:purchase-history:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log purchase history daily';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $purchaseHistories = PurchaseHistory::where('available_stock', '>', 0)->get();
        $today = Carbon::today();

        DB::transaction(function () use ($purchaseHistories, $today) {
            foreach ($purchaseHistories as $purchaseHistory) {
                $availableQuantity = $purchaseHistory->available_stock;
                $purchasePrice = $purchaseHistory->purchase_price;
                $totalAmount = $availableQuantity * $purchasePrice;

                PurchaseHistoryLog::create([
                    'product_id' => $purchaseHistory->product_id,
                    'available_quantity' => $availableQuantity,
                    'purchase_price' => $purchasePrice,
                    'total_amount' => $totalAmount,
                    'log_date' => $today
                ]);
            }
        });

        return Command::SUCCESS;
    }
}