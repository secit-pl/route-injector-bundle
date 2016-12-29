<?php

namespace SecIT\RouteInjectorBundle\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use SecIT\RouteInjectorBundle\Metadata\PropertyMetadata;
use SecIT\RouteInjectorBundle\Mapping\Annotation;

/**
 * Class AnnotationDriver.
 *
 * @author Tomasz Gemza
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * AnnotationDriver constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, Annotation\InjectRoute::class);
            if ($annotation === null) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());
            $propertyMetadata
                ->setRoute($annotation->getRoute())
                ->setParametersMapping($annotation->getParametersMapping())
                ->setAbsolute($annotation->isAbsolute())
                ->setInjectIfNotEmpty($annotation->shouldInjectIfNotEmpty())
            ;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}
