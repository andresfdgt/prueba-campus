<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('product');
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(["message" => "Product not found"], 404);
        }

        return response()->json($product);
    }

    public function list()
    {
        $products = Product::orderByTotal('desc')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|max:50',
                'descripcion' => 'required|max:300',
                'estado' => 'integer|in:0,1',
                'initial_quantity' => 'required|integer',
                'id_bodega' => 'exists:bodegas,id',
                'created_by' => 'required|exists:users,id',
            ]);

            DB::beginTransaction();

            $product = new Product();
            $product->nombre = $validatedData['nombre'];
            $product->descripcion = $validatedData['descripcion'];
            $product->estado = $validatedData['estado'] ?? 1;
            $product->created_by = $validatedData['created_by'];
            $product->updated_by = $validatedData['updated_by'];
            $product->save();

            $product->inventarios()->create([
                'id_bodega' => $request->id_bodega ?? 1,
                'cantidad' => $validatedData['initial_quantity'],
                'created_by' => $validatedData['created_by'],
                'updated_by' => null,
            ]);

            DB::commit();

            return response()->json($product, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
