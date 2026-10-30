<?php

namespace App\Http\Controllers\Analytics;

use App\Enums\TimeRange;
use App\Http\Controllers\Controller;
use App\Services\Analytics\GenreAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GenreAnalyticsController extends Controller
{
    public function __construct(
        protected GenreAnalysisService $genreService
    ) {}

    /**
     * Get genre distribution, rankings, and obscurity analysis.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'time_range' => ['nullable', 'string', Rule::enum(TimeRange::class)],
        ]);

        $timeRange = $validated['time_range'] ?? TimeRange::MediumTerm->value;

        $user = Auth::user();
        $analysis = $this->genreService->getGenreAnalysis($user, $timeRange);

        return response()->json([
            'success' => true,
            'time_range' => $timeRange,
            'time_range_label' => TimeRange::from($timeRange)->label(),
            'data' => $analysis,
        ]);
    }
}
