<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:service,package',
            'item_id' => 'required|integer',
        ]);

        $type = $request->input('item_type');
        $id = $request->input('item_id');

        if ($type === 'service') {
            $request->validate(['item_id' => 'exists:services,id']);
        } else {
            $request->validate(['item_id' => 'exists:service_packages,id']);
        }

        $cartKey = $type . '_' . $id;
        $cart = $request->session()->get('cart', []);

        if (!in_array($cartKey, $cart)) {
            $cart[] = $cartKey;
            $request->session()->put('cart', $cart);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cartCount' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', ($type === 'service' ? 'Service' : 'Package') . ' added to cart!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:service,package',
            'item_id' => 'required|integer',
        ]);

        $type = $request->input('item_type');
        $id = $request->input('item_id');
        $cartKey = $type . '_' . $id;
        $cart = $request->session()->get('cart', []);
        $cart = array_filter($cart, fn($key) => $key != $cartKey);
        $request->session()->put('cart', array_values($cart));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cartCount' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', ($type === 'service' ? 'Service' : 'Package') . ' removed from cart!');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');
        return redirect()->back()->with('success', 'Cart cleared!');
    }

    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $cartItems = collect();
        $cartServices = [];
        $cartPackages = [];

        foreach ($cart as $item) {
            [$type, $id] = explode('_', $item, 2);
            $model = $type === 'service' ? Service::find($id) : ServicePackage::find($id);
            if ($model) {
                $model->cart_type = $type;
                $cartItems->push($model);
                if ($type === 'service') {
                    $cartServices[] = $id;
                } else {
                    $cartPackages[] = $id;
                }
            }
        }

        return view('cart', compact('cartItems', 'cart', 'cartServices', 'cartPackages'));
    }
}