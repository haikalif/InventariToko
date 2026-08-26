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
        $this->authorize('viewAny', Category::class);
        $category = Category::paginate(10);
        return (new CategoryCollection($category))->additional([
            'message' => 'category berhasil di tampilkan',
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        $category = Category::create($request->validated());
        $response = [
            'message' => 'category berhasil di buat',
            'data' => new CategoryResource($category),
        ];
        return response()->json($response, 201);
    }

    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('view', $category);
        return response()->json([
            'message' => 'category berhasil di tampilkan',
            'data' => new CategoryResource($category),
        ], 200);
    }

    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);
        $category->update($request->validated());

        return response()->json([
            'message' => 'berhasil update data',
            'data' => new CategoryResource($category),
        ], 200);
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);
        $category->delete();

        return response()->json([
            'message' => 'category berhasil di hapus',
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $category);
        $category->forceDelete();

        return response()->json([
            'message' => 'category permanently deleted successfully',
        ]);
    }

    public function restore(string $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $category);
        $category->restore();

        return response()->json([
            'message' => 'category restored successfully',
            'data' => new CategoryResource($category),
        ]);
    }

    public function trashed()
    {
        $this->authorize('viewAny', Category::class);
        $trashedCategory = Category::onlyTrashed()->paginate(10);

        return (new CategoryCollection($trashedCategory))->additional([
            'message' => 'category yang di hapus berhasil ditampilkan',
        ]);
    }
}
