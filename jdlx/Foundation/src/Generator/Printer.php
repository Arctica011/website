<?php
namespace Jdlx\Generator;

class Printer
{
    protected static $indentationSize = 4;

    protected function indent($lines, $depth){
        $padding = str_repeat(" ", $depth * self::$indentationSize);
        return array_map(function($line) use ($padding) { return "{$padding}{$line}"; }, $lines);
    }

    protected function formatArray($values, $depth = 2): string
    {
        $values = $this->indent(array_merge($this->indent($values, 1), ["]"]), $depth);
        return "[\n". implode(",\n" ,$values);
    }

    protected function formatLines($lines, $prefix = ""): string
    {
        return implode("\n{$prefix}", $lines);
    }

    protected function makeDoc($properties, $indentation = 1, $pre = "  * ", $withOpen = false, $indentationSize = 2)
    {
        $lines = $this->docLines($properties);
        return $this->toDocString($lines, $pre, $indentation, $indentationSize);
    }

    private function toDocString($lines, $pre, $indentation, $indentationSize)
    {
        $padding = str_repeat(" ", $indentation * $indentationSize);
        $prefix = $pre . $padding;
        foreach ($lines as $key => $line) {
            if (is_array($line)) {

                $props = $this->toDocString($line['children'], $pre, $indentation + 1, $indentationSize);
                $lines[$key] = $prefix . $line['prop'] . "(\n" . $props . "\n" . $prefix . ")";
            } else {
                $lines[$key] = $pre . $padding . $lines[$key];
            }
        }

        return implode(",\n", $lines);
    }

    private function docLines($properties)
    {
        $lines = [];
        foreach ($properties as $key => $value) {
            if (is_array($value)) {
                $lines[] = ["prop" => $key, "children" => $this->docLines($value)];
            } else {
                if (is_bool($value)) {
                    $lines[] = "{$key}=" . ($value ? "true" : "false");
                } else {
                    $lines[] = "{$key}=\"{$value}\"";
                }
            }
        }
        return $lines;
    }
}
