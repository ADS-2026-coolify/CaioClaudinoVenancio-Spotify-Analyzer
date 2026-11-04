<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Services\Analytics\MusicInsightsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsightsController extends Controller
{
    public function __construct(
        protected MusicInsightsService $insightsService
    ) {}

    /**
     * Get deterministic, data-driven musical insights for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $insights = $this->insightsService->getInsights($user);

        return response()->json([
            'success' => true,
            'title' => 'Seu Perfil Musical',
            'subtitle' => 'Insights determinísticos baseados exclusivamente nos seus dados reais de reprodução.',
            'total' => count($insights),
            'insights' => $insights,
        ]);
    }
}

