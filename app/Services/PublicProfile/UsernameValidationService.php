<?php

namespace App\Services\PublicProfile;

use App\Models\User;

class UsernameValidationService
{
    /**
     * Reserved usernames that cannot be used
     */
    private const RESERVED_USERNAMES = [
        // System
        'admin', 'administrator', 'root', 'superuser', 'moderator', 'mod', 'support', 'help', 
        'contact', 'info', 'sales', 'billing', 'api', 'www', 'ftp', 'mail', 'smtp', 'pop', 
        'imap', 'webmaster', 'postmaster', 'hostmaster', 'abuse', 'noreply', 'no-reply',
        
        // Platform
        'thetradevisor', 'tradevisor', 'enterprise', 'leaderboard', 'top-traders', 'toptraders',
        'profiles', 'public', 'private', 'settings', 'dashboard', 'analytics', 'performance',
        'broker', 'brokers', 'account', 'accounts', 'trade', 'trades', 'trading', 'trader',
        'traders', 'user', 'users', 'profile', 'export', 'import', 'download', 'upload',
        
        // Common names
        'john', 'jane', 'test', 'demo', 'guest', 'anonymous', 'official', 'verified', 
        'premium', 'pro', 'elite', 'vip', 'staff', 'team', 'service', 'system', 'bot',
        'null', 'undefined', 'none', 'unknown', 'default', 'example', 'sample', 'temp',
        
        // Social platforms
        'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok', 'telegram',
        'whatsapp', 'snapchat', 'reddit', 'pinterest', 'tumblr', 'discord', 'slack',
        
        // Major brokers
        'xm', 'exness', 'ic-markets', 'icmarkets', 'pepperstone', 'fxpro', 'hotforex',
        'fbs', 'roboforex', 'octafx', 'tickmill', 'fxtm', 'forex', 'forexcom', 'oanda',
        'ig', 'plus500', 'etoro', 'avatrade', 'fxcm', 'saxo', 'interactive', 'td-ameritrade',
        
        // Technical
        'webhook', 'callback', 'oauth', 'auth', 'login', 'logout', 'register', 'signup',
        'signin', 'signout', 'password', 'reset', 'verify', 'confirmation', 'activate',
        'deactivate', 'delete', 'remove', 'update', 'edit', 'create', 'new', 'old',
        
        // Routes
        'home', 'about', 'contact-us', 'terms', 'privacy', 'legal', 'faq', 'blog', 'news',
        'pricing', 'plans', 'features', 'documentation', 'docs', 'wiki', 'forum', 'community',
        
        // Status
        'online', 'offline', 'active', 'inactive', 'pending', 'approved', 'rejected', 'banned',
        'suspended', 'deleted', 'archived', 'draft', 'published', 'hidden', 'visible',
        
        // Actions
        'search', 'find', 'filter', 'sort', 'view', 'show', 'hide', 'list', 'index',
        'details', 'summary', 'report', 'statistics', 'stats', 'chart', 'graph', 'data',
        
        // Security
        'security', 'secure', 'ssl', 'https', 'certificate', 'encryption', 'decrypt', 'encrypt',
        'hash', 'token', 'session', 'cookie', 'cache', 'redis', 'database', 'db', 'sql',
        
        // File operations
        'file', 'files', 'folder', 'directory', 'path', 'url', 'link', 'redirect', 'forward',
        'backup', 'restore', 'copy', 'move', 'rename', 'compress', 'extract', 'zip', 'tar',
        
        // Time
        'today', 'yesterday', 'tomorrow', 'now', 'current', 'latest', 'recent', 'old', 'new',
        'past', 'future', 'history', 'archive', 'log', 'logs', 'event', 'events',
        
        // Numbers
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
        'hundred', 'thousand', 'million', 'billion', 'first', 'second', 'third', 'last',
        
        // Boolean
        'true', 'false', 'yes', 'no', 'on', 'off', 'enabled', 'disabled', 'allow', 'deny',
        'grant', 'revoke', 'accept', 'decline', 'approve', 'reject', 'confirm', 'cancel',
        
        // Generic
        'page', 'site', 'website', 'web', 'app', 'application', 'platform', 'portal', 'gateway',
        'interface', 'client', 'server', 'host', 'domain', 'subdomain', 'endpoint', 'resource',
        
        // MT4/MT5
        'mt4', 'mt5', 'metatrader', 'meta-trader', 'metaquotes', 'mql4', 'mql5', 'ea', 'expert',
        'advisor', 'indicator', 'script', 'robot', 'signal', 'signals', 'copy', 'copytrade',
        
        // Trading terms
        'buy', 'sell', 'long', 'short', 'position', 'positions', 'order', 'orders', 'deal',
        'deals', 'profit', 'loss', 'pnl', 'balance', 'equity', 'margin', 'leverage', 'lot',
        'lots', 'pip', 'pips', 'spread', 'commission', 'swap', 'fee', 'fees', 'deposit',
        'withdrawal', 'transfer', 'bonus', 'promotion', 'contest', 'competition', 'demo', 'real',
        
        // Currencies
        'usd', 'eur', 'gbp', 'jpy', 'chf', 'aud', 'cad', 'nzd', 'btc', 'eth', 'crypto',
        'forex', 'fx', 'currency', 'currencies', 'pair', 'pairs', 'symbol', 'symbols',
        
        // Analysis
        'analysis', 'technical', 'fundamental', 'sentiment', 'trend', 'trends', 'pattern',
        'patterns', 'strategy', 'strategies', 'system', 'systems', 'method', 'methods',
        
        // Additional common
        'master', 'slave', 'primary', 'secondary', 'main', 'backup', 'test', 'testing',
        'development', 'dev', 'staging', 'production', 'prod', 'local', 'localhost',
    ];

