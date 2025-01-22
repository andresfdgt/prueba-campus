<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouse.index');
    }

    public function show($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(["message" => "Warehouse not found"], 404);
        }

        return response()->json($warehouse);
    }

    public function list()
    {
        $warehouses = Warehouse::orderBy("nombre", "asc")->get();
        return response()->json($warehouses);
    }

    /**
     * store
     *
     * @param  mixed $request {nombre, id_responsable, estado}
     * @return void
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|max:30',
                'id_responsable' => 'exists:users,id',
                'estado' => 'integer|in:0,1',
                'created_by' => 'required|exists:users,id',
            ]);

            DB::beginTransaction();

            $warehouse = new Warehouse();
            $warehouse->nombre = $validatedData['nombre'];
            $warehouse->id_responsable = $validatedData['id_responsable'];
            $warehouse->estado = $validatedData['estado'] ?? 1;
            $warehouse->created_by = $validatedData['created_by'];
            $warehouse->updated_by = $validatedData['created_by'];
            $warehouse->save();

            DB::commit();

            return response()->json($warehouse, 201);
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
