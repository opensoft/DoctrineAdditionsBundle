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

use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassMetadata as DoctrineAdditionsClassMetadata;
use Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassAssociationMetadata as DoctrineAdditionsClassAssociationMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Driver\DriverTrait
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
trait MetadataConverterTrait {

    public function convertMetadata(DoctrineAdditionsClassMetadata $doctrineAdditionsClassMetadata, DoctrineClassMetadata $doctrineClassMetadata)
    {
        foreach($doctrineAdditionsClassMetadata->getAssociations() as $associationName => $doctrineAdditionsAssociationClassMetadata){
            if (null !== $doctrineAdditionsAssociationClassMetadata) {
                if (isset($doctrineClassMetadata->getAssociationMappings()[$associationName])) {
                    $associationMapping = $doctrineClassMetadata->associationMappings[$associationName];
                    if (null !== $doctrineAdditionsAssociationClassMetadata->isOrphanRemoval()) {
                        $doctrineClassMetadata->associationMappings[$associationName]['orphanRemoval'] = $doctrineAdditionsAssociationClassMetadata->isOrphanRemoval();
                    }
                    if (null !== $doctrineAdditionsAssociationClassMetadata->getMappedBy() && null !== $doctrineAdditionsAssociationClassMetadata->getInversedBy()) {
                        throw new \Exception('You can not use mappedBy and inversedBy properties at the same time');
                    }
                    if (null !== $doctrineAdditionsAssociationClassMetadata->getInversedBy()) {
                        if (ClassMetadataInfo::ONE_TO_MANY === $associationMapping['type'] || null !== $associationMapping['mappedBy']) {
                            throw new \Exception('You can not use inversedBy on the inverse side of a bi-directional relationship');
                        }
                        $doctrineClassMetadata->associationMappings[$associationName]['inversedBy'] = $doctrineAdditionsAssociationClassMetadata->getInversedBy();
                    }
                    if (null !== $doctrineAdditionsAssociationClassMetadata->getMappedBy()) {
                        if (ClassMetadataInfo::MANY_TO_ONE === $associationMapping['type'] || null !== $associationMapping['inversedBy']) {
                            throw new \Exception('You can not use mappedBy on the owning side of a bi-directional relationship');
                        }
                        $doctrineClassMetadata->associationMappings[$associationName]['mappedBy'] = $doctrineAdditionsAssociationClassMetadata->getMappedBy();
                    }
                }
            }
        }
        if (null !== $doctrineAdditionsClassMetadata->getGeneratorType()) {
            $doctrineClassMetadata->setIdGeneratorType($doctrineAdditionsClassMetadata->getGeneratorType());
        }
        if (null !== $doctrineAdditionsClassMetadata->getIdGenerator()) {
            $doctrineClassMetadata->setIdGenerator($doctrineAdditionsClassMetadata->getIdGenerator());
        }
    }

    /**
     * @param string $name
     * @param DoctrineAdditionsClassMetadata $doctrineAdditionsClassMetadata
     * @return DoctrineAdditionsClassAssociationMetadata
     */
    protected function getDoctrineAdditionsClassAssociationMetadataByName(
        $name,
        DoctrineAdditionsClassMetadata $doctrineAdditionsClassMetadata
    ) {
        $associationMetadata = $doctrineAdditionsClassMetadata->getAssociationByName($name);
        if (null === $associationMetadata) {
            $associationMetadata = new DoctrineAdditionsClassAssociationMetadata();
            $doctrineAdditionsClassMetadata->addAssociation($name, $associationMetadata);
        }

        return $associationMetadata;
    }

}
