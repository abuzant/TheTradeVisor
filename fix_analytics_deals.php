<?php
/**
 * Script to fix all Deal queries in AnalyticsController
 * Adds ->where('entry', 'out') to queries that don't have it
 */

$file = '/www/app/Http/Controllers/AnalyticsController.php';
$content = file_get_contents($file);

// Patterns to fix - add entry='out' filter
$patterns = [
    // Pattern 1: Deal::select(...)->whereNotNull('symbol')->where('symbol', '!=', '')->where('time'
    [
        'search' => "Deal::select(",
        'check' => "->where('entry',",
        'add_after' => "->whereNotNull('symbol')\n            ->where('symbol', '!=', '')",
        'insert' => "\n            ->where('entry', 'out')"
    ],
    // Pattern 2: Deal::whereNotNull('symbol')->where('symbol', '!=', '')->where('time'
    [
        'search' => "Deal::whereNotNull('symbol')",
        'check' => "->where('entry',",
        'add_after' => "->where('symbol', '!=', '')",
        'insert' => "\n            ->where('entry', 'out')"
    ],
    // Pattern 3: Deal::join(...) queries
    [
        'search' => "Deal::join('trading_accounts'",
        'check' => "->where('deals.entry',",
        'add_after' => "->select(",
        'insert' => "\n            ->where('deals.entry', 'out')"
    ],
];

// Count fixes
$fixes = 0;

// Split into lines for easier processing
$lines = explode("\n", $content);
$newLines = [];

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $newLines[] = $line;
    
    // Check if this line has Deal:: query
    if (strpos($line, 'Deal::') !== false) {
        // Check if next few lines already have ->where('entry'
        $hasEntry = false;
        for ($j = $i; $j < min($i + 10, count($lines)); $j++) {
            if (strpos($lines[$j], "->where('entry',") !== false || 
                strpos($lines[$j], '->where("entry",') !== false ||
                strpos($lines[$j], "->closedTrades()") !== false) {
                $hasEntry = true;
                break;
            }
        }
        
        // If no entry filter and has symbol filter, add it
        if (!$hasEntry && strpos($line, "whereNotNull('symbol')") === false) {
            // Look ahead for whereNotNull or where time
            for ($j = $i + 1; $j < min($i + 5, count($lines)); $j++) {
                if (strpos($lines[$j], "->where('time',") !== false || 
                    strpos($lines[$j], '->where("time",') !== false) {
                    // Insert before this line
                    $indent = str_repeat(' ', strlen($lines[$j]) - strlen(ltrim($lines[$j])));
                    $newLines[] = $indent . "->where('entry', 'out')";
                    $fixes++;
                    break;
                }
            }
        }
    }
}

echo "Analysis complete. Found potential fixes needed.\n";
echo "Please review the file manually and use closedTrades() scope instead.\n";
echo "\nRecommendation: Use Deal::closedTrades() for all historical queries.\n";
?>
