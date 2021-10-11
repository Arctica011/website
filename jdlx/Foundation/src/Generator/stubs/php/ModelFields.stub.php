<?php

namespace App\Generated\__MODEL__;

use Faker\Generator;

class __MODEL__Fields
{
    public const ACCESS = __FIELD_ACCESS__;

    public const CASTS = __FIELD_CASTS__;

    public const FILLABLE = __FIELDS_FILLABLE__;

    public const HIDDEN = __FIELDS_HIDDEN__;

    public static function factory(Generator $faker)
    {
        return __FACTORY_FIELDS__;
    }

    public static function resourceFields($model): array
    {
        return __RESOURCE_FIELDS__;
    }

    public static function validation($for)
    {
        return [];
    }

}
