<?php

namespace Chenos\V8Js\ModuleLoader;

interface FileSystemInterface
{
    public function exists($path);

    public function dirname($path);

    public function filename($path);

    public function get($path);

    public function pathJoin(...$args);

    public function isFile($path);
}
