<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    /**
     * Export data to CSV
     */
    public function exportToCsv(Collection $data, array $headers, string $filename = 'export.csv')
    {
        $callback = function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($file, $headers);
            
            // Write data
            foreach ($data as $row) {
                fputcsv($file, is_array($row) ? $row : (array) $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
    
    /**
     * Export data to PDF
     */
    public function exportToPdf($view, array $data, string $filename = 'export.pdf', $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', $orientation)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);
        
        return $pdf->download($filename);
    }
    
    /**
     * Prepare trades data for export
     */
    public function prepareTradesForExport($deals, $displayCurrency = 'USD')
    {
        return $deals->map(function($deal) use ($displayCurrency) {
            return [
                'Date/Time' => $deal->time ? \Carbon\Carbon::parse($deal->time)->format('Y-m-d H:i:s') : 'N/A',
                'Symbol' => $deal->symbol ?? 'N/A',
                'Type' => $deal->type ?? 'N/A',
                'Volume' => number_format($deal->volume ?? 0, 2),
                'Price' => number_format($deal->price ?? 0, 5),
                'Profit (' . $displayCurrency . ')' => number_format($deal->profit ?? 0, 2),
                'Commission' => number_format($deal->commission ?? 0, 2),
                'Swap' => number_format($deal->swap ?? 0, 2),
                'Broker' => $deal->tradingAccount->broker_name ?? 'N/A',
                'Account' => $deal->tradingAccount->account_number ?? 'N/A',
            ];
        });
    }
    
    /**
     * Prepare account summary for export
     */
    public function prepareAccountSummary($account, $deals, $displayCurrency = 'USD')
    {
        $totalProfit = $deals->sum('profit');
        $totalVolume = $deals->sum('volume');
        $winningTrades = $deals->where('profit', '>', 0)->count();
        $losingTrades = $deals->where('profit', '<', 0)->count();
        $totalTrades = $deals->count();
        $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 2) : 0;
        
        return [
            'Account Information' => [
                'Account Number' => $account->account_number,
                'Broker' => $account->broker_name,
                'Status' => $account->is_active ? 'Active' : 'Paused',
                'Created' => $account->created_at->format('Y-m-d H:i:s'),
            ],
            'Trading Statistics' => [
                'Total Trades' => $totalTrades,
                'Winning Trades' => $winningTrades,
                'Losing Trades' => $losingTrades,
                'Win Rate' => $winRate . '%',
                'Total Profit (' . $displayCurrency . ')' => number_format($totalProfit, 2),
                'Total Volume' => number_format($totalVolume, 2),
                'Average Profit per Trade' => $totalTrades > 0 ? number_format($totalProfit / $totalTrades, 2) : '0.00',
            ],
        ];
    }
    
    /**
     * Create a complete account data export (for account deletion)
     */
    public function createAccountDataExport($user, $displayCurrency = 'USD')
    {
        $accounts = $user->tradingAccounts()->with('deals')->get();
        $allDeals = collect();
        
        foreach ($accounts as $account) {
            $allDeals = $allDeals->merge($account->deals);
        }
        
        return [
            'user' => [
                'Name' => $user->name,
                'Email' => $user->email,
                'Display Currency' => $displayCurrency,
                'Member Since' => $user->created_at->format('Y-m-d H:i:s'),
            ],
            'accounts' => $accounts->map(function($account) {
                return [
                    'Account Number' => $account->account_number,
                    'Broker' => $account->broker_name,
                    'Status' => $account->is_active ? 'Active' : 'Paused',
                    'Created' => $account->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'deals' => $this->prepareTradesForExport($allDeals, $displayCurrency),
        ];
    }
}