    /**
     * Profanity words that cannot be used
     * Note: This is a basic list. In production, use a comprehensive profanity filter library
     */
    private const PROFANITY_WORDS = [
        // Racial slurs and hate speech (censored versions for code)
        'n*gger', 'n*gga', 'f*ggot', 'f*g', 'k*ke', 'ch*nk', 'sp*c', 'w*tback', 'r*ghead',
        'sand-n*gger', 'towel-head', 'camel-jockey', 'g*ok', 'sl*nt', 'z*pperhead',
        
        // Sexual content
        'f*ck', 'f*cking', 'f*cker', 'f*cked', 'sh*t', 'b*tch', 'c*nt', 'p*ssy', 'c*ck',
        'd*ck', 'p*nis', 'v*gina', 'a**hole', 'a**', 'b*stard', 'wh*re', 'sl*t', 'tw*t',
        'p*ss', 'cr*p', 'd*mn', 'h*ll', 'pr*ck', 'b*llocks', 'b*lls', 'scr*w', 'suck',
        
        // Offensive terms
        'retard', 'retarded', 'idiot', 'moron', 'imbecile', 'stupid', 'dumb', 'loser',
        'nazi', 'hitler', 'terrorist', 'jihad', 'isis', 'alqaeda', 'taliban',
        
        // Drug references
        'cocaine', 'heroin', 'meth', 'crack', 'weed', 'marijuana', 'cannabis', 'drug',
        'dealer', 'pusher', 'junkie', 'addict', 'high', 'stoned', 'baked',
        
        // Violence
        'kill', 'murder', 'rape', 'molest', 'abuse', 'torture', 'genocide', 'massacre',
        'suicide', 'bomb', 'terrorist', 'weapon', 'gun', 'shoot', 'stab', 'attack',
        
        // Scam/Fraud
        'scam', 'scammer', 'fraud', 'fraudster', 'ponzi', 'pyramid', 'cheat', 'cheater',
        'hack', 'hacker', 'steal', 'thief', 'rob', 'robber', 'criminal', 'illegal',
        
        // Spam
        'spam', 'spammer', 'bot', 'fake', 'phishing', 'malware', 'virus', 'trojan',
        
        // Add more as needed...
    ];

    /**
     * Validate username format and availability
     */
    public function validate(string $username): array
    {
        $errors = [];

        // Check length
        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be between 3 and 50 characters.';
        }

        // Check format (letters, numbers, underscores only)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        }

        // Check if reserved
        if ($this->isReserved($username)) {
            $errors[] = 'This username is reserved and cannot be used.';
        }

        // Check if profanity
        if ($this->containsProfanity($username)) {
            $errors[] = 'This username contains inappropriate content.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Check if username is reserved
     */
    public function isReserved(string $username): bool
    {
        return in_array(strtolower($username), self::RESERVED_USERNAMES);
    }

    /**
     * Check if username contains profanity
     */
    public function containsProfanity(string $username): bool
    {
        $usernameLower = strtolower($username);
        
        foreach (self::PROFANITY_WORDS as $word) {
            // Remove asterisks for matching
            $cleanWord = str_replace('*', '', $word);
            if (stripos($usernameLower, $cleanWord) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if username is available
     */
    public function isAvailable(string $username): bool
    {
        return !User::where('public_username', $username)->exists();
    }

    /**
     * Get suggested usernames if taken
     */
    public function getSuggestions(string $username, int $count = 5): array
    {
        $suggestions = [];
        
        for ($i = 0; $i < $count; $i++) {
            $randomNumber = rand(1, 999);
            $suggestion = $username . '_' . str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
            
            if ($this->isAvailable($suggestion) && !$this->isReserved($suggestion)) {
                $suggestions[] = $suggestion;
            }
        }
        
        return $suggestions;
    }
}
