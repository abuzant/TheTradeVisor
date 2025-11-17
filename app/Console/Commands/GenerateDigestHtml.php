<?php

namespace App\Console\Commands;

use App\Services\DigestRenderService;
use Illuminate\Console\Command;
use App\Models\User;

class GenerateDigestHtml extends Command
{
    protected $signature = 'digests:generate-html {user?}';
    protected $description = 'Generate a performance digest HTML file for a user (or first admin if none provided)';

    public function handle(DigestRenderService $renderService): int
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

        $this->info("Generating HTML digest for User {$user->id} ({$user->email})...");

        $date = now()->format('Y-m-d');
        $relativePath = $renderService->renderPerformancePage($user, $date);
        $fullPath = $renderService->localPath($relativePath);

        $this->line("Saved to: {$fullPath}");

        if (file_exists($fullPath)) {
            $size = filesize($fullPath);
            $this->line("File size: " . number_format($size / 1024, 2) . ' KB');
            $this->line("Lines: " . trim(shell_exec("wc -l < '$fullPath'")));
        } else {
            // Fallback: search in the nested private path if Laravel Storage created it there
            $fallback = str_replace('/storage/app/', '/storage/app/private/', $fullPath);
            if (file_exists($fallback)) {
                $size = filesize($fallback);
                $this->line("File size: " . number_format($size / 1024, 2) . ' KB');
                $this->line("Lines: " . trim(shell_exec("wc -l < '$fallback'")));
                $this->line("(Stored at: {$fallback})");
            } else {
                $this->error('File not found after save.');
            }
        }

        $this->info('Done. You can open the file in a browser to verify.');

        return self::SUCCESS;
    }
}
