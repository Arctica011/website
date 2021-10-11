<?php

namespace Jdlx\Generator\Printer;

use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class FieldCast extends Printer
{
    public $fieldInfo;


    public function __construct($fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
    }

    public function addToStub(PhpStub $stub): void
    {
        $casts = [];
        foreach ($this->fieldInfo as $info) {
            $name = $info["name"];
            $type = $info["type"];

            if ($type === "json") {
                $casts[] = "'${name}' => 'json'";
            }

            if ($type === "timestamp") {
                $casts[] = "'${name}' => 'datetime'";
            }
        }

        $stub->generatePlaceholders("field_casts",  $this->formatArray($casts, 1));
    }
}
