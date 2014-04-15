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
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use InvalidArgumentException;
use Opensoft\DoctrineAdditionsBundle\Mapping\Annotation\IdGenerationStrategyOverride;

/**
 * @author Vladimir Prudilin <vladimir.prudilin@opensoftdev.ru>
 */
class IdGenerationStrategyOverrideSubscriber implements EventSubscriber
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var string
     */
    protected $annotationClass = 'Opensoft\\DoctrineAdditionsBundle\\Mapping\\Annotation\\IdGenerationStrategyOverride';

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
        /** @var IdGenerationStrategyOverride $annotation */
        $annotation = $this->annotationReader->getClassAnnotation(
            $metadata->getReflectionClass(),
            $this->annotationClass
        );
        if (null !== $annotation) {
            $idGenerationStrategyClass = $annotation->getIdGenerationStrategyClass();
            if (!is_subclass_of($idGenerationStrategyClass, 'Doctrine\ORM\Id\AbstractIdGenerator')) {
                throw new InvalidArgumentException('Passed generator class must be inherited from a Doctrine\\ORM\\Id\\AbstractIdGenerator');
            }
            $metadata->setIdGenerator(new $idGenerationStrategyClass());
        }
    }
}
