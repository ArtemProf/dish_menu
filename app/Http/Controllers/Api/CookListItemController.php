<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCookListItemRequest;
use App\Http\Requests\UpdateCookListItemRequest;
use App\Models\CookList;
use App\Models\CookListItem;
use App\Traits\SimpleResponseCachingTrait;
use App\Http\Resources\CookListItemResource;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class CookListItemController extends Controller
{
    use SimpleResponseCachingTrait;

    const CACHE_KEY = 'cook_list_item';

    /**
     * Display a listing of the resource.
     */
    public function index(): mixed
    {
        return Cache::rememberForever($this->getCacheKey($this->authUserId), function () {
            return CookListItemResource::collection(CookListItem::whereUserId($this->authUserId)->get());
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCookListItemRequest $request): CookListItemResource
    {
        $cookListItem = new CookListItem($request->validated());
        $cookListItem->user_id = $this->authUserId;
        if (empty($cookListItem->cook_list_id)) {
            $cookListItem->cook_list_id = CookList::findOrCreateDefault($this->authUserId)?->getKey();
        }
        $cookListItem->save();
        $this->clearCache();
        return CookListItemResource::make($cookListItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(CookListItem $cookListItem)
    {
        $this->checkUserPermissionsOrException($cookListItem);

        return CookListItemResource::make($cookListItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCookListItemRequest $request, CookListItem $cookListItem)
    {
        $this->checkUserPermissionsOrException($cookListItem);

        $cookListItem->update($request->validated());
        $cookListItem->updateUser($this->authUserId);
        $this->clearCache();
        return CookListItemResource::make($cookListItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CookListItem $cookListItem)
    {
        $this->checkUserPermissionsOrException($cookListItem);

        $cookListItem->delete();
        $this->clearCache();
    }

    private function checkUserPermissionsOrException(CookListItem $cookListItem)
    {
        if (!$this->doesAuthUserEquals($cookListItem->user_id)) {
            throw new Exception(__('exceptions.no_permission_to_change_item'));
        }
    }
}
