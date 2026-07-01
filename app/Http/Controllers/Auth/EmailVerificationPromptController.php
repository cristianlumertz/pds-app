<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $destination = $request->user()->is_admin
            ? route('admin.dashboard', absolute: false)
            : route('user.dashboard', absolute: false);

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended($destination)
                    : view('auth.verify-email');
    }
}
