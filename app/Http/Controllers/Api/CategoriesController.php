<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::paginate(10);
        return (new CategoryCollection($category))->additional([
            'message' => 'category berhasil di tampilkan',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        $response = [
            'message' => 'category berhasil di buat',
            'data' => new CategoryResource($category),
        ];
        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json([
            'message' => 'category berhasil di tampilkan',
            'data' => new CategoryResource($category),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return response()->json([
            'message' => 'berhasil update data',
            'data' => new CategoryResource($category),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'category berhasil di hapus',
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();

        return response()->json([
            'message' => 'category permanently deleted successfully',
        ]);
    }

    public function restore(string $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return response()->json([
            'message' => 'category restored successfully',
            'data' => new CategoryResource($category),
        ]);
    }

    public function trashed()
    {
        $trashedCategory = Category::onlyTrashed()->paginate(10);

        return (new CategoryCollection($trashedCategory))->additional([
            'message' => 'category yang di hapus berhasil ditampilkan',
        ]);
    }
}
