<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pizza;
use App\Events\OrderCreated;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request){
        $pizza = Pizza::findOrFail($request->pizza_id);
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'pizza_id' => $pizza->id,
            'quantity' => $request->quantity,
            'total' => $pizza->price * $request->quantity,
            'ordered_at' => now(),
        ]);

        $order->load(['pizza.ingredients', 'user']);
        OrderCreated::dispatch($order);

        return response()->json([
            'message' => 'Pedido creado correctamente',
            'order' => $order
        ]);
    }
    public function index(){
        if(auth()->user()->role !== 'admin'){
            return response()->json([
                'message' => 'No tienes permisos para ver los pedidos'
            ], 403);
        }
       
        return response()->json(Order::with('pizza', 'user')->get());
    }
    public function show(Order $order){
        if(auth()->user()->role !== 'admin' && auth()->user()->id !== $order->user_id){
            return response()->json([
                'message' => 'No tienes permisos para ver este pedido'
            ], 403);
        }
        return response()->json($order->load('pizza', 'user'));
    }
    public function destroy(Order $order){
        $order->delete();
        return response()->json([
            'message' => 'Pedido eliminado correctamente',
            'order' => $order
        ]);
    }
}
