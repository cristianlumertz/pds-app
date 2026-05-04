@extends('layouts.app')

@section('content')
    <section class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <livewire:cart-page />
        </div>

        <aside class="lg:col-span-1">
            <livewire:cart-summary />
        </aside>
    </section>
@endsection
