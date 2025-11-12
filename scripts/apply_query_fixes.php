<?php

/**
 * Apply remaining query fixes to controllers
 */

$fixes = [
    // CountryAnalyticsController.php
    '/www/app/Http/Controllers/CountryAnalyticsController.php' => [
        [
            'search' => "                ->groupBy('country_code', 'country_name')\n                ->get()",
            'replace' => "                ->groupBy('country_code', 'country_name')\n                ->limit(50)\n                ->get()",
            'line' => 35,
        ],
        [
            'search' => "            ->groupBy('symbol')\n            ->get()",
            'replace' => "            ->groupBy('symbol')\n            ->limit(50)\n            ->get()",
            'line' => 98,
        ],
        [
            'search' => "                ->groupBy('broker_name')\n                ->get()",
            'replace' => "                ->groupBy('broker_name')\n                ->limit(50)\n                ->get()",
            'line' => 134,
        ],
        [
            'search' => "            ->groupBy('hour')\n            ->get();",
            'replace' => "            ->groupBy('hour')\n            ->limit(24)\n            ->get();",
            'line' => 206,
        ],
    ],
    
    // BrokerDetailsController.php
    '/www/app/Http/Controllers/BrokerDetailsController.php' => [
        [
            'search' => "            ->orderBy('balance', 'desc')\n            ->get();",
            'replace' => "            ->orderBy('balance', 'desc')\n            ->limit(100)\n            ->get();",
            'line' => 25,
        ],
        [
            'search' => "            ->orderBy('total_profit', 'desc')\n            ->get()",
            'replace' => "            ->orderBy('total_profit', 'desc')\n            ->limit(50)\n            ->get()",
            'line' => 77,
        ],
        [
            'search' => "            ->orderBy('hour')\n            ->get();",
            'replace' => "            ->orderBy('hour')\n            ->limit(24)\n            ->get();",
            'line' => 89,
        ],
    ],
    
    // Admin/TradesController.php
    '/www/app/Http/Controllers/Admin/TradesController.php' => [
        [
            'search' => "        \$users = User::orderBy('name')->get();",
            'replace' => "        \$users = User::orderBy('name')->limit(1000)->get();",
            'line' => 93,
        ],
    ],
    
    // Admin/SymbolManagementController.php
    '/www/app/Http/Controllers/Admin/SymbolManagementController.php' => [
        [
            'search' => "        \$symbols = SymbolMapping::where('is_verified', false)->get();",
            'replace' => "        \$symbols = SymbolMapping::where('is_verified', false)->limit(500)->get();",
            'line' => 107,
        ],
    ],
    
    // TradesController.php
    '/www/app/Http/Controllers/TradesController.php' => [
        [
            'search' => "            ->orderBy('time', 'desc')\n            ->get();",
            'replace' => "            ->orderBy('time', 'desc')\n            ->limit(1000)\n            ->get();",
            'line' => 95,
        ],
    ],
];

$totalFixed = 0;
$errors = [];

foreach ($fixes as $file => $fileFixes) {
    if (!file_exists($file)) {
        $errors[] = "File not found: $file";
        continue;
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;
    $fileFixed = 0;
    
    foreach ($fileFixes as $fix) {
        $count = 0;
        $content = str_replace($fix['search'], $fix['replace'], $content, $count);
        
        if ($count > 0) {
            $fileFixed += $count;
            $totalFixed += $count;
            echo "✓ Fixed line ~{$fix['line']} in " . basename($file) . "\n";
        } else {
            $errors[] = "Could not find pattern at line {$fix['line']} in " . basename($file);
        }
    }
    
    if ($fileFixed > 0 && $content !== $originalContent) {
        file_put_contents($file, $content);
        echo "  → Saved " . basename($file) . " with $fileFixed fixes\n\n";
    }
}

echo "\n===========================================\n";
echo "SUMMARY\n";
echo "===========================================\n";
echo "Total fixes applied: $totalFixed\n";

if (!empty($errors)) {
    echo "\nWarnings:\n";
    foreach ($errors as $error) {
        echo "  ⚠ $error\n";
    }
}

echo "\n✅ Query optimization complete!\n";
