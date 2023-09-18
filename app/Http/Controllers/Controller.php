<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    const CACHE_KEY = '';

    protected int $authUserId;

    public function __construct()
    {
        //TODO: uncomment when user auth will be implemented
        $this->authUserId = auth()->user()?->id ?? 1;
//        $this->authUserId = 1;
    }

    protected function doesAuthUserEquals(int $userId)
    {
        return $this->authUserId && $userId == $this->authUserId;
    }
}
