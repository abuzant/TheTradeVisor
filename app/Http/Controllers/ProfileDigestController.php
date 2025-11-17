<?php

namespace App\Http\Controllers;

use App\Models\DigestSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProfileDigestController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $digestInput = $request->input('digest', []);
        $allowedAccountIds = $user->tradingAccounts()->pluck('id')->all();

        $desired = [];
        foreach ($digestInput as $key => $frequencies) {
            $accountId = $key === 'global' ? null : (int) $key;

            if (!is_null($accountId) && !in_array($accountId, $allowedAccountIds, true)) {
                continue;
            }

            foreach (['daily', 'weekly'] as $frequency) {
                if (!empty($frequencies[$frequency])) {
                    $desired[] = [
                        'trading_account_id' => $accountId,
                        'frequency' => $frequency,
                    ];
                }
            }
        }

        // Reset and insert fresh preferences
        DigestSubscription::where('user_id', $user->id)->delete();

        foreach ($desired as $item) {
            DigestSubscription::create([
                'user_id' => $user->id,
                'trading_account_id' => $item['trading_account_id'],
                'frequency' => $item['frequency'],
                'is_active' => true,
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'digest-preferences-updated');
    }
}
