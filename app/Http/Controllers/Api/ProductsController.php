<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Product::class);
        $product = Product::paginate(10);
        return (new ProductCollection($product))->additional([
            'message' => 'data produk berhasil di tampilkan'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $this->authorize('create', Product::class);
        $product = Product::create($request->validated());
        return response()->json([
            'message' => 'data produk berhasil di tambahkan',
            'data' => new ProductResource($product)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('view', $product);
        return response()->json([
            'message' => 'Produk berhasil di tampilkan',
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);
        $product->update($request->validated());

        return response()->json([
            'message' => 'data produk berhasil di ubah',
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);
        $product->delete();

        return response()->json([
            'message' => 'data produk berhasil di hapus',
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $product);
        $product->forceDelete();

        return response()->json([
            'message' => 'data produk berhasil di hapus permanen',
        ]);
    }

    public function restore(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $product);
        $product->restore();

        return response()->json([
            'message' => 'data produk berhasil di pulihkan',
            'data' => new ProductResource($product),
        ]);
    }

    public function trashed()
    {
        $this->authorize('viewAny', Product::class);
        $trashedProduct = Product::onlyTrashed()->paginate(10);

        return (new ProductCollection($trashedProduct))->additional([
            'message' => 'data produk yang di hapus berhasil ditampilkan',
        ]);
    }
}
