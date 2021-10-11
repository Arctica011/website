<?php

namespace App\Models;

use App\Generated\__MODEL__\__MODEL__Fields;
use App\Generated\__MODEL__\With__MODEL__CrudFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class __MODEL__ extends Model
{
    use HasFactory;
    use With__MODEL__CrudFields;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
    protected $fillable = __MODEL__Fields::FILLABLE;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = __MODEL__Fields::HIDDEN;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = __MODEL__Fields::CASTS;
}
