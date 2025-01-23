<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventories');
    }

    /**
     * store
     *
     * @param  mixed $request {id_producto, id_bodega, cantidad, created_by}
     * @return void
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_producto' => 'required|exists:productos,id',
                'id_bodega' => 'required|exists:bodegas,id',
                'cantidad' => 'required|numeric|min:0',
                'created_by' => 'required|exists:users,id',
            ]);

            DB::beginTransaction();

            $inventario = Inventory::firstOrNew([
                'id_producto' => $validatedData['id_producto'],
                'id_bodega' => $validatedData['id_bodega'],
            ]);

            if ($inventario->exists) {
                $inventario->cantidad += $validatedData['cantidad'];
            } else {
                $inventario->cantidad = $validatedData['cantidad'];
            }

            $inventario->created_by = $validatedData['created_by'];
            $inventario->updated_by = $validatedData['created_by'];

            $inventario->save();

            DB::commit();

            return response()->json($inventario, 201);
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

    /**
     * trasladar
     *
     * @param  mixed $request {id_producto, id_bodega_origen, id_bodega_destino, cantidad, created_by}
     * @return void
     */
    public function transfer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_producto' => 'required|exists:productos,id',
                'id_bodega_origen' => 'required|exists:bodegas,id',
                'id_bodega_destino' => 'required|exists:bodegas,id|different:id_bodega_origen',
                'cantidad' => 'required|numeric|min:1',
                'created_by' => 'required|exists:users,id',
            ]);

            return DB::transaction(function () use ($validatedData) {
                $origen = Inventory::where([
                    'id_producto' => $validatedData['id_producto'],
                    'id_bodega' => $validatedData['id_bodega_origen'],
                ])->firstOrFail();

                if ($origen->cantidad < $validatedData['cantidad']) {
                    return response()->json([
                        'message' => 'Not enough inventory in the source warehouse',
                    ], 400);
                }

                $destino = Inventory::firstOrNew([
                    'id_producto' => $validatedData['id_producto'],
                    'id_bodega' => $validatedData['id_bodega_destino'],
                ]);

                $origen->cantidad -= $validatedData['cantidad'];
                $origen->updated_by = $validatedData['created_by'];
                $origen->save();

                if ($destino->exists) {
                    $destino->cantidad += $validatedData['cantidad'];
                } else {
                    $destino->cantidad = $validatedData['cantidad'];
                }
                $destino->created_by = $validatedData['created_by'];
                $destino->updated_by = $validatedData['created_by'];
                $destino->save();

                History::create([
                    'cantidad' => $validatedData['cantidad'],
                    'id_bodega_origen' => $validatedData['id_bodega_origen'],
                    'id_bodega_destino' => $validatedData['id_bodega_destino'],
                    'id_inventario' => $destino->id,
                    'created_by' => $validatedData['created_by'],
                ]);

                return response()->json([
                    'message' => 'Traslado realizado con éxito',
                    'origen' => $origen,
                    'destino' => $destino,
                ], 200);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El inventario no existe en la bodega de origen',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error al procesar el traslado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
