<?php

namespace Jdlx\Generator\Printer;

use Illuminate\Support\Str;
use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class ResourceFields extends Printer
{
    public $fieldInfo;


    public function __construct($fieldInfo, $fieldAccess)
    {
        $this->fieldInfo = $fieldInfo;
        $this->fieldAccess = $fieldAccess;
    }

    public function addToStub(PhpStub $stub): void
    {
        $fields = [];
        foreach ($this->fieldInfo as $field) {
            $access = $this->fieldAccess[$field['name']];

            $name = $field['name'];
            $label = $field['name'];
            $type = $field['type'];

            if (!$access["writeOnly"]) {
                $assign = "'{$label}'";
                $value = '$model->' . $name;

                switch ($type){
                    case "timestamp":
                    case "datetime":
                        $field = $value;
                        //$value .= "->toRfc3339String()";
                        $value = "is_null({$field}) ? null : {$field}->toRfc3339String()";
                        break;
                    case "json":
                        $value = "is_array({$value}) ? {$value} : json_decode({$value})";
                        break;
                    default:
                }
                $fields[] = "{$assign} => ${value}";
            }
        }

        $stub->generatePlaceholders("resource_fields", $this->formatArray($fields, 2));
    }
}
