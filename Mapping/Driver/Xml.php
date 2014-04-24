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
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassMetadata as DoctrineAdditionsClassMetadata;
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassAssociationMetadata as DoctrineAdditionsClassAssociationMetadata;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use SimpleXMLElement;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Driver\Xml
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class Xml extends SimplifiedXmlDriver implements DriverInterface
{
    use MetadataConverterTrait;

    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $reflectionClass = $metadata->getReflectionClass();
        $doctrineAdditionsClassMetadata = new DoctrineAdditionsClassMetadata();

        /** @var SimpleXMLElement $xmlRoot */
        $xmlRoot = $this->getElement($className);
        $xmlRoot = $xmlRoot->children('doctrine-additions', true);

        if (isset($xmlRoot->{'association-property-overrides'})) {
            foreach ($xmlRoot->{'association-property-overrides'}->children('doctrine-additions', true)->{'association-property-override'} as $overrideElement) {
                $associationName = (string)$overrideElement->attributes()['name'];
                $doctrineAdditionsClassAssociationMetadata = $this->getDoctrineAdditionsClassAssociationMetadataByName(
                    $associationName,
                    $doctrineAdditionsClassMetadata
                );
                $doctrineAdditionsClassAssociationMetadata->setOrphanRemoval(
                    (boolean)$overrideElement->attributes()['orphan-removal']
                );
                $mappedBy = "" !== (string)$overrideElement->attributes()['mapped-by'] ? (string)$overrideElement->attributes()['mapped-by'] : null;
                $doctrineAdditionsClassAssociationMetadata->setMappedBy($mappedBy);
                $inversedBy = "" !== (string)$overrideElement->attributes()['inversed-by'] ? (string)$overrideElement->attributes()['inversed-by'] : null;
                $doctrineAdditionsClassAssociationMetadata->setInversedBy($inversedBy);
                $doctrineAdditionsClassMetadata->addAssociation(
                    $associationName,
                    $doctrineAdditionsClassAssociationMetadata
                );
            }
        }
        if (isset($xmlRoot->{'id-generator-stragegy-override'})) {
            /** @var IdGenerationStrategyOverride $annotation */
            $strategy = (string) $xmlRoot->{'id-generator-stragegy-override'}->attributes()['strategy'];
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
