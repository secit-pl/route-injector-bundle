<?php

namespace SecIT\RouteInjectorBundle\Mapping\Annotation;

/**
 * Class Route.
 *
 * @author Tomasz Gemza
 *
 * @Annotation
 */
class InjectRoute
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
     * Route constructor.
     *
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $options['route'] = $options['value'];
            unset($options['value']);
        } else {
            throw new \InvalidArgumentException('The route name is required.');
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * Get route name.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
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
     * Get is absolute.
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    /**
     * Should inject if not empty?
     *
     * @return bool
     */
    public function shouldInjectIfNotEmpty()
    {
        return $this->injectIfNotEmpty;
    }
}
