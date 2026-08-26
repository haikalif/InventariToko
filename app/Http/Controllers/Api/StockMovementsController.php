<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementCollection;
use App\Models\ModelStockMovements;

class StockMovementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $stockMovemnet = ModelStockMovements::findOrFail($id);
        return response()->json([
            'message' => 'data yang di pilih berhasil di tampilkan'
        ])
    }

}
