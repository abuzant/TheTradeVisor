<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Deal;

echo "=== Backfilling Deal.time where NULL ===\n";

$totalNull = Deal::whereNull('time')->count();

echo "Deals with time=NULL: {$totalNull}\n";

if ($totalNull == 0) {
    echo "Nothing to backfill.\n";
    exit(0);
}

$updated = 0;

// Fix NULL times only (timestamp columns should not hold empty strings)
$batch = 0;
Deal::whereNull('time')->chunkById(1000, function($deals) use (&$updated, &$batch) {
    $batch++;
    foreach ($deals as $deal) {
        $deal->time = $deal->created_at ?? now();
        $deal->save();
        $updated++;
    }
    echo "Processed batch (null) {$batch}, total updated={$updated}\n";
});

echo "=== Backfill complete. Updated {$updated} deals. ===\n";
