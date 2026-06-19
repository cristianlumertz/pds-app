@extends('layouts.app')

@section('content')
    <div class="flex min-h-[calc(100vh-10rem)] items-center justify-center py-8">
        <div class="w-full max-w-md rounded-3xl bg-white px-6 py-10 text-center shadow-sm sm:px-10">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-[#993C1D]/10 text-[#993C1D]">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-9 w-9"
                    aria-hidden="true"
                >
                    <rect width="18" height="14" x="3" y="5" rx="2"></rect>
                    <path d="m3 7 9 6 9-6"></path>
                    <path d="m8 9 8 8"></path>
                    <path d="m16 9-8 8"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-[#3D3D3A]">
                Cancelar recebimento de e-mails
            </h1>

            <p class="mt-4 text-sm leading-6 text-slate-600">
                Confirme abaixo para remover
                <strong class="break-all font-semibold text-[#3D3D3A]">{{ $email }}</strong>
                da nossa lista de novidades.
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-[#993C1D]/20 bg-[#993C1D]/10 px-4 py-3 text-sm font-semibold text-[#993C1D]" role="alert">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('newsletter.confirm-unsubscribe') }}" class="mt-8">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <button
                    type="submit"
                    class="w-full rounded-xl border-2 border-[#993C1D] bg-white px-5 py-3 text-sm font-bold text-[#993C1D] transition hover:bg-[#993C1D] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#993C1D] focus:ring-offset-2"
                >
                    Confirmar descadastro
                </button>
            </form>

            <a
                href="{{ route('store.home') }}"
                class="mt-5 inline-block rounded-lg px-4 py-2 text-sm font-semibold text-[#185FA5] underline decoration-transparent underline-offset-4 transition hover:decoration-current focus:outline-none focus:ring-2 focus:ring-[#185FA5] focus:ring-offset-2"
            >
                Voltar para a loja
            </a>
        </div>
    </div>
@endsection
