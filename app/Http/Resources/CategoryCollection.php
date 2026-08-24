<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public $collects = CategoryResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total' => $this->resource->total(),
                'count' => $this->resource->count(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'total_pages' => $this->resource->lastPage(),
            ],
            'links' => [
                'first' => $this->resource->url(1),
                'last'  => $this->resource->url($this->resource->lastPage()),
                'prev'  => $this->resource->previousPageUrl(),
                'next'  => $this->resource->nextPageUrl(),
            ],
        ];
    }
}
