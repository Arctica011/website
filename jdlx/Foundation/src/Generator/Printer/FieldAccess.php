<?php

namespace Jdlx\Generator\Printer;

use Jdlx\Generator\Printer;
use Jdlx\Generator\Stub\PhpStub;

class FieldAccess extends Printer
{
    public $fieldAccess;

    public function __construct($fieldAccess)
    {
        $this->fieldAccess = $fieldAccess;
    }

    public function addToStub(PhpStub $stub): void
    {
        $fields = [];
        foreach ($this->fieldAccess as $name => $options) {
            $args = $this->activeOptions($options);
            $fields[] = "'{$name}' => ['" . implode("', '", $args) . "']";
        }

        $stub->generatePlaceholders("field_access", $this->formatArray($fields, 1));
    }

    protected function activeOptions($field): array
    {
        return array_keys(array_filter($field, function ($x) {
            return $x === true;
        }));
    }
}
