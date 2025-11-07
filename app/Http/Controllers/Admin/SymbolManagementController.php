<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SymbolMapping;
use Illuminate\Support\Facades\Cache;

class SymbolManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $verified = $request->get('verified');
        
        $symbols = SymbolMapping::query()
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('raw_symbol', 'like', "%{$search}%")
                      ->orWhere('normalized_symbol', 'like', "%{$search}%");
                });
            })
            ->when($verified !== null, function($query) use ($verified) {
                $query->where('is_verified', $verified === 'verified');
            })
            ->orderBy('normalized_symbol')
            ->orderBy('raw_symbol')
            ->paginate(50);
        
        // Get statistics
        $stats = [
            'total' => SymbolMapping::count(),
            'verified' => SymbolMapping::where('is_verified', true)->count(),
            'unverified' => SymbolMapping::where('is_verified', false)->count(),
            'unique_normalized' => SymbolMapping::distinct('normalized_symbol')->count('normalized_symbol'),
        ];
        
        return view('admin.symbols.index', compact('symbols', 'stats', 'search', 'verified'));
    }
    
    public function update(Request $request, SymbolMapping $symbol)
    {
        $validated = $request->validate([
            'normalized_symbol' => 'required|string|max:20',
            'is_verified' => 'required|boolean',
        ]);
        
        $symbol->update($validated);
        
        // Clear cache
        Cache::forget('symbol_mapping_' . $symbol->raw_symbol);
        
        return redirect()
            ->route('admin.symbols.index')
            ->with('success', 'Symbol mapping updated successfully');
    }
    
    public function bulkVerify(Request $request)
    {
        $ids = $request->input('symbol_ids', []);
        
        if (empty($ids)) {
            return redirect()
                ->route('admin.symbols.index')
                ->with('error', 'No symbols selected');
        }
        
        SymbolMapping::whereIn('id', $ids)->update(['is_verified' => true]);
        
        return redirect()
            ->route('admin.symbols.index')
            ->with('success', count($ids) . ' symbols verified successfully');
    }
    
    public function bulkNormalize(Request $request)
    {
        $mapping = $request->input('mapping', []);
        
        if (empty($mapping)) {
            return redirect()
                ->route('admin.symbols.index')
                ->with('error', 'No mappings provided');
        }
        
        $updated = 0;
        foreach ($mapping as $id => $normalized) {
            if (!empty($normalized)) {
                SymbolMapping::where('id', $id)->update([
                    'normalized_symbol' => $normalized,
                    'is_verified' => true,
                ]);
                $updated++;
            }
        }
        
        // Clear cache
        Cache::flush();
        
        return redirect()
            ->route('admin.symbols.index')
            ->with('success', $updated . ' symbol mappings updated');
    }
    
    public function autoNormalize(Request $request)
    {
        $symbols = SymbolMapping::where('is_verified', false)->get();
        
        $updated = 0;
        foreach ($symbols as $symbol) {
            // Auto-normalize using the model's logic
            $normalized = SymbolMapping::autoNormalize($symbol->raw_symbol);
            
            if ($normalized !== $symbol->normalized_symbol) {
                $symbol->update([
                    'normalized_symbol' => $normalized,
                ]);
                $updated++;
            }
        }
        
        return redirect()
            ->route('admin.symbols.index')
            ->with('success', $updated . ' symbols auto-normalized');
    }
}
