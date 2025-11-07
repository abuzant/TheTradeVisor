<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        // Get some impressive stats for the landing page
        $stats = [
            'total_traders' => User::count(),
            'total_accounts' => TradingAccount::count(),
            'total_trades' => Deal::count(),
            'countries' => TradingAccount::whereNotNull('detected_country')->distinct('detected_country')->count('detected_country'),
        ];
        
        return view('landing', compact('stats'));
    }
}
