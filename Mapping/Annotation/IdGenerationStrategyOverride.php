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
 * Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\IdGenerationStrategyOverride
 *
 * Allows to override id generation strategy
 *
 * @Annotation
 *
 * @author Vladimir Prudilin <vladimir.prudilin@opensoftdev.ru>
 */
class IdGenerationStrategyOverride
{
    /**
     * @var string
     */
    protected $strategyClass;

    /**
     * Constructor
     *
     * @param  array                    $options
     * @throws InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (!isset($options['strategy'])) {
            throw new InvalidArgumentException('Id generation class name has to be specified');
        }
        if (!class_exists($options['strategy'])) {
            throw new InvalidArgumentException(sprintf('Id generation class "%s" does not exist', $options['strategy']));
        }

        $this->strategyClass = $options['strategy'];
    }

    /**
     * @return string
     */
    public function getIdGenerationStrategyClass()
    {
        return $this->strategyClass;
    }
}
