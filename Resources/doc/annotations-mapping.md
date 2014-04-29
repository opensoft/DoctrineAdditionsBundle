#Annotations Mapping
---

## Contents

* [Change Type Of Your Entities Relationship](#change-type-of-your-entities-relationship)
* [Add/Remove Orphan Removal Strategy](#addremove-orphan-removal-strategy)
* [Override Id Generation Strategy](#override-id-generation-strategy)

## <a id="relationship-type"></a>Change Type Of Your Entities Relationship

For association property overriding you can use `AssociationPropertyOverride` annotation.
For example you have two mapped superclasses with unidirectional relationship and you want to make entities with bidirectional relationship based on specified mapped superclasses.
Lets call them BaseUser and BaseUserProfile.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * Acme\Bundle\DemoBundle\Entity\BaseUser
     *
     * @ORM\MappedSuperclass()
     */
    abstract class BaseUser
    {
        /**
         * @var integer
         *
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="AUTO")
         * @ORM\Column(name="id", type="integer")
         */
        protected $id;

        /**
         * @var BaseUserProfile
         *
         * @ORM\OneToOne(targetEntity="BaseUserProfile")
         * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
         */
         protected $profile;

         //...
    }
```

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * Acme\Bundle\DemoBundle\Entity\BaseUserProfile
     *
     * @ORM\MappedSuperclass()
     */
    abstract class BaseUserProfile
    {
        /**
         * @var integer
         *
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="AUTO")
         * @ORM\Column(name="id", type="integer")
         */
        protected $id;

        //...
    }
```

And call their children User and UserProfile. Also lets add AssociationPropertyOverride annotation as shown below.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use pensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     *
     * @ORM\Entity(repositoryClass="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository")
     * @ORM\Table(name="user")
     * @DA\AssociationPropertyOverride("profile", inversedBy="user")
     */
    class User
    {
         //...
    }
```

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use pensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

    /**
     * Acme\Bundle\DemoBundle\Entity\UserProfile
     *
     * @ORM\Entity(repositoryClass="Acme\Bundle\DemoBundle\Entity\Repository\UserProfileRepository")
     * @ORM\Table(name="user_profile")
     * @DA\AssociationPropertyOverride("user", mappedBy="profile")
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
After adding annotations to your children entities relationship between them became bidirectional.

## <a id="orphan-removal-override"></a>Add/Remove Orphan Removal Strategy.

If you want to add or remove orphanRemoval strategy to your relations you can do it in two ways.

The first one is use orphanRemoval property from `AssociationPropertyOverride` class annotation.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use pensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     *
     * @ORM\Entity(repositoryClass="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository")
     * @ORM\Table(name="user")
     * @DA\AssociationPropertyOverride("profile", orphanRemoval="true")
     */
    class User
    {
         //...
    }
```

And the second one is use `OrphanRemoval` field annotation.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use pensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     *
     * @ORM\Entity(repositoryClass="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository")
     * @ORM\Table(name="user")
     */
    class User
    {
        /**
         * @var BaseUserProfile
         *
         * @DA\OrphanRemoval(true)
         */
         protected $profile;

         //...
    }
```

And that's it. Now orphan UserProfile will be authomatically removed after removing link to it from User entity and flush.

## <a id="id-generation-override"></a>Override Id Generation Strategy

If you want to override generation strategy in your child entity to another one you need to use `IdGenerationStrategyOverride` annotation.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use pensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

    /**
     * Acme\Bundle\DemoBundle\Entity\User
     *
     * @ORM\Entity(repositoryClass="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository")
     * @ORM\Table(name="user")
     * @DA\IdGenerationStrategyOverride(strategy="TABLE")
     */
    class User
    {
         //...
    }
```

As a value for strategy property you can use the name of one of [doctrine stragegies](http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#identifier-generation-strategies) or a path to your custom strategy.
Please remember that your custom strategy class should be inherited from `Doctrine\ORM\Id\AbstractIdGenerator`

---
[Index](../../README.md)
