<x-guest-layout>
    <h1 class="text-xl font-bold text-[#1A1A1A]">Entrar na minha conta</h1>
    <p class="mt-1 text-sm text-[#767676]">Acesse sua conta para continuar suas compras.</p>

    <x-auth-session-status class="mt-4 rounded border-l-4 border-[#198754] bg-[#198754]/10 px-4 py-3 text-[#198754]" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6">
        @csrf

        <div>
            <label for="email" class="block text-[13px] font-semibold text-[#444444]">E-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition placeholder:text-[#767676] focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-5">
            <label for="password" class="block text-[13px] font-semibold text-[#444444]">Senha</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="flex cursor-pointer items-center gap-2 text-sm text-[#444444]">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-[#E0E0E0] text-[#D42B2B] focus:ring-[#D42B2B]"
                    @checked(old('remember'))
                >
                <span>Lembrar-me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#2B5FAA] underline underline-offset-2 hover:text-[#1A3A6B]">
                    Esqueci minha senha
                </a>
            @endif
        </div>

        <button
            type="submit"
            class="mt-6 w-full rounded bg-[#D42B2B] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
        >
            Entrar
        </button>

        <p class="mt-6 text-center text-sm text-[#767676]">
            Não tem conta?
            <a href="{{ route('register') }}" class="font-semibold text-[#2B5FAA] hover:text-[#1A3A6B] hover:underline">
                Cadastre-se
            </a>
        </p>
    </form>
</x-guest-layout>
