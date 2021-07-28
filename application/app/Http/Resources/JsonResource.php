<?php

namespace App\Responses;

use Illuminate\Http\Resources\Json\JsonResource as BaseResource;

class JsonResource extends BaseResource
{
    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }
}

// MyResource::using(['my_extra_data' => 123]);
// MyResource::collection($resources);

// $this->merge(static::$using)
