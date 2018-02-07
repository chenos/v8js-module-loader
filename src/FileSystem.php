<?php

namespace Chenos\V8JsModuleLoader;

use Webmozart\PathUtil\Path;

class FileSystem implements FileSystemInterface
{
    public function dirname($path)
    {
        return Path::getDirectory($path);
    }

    public function filename($path)
    {
        return Path::getFilename($path);
    }

    public function pathJoin(...$args)
    {
        return Path::join(...$args);
    }

    /**
     * Determine if a file or directory exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    public function get($path)
    {
        if (substr($path, -5) === '.json') {
            return json_decode(file_get_contents($path));
        }

        return file_get_contents($path);
    }

    public function isFile($path)
    {
        return is_file($path);
    }
}
