<?php

namespace Jdlx\Generator\Printer;

use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class FieldFillable extends Printer
{
    public $fieldAccess;

    public function __construct($fieldAccess)
    {
        $this->fieldAccess = $fieldAccess;
    }

    public function addToStub(PhpStub $stub): void
    {
        $fillable = [];
        foreach ($this->fieldAccess as $name => $options) {
            if (!$options['readOnly']) {
                $fillable[] = "'$name'";
            }
        }

        $stub->generatePlaceholders("fields_fillable",  $this->formatArray($fillable, 1));
    }
}
