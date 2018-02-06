<?php

namespace Chenos\V8Js\ModuleLoader;

class ModuleLoader
{
    protected $fs;

    protected $entryDir;

    protected $overrides = [];

    protected $extensions = ['.js', '.json'];

    protected $nativeModules = [];

    protected $vendorDirs = [];

    protected $pathCache = [];

    public function __construct($entryDir = null, FileSystemInterface $filesystem = null)
    {
        $this->setEntryDir($entryDir ?: getcwd());
        $this->setFileSystem(
            $filesystem instanceof FileSystemInterface 
            ? $filesystem : new FileSystem());
    }

    public function addOverride($name, $override = null)
    {
        if (is_array($name)) {
            $this->overrides = array_merge($this->overrides, $name);
        } elseif (func_num_args() == 2) {
            $this->overrides[$name] = $override;
        }

        return $this;
    }

    public function addVendorDir(...$vendorDirs)
    {
        $this->vendorDirs = array_merge(
            $this->vendorDirs, $vendorDirs);

        return $this;
    }

    public function setEntryDir($entryDir)
    {
        $this->entryDir = $entryDir;

        return $this;
    }

    public function setExtensions(...$extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    public function setFileSystem(FileSystemInterface $filesystem)
    {
        $this->fs = $filesystem;

        return $this;
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
            foreach ($this->vendorDirs as $dir) {
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
            $file = $this->getModuleFile($this->fs->pathJoin($this->entryDir, $base, $moduleName));
        }

        if (! $file) {
            throw new ModuleNotFoundException("Cannot find module '$moduleName'");
        }

        return [$this->fs->dirname($file), $this->fs->filename($file)];
    }

    public function loadModule($moduleName)
    {
        if (! isset($this->overrides[$moduleName])) {
            if (strpos($moduleName, '/') !== 0) {
                $moduleName = implode('/', $this->normaliseIdentifier('', $moduleName));
            }
        
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

        foreach ($this->extensions as $extension) {
            if ($this->fs->exists($path.$extension)) {
                return $this->pathCache[$path] = $path.$extension;
            }
        }

        foreach ($this->extensions as $extension) {
            if ($this->fs->exists("{$path}/index{$extension}")) {
                return $this->pathCache[$path] = "{$path}/index{$extension}";
            }
        }

        return false;
    }
}
