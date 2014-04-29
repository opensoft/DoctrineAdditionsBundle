#XML Mapping
---

## Contents

* [Change Type Of Your Entities Relationship](#change-type-of-your-entities-relationship)
* [Add/Remove Orphan Removal Strategy](#addremove-orphan-removal-strategy)
* [Override Id Generation Strategy](#override-id-generation-strategy)

## <a id="relationship-type"></a>Change Type Of Your Entities Relationship

If you have two mapped superclasses with unidirectional relationship and you want to make entities with bidirectional relationship based on specified mapped superclasses you need to use `association-overrides` tag.


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

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <mapped-superclass name="Acme\Bundle\DemoBundle\Entity\BaseUser">

            <id name="id" column="id" type="integer">
                <generator strategy="AUTO"/>
            </id>

            <one-to-one field="order" target-entity="Acme\Bundle\DemoBundle\Entity\BaseUserProfile">
                <join-column name="profile_id" referenced-column-name="id"/>
            </one-to-one>

        </mapped-superclass>

    </doctrine-mapping>
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

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <mapped-superclass name="Acme\Bundle\DemoBundle\Entity\BaseUserProfile">

            <id name="id" column="id" type="integer">
                <generator strategy="AUTO"/>
            </id>

        </mapped-superclass>

    </doctrine-mapping>
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

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xmlns:doctrine-additions="http://opensoftdev.ru/schemas/orm/doctrine-additions-mapping"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\Bundle\DemoBundle\Entity\User" table="user"
            repository-class="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository">

            <doctrine-additions:association-property-overrides>
                <doctrine-additions:association-property-override name="profile" inversed-by="user">
            </doctrine-additions:association-property-overrides>

        </entity>

    </doctrine-mapping>
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

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xmlns:doctrine-additions="http://opensoftdev.ru/schemas/orm/doctrine-additions-mapping"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\Bundle\DemoBundle\Entity\UserProfile" table="user_profile"
            repository-class="Acme\Bundle\DemoBundle\Entity\Repository\UserProfileRepository">

            <doctrine-additions:association-property-overrides>
                <doctrine-additions:association-property-override name="user" mapped-by="profile">
            </doctrine-additions:association-property-overrides>

        </entity>

    </doctrine-mapping>
```

    Please do not forget to add xml namespcae toy your mapping file. xmlns:doctrine-additions="http://opensoftdev.ru/schemas/orm/doctrine-additions-mapping"

After adding `association-property-override` tag to your children entities relationship between them became bidirectional.

## <a id="orphan-removal-override"></a>Add/Remove Orphan Removal Strategy.

If you want to add or remove orphanRemoval strategy to your relations you need to use `orphan-removal` property of `association-property-override` tag

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

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xmlns:doctrine-additions="http://opensoftdev.ru/schemas/orm/doctrine-additions-mapping"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\Bundle\DemoBundle\Entity\User" table="user"
            repository-class="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository">

            <doctrine-additions:association-property-overrides>
                <doctrine-additions:association-property-override name="user" orphan-removal="true">
            </doctrine-additions:association-property-overrides>

        </entity>

    </doctrine-mapping>
```

And that's it. Now orphan UserProfile will be authomatically removed after removing link to it from User entity and flush.

## <a id="id-generation-override"></a>Override Id Generation Strategy

If you want to override generation strategy in your child entity to another one you need to use `id-generator-stragegy-override` tag.

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

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xmlns:doctrine-additions="http://opensoftdev.ru/schemas/orm/doctrine-additions-mapping"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\Bundle\DemoBundle\Entity\User" table="user"
            repository-class="Acme\Bundle\DemoBundle\Entity\Repository\UserRepository">

            <doctrine-additions:id-generator-stragegy-override="SEQUENCE" />

        </entity>

    </doctrine-mapping>
```

As a value for strategy property you can use the name of one of [doctrine stragegies](http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#identifier-generation-strategies) or a path to your custom strategy.
Please remember that your custom strategy class should be inherited from `Doctrine\ORM\Id\AbstractIdGenerator`

---
[Index](../../README.md)
