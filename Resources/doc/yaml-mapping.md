#YAML Mapping
---

## Contents

* [Change Type Of Your Entities Relationship](#change-type-of-your-entities-relationship)
* [Add/Remove Orphan Removal Strategy](#addremove-orphan-removal-strategy)
* [Override Id Generation Strategy](#override-id-generation-strategy)

## <a id="relationship-type"></a>Change Type Of Your Entities Relationship

If you have two mapped superclasses with a unidirectional relationship and you want to create entities with a bidirectional relationship based on those superclasses, you need to use the `associationPropertyOverrides` tag.


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

Let's call their children User and UserProfile.

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

After adding the `associationPropertyOverrides` tag to your children entities, the relationship between them becomes bidirectional.

## <a id="orphan-removal-override"></a>Add/Remove Orphan Removal Strategy.

If you want to add or remove the orphanRemoval strategy to your relations, you need to use the `orphanRemoval` field of the `associationPropertyOverride` tag.

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

And that's it. Now orphaned UserProfile entities will be automatically removed after removing the link to them from the User entity.

## <a id="id-generation-override"></a>Override Id Generation Strategy

If you want to override the generation strategy in your child entity, you need to use the `idGenerationStrategyOverride` annotation.

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
      idGenerationStrategyOverride: SEQUENCE
```

As a value for the `strategy` property, you can use the name from one of the [doctrine strategies](http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#identifier-generation-strategies) or a path to your custom strategy.
Please remember that your custom strategy class should be inherited from `Doctrine\ORM\Id\AbstractIdGenerator`.

---
[Index](../../README.md)
