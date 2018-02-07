<?php

require __DIR__.'/../vendor/autoload.php';

use Chenos\V8JsModuleLoader\ModuleLoader;

$loader = new ModuleLoader(__DIR__);

$loader->setExtensions('.js', '.json');
$loader->addOverride('vue', 'vue/dist/vue.runtime.common.js');
$loader->addVendorDir(__DIR__.'/node_modules');

$v8 = new V8Js();

$v8->setModuleNormaliser([$loader, 'normaliseIdentifier']);
$v8->setModuleLoader([$loader, 'loadModule']);

$js = <<<JS
this.process = { env: { VUE_ENV: 'server', NODE_ENV: 'production' } }
this.global = { process: process }

const Vue = require('vue')
const renderVueComponentToString = require('vue-server-renderer/basic')

const app = new Vue({
  template: `<div>Hello Vue!</div>`
})

renderVueComponentToString(app, (err, html) => {
    print(html)
})
JS;

$v8->executeString($js);