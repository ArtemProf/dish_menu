<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCookListRequest;
use App\Http\Requests\UpdateCookListRequest;
use App\Http\Resources\CookListResource;
use App\Models\CookList;
use App\Models\CookListItem;
use App\Traits\SimpleResponseCachingTrait;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class CookListController extends Controller
{
    use SimpleResponseCachingTrait;

    const CACHE_KEY = 'cook_list';
    const CACHE_KEY_SECONDARY = CookListItemController::CACHE_KEY;

    /**
     * Display a listing of the resource.
     */
    public function index(): mixed
    {
        return Cache::rememberForever($this->getCacheKey($this->authUserId), function () {
            return CookListResource::collection(CookList::whereUserId($this->authUserId)->get());
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCookListRequest $request): CookListResource
    {
        $data = $request->validated();

        [$items, $data] = $this->unsetItems($data);

        $cookList = new CookList($data);
        $cookList->user_id = $this->authUserId;
        $cookList->save();

        $this->createOrUpdateItems($cookList->getKey(), $items);

        $this->clearCache();
        $this->clearCacheByKey(self::CACHE_KEY_SECONDARY);
        return CookListResource::make($cookList);
    }

    private function createOrUpdateItems(int $cookListId, array $items = [])
    {
        if (empty($items)) {
            return;
        }
        foreach ($items as $item) {
            if (!isset($item['id'])) {
                $item['cook_list_id'] = $cookListId;
                $item = (new CookListItem($item));
                $item->user()->associate($this->authUserId);
                $item->save();

                continue;
            }

            CookListItem::findOrFail($item['id'])?->update($item);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $cookListId)
    {
        if ($cookListId < 0) {
            $cookList = CookList::findOrCreateDefault($this->authUserId);
            return CookListResource::make($cookList);
        }
        $cookList = CookList::find($cookListId);
        $this->checkUserPermissionsOrException($cookList);

        return CookListResource::make($cookList);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCookListRequest $request, CookList $cookList)
    {
        $this->checkUserPermissionsOrException($cookList);

        $data = $request->validated();

        [$items, $data] = $this->unsetItems($data);
        $cookList->update($data);

        $this->createOrUpdateItems($cookList->getKey(), $items);

        $this->clearCache();
        $this->clearCacheByKey(self::CACHE_KEY_SECONDARY);
        return CookListResource::make($cookList);
    }

    public function unsetItems(array $data): array
    {
        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        return [$items, $data];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CookList $cookList)
    {
        $this->checkUserPermissionsOrException($cookList);

        $cookList->delete();
        $this->clearCache();
    }

    private function checkUserPermissionsOrException(CookList $cookList)
    {
        if (!$this->doesAuthUserEquals($cookList->user_id)) {
            throw new Exception(__('exceptions.no_permission_to_change_list'));
        }
    }
}
