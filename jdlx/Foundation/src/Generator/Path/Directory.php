<?php

namespace Jdlx\Generator\Path;

use Illuminate\Support\Str;
use Jdlx\Generator\Exception\FileExistsException;
use Jdlx\Generator\Path;

class Directory extends Path
{
    public function getStubs(){
        $base = realpath(Path::getFullPath($this->path));
        $pattern = "$base/*.stub.js";
        $files = $this->rglob($pattern);

        return array_map(function($file) use ($base) {
               return [
                   "path" => $file,
                   "dest" => Str::replaceFirst($base, "", $file)
               ];
        }, $files);
    }


// Does not support flag GLOB_BRACE
    protected function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}
