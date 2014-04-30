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

* That's it! Congratulations, Opensoft/DoctrineAdditionsBundle has been successfully installed!

---
[Index](../../README.md)
