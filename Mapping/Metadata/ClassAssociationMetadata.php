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

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Metadata\ClassAssociationMetadata
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class ClassAssociationMetadata
{
    /**
     * @var string
     */
    protected $mappedBy;

    /**
     * @var string
     */
    protected $inversedBy;

    /**
     * @var boolean
     */
    protected $orphanRemoval;

    /**
     * @param string $inversedByOverride
     * @return ClassAssociationMetadata
     */
    public function setInversedBy($inversedByOverride)
    {
        $this->inversedBy = $inversedByOverride;

        return $this;
    }

    /**
     * @return string
     */
    public function getInversedBy()
    {
        return $this->inversedBy;
    }

    /**
     * @param string $mappedByOverride
     * @return ClassAssociationMetadata
     */
    public function setMappedBy($mappedByOverride)
    {
        $this->mappedBy = $mappedByOverride;

        return $this;
    }

    /**
     * @return string
     */
    public function getMappedBy()
    {
        return $this->mappedBy;
    }

    /**
     * @param boolean $orphanRemovalOverride
     * @return ClassAssociationMetadata
     */
    public function setOrphanRemoval($orphanRemovalOverride)
    {
        $this->orphanRemoval = $orphanRemovalOverride;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isOrphanRemoval()
    {
        return $this->orphanRemoval;
    }
} 
