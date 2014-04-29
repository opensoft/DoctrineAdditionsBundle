#YAML Mapping
---

## Contents

* [Change Type Of Your Entities Relationship](#change-type-of-your-entities-relationship)
* [Add/Remove Orphan Removal Strategy](#addremove-orphan-removal-strategy)
* [Override Id Generation Strategy](#override-id-generation-strategy)

## <a id="relationship-type"></a>Change Type Of Your Entities Relationship

If you have two mapped superclasses with unidirectional relationship and you want to make entities with bidirectional relationship based on specified mapped superclasses you need to use `associationOverrides` tag.


```php
    namespace Acme\Bundle\DemoBundle\Entity;

    /**
     * Acme\Bundle\DemoBundle\Entity\BaseUser
     */
    abstract class BaseUser
    {
        /**
         * @var integer
         */
        protected $id;

        /**
         * @var BaseUserProfile
         */
         protected $profile;

         //...
    }
```

```yaml
    Acme\Bundle\DemoBundle\Entity\BaseUser:
      type: mappedSuperclass
      id:
        id:
          type: integer
          generator:
            strategy: AUTO
      oneToOne:
        profile:
          targetEntity: Acme\Bundle\DemoBundle\Entity\BaseUserProfile
          joinColumn:
            name: profile_id
            referencedColumnName: id

```

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    /**
     * Acme\Bundle\DemoBundle\Entity\BaseUserProfile
     */
    abstract class BaseUserProfile
    {
        /**
         * @var integer
         */
        protected $id;

        //...
    }
```

```yaml
    Acme\Bundle\DemoBundle\Entity\BaseUserProfile:
      type: mappedSuperclass
      id:
        id:
          type: integer
          generator:
            strategy: AUTO
```

Lets call their children User and UserProfile.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     */
    class User
    {
         //...
    }
```

```yaml
    Acme\Bundle\DemoBundle\Entity\User:
      type: entity
      table: user
      associationPropertyOverrides:
        profile:
          inversedBy: user
```

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    /**
     * Acme\Bundle\DemoBundle\Entity\UserProfile
     */
    class UserProfile
    {
         /**
          * @var User
          */
          protected $user;

          //...
    }
```

```yaml
    Acme\Bundle\DemoBundle\Entity\UserProfile:
      type: entity
      table: user_profile
      associationPropertyOverrides:
        user:
          mappedBy: profile
```

After adding `associationPropertyOverrides` to your children entities relationship between them became bidirectional.

## <a id="orphan-removal-override"></a>Add/Remove Orphan Removal Strategy.

If you want to add or remove orphanRemoval strategy to your relations you need to use `orphanRemoval` field of `associationPropertyOverride`

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     */
    class User
    {
         //...
    }
```

```yaml
    Acme\Bundle\DemoBundle\Entity\User:
      type: entity
      table: user
      associationPropertyOverrides:
        profile:
          orphanRemoval: true
```

And that's it. Now orphan UserProfile will be authomatically removed after removing link to it from User entity and flush.

## <a id="id-generation-override"></a>Override Id Generation Strategy

If you want to override generation strategy in your child entity to another one you need to use `idGeneratorStrategyOverride`

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     */
    class User
    {
         //...
    }
```

```yaml
    Acme\Bundle\DemoBundle\Entity\User:
      type: entity
      idGeneratorStrategyOverride: SEQUENCE
```

As a value for strategy property you can use the name of one of [doctrine stragegies](http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#identifier-generation-strategies) or a path to your custom strategy.
Please remember that your custom strategy class should be inherited from `Doctrine\ORM\Id\AbstractIdGenerator`

---
[Index](../../README.md)
