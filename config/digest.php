<?php

return [
    // Enable/disable digest generation and sending globally
    'enabled' => env('DIGEST_ENABLED', false),

    // LLM integration (optional)
    'llm_enabled' => env('DIGEST_LLM_ENABLED', false),
    'llm_endpoint' => env('DIGEST_LLM_ENDPOINT', 'http://127.0.0.1:11434/api/generate'),
    'model' => env('DIGEST_MODEL', 'gemma2:2b'),

    // Ollama service info (for admin panel)
    'ollama' => [
        'service_name' => 'ollama.service',
        'models_path' => '/usr/share/ollama/.ollama/models',
        'api_url' => 'http://127.0.0.1:11434',
    ],
];
