<?php

namespace Chenos\V8JsModuleLoader;

interface FileSystemInterface
{
    public function exists($path);

    public function dirname($path);

    public function filename($path);

    public function get($path);

    public function pathJoin(...$args);

    public function isFile($path);
}
