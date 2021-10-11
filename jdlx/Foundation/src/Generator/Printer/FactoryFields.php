<?php

namespace Jdlx\Generator\Printer;

use Illuminate\Support\Str;
use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class FactoryFields extends Printer
{
    public $fieldInfo;


    public function __construct($fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
    }

    public function addToStub(PhpStub $stub): void
    {
        $fillers = [];
        foreach ($this->fieldInfo as $field) {
            $name = $field["name"];
            $type = $field["type"];

            $assign = "'" . $name . "' => ";
            $generator = null;
            if ($name === "id" && $type === "integer") {
                continue;
            } else if (($name === "id" || Str::endsWith($name, "_id")) && $type === "text") {
                $generator = '$faker->uuid';
            } else if (Str::endsWith($name, "name")) {
                $generator = '$faker->name';
            } else if ($type === "string" && Str::contains($name, "email")) {
                $generator = '$faker->unique()->safeEmail';
            } else {
                switch ($type) {
                    case 'integer':
                        $generator = '$faker->randomNumber(2)';
                        break;
                    case 'text':
                        $generator = ' $faker->words(1, true)';
                        break;
                    case 'textarea':
                        $generator = '$faker->paragraph';
                        break;
                    case 'timestamp':
                        $generator = '$faker->dateTimeBetween(\'-1 years\', \'-1 hour\')';
                        break;
                    case 'datetime':
                        $generator = '$faker->dateTimeBetween(\'-1 years\', \'-1 hour\')';
                        break;
                    case 'boolean':
                        $generator = '$faker->boolean';
                        break;
                    case 'json':
                        $generator = '["foo" => "bar"]';
                        break;
                    default:
                        $generator = "''";
                }
            }

            $fillers[] = $assign . $generator;
        }

        $stub->generatePlaceholders("factory_fields", $this->formatArray($fillers, 2));
    }
}
