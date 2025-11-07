<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Sortable
{
    /**
     * Apply sorting to a query
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param array $sortableColumns Array of allowed columns to sort by
     * @param string $defaultColumn Default column to sort by
     * @param string $defaultDirection Default direction (asc or desc)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applySorting($query, Request $request, array $sortableColumns, string $defaultColumn = 'created_at', string $defaultDirection = 'desc')
    {
        $sortBy = $request->get('sort_by', $defaultColumn);
        $sortDirection = $request->get('sort_direction', $defaultDirection);

        // Validate sort column
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = $defaultColumn;
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = $defaultDirection;
        }

        return $query->orderBy($sortBy, $sortDirection);
    }

    /**
     * Get the opposite sort direction
     * 
     * @param string $currentDirection
     * @return string
     */
    protected function toggleDirection(string $currentDirection): string
    {
        return $currentDirection === 'asc' ? 'desc' : 'asc';
    }
}
