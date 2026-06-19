<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function unsubscribe(Request $request): View|RedirectResponse
    {
        $email = $request->query('email');

        if (! $email) {
            return redirect()->route('store.home')
                ->with('status', 'Link inválido.');
        }

        return view('store.newsletter.unsubscribe', compact('email'));
    }

    public function confirmUnsubscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'O endereço de e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.exists' => 'Não encontramos uma inscrição para este e-mail.',
        ]);

        User::where('email', $validated['email'])
            ->update(['newsletter_opt_in' => false]);

        return redirect()->route('store.home')
            ->with('status', 'Você foi removido da nossa lista de e-mails.');
    }
}
