<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Opensoft
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Opensoft\DoctrineAdditionsBundle\Mapping\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\GeneratedValue;
use Opensoft\DoctrineAdditionsBundle\Mapping\Generator\GeneratorInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\EventSubscriber\GeneratorSubscriber
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class GeneratorSubscriber implements EventSubscriber
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var string
     */
    protected $annotationClass = 'Opensoft\\DoctrineAdditionsBundle\\Mapping\\Annotation\\GeneratedValue';

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['prePersist'];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->inject($args->getEntityManager(), $args->getEntity());
    }

    /**
     * @param  ObjectManager $entityManager
     * @param $entity
     * @throws \Exception
     */
    protected function inject(ObjectManager $entityManager, $entity)
    {
        $reflectionObject = new \ReflectionObject($entity);
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            /** @var GeneratedValue $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);
            if (null !== $annotation) {
                $generatorClass = $annotation->getGenerator();
                if (class_exists($generatorClass)) {
                    /** @var GeneratorInterface $generator */
                    $generator = new $generatorClass;
                    if ($generator instanceof GeneratorInterface) {
                        $identifier = $generator->generate(
                            $entityManager,
                            $entity,
                            $reflectionProperty
                        );
                        $reflectionProperty->setAccessible(true);
                        if (null === $reflectionProperty->getValue($entity)) {
                            $reflectionProperty->setValue($entity, $identifier);
                        }
                        $reflectionProperty->setAccessible(false);
                    } else {
                        throw new \Exception(sprintf(
                            'Class %s should implement interface Opensoft\\DoctrineAdditionsBundle\\Mapping\\Generator\\GeneratorInterface',
                            get_class($generatorClass)
                        ));
                    }
                } else {
                    throw new \Exception(sprintf('Class %s does not exists', get_class($generatorClass)));
                }
            }
        }
    }
}
