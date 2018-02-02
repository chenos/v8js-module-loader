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

        $v8 = new V8Js();

        $v8->setModuleNormaliser([$loader, 'normaliseIdentifier']);
        $v8->setModuleLoader([$loader, 'loadModule']);

        $this->v8 = $v8;
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
