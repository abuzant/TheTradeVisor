<?php

namespace App\Observers;

use App\Models\Deal;

class DealObserver
{
    public function creating(Deal $deal): void
    {
        // Auto-categorize deal
        $deal->deal_category = $this->categorizeDeal($deal);
    }

    private function categorizeDeal(Deal $deal): string
    {
        // Check entry type
        if ($deal->entry === 'in' || $deal->entry === 'out' || $deal->entry === 'inout') {
            return 'trade';
        }

        // Check by type
        if (str_contains(strtolower($deal->type), 'balance')) {
            if ($deal->profit > 0) {
                return 'deposit';
            } else {
                return 'withdrawal';
            }
        }

        // Check commission/fee
        if ($deal->commission != 0) {
            return 'commission';
        }

        if ($deal->fee != 0) {
            return 'fee';
        }

        if ($deal->swap != 0) {
            return 'swap';
        }

        return 'trade'; // Default
    }
}
