<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $itemCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('cart:updated')]
    #[On('cart:item-added')]
    #[On('cart:item-removed')]
    #[On('cart:cleared')]
    public function refreshCount(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->itemCount = 0;

            return;
        }

        $this->itemCount = (int) ($user->carts()->latest('id')->value('item_count') ?? 0);
    }

    public function render(): View
    {
        return view('livewire.cart-icon');
    }
}
