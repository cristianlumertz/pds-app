<section>
    <header>
        <h2 class="text-xl font-black text-[#1A3A6B]">
            Dados da conta
        </h2>

        <p class="mt-1 text-sm text-[#767676]">
            Atualize seus dados pessoais e informações de contato.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 grid gap-5 md:grid-cols-2">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="cpf" value="CPF" />
            <x-text-input id="cpf" type="text" class="mt-1 block w-full bg-[#F3F5F8] text-[#767676]" :value="$user->cpf" disabled />
            <p class="mt-1 text-xs text-[#767676]">Para alterar CPF, entre em contato com o atendimento.</p>
        </div>

        <div>
            <x-input-label for="phone" value="Telefone" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="status" value="Status da conta" />
            <x-text-input id="status" type="text" class="mt-1 block w-full bg-[#F3F5F8] text-[#767676]" :value="ucfirst((string) $user->status)" disabled />
        </div>

        <label class="flex items-center gap-3 rounded border border-[#E0E0E0] bg-[#F8FAFC] px-4 py-3 text-sm font-semibold text-[#3D3D3A]">
            <input type="checkbox" name="newsletter_opt_in" value="1" @checked(old('newsletter_opt_in', $user->newsletter_opt_in)) class="rounded border-[#3D3D3A]/30 text-[#1A3A6B] focus:ring-[#1A3A6B]">
            Quero receber ofertas por e-mail
        </label>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
            <div class="md:col-span-2 rounded border {{ $user->hasVerifiedEmail() ? 'border-[#1D9E75]/20 bg-[#1D9E75]/10 text-[#16765A]' : 'border-[#FFF3CD] bg-[#FFF9E6] text-[#856404]' }} px-4 py-3 text-sm font-semibold">
                @if ($user->hasVerifiedEmail())
                    E-mail verificado.
                @else
                    Seu e-mail ainda não foi verificado.
                    <button form="send-verification" class="ml-1 underline">
                        Reenviar verificação.
                    </button>
                @endif

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-bold text-[#16765A]">Novo link de verificação enviado.</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4 md:col-span-2">
            <button type="submit" class="rounded bg-[#1A3A6B] px-5 py-2.5 text-sm font-black text-white transition hover:bg-[#14305A]">
                Salvar dados
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-bold text-[#16765A]"
                >Dados salvos.</p>
            @endif
        </div>
    </form>
</section>
