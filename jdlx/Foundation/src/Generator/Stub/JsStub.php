<?php

namespace Jdlx\Generator\Stub;

use Jdlx\Generator\Path;
use Jdlx\Generator\Stub;

class JsStub extends Stub
{

    public function generate(): string
    {
        $string = parent::generate();
        return $string;
    }

    /**
     * Generate the path, but also rewrite the paths
     * to match
     *
     * @param $destination
     */
    public function generateForDestination($destination): string
    {
       return $this->makeRelativePaths($this->generate(), $destination);
    }

    protected function makeRelativePaths($content, $destination){
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            $lines[$i] = $this->findAndReplaceImport($lines[$i], "/from( *)\"(\/([A-Za-z0-9_\-\/]*))\";/", 2,$destination);
            $lines[$i] = $this->findAndReplaceImport($lines[$i], "/import( ?)\(\"(\/([A-Za-z0-9_\-\/]*))\"\)/", 2,$destination);

        }

        return implode("\n", $lines);
    }

    protected function findAndReplaceImport($content, $pattern, $group, $destination){
        preg_match($pattern, $content, $matches);
        if ($matches) {
            $match = $matches[$group];
            return str_replace($match, Path::toRelative($destination, $match), $content);
        }
        return $content;
    }

}
