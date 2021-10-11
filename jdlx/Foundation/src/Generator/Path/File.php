<?php

namespace Jdlx\Generator\Path;

use Illuminate\Support\Str;
use Jdlx\Generator\Exception\FileExistsException;
use Jdlx\Generator\Path;

class File extends Path
{

    public function getContents(): string
    {
        return file_get_contents(self::getFullPath($this->path));
    }

    /**
     * @param $contents
     * @param false $overwrite
     * @return bool
     * @throws FileExistsException
     */
    public function writeContents(string $contents, bool $overwrite = false, bool $ignoreExists = false): bool
    {
        if (!$overwrite && $this->exists()) {
            if (!$ignoreExists) {
                throw new FileExistsException($this->path);
            } else {
                return 0;
            }
        }

        $this->createDirectory();
        return file_put_contents(self::getFullPath($this->path), $contents);
    }
}
