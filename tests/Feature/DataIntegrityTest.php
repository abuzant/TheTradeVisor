<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\TradingDataValidationService;
use App\Jobs\ProcessTradingData;
use App\Jobs\ProcessHistoricalData;
use App\Models\Deal;
use Carbon\Carbon;

class DataIntegrityTest extends TestCase
{
    /**
     * Test that validation service converts time_msc to time
     */
    public function test_validation_service_converts_time_msc()
    {
        $validator = new TradingDataValidationService();
        
        // Simulate EA data with only time_msc (no time field)
        $dealData = [
            'ticket' => 123456,
            'symbol' => 'EURUSD',
            'type' => 'buy',
            'entry' => 'out',
            'volume' => 1.0,
            'price' => 1.0850,
            'profit' => 50.00,
            'time' => '', // Empty time field (like historical data)
            'time_msc' => 1700000000000, // Milliseconds timestamp
        ];
        
        $validated = $validator->validateDeal($dealData);
        
        // Assert time was converted from time_msc
        $this->assertInstanceOf(Carbon::class, $validated['time']);
        $this->assertNotNull($validated['time']);
        $this->assertEquals(1700000000, $validated['time']->timestamp);
        
        echo "✅ Validation service correctly converts time_msc to time\n";
    }
    
    /**
     * Test that validation service rejects deals without timestamp
     */
    public function test_validation_service_requires_timestamp()
    {
        $validator = new TradingDataValidationService();
        
        // Simulate EA data with NO timestamp at all
        $dealData = [
            'ticket' => 123456,
            'symbol' => 'EURUSD',
            'type' => 'buy',
            'entry' => 'out',
            'volume' => 1.0,
            'price' => 1.0850,
            'profit' => 50.00,
            'time' => '',
            'time_msc' => null,
        ];
        
        try {
            $validated = $validator->validateDeal($dealData);
            $this->fail('Should have thrown exception for missing timestamp');
        } catch (\Exception $e) {
            $this->assertStringContainsString('No valid timestamp', $e->getMessage());
            echo "✅ Validation service correctly rejects deals without timestamp\n";
        }
    }
    
    /**
     * Test that validation service validates required fields
     */
    public function test_validation_service_validates_required_fields()
    {
        $validator = new TradingDataValidationService();
        
        // Missing ticket
        try {
            $validator->validateDeal(['symbol' => 'EURUSD']);
            $this->fail('Should have thrown exception for missing ticket');
        } catch (\Exception $e) {
            $this->assertStringContainsString('ticket is required', $e->getMessage());
        }
        
        // Missing symbol
        try {
            $validator->validateDeal(['ticket' => 123]);
            $this->fail('Should have thrown exception for missing symbol');
        } catch (\Exception $e) {
            $this->assertStringContainsString('symbol is required', $e->getMessage());
        }
        
        echo "✅ Validation service correctly validates required fields\n";
    }
    
    /**
     * Test that deals created through validation never have NULL time
     */
    public function test_deals_never_have_null_time()
    {
        $validator = new TradingDataValidationService();
        
        // Test with time_msc only
        $dealData = [
            'ticket' => 999999,
            'symbol' => 'GBPUSD',
            'type' => 'sell',
            'entry' => 'out',
            'volume' => 0.5,
            'price' => 1.2500,
            'profit' => -25.00,
            'time' => '',
            'time_msc' => Carbon::now()->timestamp * 1000,
        ];
        
        $validated = $validator->validateDeal($dealData);
        
        $this->assertNotNull($validated['time']);
        $this->assertInstanceOf(Carbon::class, $validated['time']);
        
        echo "✅ Deals created through validation never have NULL time\n";
    }
}
