<?php

namespace Jdlx\Generator;

use Illuminate\Support\Str;
use Jdlx\Generator\Path\StubFile;

class Stub
{
    /**
     * @var StubFile
     */
    protected $stubPath;

    /**
     * @var array
     */
    protected $replacements = [];

    public function __construct(StubFile $stubPath)
    {
        $this->stubPath = $stubPath;
    }

    /**
     * Adds multiple replacements using a variety of possible placholders.
     * These include:
     * \__PLACHOLDER__
     * {{ placeholder }}
     * {{placeholder}}
     *
     * @param $placeholder
     * @param $replacement
     * @return $this
     */
    public function generatePlaceholders($placeholder, $replacement): Stub
    {
        return $this
            ->addReplacement("__" . Str::upper($placeholder) . "__", $replacement)
            ->addReplacement("{{ " . $placeholder . " }}", $replacement)
            ->addReplacement("{{" . $placeholder . "}}", $replacement);
    }

    public function addReplacement($placeholder, $replacement): Stub
    {
        $this->replacements[$placeholder] = $replacement;
        return $this;
    }

    public function addReplacements($replacements): Stub
    {
        $this->replacements = array_merge($this->replacements, $replacements);
        return $this;
    }

    public function generate(): string
    {
        $content = $this->stubPath->getContents();
        return str_replace(
            array_keys($this->replacements), array_values($this->replacements), $content
        );
    }

    public function generatePath($path = null): string
    {
        $path = str_replace(".stub", "",$path ?? $this->stubPath->getPath());

        return str_replace(
            array_keys($this->replacements), array_values($this->replacements), $path
        );
    }

    /**
     * @param $order string before or after
     * @param $regexp
     * @param $content
     * @return array
     */
    public function cut(string $order, string $regexp, string $content): array
    {
        $lines = explode("\n", $content);
        $start = [];

        while (sizeof($lines) > 0) {
            $line = array_shift($lines);
            if (preg_match($regexp, $line)) {
                if ($order === "before") {
                    array_unshift($lines, $line);
                } else {
                    $start[] = $line;
                }
                break;
            } else {
                $start[] = $line;
            }
        }

        return [
            implode("\n", $start),
            implode("\n", $lines)
        ];
    }

    public function cutOn()
    {

    }
}
