@props(['items'])

@if ($items->hasPages())
    <div class="mt-5">
        {{ $items->withQueryString()->links() }}
    </div>
@endif
