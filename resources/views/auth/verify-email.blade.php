@php
    $verificationLinkSent = session('status') === 'verification-link-sent';

    if ($verificationLinkSent) {
        session()->forget('status');
    }
@endphp

@extends('layouts.app')

@section('content')
    <div class="flex min-h-[calc(100vh-10rem)] items-center justify-center py-8">
        <div class="w-full max-w-md rounded-3xl bg-white px-6 py-10 shadow-sm sm:px-10">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-[#185FA5]/10 text-[#185FA5]">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-11 w-11"
                    aria-hidden="true"
                >
                    <rect width="18" height="14" x="3" y="5" rx="2"></rect>
                    <path d="m3 7 9 6 9-6"></path>
                </svg>
            </div>

            <div class="text-center">
                <h1 class="text-3xl font-bold tracking-tight text-[#3D3D3A]">
                    Verifique seu e-mail
                </h1>

                <p class="mt-4 text-base leading-7 text-[#3D3D3A]">
                    Enviamos um link de confirmação para
                    <strong class="font-semibold text-[#185FA5]">{{ auth()->user()->email }}</strong>
                </p>

                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Clique no link do e-mail para ativar sua conta. Verifique também a pasta de spam.
                </p>
            </div>

            @if ($verificationLinkSent)
                <div class="mt-6 rounded-2xl border border-[#3B6D11]/20 bg-[#3B6D11]/10 px-4 py-3 text-center text-sm font-semibold text-[#3B6D11]" role="status">
                    Um novo link de verificação foi enviado para o seu e-mail.
                </div>
            @endif

            <div
                class="mt-8"
                x-data="{
                    seconds: {{ $verificationLinkSent ? 60 : 0 }},
                    timer: null,
                    startCountdown() {
                        this.seconds = 60;
                        clearInterval(this.timer);
                        this.timer = setInterval(() => {
                            if (this.seconds > 0) {
                                this.seconds--;
                            } else {
                                clearInterval(this.timer);
                            }
                        }, 1000);
                    }
                }"
                x-init="if (seconds > 0) startCountdown()"
            >
                <form method="POST" action="{{ route('verification.send') }}" x-on:submit="startCountdown()">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-[#1D9E75] px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#178260] focus:outline-none focus:ring-2 focus:ring-[#1D9E75] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                        x-bind:disabled="seconds > 0"
                    >
                        <span x-show="seconds === 0">Reenviar e-mail de verificação</span>
                        <span x-show="seconds > 0" x-cloak>
                            Reenviar em <span x-text="seconds"></span>s
                        </span>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-[#993C1D] underline decoration-transparent underline-offset-4 transition hover:decoration-current focus:outline-none focus:ring-2 focus:ring-[#993C1D] focus:ring-offset-2"
                    >
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
