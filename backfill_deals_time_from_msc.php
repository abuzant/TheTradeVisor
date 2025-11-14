<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Deal;
use Carbon\Carbon;

echo "=== Backfilling Deal.time from time_msc where available ===\n";

// Count MT5 deals with time_msc present
$totalWithMsc = Deal::whereNotNull('time_msc')->count();
echo "Deals with time_msc NOT NULL: {$totalWithMsc}\n";

if ($totalWithMsc == 0) {
    echo "No deals with time_msc to backfill.\n";
    exit(0);
}

$updated = 0;
$batch = 0;

Deal::whereNotNull('time_msc')
    ->orderBy('id')
    ->chunkById(1000, function($deals) use (&$updated, &$batch) {
        $batch++;
        foreach ($deals as $deal) {
            try {
                // MT5 DEAL_TIME_MSC is milliseconds since 1970-01-01
                $msc = (int) $deal->time_msc;
                if ($msc <= 0) {
                    continue;
                }
                $deal->time = Carbon::createFromTimestampMs($msc);
                $deal->save();
                $updated++;
            } catch (\Throwable $e) {
                echo "Error on deal ID {$deal->id}: {$e->getMessage()}\n";
            }
        }
        echo "Processed batch {$batch}, total updated={$updated}\n";
    });

echo "=== Backfill from time_msc complete. Updated {$updated} deals. ===\n";
