<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardAnalytics;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Render the authenticated user's analytical dashboard.
     *
     * Analytics are resolved through one owner-scoped service so the page does
     * not duplicate account and transaction query rules in the controller.
     */
    public function __invoke(Request $request, DashboardAnalytics $analytics): Response
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return Inertia::render('dashboard', [
            'analytics' => $analytics->for($user),
        ]);
    }
}
