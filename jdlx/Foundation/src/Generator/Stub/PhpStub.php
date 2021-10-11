<?php

namespace Jdlx\Generator\Stub;

use Illuminate\Support\Str;
use Jdlx\Generator\Stub;

class PhpStub extends Stub
{
    protected $uses = [];

    protected $traits = [];

    public function addUse($fqcn): PhpStub
    {
        $this->uses[] = $fqcn;
        return $this;
    }

    public function addTraits($fqcn): PhpStub
    {
        $this->addUse($fqcn);
        $this->traits[] = class_basename($fqcn);
        return $this;
    }

    public function generate(): string
    {
        $string = parent::generate();

        // Inject uses
        $string = $this->injectUses($string);
        // Inject traits
        $string = $this->injectTraits($string);

        return $string;
    }

    public function injectUses($string): string
    {
        $nsRegexp = '/use (.*);/';

        if (preg_match($nsRegexp, $string)) {
            // We can find uses, let's use find em
            list($start, $string) = $this->cut("before", $nsRegexp, $string);
            list($middle, $end) = $this->cut("before", '/(\/\*\*|trait|class|interface)/', $string);
        } else { // Inject traits after namespace
            list($start, $end) = $this->cut("after", '/namespace/', $string);
            $start .= "\n";
            $middle = "";
        }

        // Extract the exact namespaces
        // Filter out any empty lines
        // Merge with uses we know we need to add
        // Remove duplicates
        $uses = array_unique(array_merge(array_filter(array_map(function ($line) use ($nsRegexp) {
                if (preg_match($nsRegexp, $line, $matches)) {
                    return $matches[1];
                } else {
                    return false;
                }
            }, explode("\n", $middle))
        ), $this->uses));

        sort($uses);

        return implode("\n", [
            $start,
            implode("", array_map(function ($use) {
                return "use {$use};\n";
            }, $uses)),
            $end
        ]);
    }

    public function injectTraits($string): string
    {
        $traitRegexp = '/\s\s(\s*)use (.*);/';

        if (preg_match($traitRegexp, $string)) {
            // We can find traits, let's use find em
            list($start, $string) = $this->cut("before", $traitRegexp, $string);
            list($traits, $end) = $this->cut("before", '/(\/\*\*|protected|private|public|\})/', $string);
        } else { // Inject traits in between
            list($start, $end) = $this->cut("after", '/(\{|class(.*){)/', $string);
            $traits = "";
        }


        // Extract the exact namespaces
        // Don't filter empty or comment lines
        // Merge with uses we know we need to add
        // Remove duplicates

        $uses = array_unique(array_merge(array_map(function ($line) use ($traitRegexp) {
                if (preg_match($traitRegexp, $line, $matches)) {
                    return "__USE__" . $matches[2];
                } else {
                    return $line;
                }
            }, explode("\n", $traits)
        ), $this->traits));

        return implode("\n", [
            $start,
            implode("", array_map(function ($use) {
                if (Str::startsWith($use, "__USE__")) {
                    return "    use " . Str::replaceFirst("__USE__", "", $use) . ";\n";
                }
                return $use . "\n";
            }, $uses)),
            $end
        ]);
    }
}
