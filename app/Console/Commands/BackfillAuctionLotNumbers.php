<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuctionHandler;

class BackfillAuctionLotNumbers extends Command
{
    protected $signature = 'auctions:backfill-lot-numbers {--dry-run : Show how many would be updated without saving}';

    protected $description = 'Populate missing lot_number values for existing auctions (format: Lot#XXXXXX)';

    public function handle(): int
    {
        $missing = AuctionHandler::whereNull('lot_number')->orWhere('lot_number','')->get();
        if ($missing->isEmpty()) {
            $this->info('No auctions need lot_number backfill.');
            return self::SUCCESS;
        }

        $this->info('Found '.$missing->count().' auctions missing lot_number.');

        if ($this->option('dry-run')) {
            $this->line('Dry-run mode: no changes written.');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($missing as $auction) {
            $auction->lot_number = $this->generateUniqueLot();
            $auction->save();
            $updated++;
        }

        $this->info("Updated {$updated} auctions with new lot numbers.");
        return self::SUCCESS;
    }

    private function generateUniqueLot(): string
    {
        do {
            $number = 'Lot#'.str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (AuctionHandler::where('lot_number', $number)->exists());
        return $number;
    }
}
