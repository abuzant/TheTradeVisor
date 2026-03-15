<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SymbolMapping;
use Illuminate\Support\Facades\DB;

class SyncSymbols extends Command
{
    protected $signature = 'symbols:sync';
    protected $description = 'Sync all unique symbols from deals, positions, and orders to symbol_mappings table';

    public function handle()
    {
        $this->info('Scanning database for unique symbols...');
        
        // Get all unique symbols
        $dealsSymbols = DB::table('deals')
            ->select('symbol')
            ->distinct()
            ->whereNotNull('symbol')
            ->where('symbol', '<>', '')
            ->pluck('symbol');
            
        $positionsSymbols = DB::table('positions')
            ->select('symbol')
            ->distinct()
            ->whereNotNull('symbol')
            ->where('symbol', '<>', '')
            ->pluck('symbol');
            
        $ordersSymbols = DB::table('orders')
            ->select('symbol')
            ->distinct()
            ->whereNotNull('symbol')
            ->where('symbol', '<>', '')
            ->pluck('symbol');
        
        $allSymbols = $dealsSymbols
            ->merge($positionsSymbols)
            ->merge($ordersSymbols)
            ->unique()
            ->filter()
            ->values();
        
        $this->info("Found {$allSymbols->count()} unique symbols");
        
        $created = 0;
        $skipped = 0;
        
        foreach ($allSymbols as $rawSymbol) {
            $exists = SymbolMapping::where('raw_symbol', $rawSymbol)->exists();
            
            if (!$exists) {
                $normalized = SymbolMapping::autoNormalize($rawSymbol);
                
                SymbolMapping::create([
                    'raw_symbol' => $rawSymbol,
                    'normalized_symbol' => $normalized,
                    'is_verified' => false,
                ]);
                
                $this->line("✓ Created: {$rawSymbol} → {$normalized}");
                $created++;
            } else {
                $skipped++;
            }
        }
        
        $this->newLine();
        $this->info("Sync complete!");
        $this->info("Created: {$created}");
        $this->info("Skipped: {$skipped}");
        $this->info("Total in mapping table: " . SymbolMapping::count());
        
        return 0;
    }
}
