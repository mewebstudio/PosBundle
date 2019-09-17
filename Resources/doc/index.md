#Virtual POS bundle for Turkey banks.

###Installation
Add the `mews/pos-bundle` package to your `require` section in the `composer.json` file.

```bash
$ composer require mews/pos-bundle
```
Add the RedisBundle to your application's kernel:
```php
<?php
public function registerBundles()
{
    $bundles = [
        // ...
        new Mews\PosBundle\PosBundle(),        
        // ...
    ];
    ...
}
```

###Usage
