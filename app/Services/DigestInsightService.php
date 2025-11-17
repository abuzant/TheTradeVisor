<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigestInsightService
{
    private string $llmEndpoint;
    private string $model;
    private bool $llmEnabled;

    public function __construct()
    {
        $this->llmEnabled = config('digest.llm_enabled', false);
        $this->llmEndpoint = config('digest.llm_endpoint', 'http://127.0.0.1:11434/api/generate');
        $this->model = config('digest.model', 'gemma2:2b');
    }

    /**
     * Generate digest insights from analytics data.
     *
     * @param array $data Analytics payload from DigestService
     * @return array Structured insights (intro + sections)
     */
    public function generate(array $data): array
    {
        $insights = [];

        // Intro
        $insights['intro'] = $this->generateIntro($data);

        // Pairs that made money
        $insights['profitable_pairs'] = $this->generateProfitablePairs($data);

        // Pairs with losses
        $insights['losing_pairs'] = $this->generateLosingPairs($data);

        // Volume trend
        $insights['volume_trend'] = $this->generateVolumeTrend($data);

        // Best/worst times
        $insights['time_insights'] = $this->generateTimeInsights($data);

        // Riskiest trades
        $insights['risk_summary'] = $this->generateRiskSummary($data);

        // Long-running trades
        $insights['long_positions'] = $this->generateLongPositions($data);

        return $insights;
    }

    private function generateIntro(array $data): string
    {
        $period = $data['days'] === 1 ? 'today' : 'this week';
        $trades = $data['total_trades'];
        $pnl = $data['total_profit'];

        if ($this->llmEnabled) {
            $prompt = "Write a 1-sentence intro for a trading digest. Context: {$period} the user made {$trades} trades with a total PnL of {$pnl}. Keep it brief and friendly.";
            return $this->callLlm($prompt) ?? "Here's your trading summary for {$period}.";
        }

        return "Here's your trading summary for {$period}.";
    }

    private function generateProfitablePairs(array $data): string
    {
        $top = $data['top_pairs'] ?? [];
        if ($top->isEmpty()) return 'No profitable pairs in this period.';

        $list = $top->pluck('symbol')->implode(', ');
        $total = $top->sum('profit');

        if ($this->llmEnabled) {
            $prompt = "Explain in one short sentence: these pairs made the most money: {$list}. Total profit: {$total}. Keep it simple.";
            return $this->callLlm($prompt) ?? "Your most profitable pairs were {$list}, generating a total profit of {$total}.";
        }

        return "Your most profitable pairs were {$list}, generating a total profit of {$total}.";
    }

    private function generateLosingPairs(array $data): string
    {
        $bottom = $data['bottom_pairs'] ?? [];
        if ($bottom->isEmpty()) return 'No losing pairs in this period.';

        $list = $bottom->pluck('symbol')->implode(', ');
        $total = $bottom->sum('profit');

        if ($this->llmEnabled) {
            $prompt = "Explain in one short sentence: these pairs lost the most money: {$list}. Total loss: {$total}. Keep it simple.";
            return $this->callLlm($prompt) ?? "Your biggest losses came from {$list}, with a combined loss of {$total}.";
        }

        return "Your biggest losses came from {$list}, with a combined loss of {$total}.";
    }

    private function generateVolumeTrend(array $data): string
    {
        $change = $data['volume_change_percent'] ?? 0;
        $direction = $change > 0 ? 'increased' : ($change < 0 ? 'decreased' : 'remained steady');

        if ($this->llmEnabled) {
            $prompt = "Explain in one sentence: trading volume {$direction} by {$change}% this period. Keep it simple.";
            return $this->callLlm($prompt) ?? "Your trading volume {$direction} by {$change}% this period.";
        }

        return "Your trading volume {$direction} by {$change}% this period.";
    }

    private function generateTimeInsights(array $data): string
    {
        $best = $data['best_time'] ?? [];
        $worst = $data['worst_time'] ?? [];

        if (empty($best) && empty($worst)) return 'No clear time-based patterns this period.';

        $parts = [];
        if ($best) $parts[] = "best performance around {$best['label']}";
        if ($worst) $parts[] = "worst around {$worst['label']}";

        if ($this->llmEnabled) {
            $prompt = "Explain in one sentence: trading performance varied by time: " . implode(', ', $parts) . ". Keep it simple.";
            return $this->callLlm($prompt) ?? "You traded best around {$best['label']} and worst around {$worst['label']}.";
        }

        return "You traded best around {$best['label']} and worst around {$worst['label']}.";
    }

    private function generateRiskSummary(array $data): string
    {
        $risky = $data['riskiest_symbols'] ?? [];
        if (empty($risky)) return 'No high-risk symbols detected.';

        $list = implode(', ', array_column($risky, 'symbol'));

        if ($this->llmEnabled) {
            $prompt = "Explain in one short sentence: these symbols carried the most risk: {$list}. Keep it simple.";
            return $this->callLlm($prompt) ?? "Your riskiest trades were in {$list}.";
        }

        return "Your riskiest trades were in {$list}.";
    }

    private function generateLongPositions(array $data): string
    {
        $long = $data['long_positions'] ?? [];
        if (empty($long)) return 'No positions have been open for an extended period.';

        $count = count($long);

        if ($this->llmEnabled) {
            $prompt = "Write a 1-sentence nudge to review {$count} trades that have been open for a long time. Keep it simple and actionable.";
            return $this->callLlm($prompt) ?? "Consider reviewing {$count} trades that have been open for a while.";
        }

        return "Consider reviewing {$count} trades that have been open for a while.";
    }

    private function callLlm(string $prompt): ?string
    {
        try {
            $response = Http::timeout(10)->post($this->llmEndpoint, [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
            ]);

            if (!$response->successful()) {
                Log::warning('DigestInsightService: LLM call failed', ['status' => $response->status()]);
                return null;
            }

            $data = $response->json();
            return $data['response'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('DigestInsightService: LLM exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
