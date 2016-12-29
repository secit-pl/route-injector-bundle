# Route injector annotation

Route injector annotation for Symfony 2.8 and 3.0+.



## Installation

From the command line run

```
$ composer require secit-pl/route-injector-bundle
```

Update your AppKernel by adding the bundle declaration

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...
            new SecIT\RouteInjectorBundle\RouteInjectorBundle(),
        ];

        ...
    }
}
```

## Usage

### The Annotation

To inject route to the variable just add the @InjectRoute annotation to it.
The the first argument is the route name.
The second and other arguments are the optional configuration options.

```php
use SecIT\RouteInjectorBundle\Mapping\Annotation\InjectRoute;

/**
 * @InjectRoute("route_name", configuration options...);
 */
private $url;
```

### Basic Usage

Here is the basic usage example.

The class:

```php
use SecIT\RouteInjectorBundle\Mapping\Annotation\InjectRoute;

class Example
{
    /**
     * @var string
     *
     * @InjectRoute("route_name", parametersMapping={"param": "getRouteParam"});
     */
    private $url;
    
    private $routeParam = '';
    
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @return string
     */
    public function getRouteParam()
    {
        return $this->routeParam;
    }
    
    /**
     * @param string $value
     */
    public function setRouteParam($value)
    {
        $this->routeParam = $value;
    }
}
```

And how to manually run the injector.

```php
$processor = $this->container->get('secit.route_injector.processor');
$exampleEbject = new Example();
$exampleEbject->setRouteParam('test');
$processor->injectRoutes($exampleEbject);
```

### Doctrine Entity Usage

This bundle provides the Doctrine integration. So if you use the @InjectRoute annotation in the entity
the injectRoutes processor method will be triggered on every (depending on the conditions) load, update and persist action.

Example entity:

```php
namespace ExampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SecIT\RouteInjectorBundle\Mapping\Annotation\InjectRoute;

/**
 * @ORM\Table(name="example")
 * @ORM\Entity()
 */
class Example
{
    /**
     * @var string
     *
     * @ORM\Column(name="param", type="string")
     */
    private $param;

    /**
     * @var string
     *
     * @InjectRoute("route_name", parametersMapping={"param": "getParam"});
     */
    private $url;
    
    /**
     * @return string
     */
    public function getParam()
    {
        return $this->string;
    }
    
    /**
     * @param string $param
     */
    public function setParam($param)
    {
        $this->string = $param;
    }
    
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
```

When you create a new Example entity instance the $url will be null. But just after persist
the route will be automatically injected and accessible.

```php
$exampleObject = new ExampleBundle\Entity\Example();

$this->getDoctrine()->getManager()->persist($exampleObject);
```

### Materialized Routes

In the previous example the route will be injected on each time we will load, update or store the entity
but in some cases we would like to store it in the database. To achieve this simply add the Doctrine @Column
annotation to make it a database field.

```php
namespace ExampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SecIT\RouteInjectorBundle\Mapping\Annotation\InjectRoute;

/**
 * @ORM\Table(name="example")
 * @ORM\Entity()
 */
class Example
{
    ...
    
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     * @InjectRoute("route_name", parametersMapping={"param": "getParam"});
     */
    private $url;
    
    ...
}
```

## Configuration options

Here are all possible configuration options:

```php
@InjectRoute("route_name", configuration options...);
```

##### parametersMapping = array, default empty array
The array of the route parameters mapped to the public class methods from which the parameters
values should be taken.

```php
@InjectRoute("route_name", parametersMapping={"param": "publicGetterMethodName"});
```

**Warning!** The route will not be injected if any of the parameters will be null.

##### absolute = bool, default false
Set to true if you'd like to have the generated URLs with the protocol and hostname prefix.

```php
@InjectRoute("route_name", absolute=true);
```

##### injectIfNotEmpty = bool, default false
By default the route will be injected only if the current value is empty (null, false, or the empty string).
To make it updatable event it it's not empty set this option to true.

```php
@InjectRoute("route_name", injectIfNotEmpty=true);
```
