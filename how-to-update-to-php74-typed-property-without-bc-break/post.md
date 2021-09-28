How to Update to PHP 7.4 Typed Property Without BC Break with Rector
====================================================================

[Typed Property](https://wiki.php.net/rfc/typed_properties_v2) is one of the PHP 7.4 feature that allow to write that previously like this:

```php
namespace Lib;

class SomeClass
{
    /** @var int */
    public $a;

    /** @var string */
    protected $b;

    /** @var bool */
    private $c;
}
```

to this:

```php
namespace Lib;

class SomeClass
{
    public int $a;

    protected string $b;

    private bool $c;
}
```

If you follow [Semver](https://semver.org) for versioning, and you don't want to update to major change, eg: version 1 to version 2, changing this will make Break Backward Compatibility, for example:

```php
namespace Lib;

class SomeClass
{
    protected string $b;
}
```

has child in application consumer:

```php
namespace App;

use Lib\SomeClass;

class AChild extends SomeClass
{
    protected $b;
}
```

will result a fatal error:

```
Fatal error: Type of AChild::$b must be string (as in class SomeClass)
```

see https://3v4l.org/X9Yvd . To avoid that, you should only change to private modifier only, so, the change will only to private property:

```diff
class SomeClass
{
    /** @var int */
    public $a;

    /** @var string */
    protected $b;

-    /** @var bool */
-    private $c;
+    private bool $c;
}
```

Want to automate that? You can use [Rector](https://github.com/rectorphp/rector) for it. First, let say, we have an library with the following structure:

```
lib
├── composer.json
├── composer.lock
├── src
│   └── SomeClass.php
```

with composer.json config like this:

```
{
    "require": {
        "php": "^7.4"
    },
    "autoload": {
        "psr-4": {
            "Lib\\": "src/"
        }
    }
}

```

Now, what you need is require the rector as dev dependency:

```bash
cd lib/
composer require --dev rector/rector
```

Then, create a `rector.php` configuration inside the `lib`:

```php
<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);

    // import php 7.4 set list for php 7.4 features
    $containerConfigurator->import(SetList::PHP_74);

    // set Typed Property only for private property
    $services = $containerConfigurator->services();
    $services->set(TypedPropertyRector::class)
        ->call('configure', [[
            TypedPropertyRector::PRIVATE_PROPERTY_ONLY => true,
        ]]);
};
```

Above, we import php 7.4 set list, with configured `TypedPropertyRector` for update to typed property to only change private property only.

Now, let's run rector to see the diff and verify:

```bash
vendor/bin/rector --dry-run
```

![Screen Shot 2021-09-28 at 09 14 28](https://user-images.githubusercontent.com/459648/135011312-138bebac-8ece-4596-b318-0f888b30acc2.png)

Everything seems correct! Let's apply the change:

```bash
vendor/bin/rector
```

![Screen Shot 2021-09-28 at 09 17 03](https://user-images.githubusercontent.com/459648/135011536-1fa49a8b-f295-4b87-9f2e-5209a2fbc2d8.png)

Now, you have typed property in your code! That's it ;)

That's it!