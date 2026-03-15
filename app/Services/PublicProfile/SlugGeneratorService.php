<?php

namespace App\Services\PublicProfile;

use Illuminate\Support\Str;

class SlugGeneratorService
{
    /**
     * Generate unique account slug
     * Format: account-{random-5-chars}
     */
    public function generate(): string
    {
        return 'account-' . Str::lower(Str::random(5));
    }

    /**
     * Clean user-provided slug
     */
    public function clean(string $slug): string
    {
        // Convert to lowercase
        $slug = strtolower($slug);
        
        // Replace spaces and underscores with hyphens
        $slug = str_replace(['_', ' '], '-', $slug);
        
        // Remove all except letters, numbers, hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        // Limit length
        $slug = substr($slug, 0, 100);
        
        return $slug;
    }

    /**
     * Validate slug format
     */
    public function validate(string $slug): array
    {
        $errors = [];

        if (strlen($slug) < 3) {
            $errors[] = 'Slug must be at least 3 characters.';
        }

        if (strlen($slug) > 100) {
            $errors[] = 'Slug must not exceed 100 characters.';
        }

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors[] = 'Slug can only contain lowercase letters, numbers, and hyphens.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
