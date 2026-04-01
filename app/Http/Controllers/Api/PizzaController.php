<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pizza;
use App\Http\Requests\StorePizzaRequest;
use App\Http\Requests\UpdatePizzaRequest;
use App\Services\CloudinaryService;
use Throwable;

class PizzaController extends Controller
{
    public function __construct(private CloudinaryService $cloudinaryService)
    {
    }

    public function index(){
        $pizzas=Pizza::with('ingredients')->get();
        return response()->json($pizzas);
    }

    public function store(StorePizzaRequest $request){
        $validated = $request->validated();

        try {
            $imageUrl = $validated['image_url'] ?? null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->cloudinaryService->uploadImage($request->file('image'));
            }

            $pizza = Pizza::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'image' => $imageUrl,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'No se pudo subir la imagen a Cloudinary',
                'error' => $exception->getMessage(),
            ], 422);
        }

        if($request->filled('ingredients')){
            $pizza->ingredients()->attach($request->ingredients);
        }
        return response()->json([
            'message' => 'Pizza creada correctamente',
            'pizza' => $pizza
        ]);
    }
    public function show(Pizza $pizza){
        return response()->json($pizza->load('ingredients'));
    }
    public function update(UpdatePizzaRequest $request, Pizza $pizza){
        $validated = $request->validated();
        $updateData = collect($validated)->except(['ingredients', 'image', 'image_url'])->all();

        try {
            if ($request->hasFile('image')) {
                $updateData['image'] = $this->cloudinaryService->uploadImage($request->file('image'));
            } elseif (array_key_exists('image_url', $validated)) {
                $updateData['image'] = $validated['image_url'];
            }
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'No se pudo subir la imagen a Cloudinary',
                'error' => $exception->getMessage(),
            ], 422);
        }

        if (!empty($updateData)) {
            $pizza->update($updateData);
        }

        if($request->filled('ingredients')){
            $pizza->ingredients()->sync($request->ingredients);
        }
        return response()->json([
            'message' => 'Pizza actualizada correctamente',
            'pizza' => $pizza->load('ingredients')
        ]);
    }
    public function destroy(Pizza $pizza){
        $pizza->delete();
        return response()->json([
            'message' => 'Pizza eliminada correctamente',
            'pizza' => $pizza
        ]);
    }
}
