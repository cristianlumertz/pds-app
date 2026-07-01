<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $cart = $user?->carts()->latest('id')->first();

        if (! $cart || $cart->isEmpty()) {
            return view('store.cart.empty');
        }

        return view('store.cart.index', compact('cart'));
    }
}
