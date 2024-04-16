<?php

declare(strict_types=1);

namespace Spyck\SonataExtension\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Spyck\SonataExtension\Security\SecurityInterface;

abstract class AbstractAdmin extends SonataAbstractAdmin implements SecurityInterface
{
    public function getRole(): ?string
    {
        return null;
    }

    protected function getAddRoutes(): iterable
    {
        return [];
    }

    protected function getRemoveRoutes(): iterable
    {
        return [];
    }

    protected function configureRoutes(RouteCollectionInterface $routeCollection): void
    {
        foreach ($this->getAddRoutes() as $route) {
            $routeCollection->add($route, sprintf('%s/%s', $this->getRouterIdParameter(), $route));
        }

        foreach ($this->getRemoveRoutes() as $route) {
            $routeCollection->remove($route);
        }
    }

    protected function createNewInstance(): object
    {
        if ($this->isCurrentRoute('clone')) {
            return clone $this->getSubject();
        }

        return parent::createNewInstance();
    }
}
