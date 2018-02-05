<?php

namespace Chenos\V8Js\ModuleLoader\Tests;

use V8Js;
use PHPUnit\Framework\TestCase;
use Chenos\V8Js\ModuleLoader\ModuleLoader;

class ModuleLoaderTest extends TestCase
{
    public function setUp()
    {
        $this->loader = new ModuleLoader(__DIR__.'/javascript/entry');

        $this->loader->setExtensions('.js', '.json');
        $this->loader->addVendorDirectory(__DIR__.'/javascript/node_modules');

        $v8 = new V8Js();

        $v8->setModuleNormaliser([$this->loader, 'normaliseIdentifier']);
        $v8->setModuleLoader([$this->loader, 'loadModule']);

        $this->v8 = $v8;
    }

    public function testAddOverride1()
    {
        $overrides = ['vue' => 'vue/dist/vue.js'];
        $this->loader->addOverride($overrides);
        $this->assertAttributeEquals($overrides, 'overrides', $this->loader);
    }

    public function testAddOverride2()
    {
        $overrides = ['vue' => 'vue/dist/vue.js'];
        foreach ($overrides as $key => $value) {
            $this->loader->addOverride($key, $value);
        }
        $this->assertAttributeEquals($overrides, 'overrides', $this->loader);
    }

    public function testNestRequire()
    {
        $this->assertOutputEquals("require('./fn')();", 'hello1hello2');
    }

    public function testJsonFile()
    {
        $this->assertOutputEquals("var foo = require('./foo.json'); print(foo.main)", 'foo.js');
    }

    protected function assertOutputEquals($expected, $actual, $message = '')
    {
        ob_start();
        $this->v8->executeString($expected);
        $this->assertEquals(ob_get_clean(), $actual, $message);
    }
}
