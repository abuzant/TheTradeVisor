<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DigestSubscription;
use App\Mail\DailyWeeklyDigestMail;
use App\Services\DigestService;
use Illuminate\Support\Facades\Mail;

class SendDigests extends Command
{
    protected $signature = 'digests:send {frequency : daily or weekly}';

    protected $description = 'Send daily or weekly trading digests to subscribed users';

    public function handle(DigestService $digestService): int
    {
        $frequency = $this->argument('frequency');
        if (!in_array($frequency, ['daily', 'weekly'], true)) {
            $this->error('Frequency must be daily or weekly');
            return self::FAILURE;
        }

        $this->info("Sending {$frequency} digests...");

        $subs = DigestSubscription::with('user')
            ->whereNull('trading_account_id')
            ->where('frequency', $frequency)
            ->where('is_active', true)
            ->get()
            ->groupBy('user_id');

        $days = $frequency === 'daily' ? 1 : 7;

        foreach ($subs as $userId => $userSubs) {
            $user = $userSubs->first()->user;
            if (!$user || !$user->email) {
                continue;
            }

            $digest = $digestService->buildUserDigest($user, $days);

            Mail::to($user->email)->queue(new DailyWeeklyDigestMail($user, $digest, $frequency));
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
