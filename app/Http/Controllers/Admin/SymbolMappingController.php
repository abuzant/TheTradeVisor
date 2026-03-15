<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SymbolMapping;
use Illuminate\Http\Request;

class SymbolMappingController extends Controller
{
    /**
     * Display all symbol mappings
     */
    public function index()
    {
        $symbols = SymbolMapping::orderBy('is_verified', 'asc')
            ->orderBy('raw_symbol')
            ->paginate(50);
        
        $stats = [
            'total' => SymbolMapping::count(),
            'verified' => SymbolMapping::where('is_verified', true)->count(),
            'unverified' => SymbolMapping::where('is_verified', false)->count(),
        ];
        
        return view('admin.symbols.index', compact('symbols', 'stats'));
    }
    
    /**
     * Update a symbol mapping
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'normalized_symbol' => 'required|string|max:20',
            'is_verified' => 'boolean',
        ]);
        
        $symbol = SymbolMapping::findOrFail($id);
        $symbol->update([
            'normalized_symbol' => strtoupper($request->normalized_symbol),
            'is_verified' => $request->boolean('is_verified'),
        ]);
        
        return redirect()->route('admin.symbols.index')
            ->with('success', 'Symbol mapping updated successfully');
    }
    
    /**
     * Verify a symbol
     */
    public function verify($id)
    {
        $symbol = SymbolMapping::findOrFail($id);
        $symbol->update(['is_verified' => true]);
        
        return back()->with('success', 'Symbol verified successfully');
    }
    
    /**
     * Bulk verify symbols
     */
    public function bulkVerify(Request $request)
    {
        $ids = $request->input('ids', []);
        SymbolMapping::whereIn('id', $ids)->update(['is_verified' => true]);
        
        return back()->with('success', count($ids) . ' symbols verified');
    }
}
