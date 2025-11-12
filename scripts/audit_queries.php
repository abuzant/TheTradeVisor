<?php

/**
 * Audit all controllers for dangerous unbounded queries
 */

$controllersPath = '/www/app/Http/Controllers';
$dangerousPatterns = [
    'get()' => 'Unbounded get() without limit/take/paginate',
    'all()' => 'Unbounded all() - loads everything',
];

$findings = [];
$totalIssues = 0;

// Recursively scan all controller files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($controllersPath)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    
    $filepath = $file->getPathname();
    $content = file_get_contents($filepath);
    $lines = explode("\n", $content);
    
    foreach ($lines as $lineNum => $line) {
        $lineNum++; // 1-indexed
        
        // Check for ->get() without limit/take/paginate/first
        if (preg_match('/->get\(\)/', $line)) {
            // Look back a few lines to see if there's a limit/take/paginate
            $context = implode("\n", array_slice($lines, max(0, $lineNum - 10), 10));
            
            if (!preg_match('/->(?:limit|take|paginate|first)\(/', $context)) {
                $findings[] = [
                    'file' => str_replace('/www/', '', $filepath),
                    'line' => $lineNum,
                    'code' => trim($line),
                    'issue' => 'Unbounded ->get()',
                    'severity' => 'HIGH',
                ];
                $totalIssues++;
            }
        }
        
        // Check for ->all()
        if (preg_match('/->all\(\)/', $line)) {
            $findings[] = [
                'file' => str_replace('/www/', '', $filepath),
                'line' => $lineNum,
                'code' => trim($line),
                'issue' => 'Unbounded ->all()',
                'severity' => 'CRITICAL',
            ];
            $totalIssues++;
        }
    }
}

// Sort by severity and file
usort($findings, function($a, $b) {
    if ($a['severity'] !== $b['severity']) {
        return $a['severity'] === 'CRITICAL' ? -1 : 1;
    }
    return strcmp($a['file'], $b['file']);
});

// Output results
echo "===========================================\n";
echo "DATABASE QUERY AUDIT REPORT\n";
echo "===========================================\n\n";
echo "Total Issues Found: {$totalIssues}\n\n";

$byFile = [];
foreach ($findings as $finding) {
    $byFile[$finding['file']][] = $finding;
}

foreach ($byFile as $file => $issues) {
    echo "\n📄 {$file}\n";
    echo str_repeat('-', 80) . "\n";
    
    foreach ($issues as $issue) {
        $severity = $issue['severity'] === 'CRITICAL' ? '🔴' : '🟠';
        echo "  {$severity} Line {$issue['line']}: {$issue['issue']}\n";
        echo "     " . substr($issue['code'], 0, 100) . "\n";
    }
}

echo "\n\n===========================================\n";
echo "SUMMARY BY CONTROLLER\n";
echo "===========================================\n\n";

$summary = [];
foreach ($byFile as $file => $issues) {
    $controller = basename($file);
    $summary[$controller] = count($issues);
}

arsort($summary);

foreach ($summary as $controller => $count) {
    echo sprintf("%-50s %3d issues\n", $controller, $count);
}

echo "\n";
