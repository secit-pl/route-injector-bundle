<?php

namespace SecIT\RouteInjectorBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SecIT\RouteInjectorBundle\Data\RouteProcessor;

/**
 * Class RouteInjectorSubscriber.
 *
 * @author Tomasz Gemza
 */
class RouteInjectorSubscriber implements EventSubscriber
{
    /**
     * @var RouteProcessor
     */
    protected $routeProcessor;

    /**
     * Set route processor.
     *
     * @param RouteProcessor $routeProcessor
     */
    public function setProcessor(RouteProcessor $routeProcessor)
    {
        $this->routeProcessor = $routeProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'postLoad',
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * Post load.
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $this->inject($args);
    }

    /**
     * Pre persist.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->inject($args);
    }

    /**
     * Pre update.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->inject($args);
    }

    /**
     * Inject routes.
     *
     * @param LifecycleEventArgs $args
     */
    protected function inject(LifecycleEventArgs $args)
    {
        $this->routeProcessor->injectRoutes($args->getEntity());
    }
}
