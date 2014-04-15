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

namespace Opensoft\DoctrineAdditionsBundle\Mapping\Annotation;

use InvalidArgumentException;

/**
 * Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\OrphanRemoval
 *
 * Allows to override orphan-removal property at the Doctrine mapping
 *
 * @Annotation
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class OrphanRemoval
{
    /**
     * @var boolean
     */
    protected $removeOrphans;

    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $options['removeOrphans'] = $options['value'];
            unset($options['value']);
        }
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * @return boolean
     */
    public function isRemoveOrphans()
    {
        return $this->removeOrphans;
    }
}
