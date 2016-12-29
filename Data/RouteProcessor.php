<?php

namespace SecIT\RouteInjectorBundle\Data;

use Metadata\MetadataFactoryInterface;
use SecIT\RouteInjectorBundle\Metadata\PropertyMetadata;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class RouteProcessor.
 *
 * @author Tomasz Gemza
 */
class RouteProcessor
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * RouteProcessor constructor.
     *
     * @param MetadataFactoryInterface $metadataFactory
     * @param Router                   $router
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, Router $router)
    {
        $this->metadataFactory = $metadataFactory;
        $this->router = $router;
    }

    /**
     * Inject routes.
     *
     * @param object $object
     *
     * @return object
     */
    public function injectRoutes($object)
    {
        $classMetadata = $this->getObjectMetadata($object);
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            if ($propertyMetadata->isInjectable($object)) {
                $propertyMetadata->setValue($object, $this->generateRoute($propertyMetadata, $object));
            }
        }

        return $object;
    }

    /**
     * Generate route.
     *
     * @param PropertyMetadata $propertyMetadata
     * @param object           $object
     *
     * @return string
     */
    protected function generateRoute(PropertyMetadata $propertyMetadata, $object)
    {
        return $this->router->generate(
            $propertyMetadata->getRoute(),
            $propertyMetadata->getParameters($object),
            $propertyMetadata->isAbsolute() ? UrlGenerator::ABSOLUTE_URL : UrlGenerator::ABSOLUTE_PATH
        );
    }

    /**
     * Get object metadata.
     *
     * @param object $object
     *
     * @return \Metadata\ClassHierarchyMetadata|\Metadata\MergeableClassMetadata|null
     */
    protected function getObjectMetadata($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('No object provided');
        }

        return $this->metadataFactory->getMetadataForClass(get_class($object));
    }
}
