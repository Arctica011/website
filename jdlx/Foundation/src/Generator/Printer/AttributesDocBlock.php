<?php

namespace Jdlx\Generator\Printer;

use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class AttributesDocBlock extends Printer
{
    public $fieldInfo;

    public function __construct($fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
    }

    public function addToStub(PhpStub $stub): void
    {
        $props = [];
        foreach ($this->fieldInfo as $info) {
            $name = $info["name"];
            $type = $info["type"];

            switch ($type) {
                case "integer":
                    $type = "integer";
                    break;
                case "json":
                    $type = "object";
                    break;
                case "datetime":
                case "timestamp":
                    $stub->addUse("Carbon\Carbon");
                    $type = "Carbon";
                    break;
                case "text":
                case "textarea":
                default:
                    $type = "string";
                    break;
            }

            $props[] = "@property {$type} {$name}";
        }

        $stub->generatePlaceholders("php-doc-props",  $this->formatLines($props, " *  "));
    }
}
