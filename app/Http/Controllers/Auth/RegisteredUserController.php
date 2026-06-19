<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $cpf = preg_replace('/\D+/', '', (string) $request->input('cpf'));
        $request->merge(['cpf' => $cpf]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'cpf' => [
                'required',
                'string',
                'size:11',
                'unique:'.User::class,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $cpf = (string) $value;

                    if (! ctype_digit($cpf) || preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
                        $fail('O CPF informado e invalido.');

                        return;
                    }

                    for ($position = 9; $position < 11; $position++) {
                        $sum = 0;

                        for ($index = 0; $index < $position; $index++) {
                            $sum += (int) $cpf[$index] * (($position + 1) - $index);
                        }

                        $digit = ((10 * $sum) % 11) % 10;

                        if ((int) $cpf[$position] !== $digit) {
                            $fail('O CPF informado e invalido.');

                            return;
                        }
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'newsletter_opt_in' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'password' => Hash::make($request->password),
            'newsletter_opt_in' => $request->boolean('newsletter_opt_in'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false));
    }
}
