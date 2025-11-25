<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\UninstallFeedback;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UninstallFeedbackController extends Controller
{
    use Sortable;

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display the uninstall feedback dashboard
     */
    public function index(Request $request)
    {
        // Get date range from request
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get base query with date filtering
        $query = UninstallFeedback::whereBetween('submitted_at', [$startDate, $endDate]);

        // Apply filters
        if ($request->filled('reason')) {
            $query->where('reason', $request->input('reason'));
        }
        if ($request->filled('experience_rating')) {
            $query->where('experience_rating', $request->input('experience_rating'));
        }
        if ($request->filled('would_return')) {
            $query->where('would_return', $request->input('would_return'));
        }

        // Get analytics data
        $analytics = $this->getAnalytics($startDate, $endDate);

        // Get recent feedback with pagination
        $recentFeedback = $query->orderBy('submitted_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Get filter options
        $reasons = UninstallFeedback::select('reason')->distinct()->pluck('reason');
        $experienceRatings = UninstallFeedback::select('experience_rating')->distinct()->pluck('experience_rating');
        $returnOptions = UninstallFeedback::select('would_return')->distinct()->pluck('would_return');

        return view('admin.uninstall-feedback.index', compact(
            'analytics',
            'recentFeedback',
            'reasons',
            'experienceRatings',
            'returnOptions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show individual feedback details
     */
    public function show($id)
    {
        $feedback = UninstallFeedback::findOrFail($id);
        
        return view('admin.uninstall-feedback.show', compact('feedback'));
    }

    /**
     * Export feedback to CSV
     */
    public function export(Request $request)
    {
        // Get date range from request
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get feedback data
        $feedback = UninstallFeedback::whereBetween('submitted_at', [$startDate, $endDate])
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Create CSV
        $filename = "uninstall-feedback-{$startDate}-to-{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($feedback) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID', 'Submitted At', 'Reason', 'Experience Rating', 'Would Return',
                'Email', 'Comments', 'IP Address', 'User Agent', 'Referer'
            ]);

            // Data rows
            foreach ($feedback as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->submitted_at,
                    $item->reason,
                    $item->experience_rating,
                    $item->would_return,
                    $item->email,
                    $item->comments,
                    $item->ip_address,
                    $item->user_agent,
                    $item->referer
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get analytics data for the dashboard
     */
    private function getAnalytics($startDate, $endDate)
    {
        $baseQuery = UninstallFeedback::whereBetween('submitted_at', [$startDate, $endDate]);

        return [
            'total_feedback' => $baseQuery->count(),
            'reason_distribution' => $baseQuery->selectRaw('reason, COUNT(*) as count')
                ->groupBy('reason')
                ->orderByRaw('COUNT(*) DESC')
                ->get(),
            'experience_distribution' => $baseQuery->selectRaw('experience_rating, COUNT(*) as count')
                ->groupBy('experience_rating')
                ->orderByRaw('COUNT(*) DESC')
                ->get(),
            'return_distribution' => $baseQuery->selectRaw('would_return, COUNT(*) as count')
                ->groupBy('would_return')
                ->orderByRaw('COUNT(*) DESC')
                ->get(),
            'daily_submissions' => $baseQuery->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get(),
            'with_email' => $baseQuery->whereNotNull('email')->count(),
            'with_comments' => $baseQuery->whereNotNull('comments')->count(),
            'average_per_day' => $baseQuery->count() / max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1),
        ];
    }
}
