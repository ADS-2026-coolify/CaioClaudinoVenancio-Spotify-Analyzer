<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Analytics\MusicAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected MusicAnalyticsService $analyticsService
    ) {}

    /**
     * Return consolidated dashboard data for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $this->analyticsService->getDashboardData($user);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
