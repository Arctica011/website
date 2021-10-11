<?php

namespace Jdlx\Generator\Path;

class StubFile extends File
{
    public static function phpStub($name): StubFile
    {
        $path = __DIR__ . "/../stubs/php/{$name}.stub.php";

        if (!file_exists($path)) {
            throw new \Exception("Stub {$name} not found, did you make sure to make the extension .stub.php");
        }
        return new StubFile(realpath($path));
    }
}
