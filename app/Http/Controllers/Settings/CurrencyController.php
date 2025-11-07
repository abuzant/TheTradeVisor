<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Show currency settings page
     */
    public function edit()
    {
        $currencyService = app(CurrencyService::class);
        $currencies = $currencyService->getSupportedCurrencies();
        $currentCurrency = auth()->user()->display_currency ?? 'USD';

        return view('settings.currency', compact('currencies', 'currentCurrency'));
    }


    public function update(Request $request)
    {
        // Log what we received
        \Log::info('Currency update attempt', [
            'user_id' => $request->user()->id,
            'old_currency' => $request->user()->display_currency,
            'new_currency' => $request->display_currency,
            'all_data' => $request->all()
        ]);
    
        $validated = $request->validate([
            'display_currency' => 'required|string|size:3|in:USD,EUR,GBP,AED,JPY,CHF,AUD,CAD,NZD,SGD',
        ]);
    
        // Update the user
        $updated = $request->user()->update([
            'display_currency' => strtoupper($validated['display_currency']),
        ]);
    
        // Log the result
        \Log::info('Currency update result', [
            'updated' => $updated,
            'new_value_in_db' => $request->user()->fresh()->display_currency
        ]);
    
        return redirect()
            ->back()
            ->with('success', 'Display currency updated to ' . strtoupper($validated['display_currency']));
    }

}
