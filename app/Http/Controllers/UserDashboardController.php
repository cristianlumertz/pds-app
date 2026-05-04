<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'orders_count' => $user->orders()->count(),
            'carts_count' => $user->carts()->count(),
        ];

        $latestOrders = $user->orders()
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'latestOrders'));
    }
}
