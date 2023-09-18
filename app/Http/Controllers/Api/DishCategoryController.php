<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishCategoryRequest;
use App\Http\Requests\UpdateDishCategoryRequest;
use App\Http\Resources\DishCategoryResource;
use App\Models\DishCategory;
use Illuminate\Support\Facades\Cache;
use App\Traits\SimpleResponseCachingTrait;

class DishCategoryController extends Controller
{
    use SimpleResponseCachingTrait;

    const CACHE_KEY = 'dish_categories';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Cache::rememberForever($this->getCacheKey(), function() {
            return DishCategoryResource::collection(DishCategory::all());
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDishCategoryRequest $request): DishCategoryResource
    {
        $dishCategory = new DishCategory($request->validated());
        $dishCategory->save();
        $this->clearCache();
        return DishCategoryResource::make($dishCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(DishCategory $dishCategory)
    {
        return DishCategoryResource::make($dishCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDishCategoryRequest $request, DishCategory $dishCategory)
    {
        $dishCategory->update($request->validated());
        $this->clearCache();
        return DishCategoryResource::make($dishCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DishCategory $dishCategory)
    {
        $dishCategory->delete();
        $this->clearCache();
    }
}
