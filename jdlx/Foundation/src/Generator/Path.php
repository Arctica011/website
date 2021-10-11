<?php

namespace Jdlx\Generator;

use Illuminate\Support\Str;

class Path
{

    protected $path;

    /**
     * Path to the RootFolder of the app, from where
     * we can generate files
     *
     * @var string
     */
    public static $rootFolder;

    public function __construct($path)
    {
        $this->setPath($path);
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return file_exists(self::getFullPath($this->path));
    }

    public static function getFullPath($relative): string
    {
        $root = self::$rootFolder ?? base_path();
        if (Str::startsWith($relative, $root)) {
            return $relative;
        }

        return implode(DIRECTORY_SEPARATOR, [
            rtrim($root, DIRECTORY_SEPARATOR),
            ltrim($relative, DIRECTORY_SEPARATOR)
        ]);
    }

    /**
     * Given two absolute paths, return the relative path
     * from [from] file to [to] file
     * @param $from
     * @param $to
     */
    public static function toRelative($from, $to): string
    {
        $ffp = pathinfo($from);
        $tfp = pathinfo($to);
        $fromDirectories = explode("/", trim(pathinfo($from, PATHINFO_DIRNAME), "/"));
        $toDirectories = explode("/", trim(pathinfo($to, PATHINFO_DIRNAME), "/"));

        //Establish the shared root
        while (isset($fromDirectories[0]) && isset($toDirectories[0]) && $fromDirectories[0] === $toDirectories[0]) {
            array_shift($fromDirectories);
            array_shift($toDirectories);
        }

        $dirUp = count($fromDirectories);
        $toFile = implode("/", [...$toDirectories, pathinfo($to, PATHINFO_BASENAME)]);
        $path = ($dirUp === 0 ? "./" : str_repeat("../", $dirUp)).$toFile;

        return $path;

    }

    protected function createDirectory()
    {
        $dir = dirname(self::getFullPath($this->path));
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
