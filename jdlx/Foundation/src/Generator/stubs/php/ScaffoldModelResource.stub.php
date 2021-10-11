<?php

namespace App\Http\Resources;

use App\Generated\__MODEL__\__MODEL__Fields;
use Illuminate\Http\Resources\Json\JsonResource;

class __MODEL__Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return __MODEL__Fields::resourceFields($this);
    }
}
