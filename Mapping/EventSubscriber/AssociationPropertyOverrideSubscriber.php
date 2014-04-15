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
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\AssociationPropertyOverride;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\EventSubscriber\AssociationPropertyOverrideSubscriber
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class AssociationPropertyOverrideSubscriber implements EventSubscriber
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var string
     */
    protected $annotationClass = 'Opensoft\\DoctrineAdditionsBundle\\Mapping\\Annotation\\AssociationPropertyOverride';

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
        $metadata = $args->getClassMetadata();
        /** @var AssociationPropertyOverride $annotation */
        $annotation = $this->annotationReader->getClassAnnotation($metadata->getReflectionClass(), $this->annotationClass);
        if (null !== $annotation) {
            if (isset($metadata->getAssociationMappings()[$annotation->getAssociationName()])) {
                $associationMapping = $metadata->associationMappings[$annotation->getAssociationName()];
                if (null !== $annotation->isRemoveOrphans()) {
                    $metadata->associationMappings[$annotation->getAssociationName()]['orphanRemoval'] = $annotation->isRemoveOrphans();
                }
                if (null !== $annotation->getMappedBy() && null !== $annotation->getInversedBy()) {
                    throw new \Exception('You can not use mappedBy and inversedBy properties at the same time');
                }
                if (null !== $annotation->getInversedBy()) {
                    if (ClassMetadataInfo::ONE_TO_MANY === $associationMapping['type'] || null !== $associationMapping['mappedBy']) {
                        throw new \Exception('You can not use inversedBy on the inverse side of a bi-directional relationship');
                    }
                    $metadata->associationMappings[$annotation->getAssociationName()]['inversedBy'] = $annotation->getInversedBy();
                }
                if (null !== $annotation->getMappedBy()) {
                    if (ClassMetadataInfo::MANY_TO_ONE === $associationMapping['type'] || null !== $associationMapping['inversedBy']) {
                        throw new \Exception('You can not use mappedBy on the owning side of a bi-directional relationship');
                    }
                    $metadata->associationMappings[$annotation->getAssociationName()]['mappedBy'] = $annotation->getMappedBy();
                }
            }
        }
    }
}
