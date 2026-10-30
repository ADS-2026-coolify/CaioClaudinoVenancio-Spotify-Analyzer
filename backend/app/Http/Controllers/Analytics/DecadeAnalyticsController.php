<?php

namespace App\Http\Controllers\Analytics;

use App\Enums\TimeRange;
use App\Http\Controllers\Controller;
use App\Services\Analytics\DecadeAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DecadeAnalyticsController extends Controller
{
    public function __construct(
        protected DecadeAnalysisService $decadeService
    ) {}

    /**
     * Get musical decade distribution and representative tracks.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'time_range' => ['nullable', 'string', Rule::enum(TimeRange::class)],
        ]);

        $timeRange = $validated['time_range'] ?? TimeRange::MediumTerm->value;

        $user = Auth::user();
        $analysis = $this->decadeService->getDecadeAnalysis($user, $timeRange);

        return response()->json([
            'success' => true,
            'time_range' => $timeRange,
            'time_range_label' => TimeRange::from($timeRange)->label(),
            'data' => $analysis,
        ]);
    }
}
