<?php

namespace App\Console\Commands;

use App\Services\DigestService;
use App\Services\DigestInsightService;
use Illuminate\Console\Command;
use App\Models\User;

class TestDigest extends Command
{
    protected $signature = 'digests:test {user?}';
    protected $description = 'Generate and display a test digest for a user (or first admin if none provided)';

    public function handle(DigestService $digestService, DigestInsightService $insightService): int
    {
        $userId = $this->argument('user');
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User {$userId} not found.");
                return self::FAILURE;
            }
        } else {
            $user = User::where('is_admin', true)->first() ?: User::first();
            if (!$user) {
                $this->error('No users found.');
                return self::FAILURE;
            }
        }

        $this->info("Generating test digest for User {$user->id} ({$user->email})...");

        $days = 7;
        $data = $digestService->buildUserDigest($user, $days);
        $insights = $insightService->generate($data);

        $this->line("\n--- Raw Data ---");
        $this->line(json_encode($data, JSON_PRETTY_PRINT));

        $this->line("\n--- Insights ---");
        foreach ($insights as $section => $text) {
            $this->line("[$section] $text");
        }

        return self::SUCCESS;
    }
}
