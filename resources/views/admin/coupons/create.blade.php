@extends('layouts.app')

@section('content')
    <section class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Novo cupom</h1>

        <form method="POST" action="{{ route('admin.coupons.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
            @csrf
            @include('admin.coupons.partials.form', ['coupon' => null])
        </form>
    </section>
@endsection
