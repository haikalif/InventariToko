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
        $this->authorize('viewAny', ModelSupliers::class);
        $suplier = ModelSupliers::paginate(10);
        return (new SupliersCollection($suplier))->additional([
            'message' => 'data supliers berhasil di ambil'
        ]);
    }

    public function store(SupplierRequest $request)
    {
        $this->authorize('create', ModelSupliers::class);
        $suplier = ModelSupliers::create($request->validated());
        return response()->json([
            'message' => 'data suplier berhasil di tambahkan',
            'data' => new SupliersResource($suplier)
        ], 201);
    }

    public function show(string $id)
    {
        $suplier = ModelSupliers::findOrFail($id);
        $this->authorize('view', $suplier);
        return response()->json([
            'message' => 'data suplier yang di minta berhasil di tampilkan',
            'data' => new SupliersResource($suplier)
        ], 200);
    }

    public function update(SupplierRequest $request, string $id)
    {
        $suplier = ModelSupliers::findOrFail($id);
        $this->authorize('update', $suplier);
        $suplier->update($request->validated());
        return response()->json([
            'message' => 'data suplier berhasil di update',
            'data' => new SupliersResource($suplier)
        ], 200);
    }

    public function destroy(string $id)
    {
        $suplier = ModelSupliers::findOrFail($id);
        $this->authorize('delete', $suplier);
        $suplier->delete();
        return response()->json([
            'message' => 'data berhasil di hapus'
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $suplier = ModelSupliers::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $suplier);
        $suplier->forceDelete();

        return response()->json([
            'message' => 'data suplier berhasil di hapus permanen',
        ], 200);
    }

    public function restore(string $id)
    {
        $suplier = ModelSupliers::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $suplier);
        $suplier->restore();

        return response()->json([
            'message' => 'category restored successfully',
            'data' => new SupliersResource($suplier),
        ], 200);
    }

    public function trashed()
    {
        $this->authorize('viewAny', ModelSupliers::class);
        $trashedSuplier = ModelSupliers::onlyTrashed()->paginate(10);

        return (new SupliersCollection($trashedSuplier))->additional([
            'message' => 'data suplier yang di hapus berhasil ditampilkan',
        ]);
    }
}
