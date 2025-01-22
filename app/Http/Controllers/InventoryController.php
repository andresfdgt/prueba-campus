<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'bodega_id' => 'required|exists:bodegas,id',
                'cantidad' => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();

            $inventario = Inventory::firstOrNew([
                'producto_id' => $validatedData['producto_id'],
                'bodega_id' => $validatedData['bodega_id'],
            ]);

            if ($inventario->exists) {
                $inventario->cantidad += $validatedData['cantidad'];
            } else {
                $inventario->cantidad = $validatedData['cantidad'];
            }

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

    public function trasladar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'bodega_origen_id' => 'required|exists:bodegas,id',
                'bodega_destino_id' => 'required|exists:bodegas,id|different:bodega_origen_id',
                'cantidad' => 'required|numeric|min:1',
            ]);

            return DB::transaction(function () use ($validatedData) {
                $origen = Inventory::where([
                    'producto_id' => $validatedData['producto_id'],
                    'bodega_id' => $validatedData['bodega_origen_id'],
                ])->firstOrFail();

                if ($origen->cantidad < $validatedData['cantidad']) {
                    abort(400, 'Cantidad insuficiente en la bodega de origen');
                }

                $destino = Inventory::firstOrNew([
                    'producto_id' => $validatedData['producto_id'],
                    'bodega_id' => $validatedData['bodega_destino_id'],
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
                    'producto_id' => $validatedData['producto_id'],
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
