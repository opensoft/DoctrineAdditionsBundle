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

namespace Opensoft\DoctrineAdditionsBundle\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use InvalidArgumentException;
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassMetadata as DoctrineAdditionsClassMetadata;
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassAssociationMetadata as DoctrineAdditionsClassAssociationMetadata;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\AssociationPropertyOverride;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\OrphanRemoval;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\IdGenerationStrategyOverride;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\GeneratedValue;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Driver\Annotation
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class Annotation extends AnnotationDriver implements DriverInterface
{
    use MetadataConverterTrait;

    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $reflectionClass = $metadata->getReflectionClass();
        $doctrineAdditionsClassMetadata = new DoctrineAdditionsClassMetadata();
        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof AssociationPropertyOverride) {
                /** @var AssociationPropertyOverride $annotation */
                $doctrineAdditionsClassAssociationMetadata = $this->getDoctrineAdditionsClassAssociationMetadataByName(
                    $annotation->getAssociationName(),
                    $doctrineAdditionsClassMetadata
                );
                $doctrineAdditionsClassAssociationMetadata->setOrphanRemoval($annotation->isRemoveOrphans());
                $doctrineAdditionsClassAssociationMetadata->setMappedBy($annotation->getMappedBy());
                $doctrineAdditionsClassAssociationMetadata->setInversedBy($annotation->getInversedBy());
                $doctrineAdditionsClassMetadata->addAssociation($annotation->getAssociationName(), $doctrineAdditionsClassAssociationMetadata);
            } else if ($annotation instanceof IdGenerationStrategyOverride) {
                /** @var IdGenerationStrategyOverride $annotation */
                $strategy = $annotation->getIdGenerationStrategyClass();
                $classMetadataInfoReflection = new \ReflectionClass('Doctrine\Common\Persistence\Mapping\ClassMetadata');
                if ($classMetadataInfoReflection->hasConstant('GENERATOR_TYPE_' . $strategy)) {
                    $doctrineAdditionsClassMetadata->setGeneratorType(
                        constant('Doctrine\Common\Persistence\Mapping\ClassMetadata::GENERATOR_TYPE_' . $strategy)
                    );
                } else {
                    if (!is_subclass_of($strategy, 'Doctrine\ORM\Id\AbstractIdGenerator')) {
                        throw new InvalidArgumentException('Passed generator class must be inherited from a Doctrine\\ORM\\Id\\AbstractIdGenerator');
                    }
                    $doctrineAdditionsClassMetadata->setIdGenerator(new $strategy());
                }
            }
        }
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            /** @var OrphanRemoval $annotation */
            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof OrphanRemoval) {
                    /** @var OrphanRemoval $annotation */
                    $doctrineAdditionsClassAssociationMetadata = $this->getDoctrineAdditionsClassAssociationMetadataByName(
                        $reflectionProperty->getName(),
                        $doctrineAdditionsClassMetadata
                    );
                    $doctrineAdditionsClassAssociationMetadata->setOrphanRemoval($annotation->isRemoveOrphans());
                }
            }
        }

        $this->convertMetadata($doctrineAdditionsClassMetadata, $metadata);
    }

}
