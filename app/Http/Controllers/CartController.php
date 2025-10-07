<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function add(Request $request)
    {
        try {
            $request->validate([
                'item_type' => 'required|in:service,package',
                'item_id' => 'required|integer',
            ]);

            $type = $request->input('item_type');
            $id = $request->input('item_id');
            $quantity = $request->input('quantity', 1);

            if ($type === 'service') {
                $request->validate(['item_id' => 'exists:services,id']);

                // Use package_count if provided, otherwise default to 1
                $packageCount = $request->input('package_type', 1);
                $cartKey = $type . '_' . $id . '_' . $packageCount;

                // Get the service to calculate the correct price
                $service = Service::find($id);
                if ($service) {
                    $basePrice = $service->price;

                    // Calculate total price for the package with discount
                    if ($packageCount == 3) {
                        $totalPrice = round($basePrice * 3 * 0.95, 2); // 5% discount for 3-pack
                    } elseif ($packageCount == 6) {
                        $totalPrice = round($basePrice * 6 * 0.92, 2); // 8% discount for 6-pack
                    } elseif ($packageCount == 8) {
                        $totalPrice = round($basePrice * 8 * 0.90, 2); // 10% discount for 8-pack
                    } else {
                        $totalPrice = $basePrice * $packageCount; // No discount for other quantities
                    }

                    // Store quantity information
                    $request->session()->put('service_quantity_' . $cartKey, $packageCount);
                    $request->session()->put('service_unit_price_' . $cartKey, $basePrice);
                    $request->session()->put('service_discount_' . $cartKey, 0);
                    $request->session()->put('service_total_price_' . $cartKey, $totalPrice);
                }
            } else {
                // Handle calculated packages (like 6x packages)
                $packageType = $request->input('package_type', 'regular');
                $cartKey = $type . '_' . $id . '_' . $packageType;

                // Store the calculated price for this package
                $packagePrice = $request->input('package_price');
                if ($packagePrice) {
                    $request->session()->put('package_price_' . $cartKey, $packagePrice);
                }
            }

            $cart = $request->session()->get('cart', []);

            if (!in_array($cartKey, $cart)) {
                $cart[] = $cartKey;
                $request->session()->put('cart', $cart);
            }

            // Get updated cart details to return
            $cartItems = [];
            $total = 0;

            foreach ($cart as $item) {
                $parts = explode('_', $item);
                $itemType = $parts[0];
                $itemId = $parts[1];

                if ($itemType === 'service') {
                    $service = Service::find($itemId);
                    if ($service) {
                        $itemQuantity = $request->session()->get('service_quantity_' . $item, 1);
                        $unitPrice = $request->session()->get('service_unit_price_' . $item, $service->price);
                        $discount = $request->session()->get('service_discount_' . $item, 0);
                        $finalPrice = $request->session()->get('service_total_price_' . $item, $unitPrice * (1 - $discount));

                        $cartItems[] = [
                            'name' => $service->name,
                            'price' => $finalPrice,
                            'type' => $itemQuantity > 1 ? $itemQuantity . 'er-Pack' : 'Einzelbehandlung',
                            'cart_key' => $item,
                            'cartKey' => $item,
                            'originalType' => 'service',
                            'originalId' => $itemId
                        ];
                        $total += (float)$finalPrice;
                    }
                }
            }

            // Always return JSON for AJAX requests
            if ($request->expectsJson() || $request->is('api/*') || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'cartCount' => count($cart),
                    'message' => ($type === 'service' ? 'Service' : 'Package') . ' added to cart!',
                    'cartItems' => $cartItems,
                    'cartTotal' => number_format($total, 2, ',', '.')
                ]);
            }

            return redirect()->back()->with('success', ($type === 'service' ? 'Service' : 'Package') . ' added to cart!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON error for AJAX requests
            if ($request->expectsJson() || $request->is('api/*') || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error: ' . implode(', ', $e->errors()['item_id'] ?? ['Invalid data'])
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            // Return JSON error for AJAX requests
            if ($request->expectsJson() || $request->is('api/*') || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding to cart: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error adding to cart: ' . $e->getMessage());
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:service,package',
            'item_id' => 'required|integer',
        ]);

        $type = $request->input('item_type');
        $id = $request->input('item_id');

        if ($type === 'service') {
            $cartKey = $type . '_' . $id;

            // Also remove quantity information
            $request->session()->forget('service_quantity_' . $cartKey);
            $request->session()->forget('service_unit_price_' . $cartKey);
            $request->session()->forget('service_discount_' . $cartKey);
            $request->session()->forget('service_total_price_' . $cartKey);
        } else {
            // Handle calculated packages (like 6x packages)
            $packageType = $request->input('package_type', 'regular');
            $cartKey = $type . '_' . $id . '_' . $packageType;

            // Also remove package price information
            $request->session()->forget('package_price_' . $cartKey);
        }

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
            $parts = explode('_', $item);
            $type = $parts[0];
            $id = $parts[1];

            if ($type === 'service') {
                $model = Service::find($id);
                if ($model) {
                    $model->cart_type = $type;
                    $model->cart_key = $item;
                    $model->quantity = $request->session()->get('service_quantity_' . $item, 1);
                    $model->unit_price = $request->session()->get('service_unit_price_' . $item, $model->price);
                    $model->discount = $request->session()->get('service_discount_' . $item, 0);
                    $model->total_price = $request->session()->get('service_total_price_' . $item, $model->price);
                    $cartItems->push($model);
                    $cartServices[] = $id;
                }
            } else {
                // Handle packages
                $packageType = isset($parts[2]) ? $parts[2] : 'regular';
                $package = ServicePackage::find($id);
                if ($package) {
                    $package->cart_type = 'package';
                    $package->cart_key = $item;
                    $package->package_type = $packageType;
                    $package->calculated_price = $request->session()->get('package_price_' . $item, $package->price);
                    $cartItems->push($package);
                    $cartPackages[] = $id;
                }
            }
        }

        return view('cart', compact('cartItems', 'cart', 'cartServices', 'cartPackages'));
    }

    public function getDetails(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        Log::info('CartController getDetails called', ['cart' => $cart, 'session_id' => $request->session()->getId()]);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $parts = explode('_', $item);
            $type = $parts[0];
            $id = $parts[1];

            if ($type === 'service') {
                $service = Service::find($id);
                if ($service) {
                    $quantity = $request->session()->get('service_quantity_' . $item, 1);
                    $unitPrice = $request->session()->get('service_unit_price_' . $item, $service->price);
                    $discount = $request->session()->get('service_discount_' . $item, 0);
                    $finalPrice = $request->session()->get('service_total_price_' . $item, $unitPrice * (1 - $discount));

                    $cartItems[] = [
                        'name' => $service->name,
                        'price' => $finalPrice,
                        'type' => $quantity > 1 ? $quantity . 'er-Pack' : 'Einzelbehandlung',
                        'cart_key' => $item,
                        'cartKey' => $item,
                        'originalType' => 'service',
                        'originalId' => $id
                    ];
                    $total += (float)$finalPrice;
                }
            } else {
                // Handle packages
                $packageType = isset($parts[2]) ? $parts[2] : 'regular';
                $package = ServicePackage::find($id);
                if ($package) {
                    $calculatedPrice = $request->session()->get('package_price_' . $item, $package->price);
                    $cartItems[] = [
                        'name' => $package->name,
                        'price' => $calculatedPrice,
                        'type' => 'Paket',
                        'cartKey' => $item,
                        'originalType' => 'package',
                        'originalId' => $id,
                        'packageType' => $packageType
                    ];
                    $total += (float)$calculatedPrice;
                }
            }
        }

        return response()->json([
            'items' => $cartItems,
            'total' => number_format($total, 2, ',', '.')
        ]);
    }
}