<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
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
     * @param  mixed $request {id_producto, bodega_origen_id, bodega_destino_id, cantidad, created_by}
     * @return void
     */
    public function trasladar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_producto' => 'required|exists:productos,id',
                'bodega_origen_id' => 'required|exists:bodegas,id',
                'bodega_destino_id' => 'required|exists:bodegas,id|different:bodega_origen_id',
                'cantidad' => 'required|numeric|min:1',
                'created_by' => 'required|exists:users,id',
            ]);

            return DB::transaction(function () use ($validatedData) {
                $origen = Inventory::where([
                    'id_producto' => $validatedData['id_producto'],
                    'id_bodega' => $validatedData['bodega_origen_id'],
                ])->firstOrFail();

                if ($origen->cantidad < $validatedData['cantidad']) {
                    abort(400, 'Cantidad insuficiente en la bodega de origen');
                }

                $destino = Inventory::firstOrNew([
                    'id_producto' => $validatedData['id_producto'],
                    'id_bodega' => $validatedData['bodega_destino_id'],
                ]);

                $origen->cantidad -= $validatedData['cantidad'];
                $origen->save();

                if ($destino->exists) {
                    $destino->cantidad += $validatedData['cantidad'];
                } else {
                    $destino->cantidad = $validatedData['cantidad'];
                }
                $destino->save();

                History::create([
                    'id_producto' => $validatedData['id_producto'],
                    'bodega_origen_id' => $validatedData['bodega_origen_id'],
                    'bodega_destino_id' => $validatedData['bodega_destino_id'],
                    'cantidad' => $validatedData['cantidad'],
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
