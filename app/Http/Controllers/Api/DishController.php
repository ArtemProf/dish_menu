<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OcrDishRequest;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\DishResource;
use App\Http\Resources\OcrDishResource;
use App\Models\Dish;
use App\Services\OCR\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Traits\SimpleResponseCachingTrait;

class DishController extends Controller
{
    use SimpleResponseCachingTrait;

    const CACHE_KEY = 'dishes';

    /**
     * Display a listing of the resource.
     */
    public function index(): mixed
    {
        return Cache::rememberForever($this->getCacheKey(), function() {
            return DishResource::collection(Dish::all());
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDishRequest $request): DishResource
    {
        $dish = new Dish($request->validated());
        $dish->save();
        $this->clearCache();
        return DishResource::make($dish);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dish $dish)
    {
        return DishResource::make($dish);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDishRequest $request, Dish $dish)
    {
        $dish->update($request->validated());
        $this->clearCache();
        return DishResource::make($dish);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish)
    {
        $dish->delete();
        $this->clearCache();
    }

    //tools
    public function ocr(OcrDishRequest $request)
    {
        $validated = $request->validated();

        $ocr = resolve(OcrService::class);

        $result = $ocr->processImage($validated['image']);

        return OcrDishResource::make($result);
    }
}
