<?php

namespace Chenos\V8Js\ModuleLoader;

class ModuleLoader
{
    protected $fs;

    protected $root;

    protected $overrides = [];

    protected $extensions = ['.js'];

    protected $nativeModules = [];

    protected $modulesDirectories = [];

    protected $pathCache = [];

    public function __construct($root, FileSystemInterface $fs = null)
    {
        $this->root = $root;
        $this->fs = $fs instanceof FileSystemInterface ? $fs : new FileSystem();
    }

    public function setExtensions(...$extensions)
    {
        $this->extensions = $extensions;
    }

    public function addOverride($name, $override = null)
    {
        if (func_num_args() == 1 && is_array($name)) {
            $this->overrides = array_merge($this->overrides, $name);
        } elseif (func_num_args() == 2) {
            $this->overrides[$name] = $override;
        }
    }

    public function addModulesDirectory($modulesDirectory)
    {
        $this->modulesDirectories = array_merge(
            $this->modulesDirectories, (array) $modulesDirectory);
    }

    public function normaliseIdentifier($base, $moduleName)
    {
        if (isset($this->overrides[$moduleName])) {
            if (is_object($this->overrides[$moduleName])) {
                return ['', $moduleName];
            }
            $moduleName = $this->overrides[$moduleName];
        }

        if (strpos($moduleName, '.') !== 0 && strpos($moduleName, '/') !== 0) {
            foreach ($this->modulesDirectories as $dir) {
                if ($file = $this->getModuleFile($dir, $moduleName)) {
                    return [$this->fs->dirname($file), $this->fs->filename($file)];
                }
            }

            return ['', $moduleName];
        }

        if (strpos($moduleName, '/') === 0) {
            $file = $this->getModuleFile($moduleName);
        } elseif (strpos($base, '/') === 0) {
            $file = $this->getModuleFile($this->fs->pathJoin($base, $moduleName));
        } else {
            $file = $this->getModuleFile($this->fs->pathJoin($this->root, $base, $moduleName));
        }

        if (! $file) {
            throw new \Exception("'$moduleName' does not exists.");
        }

        return [$this->fs->dirname($file), $this->fs->filename($file)];
    }

    public function loadModule($moduleName)
    {
        if (! isset($this->overrides[$moduleName])) {
            return $this->fs->exists($moduleName) ? $this->fs->get($moduleName) : null;
        }

        return $this->overrides[$moduleName];
    }

    public function getModuleFile($path)
    {
        if (func_num_args() > 1) {
            $path = $this->fs->pathJoin(func_get_args());
        }

        if (isset($this->pathCache[$path])) {
            return $this->pathCache[$path];
        }

        if ($this->fs->isFile($path)) {
            return $this->pathCache[$path] = $path;
        }

        if ($this->fs->exists($this->fs->pathJoin($path, 'package.json'))) {
            $package = $this->fs->get($this->fs->pathJoin($path, 'package.json'));
            if (isset($package->main)) {
                return $this->pathCache[$path] = $this->getModuleFile($path, $package->main);
            }
        }

        if ($this->fs->exists($fullPath = $this->fs->pathJoin($path, 'index.js'))) {
            return $this->pathCache[$path] = $fullPath;
        }

        foreach ($this->extensions as $extension) {
            if ($this->fs->exists($path.$extension)) {
                return $this->pathCache[$path] = $path.$extension;
            }
        }

        return false;
    }
}
