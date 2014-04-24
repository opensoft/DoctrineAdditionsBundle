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

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Opensoft\DoctrineAdditionsBundle\Mapping\Driver\Xml;
use Opensoft\DoctrineAdditionsBundle\Mapping\Driver\Yaml;
use Opensoft\DoctrineAdditionsBundle\Mapping\Driver\Annotation;
use Opensoft\DoctrineAdditionsBundle\Mapping\Driver\DriverInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;


/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\EventSubscriber\MappingSubscriber
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class MappingSubscriber implements EventSubscriber
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

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
        return ['loadClassMetadata'];
    }

    /**
     * @param  LoadClassMetadataEventArgs $args
     * @throws \Exception
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        /** @var EntityManager $em */
        $em = $args->getEntityManager();
        $configuration = $em->getConfiguration();
        /** @var DriverChain $metadataDriverImpl */
        $metadataDriverImpl = $configuration->getMetadataDriverImpl();
        if ($metadataDriverImpl instanceof DriverChain or $metadataDriverImpl instanceof MappingDriverChain) {
            foreach ($metadataDriverImpl->getDrivers() as $namespace => $driver) {
                if ($namespace === $args->getClassMetadata()->namespace) {
                    $doctrineAdditionsDriver = $this->getDoctrineAdditionsDriver($driver);
                    if (null !== $doctrineAdditionsDriver) {
                        $doctrineAdditionsDriver->loadMetadataForClass($args->getClassMetadata()->name, $args->getClassMetadata());
                    }
                }
            }
        } else {
            $doctrineAdditionsDriver = $this->getDoctrineAdditionsDriver($metadataDriverImpl);
            if (null !== $doctrineAdditionsDriver) {
                $doctrineAdditionsDriver->loadMetadataForClass($args->getClassMetadata()->name, $args->getClassMetadata());
            }
        }
    }

    /**
     * @param MappingDriver $driver
     * @return null|DriverInterface
     */
    public function getDoctrineAdditionsDriver(MappingDriver $driver)
    {
        if ($driver instanceof AnnotationDriver || is_subclass_of($driver, 'Doctrine\ORM\Mapping\Driver\AnnotationDriver')) {
            /** @var AnnotationDriver $driver */
            return new Annotation($this->annotationReader, $driver->getPaths());
        }
        if ($driver instanceof XmlDriver || is_subclass_of($driver, 'Doctrine\ORM\Mapping\Driver\XmlDriver')) {
            /** @var XmlDriver $driver */
            return new Xml($driver->getLocator()->getNamespacePrefixes());
        }
        if ($driver instanceof YamlDriver || is_subclass_of($driver, 'Doctrine\ORM\Mapping\Driver\YamlDriver')) {
            /** @var YamlDriver $driver */
            return new Yaml($driver->getLocator()->getNamespacePrefixes());
        }

        return null;
    }
}
