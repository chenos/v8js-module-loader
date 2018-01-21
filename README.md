# V8Js ModuleLoader

```php
use Chenos\V8Js\ModuleLoader\ModuleLoader;

$loader = new ModuleLoader(__DIR__);

$loader->setExtensions('.js', '.json');
$loader->addOverride('vue', 'vue/dist/vue.runtime.common.js');
$loader->addModulesDirectory(__DIR__.'/node_modules');

$v8 = new V8Js();

$v8->setModuleNormaliser([$loader, 'normaliseIdentifier']);
$v8->setModuleLoader([$loader, 'loadModule']);
```
