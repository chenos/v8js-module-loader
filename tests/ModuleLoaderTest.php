<?php

use PHPUnit\Framework\TestCase;
use Chenos\V8Js\ModuleLoader\ModuleLoader;

class ModuleLoaderTest extends TestCase
{
    public function setUp()
    {
        $loader = new ModuleLoader(__DIR__.'/javascript/entry');

        $loader->setExtensions('.js', '.json');
        $loader->addVendorDirectory(__DIR__.'/javascript/node_modules');

        $loader->addOverride('vue', 'vue/dist/vue.runtime.common.js');

        $v8 = new V8Js();

        $v8->setModuleNormaliser([$loader, 'normaliseIdentifier']);
        $v8->setModuleLoader([$loader, 'loadModule']);

        $this->v8 = $v8;
    }

    /**
     */
    public function test1()
    {
        $this->assertEquals(true, true);
    }
}
