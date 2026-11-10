<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementCollection;
use App\Http\Resources\StockMovementResource;
use App\Models\ModelStockMovements;

class StockMovementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', ModelStockMovements::class);
        $stockMovement = ModelStockMovements::paginate(10);
        return (new StockMovementCollection($stockMovement))->additional([
            'message' => 'data berhasil di tampilkan'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stockMovement = ModelStockMovements::findOrFail($id);
        $this->authorize('view', $stockMovement);
        return response()->json([
            'message' => 'data yang di pilih berhasil di tampilkan',
            'data' => new StockMovementResource($stockMovement),
        ]);
    }

}
