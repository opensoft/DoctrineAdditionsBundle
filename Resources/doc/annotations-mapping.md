#Annotations Mapping
---

## Contents

* [Change Type Of Your Entities Relationship](#change-type-of-your-entities-relationship)
* [Add/Remove Orphan Removal Strategy](#addremove-orphan-removal-strategy)
* [Override Id Generation Strategy](#override-id-generation-strategy)

## <a id="relationship-type"></a>Change Type Of Your Entities Relationship

To override association properties, you can use the `AssociationPropertyOverride` annotation.
For example, assume you have two mapped superclasses with a unidirectional relationship, and you want to create entities with a bidirectional relationship based on those superclasses.

Let's call them BaseUser and BaseUserProfile.

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

Call their children User and UserProfile. Also, let's add the `AssociationPropertyOverride` annotation as shown below.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

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
    use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

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
After adding the annotations to your children entities, the relationship between them becomes bidirectional.

## <a id="orphan-removal-override"></a>Add/Remove Orphan Removal Strategy.

You can add or remove the orphanRemoval strategy from your relations in one of two ways.

The first way is to use the orphanRemoval property of the `AssociationPropertyOverride` class annotation.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

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

The second way is to use the `OrphanRemoval` field annotation.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

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

And that's it. Now orphaned UserProfile entities will be automatically removed after removing the link to them from the User entity.

## <a id="id-generation-override"></a>Override Id Generation Strategy

If you want to override the generation strategy in your child entity, you need to use the `IdGenerationStrategyOverride` annotation.

```php
    namespace Acme\Bundle\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation as DA;

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

As a value for the `strategy` property, you can use the name from one of the [doctrine strategies](http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#identifier-generation-strategies) or a path to your custom strategy.
Please remember that your custom strategy class should be inherited from `Doctrine\ORM\Id\AbstractIdGenerator`.

---
[Index](../../README.md)
