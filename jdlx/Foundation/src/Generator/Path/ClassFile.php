<?php

namespace Jdlx\Generator\Path;

use Illuminate\Support\Str;

class ClassFile extends File
{
    public function setPath($path)
    {
        $path = ltrim($path, DIRECTORY_SEPARATOR);
        $path = !Str::endsWith($path, ".php") ? $path . ".php" : $path;
        $this->path = $path;
    }

    public static function fromFQN($fqn): ClassFile
    {
        /**
         * Make sure the known folder paths are rewritten
         */
        $replacements = [
            "App\\" => "app/",
            "Database\\Factories\\" => "database/factories/",
            "Database\\Seeders\\" => "database/seeders/"
        ];
        $path = str_replace(array_keys($replacements), array_values($replacements), trim($fqn, "\\"));
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
        return new ClassFile("{$path}.php");
    }
}
