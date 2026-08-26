<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupliersCollection;
use App\Http\Resources\SupliersResource;
use App\Models\ModelSupliers;

class SupliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suplier = ModelSupliers::paginate(10);
        return (new SupliersCollection($suplier))->additional([
            'message' => 'data supliers berhasil di ambil'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        $suplier = ModelSupliers::create($request->validated());
        return response()->json([
            'message' => 'data suplier berhasil di tambahkan',
            'data' => new SupliersResource($suplier)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $suplier = ModelSupliers::findOrFail($id);
        return response()->json([
            'message' => 'data suplier yang di minta berhasil di tampilkan',
            'data' => new SupliersResource($suplier)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, string $id)
    {
        $suplier = ModelSupliers::findOrFail($id);
        $suplier->update($request->validated());
        return response()->json([
            'message' => 'data suplier berhasil di update',
            'data' => new SupliersResource($suplier)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $suplier = ModelSupliers::findOrFail($id);
        $suplier->delete();
        return response()->json([
            'message' => 'data berhasil di hapus'
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $suplier = ModelSupliers::withTrashed()->findOrFail($id);
        $suplier->forceDelete();

        return response()->json([
            'message' => 'data suplier berhasil di hapus permanen',
        ], 200);
    }

    public function restore(string $id)
    {
        $suplier = ModelSupliers::onlyTrashed()->findOrFail($id);
        $suplier->restore();

        return response()->json([
            'message' => 'category restored successfully',
            'data' => new SupliersResource($suplier),
        ], 200);
    }

    public function trashed()
    {
        $trashedSuplier = ModelSupliers::onlyTrashed()->paginate(10);

        return (new SupliersCollection($trashedSuplier))->additional([
            'message' => 'data suplier yang di hapus berhasil ditampilkan',
        ]);
    }
}
