#!/usr/bin/env php
<?php

/**
 * Send email alert using Laravel's mail system (Amazon SES)
 * This ensures emails are sent through configured SES credentials
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Get arguments
$level = $argv[1] ?? 'INFO';
$message = $argv[2] ?? 'No message';
$details = $argv[3] ?? '';
$alertEmail = env('ALERT_EMAIL');

if (empty($alertEmail)) {
    echo "ALERT_EMAIL not configured in .env\n";
    exit(1);
}

// Prepare email content
$timestamp = date('Y-m-d H:M:S UTC');
$hostname = gethostname();

$subject = "[$level] TheTradeVisor Alert: $message";

$body = "TheTradeVisor System Alert\n\n";
$body .= "Level: $level\n";
$body .= "Message: $message\n";
$body .= "Server: $hostname\n";
$body .= "Time: $timestamp\n\n";

if (!empty($details)) {
    $body .= "Details:\n$details\n\n";
}

$body .= "---\n";
$body .= "This is an automated alert from TheTradeVisor system monitoring.\n";
$body .= "To configure alerts, update SLACK_WEBHOOK_URL or ALERT_EMAIL in .env\n";

try {
    Mail::raw($body, function ($mail) use ($alertEmail, $subject) {
        $mail->to($alertEmail)
             ->subject($subject);
    });
    
    echo "Email sent successfully to: $alertEmail\n";
    exit(0);
} catch (Exception $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";
    Log::error('Alert email failed', [
        'error' => $e->getMessage(),
        'email' => $alertEmail,
        'level' => $level,
        'message' => $message,
    ]);
    exit(1);
}
