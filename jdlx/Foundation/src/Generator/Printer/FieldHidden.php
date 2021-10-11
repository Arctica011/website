<?php

namespace Jdlx\Generator\Printer;

use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class FieldHidden extends Printer
{
    public $fieldInfo;


    public function __construct($fieldInfo)
    {
        $this->fieldInfo = $fieldInfo;
    }

    public function addToStub(PhpStub $stub): void
    {
        $stub->generatePlaceholders("fields_hidden",  $this->formatArray([], 1));
    }
}
