<x-guest-layout>
    <h1 class="text-xl font-bold text-[#1A1A1A]">Criar minha conta</h1>
    <p class="mt-1 text-sm text-[#767676]">Cadastre-se para comprar e acompanhar seus pedidos.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6">
        @csrf

        <div>
            <label for="name" class="block text-[13px] font-semibold text-[#444444]">Nome</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-5">
            <label for="email" class="block text-[13px] font-semibold text-[#444444]">E-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-5">
            <label for="cpf" class="block text-[13px] font-semibold text-[#444444]">CPF</label>
            <input
                id="cpf"
                type="text"
                name="cpf"
                value="{{ old('cpf') }}"
                required
                inputmode="numeric"
                pattern="[0-9]{11}"
                maxlength="11"
                autocomplete="off"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <p class="mt-1 text-xs text-[#767676]">11 dígitos sem pontos</p>
            <x-input-error :messages="$errors->get('cpf')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-5">
            <label for="password" class="block text-[13px] font-semibold text-[#444444]">Senha</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-5">
            <label for="password_confirmation" class="block text-[13px] font-semibold text-[#444444]">Confirmar senha</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="mt-1.5 block w-full rounded border border-[#E0E0E0] px-3.5 py-2.5 text-sm text-[#1A1A1A] outline-none transition focus:border-[#1A3A6B] focus:ring-2 focus:ring-[#1A3A6B]/20"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-[#D42B2B]" />
        </div>

        <div class="mt-5">
            <label class="flex cursor-pointer items-start gap-2 text-sm text-[#444444]">
                <input
                    type="checkbox"
                    name="newsletter_opt_in"
                    value="1"
                    class="mt-0.5 rounded border-[#E0E0E0] text-[#D42B2B] focus:ring-[#D42B2B]"
                    @checked(old('newsletter_opt_in'))
                >
                <span>Quero receber ofertas por e-mail</span>
            </label>
            <x-input-error :messages="$errors->get('newsletter_opt_in')" class="mt-2 text-[#D42B2B]" />
        </div>

        <button
            type="submit"
            class="mt-6 w-full rounded bg-[#D42B2B] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#B02020] focus:outline-none focus:ring-2 focus:ring-[#D42B2B] focus:ring-offset-2"
        >
            Criar conta grátis
        </button>

        <p class="mt-6 text-center text-sm text-[#767676]">
            Já tenho conta →
            <a href="{{ route('login') }}" class="font-semibold text-[#2B5FAA] hover:text-[#1A3A6B] hover:underline">
                Entrar
            </a>
        </p>
    </form>
</x-guest-layout>
