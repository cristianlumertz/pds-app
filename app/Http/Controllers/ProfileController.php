<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $defaultAddress = $user->addresses()
            ->where('is_default', true)
            ->first();

        $latestOrders = $user->orders()
            ->latest()
            ->take(4)
            ->get();

        $profileStats = [
            'orders_count' => $user->orders()->count(),
            'addresses_count' => $user->addresses()->count(),
            'total_spent' => $user->orders()
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->sum('total_amount'),
        ];

        return view('profile.edit', [
            'user' => $user,
            'defaultAddress' => $defaultAddress,
            'latestOrders' => $latestOrders,
            'profileStats' => $profileStats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['newsletter_opt_in'] = $request->boolean('newsletter_opt_in');

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
