<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CookListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cookList = parent::toArray($request);
        if (!isset($cookList['items'])) {
            $cookList['items'] = [];
        }
        return $cookList;
    }
}
