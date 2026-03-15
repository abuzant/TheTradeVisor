<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestApiEndpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test {api_key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the API endpoint with a given API key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = $this->argument('api_key');
        
        $this->info("Testing API endpoint with key: {$apiKey}");
        $this->newLine();
        
        $testData = [
            'meta' => [
                'test' => true,
                'is_historical' => false,
                'is_first_run' => false,
            ],
            'account' => [
                'account_number' => 'TEST123',
                'account_hash' => hash('sha256', 'TEST123'),
                'broker' => 'Test Broker',
                'server' => 'Test-Server',
                'trade_mode' => 0, // Real account
                'balance' => 10000.00,
                'equity' => 10000.00,
                'margin' => 0.00,
                'free_margin' => 10000.00,
                'leverage' => 100,
                'currency' => 'USD',
            ],
        ];
        
        $url = 'https://thetradevisor.com/api/v1/data/collect';
        
        $this->info("Sending POST request to: {$url}");
        $this->newLine();
        
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'User-Agent' => 'TheTradeVisor-CLI-Test/1.0',
            ])->post($url, $testData);
            
            $statusCode = $response->status();
            $body = $response->json();
            
            $this->table(
                ['Property', 'Value'],
                [
                    ['Status Code', $statusCode],
                    ['Success', $statusCode === 200 ? '✓ YES' : '✗ NO'],
                ]
            );
            
            $this->newLine();
            $this->info('Response Body:');
            $this->line(json_encode($body, JSON_PRETTY_PRINT));
            $this->newLine();
            
            if ($statusCode === 200) {
                $this->info('✓ API endpoint is working correctly!');
                return 0;
            } elseif ($statusCode === 401) {
                $this->error('✗ 401 Unauthorized - API key validation failed');
                $this->newLine();
                $this->warn('Possible causes:');
                $this->line('  1. API key is incorrect or has extra spaces');
                $this->line('  2. User account is inactive');
                $this->line('  3. Cloudflare is blocking the request');
                $this->line('  4. Authorization header is not being sent correctly');
                return 1;
            } elseif ($statusCode === 429) {
                $this->error('✗ 429 Too Many Requests - Rate limit exceeded');
                return 1;
            } else {
                $this->error("✗ Unexpected status code: {$statusCode}");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Request failed with exception:');
            $this->error($e->getMessage());
            return 1;
        }
    }
}
