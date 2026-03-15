<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportController extends Controller
{
    protected $exportService;
    
    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }
    
    /**
     * Export trades to CSV
     */
    public function exportTradesCsv(Request $request)
    {
        $user = $request->user();
        // Multi-account context: Always export in USD
        $displayCurrency = 'USD';
        
        $query = Deal::closedTrades()
        ->whereHas('tradingAccount', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('symbol', '!=', 'UNKNOWN')
        ->with('tradingAccount');
        
        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->where('time', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        
        if ($request->filled('end_date')) {
            $query->where('time', '<=', Carbon::parse($request->end_date)->endOfDay());
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $query->where('symbol', 'like', '%' . $request->search . '%');
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', 'like', $request->type . '%');
        }
        
        // SAFETY: Limit exports to 10,000 records to prevent memory exhaustion
        $deals = $query->orderBy('time', 'desc')->limit(10000)->get();
        
        $data = $this->exportService->prepareTradesForExport($deals, $displayCurrency);
        
        $headers = [
            'Date/Time',
            'Symbol',
            'Type',
            'Volume',
            'Price',
            'Profit (' . $displayCurrency . ')',
            'Commission',
            'Swap',
            'Broker',
            'Account',
        ];
        
        $filename = 'trades_' . now()->format('Y-m-d_His') . '.csv';
        
        return $this->exportService->exportToCsv($data, $headers, $filename);
    }
    
    /**
     * Export trades to PDF
     */
    public function exportTradesPdf(Request $request)
    {
        $user = $request->user();
        // Multi-account context: Always export in USD
        $displayCurrency = 'USD';
        
        $query = Deal::closedTrades()
        ->whereHas('tradingAccount', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('symbol', '!=', 'UNKNOWN')
        ->with('tradingAccount');
        
        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->where('time', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        
        if ($request->filled('end_date')) {
            $query->where('time', '<=', Carbon::parse($request->end_date)->endOfDay());
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $query->where('symbol', 'like', '%' . $request->search . '%');
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', 'like', $request->type . '%');
        }
        
        // SAFETY: Limit exports to 10,000 records to prevent memory exhaustion
        $deals = $query->orderBy('time', 'desc')->limit(10000)->get();
        
        $data = [
            'deals' => $deals,
            'user' => $user,
            'displayCurrency' => $displayCurrency,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];
        
        $filename = 'trades_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $this->exportService->exportToPdf('exports.trades-pdf', $data, $filename, 'landscape');
    }
    
    /**
     * Export symbol analysis to CSV
     */
    public function exportSymbolCsv(Request $request, $symbol)
    {
        $user = $request->user();
        // Multi-account context: Always export in USD
        $displayCurrency = 'USD';
        
        $query = Deal::closedTrades()
        ->whereHas('tradingAccount', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('symbol', 'like', '%' . $symbol . '%')
        ->with('tradingAccount');
        
        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->where('time', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        
        if ($request->filled('end_date')) {
            $query->where('time', '<=', Carbon::parse($request->end_date)->endOfDay());
        }
        
        // SAFETY: Limit exports to 10,000 records to prevent memory exhaustion
        $deals = $query->orderBy('time', 'desc')->limit(10000)->get();
        
        $data = $this->exportService->prepareTradesForExport($deals, $displayCurrency);
        
        $headers = [
            'Date/Time',
            'Symbol',
            'Type',
            'Volume',
            'Price',
            'Profit (' . $displayCurrency . ')',
            'Commission',
            'Swap',
            'Broker',
            'Account',
        ];
        
        $filename = strtoupper($symbol) . '_trades_' . now()->format('Y-m-d_His') . '.csv';
        
        return $this->exportService->exportToCsv($data, $headers, $filename);
    }
    
    /**
     * Export dashboard summary to CSV
     */
    public function exportDashboardCsv(Request $request)
    {
        $user = $request->user();
        // Multi-account context: Always export in USD
        $displayCurrency = 'USD';
        
        // SAFETY: Limit accounts and eager load with constraints
        $accounts = $user->tradingAccounts()
            ->with(['deals' => function($query) {
                $query->limit(1000); // Limit deals per account
            }])
            ->limit(50)
            ->get();
        
        $data = $accounts->map(function($account) use ($displayCurrency) {
            $deals = $account->deals;
            $totalProfit = $deals->sum('profit');
            $totalVolume = $deals->sum('volume');
            $winningTrades = $deals->where('profit', '>', 0)->count();
            $totalTrades = $deals->count();
            
            return [
                'Account Number' => $account->account_number,
                'Broker' => $account->broker_name,
                'Status' => $account->is_active ? 'Active' : 'Paused',
                'Total Trades' => $totalTrades,
                'Winning Trades' => $winningTrades,
                'Total Profit (' . $displayCurrency . ')' => number_format($totalProfit, 2),
                'Total Volume' => number_format($totalVolume, 2),
                'Created' => $account->created_at->format('Y-m-d H:i:s'),
            ];
        });
        
        $headers = [
            'Account Number',
            'Broker',
            'Status',
            'Total Trades',
            'Winning Trades',
            'Total Profit (' . $displayCurrency . ')',
            'Total Volume',
            'Created',
        ];
        
        $filename = 'dashboard_summary_' . now()->format('Y-m-d_His') . '.csv';
        
        return $this->exportService->exportToCsv($data, $headers, $filename);
    }
    
    /**
     * Export account data (for account deletion)
     */
    public function exportAccountData(Request $request)
    {
        $user = $request->user();
        $displayCurrency = 'USD'; // Always USD for multi-account exports
        
        $exportData = $this->exportService->createAccountDataExport($user, $displayCurrency);
        
        // Create a comprehensive CSV with all user data
        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // User Information
            fputcsv($file, ['USER INFORMATION']);
            foreach ($exportData['user'] as $key => $value) {
                fputcsv($file, [$key, $value]);
            }
            fputcsv($file, []); // Empty line
            
            // Accounts
            fputcsv($file, ['TRADING ACCOUNTS']);
            if ($exportData['accounts']->isNotEmpty()) {
                fputcsv($file, array_keys($exportData['accounts']->first()));
                foreach ($exportData['accounts'] as $account) {
                    fputcsv($file, array_values($account));
                }
            }
            fputcsv($file, []); // Empty line
            
            // All Trades
            fputcsv($file, ['ALL TRADES']);
            if ($exportData['deals']->isNotEmpty()) {
                fputcsv($file, array_keys($exportData['deals']->first()));
                foreach ($exportData['deals'] as $deal) {
                    fputcsv($file, array_values($deal));
                }
            }
            
            fclose($file);
        };
        
        $filename = 'account_data_' . $user->id . '_' . now()->format('Y-m-d_His') . '.csv';
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
