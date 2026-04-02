<?php

namespace App\Http\Controllers\Api;

use App\Events\CheckoutCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $pizza = Pizza::findOrFail($request->pizza_id);
        $checkoutGroupId = (string) Str::uuid();

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'pizza_id' => $pizza->id,
            'quantity' => $request->quantity,
            'total' => $pizza->price * $request->quantity,
            'ordered_at' => now(),
            'checkout_group_id' => $checkoutGroupId,
        ]);

        $order->load(['pizza.ingredients', 'user']);
        CheckoutCompleted::dispatch(collect([$order]));

        return response()->json([
            'message' => 'Pedido creado correctamente',
            'order' => $order,
        ]);
    }

    public function checkout(CheckoutOrderRequest $request)
    {
        $items = $request->validated()['items'];
        $checkoutGroupId = (string) Str::uuid();

        $orders = collect();

        DB::transaction(function () use ($items, $checkoutGroupId, &$orders) {
            foreach ($items as $item) {
                $pizza = Pizza::findOrFail($item['pizza_id']);
                $orders->push(Order::create([
                    'user_id' => auth()->id(),
                    'pizza_id' => $pizza->id,
                    'quantity' => $item['quantity'],
                    'total' => $pizza->price * $item['quantity'],
                    'ordered_at' => now(),
                    'checkout_group_id' => $checkoutGroupId,
                ]));
            }
        });

        $orders->each(fn (Order $o) => $o->load(['pizza.ingredients', 'user']));
        CheckoutCompleted::dispatch($orders);

        return response()->json([
            'message' => 'Pedido creado correctamente',
            'orders' => $orders->values(),
            'checkout_group_id' => $checkoutGroupId,
        ]);
    }

    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'No tienes permisos para ver los pedidos',
            ], 403);
        }

        return response()->json(
            Order::with(['pizza', 'user'])
                ->latest()
                ->get()
        );
    }

    public function show(Order $order)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->id !== $order->user_id) {
            return response()->json([
                'message' => 'No tienes permisos para ver este pedido',
            ], 403);
        }

        return response()->json(
            $order->load(['pizza', 'user'])
        );
    }

    public function myOrders(Request $request)
    {
        $orders = Order::with(['pizza', 'user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Pedido eliminado correctamente',
            'order' => $order,
        ]);
    }
}
