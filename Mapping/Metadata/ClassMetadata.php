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

namespace Opensoft\DoctrineAdditionsBundle\Mapping\Metadata;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassMetadata
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class ClassMetadata
{
    /**
     * @var string
     */
    protected $idGenerator;

    /**
     * @var integer
     */
    protected $generatorType;

    /**
     * @var ClassAssociationMetadata[]|ArrayCollection
     */
    protected $associations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->associations = new ArrayCollection();
    }

    /**
     * @param string $idGeneratorOverride
     * @return ClassAssociationMetadata
     */
    public function setIdGenerator($idGeneratorOverride)
    {
        $this->idGenerator = $idGeneratorOverride;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdGenerator()
    {
        return $this->idGenerator;
    }

    /**
     * @param int $generatorType
     * @return ClassMetadata
     */
    public function setGeneratorType($generatorType)
    {
        $this->generatorType = $generatorType;

        return $this;
    }

    /**
     * @return int
     */
    public function getGeneratorType()
    {
        return $this->generatorType;
    }

    /**
     * @param ArrayCollection|ClassAssociationMetadata[] $associations
     * @return ClassMetadata
     */
    public function setAssociations($associations)
    {
        $this->associations = $associations;

        return $this;
    }

    /**
     * @return ArrayCollection|ClassAssociationMetadata[]
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param string $name
     * @return ClassAssociationMetadata
     */
    public function getAssociationByName($name)
    {
        return $this->associations->get($name);
    }

    /**
     * @param string $name
     * @param ClassAssociationMetadata $associationMetadata
     * @return ClassMetadata
     */
    public function addAssociation($name, ClassAssociationMetadata $associationMetadata)
    {
        $this->associations->set($name, $associationMetadata);

        return $this;
    }

    /**
     * @param string $name
     * @return ClassMetadata
     */
    public function removeAssociationByName($name)
    {
        $this->associations->remove($name);

        return $this;
    }

    /**
     * @param ClassAssociationMetadata $associationMetadata
     * @return ClassMetadata
     */
    public function removeAssociationByValue(ClassAssociationMetadata $associationMetadata)
    {
        $this->associations->removeElement($associationMetadata);

        return $this;
    }

    /**
     * @param ClassAssociationMetadata $associationMetadata
     * @return boolean
     */
    public function hasAssociation(ClassAssociationMetadata $associationMetadata)
    {
        return $this->associations->contains($associationMetadata);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAssociationWithName($name)
    {
        return $this->associations->containsKey($name);
    }
} 
