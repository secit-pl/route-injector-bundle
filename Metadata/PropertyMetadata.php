<?php

namespace SecIT\RouteInjectorBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Class PropertyMetadata.
 *
 * @author Tomasz Gemza
 */
class PropertyMetadata extends BasePropertyMetadata
{
    /**
     * Route name.
     *
     * @var string
     */
    protected $route;

    /**
     * Route parameters mapping in format ['parameter' => 'publicGetterMethod'].
     *
     * @var array
     */
    protected $parametersMapping = [];

    /**
     * Generate absolute url?
     *
     * @var bool
     */
    protected $absolute = false;

    /**
     * Inject route if the property value is not empty?
     *
     * @var bool
     */
    protected $injectIfNotEmpty = false;

    /**
     * @var \ReflectionProperty
     */
    private $reflection;

    /**
     * PropertyMetadata constructor.
     *
     * @param string $class
     * @param string $name
     */
    public function __construct(string $class, string $name)
    {
        parent::__construct($class, $name);

        $this->reflection = new \ReflectionProperty($class, $name);
        $this->reflection->setAccessible(true);
    }

    /**
     * Get value.
     *
     * @param mixed $obj
     *
     * @return mixed
     */
    public function getValue($object)
    {
        return $this->reflection->getValue($object);
    }

    /**
     * Get value.
     *
     * @param mixed $obj
     * @param mixed $value
     */
    public function setValue($object, $value)
    {
        $this->reflection->setValue($object, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->class,
            $this->name,
            $this->route,
            $this->parametersMapping,
            $this->absolute,
            $this->injectIfNotEmpty,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->class,
            $this->name,
            $this->route,
            $this->parametersMapping,
            $this->absolute,
            $this->injectIfNotEmpty
        ) = unserialize($serialized);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }

    /**
     * Is absolute?
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    /**
     * Set absolute.
     *
     * @param bool $absolute
     *
     * @return PropertyMetadata
     */
    public function setAbsolute($absolute)
    {
        $this->absolute = $absolute;

        return $this;
    }

    /**
     * Should inject if not empty?
     *
     * @return bool
     */
    public function injectIfNotEmpty()
    {
        return $this->injectIfNotEmpty;
    }

    /**
     * Set inject if not empty.
     *
     * @param bool $injectIfNotEmpty
     *
     * @return PropertyMetadata
     */
    public function setInjectIfNotEmpty($injectIfNotEmpty)
    {
        $this->injectIfNotEmpty = $injectIfNotEmpty;

        return $this;
    }

    /**
     * Get parameters mapping.
     *
     * @return array
     */
    public function getParametersMapping()
    {
        return $this->parametersMapping;
    }

    /**
     * Set parameters mapping.
     *
     * @param array $parametersMapping
     *
     * @return PropertyMetadata
     */
    public function setParametersMapping($parametersMapping)
    {
        $this->parametersMapping = $parametersMapping;

        return $this;
    }

    /**
     * Get route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set route.
     *
     * @param string $route
     *
     * @return PropertyMetadata
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Is injectable?
     *
     * @param object $object
     *
     * @return bool
     */
    public function isInjectable($object)
    {
        return $this->getRoute() &&
            (!$this->getValue($object) || $this->injectIfNotEmpty()) &&
            !$this->hasNullParameter($object);
    }

    /**
     * Get object route parameters.
     *
     * @param object $object
     *
     * @return array
     */
    public function getParameters($object)
    {
        $parameters = [];
        if ($this->getParametersMapping()) {
            foreach ($this->getParametersMapping() as $parameter => $mappedMethod) {
                $parameters[$parameter] = $object->$mappedMethod();
            }
        }

        return $parameters;
    }

    /**
     * Check if any of the route parameters is null.
     *
     * @param object $object
     *
     * @return bool
     */
    protected function hasNullParameter($object)
    {
        return in_array(null, $this->getParameters($object), true);
    }
}
