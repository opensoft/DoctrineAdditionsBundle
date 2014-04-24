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
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassMetadata as DoctrineAdditionsClassMetadata;
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassAssociationMetadata as DoctrineAdditionsClassAssociationMetadata;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Driver\Yaml
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class Yaml extends SimplifiedYamlDriver implements DriverInterface
{
    use MetadataConverterTrait;

    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $reflectionClass = $metadata->getReflectionClass();
        $doctrineAdditionsClassMetadata = new DoctrineAdditionsClassMetadata();
        if($className === 'Opensoft\Onp\Bundle\TestBundle\Entity\User'){
            $test = '';
        }

        $yamlRoot = $this->getElement($className);

        if (isset($yamlRoot['associationPropertyOverrides'])) {
            foreach ($yamlRoot['associationPropertyOverrides'] as $associationName => $overrideArray) {
                $doctrineAdditionsClassAssociationMetadata = $this->getDoctrineAdditionsClassAssociationMetadataByName(
                    $associationName,
                    $doctrineAdditionsClassMetadata
                );
                if (isset($overrideArray['orphanRemoval'])) {
                    $doctrineAdditionsClassAssociationMetadata->setOrphanRemoval($overrideArray['orphanRemoval']);
                }

                if (isset($overrideArray['mappedBy'])) {
                    $doctrineAdditionsClassAssociationMetadata->setMappedBy($overrideArray['mappedBy']);
                }
                if (isset($overrideArray['inversedBy'])) {
                    $doctrineAdditionsClassAssociationMetadata->setInversedBy($overrideArray['inversedBy']);
                }
                $doctrineAdditionsClassMetadata->addAssociation(
                    $associationName,
                    $doctrineAdditionsClassAssociationMetadata
                );
            }
        }
        if (isset($yamlRoot['idGeneratorStragegyOverride'])) {
            $strategy = $yamlRoot['idGeneratorStragegyOverride'];
            if("" !== $strategy) {
                $classMetadataInfoReflection = new \ReflectionClass('Doctrine\ORM\Mapping\ClassMetadataInfo');
                if ($classMetadataInfoReflection->hasConstant('GENERATOR_TYPE_' . $strategy)) {
                    $doctrineAdditionsClassMetadata->setGeneratorType(
                        constant('Doctrine\ORM\Mapping\ClassMetadataInfo::GENERATOR_TYPE_' . $strategy)
                    );
                } else {
                    if (!is_subclass_of($strategy, 'Doctrine\ORM\Id\AbstractIdGenerator')) {
                        throw new InvalidArgumentException('Passed generator class must be inherited from a Doctrine\\ORM\\Id\\AbstractIdGenerator');
                    }
                    $doctrineAdditionsClassMetadata->setIdGenerator(new $strategy());
                }
            }
        }

        $this->convertMetadata($doctrineAdditionsClassMetadata, $metadata);
    }
}
