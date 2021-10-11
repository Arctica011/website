<?php

namespace Jdlx\Generator\Printer;

use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\JsStub;
use Jdlx\Generator\Stub\PhpStub;

class JSModelFields extends Printer
{
    public $fieldInfo;

    public function __construct($fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
    }

    public function addToStub(JsStub $stub): void
    {
        $vals = json_encode(array_values($this->fieldInfo), JSON_PRETTY_PRINT);
        $stub->generatePlaceholders("fields", $vals);
    }
}
