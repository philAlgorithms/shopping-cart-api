<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Requests\Validators\CartValidator;
use App\Http\Resources\CartResource;
use App\Models\Products\Product;
use App\Models\Users\Buyer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    public function index(): JsonResource
    {
        if(is_null(session('cart')) || !(session('cart') instanceof Collection)) session(['cart' => collect([])]);
        return CartResource::collection(
            session('cart')
        );
    }
    public function add(): JsonResource
    {
        $validated = (new CartValidator)->validateEdit(request()->all());

        $product = Product::find($validated['product_id']);
        $cart = $product->addToCart($validated['quantity']);

        return CartResource::collection(
            $cart->all()
        );
    }

    public function remove(): JsonResource
    {
        $validated = (new CartValidator)->validateEdit(request()->all());

        $product = Product::find($validated['product_id']);
        $cart = $product->removeFromCart($validated['quantity']);

        return CartResource::collection(
            $cart->all()
        );
    }

    public function clear(): JsonResource
    {
        session(['cart' => collect([])]);
        
        return CartResource::collection(
            session('cart')->all()
        );
    }
}
