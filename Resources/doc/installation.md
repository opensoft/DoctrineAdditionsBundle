Installation
============

* Run command `php composer.phar require opensoft/doctrine:additions-bundle:dev-master`
* Add this bundle to your app/AppKernel.php:

``` php
    public function registerBundles()
    {
        return array(
            // ...
            new Opensoft\DoctrineAdditionsBundle\OpensoftDoctrineAdditionsBundle(),
            // ...
        );
    }
```

* That's it! Congradilations Opensoft/DoctrineAdditionsBundle is installed successfully!

---
[Index](../../README.md)
