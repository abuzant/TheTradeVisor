<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigestControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $enabled = config('digest.enabled', false);
        $llmEnabled = config('digest.llm_enabled', false);
        $llmEndpoint = config('digest.llm_endpoint');
        $model = config('digest.model');

        // Last run stats (placeholder for now)
        $lastRun = now()->subMinutes(15)->toDateTimeString(); // dummy
        $sentCount = 0;
        $errors = [];

        return view('admin.digest-control', compact(
            'enabled',
            'llmEnabled',
            'llmEndpoint',
            'model',
            'lastRun',
            'sentCount',
            'errors'
        ));
    }

    public function toggle(Request $request)
    {
        $this->ensureAdmin($request);

        $enabled = $request->boolean('enabled');
        $this->updateEnv('DIGEST_ENABLED', $enabled ? 'true' : 'false');

        return redirect()->route('admin.digest-control.index')->with('status', 'Digest feature updated.');
    }

    public function toggleLlm(Request $request)
    {
        $this->ensureAdmin($request);

        $enabled = $request->boolean('llm_enabled');
        $this->updateEnv('DIGEST_LLM_ENABLED', $enabled ? 'true' : 'false');

        return redirect()->route('admin.digest-control.index')->with('status', 'LLM integration updated.');
    }

    public function testGenerate(Request $request)
    {
        $this->ensureAdmin($request);

        try {
            $output = shell_exec('php artisan digests:test 2>&1');
            return redirect()->route('admin.digest-control.index')->with('test_output', $output);
        } catch (\Throwable $e) {
            return redirect()->route('admin.digest-control.index')->with('test_error', $e->getMessage());
        }
    }

    private function ensureAdmin(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            abort(403);
        }
    }

    private function updateEnv(string $key, string $value)
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) return;

        $content = file_get_contents($envPath);
        $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content, -1, $count);
        if ($count === 0) {
            $content .= "\n{$key}={$value}\n";
        }
        file_put_contents($envPath, $content);
    }
}
