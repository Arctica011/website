<?php


namespace Jdlx\Commands\Make;


trait WithStubs
{
    /*
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function fromStub($name, $replace)
    {
        $stub = $this->getStubContent($name);
        return str_replace(
            array_keys($replace), array_values($replace), $stub
        );
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStubContent($name)
    {
        $stub = "/stubs/{$name}.stub";

        $path = file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;

        return file_get_contents($path);
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
