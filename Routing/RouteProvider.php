<?php

namespace Ekyna\Bundle\CmsBundle\Routing;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;

/**
 * Class RouteProvider
 * @package Ekyna\Bundle\CmsBundle\Routing
 * @author Étienne Dauvergne <contact@ekyna.com>
 * @see http://symfony.com/doc/master/cmf/components/routing/nested_matcher.html#the-routeprovider
 */
class RouteProvider implements RouteProviderInterface
{
    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    protected $pageRepository;

    /**
     * Constructor.
     *
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Finds Routes that match the given request.
     *
     * @param Request $request
     *
     * @return RouteCollection
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        /** @var PageInterface[] $pages */
        $pages = $this->pageRepository->findBy(array(
            'path' => rawurldecode($request->getPathInfo()),
            'static' => false
        ));

        $collection = new RouteCollection();
        foreach($pages as $page) {
            $collection->add($page->getRoute(), $this->routeFromPage($page));
        }

        return $collection;
    }

    /**
     * Find Routes by name.
     *
     * @param array|null $names
     * @param array $parameters
     *
     * @return array|\Symfony\Component\Routing\Route[]
     */
    public function getRoutesByNames($names, $parameters = array())
    {
        // TODO optimize by querying only required fields
        $pages = $this->pageRepository->findBy(array('route' => $names));

        $routes = array();
        foreach($pages as $page) {
            $routes[] = $this->routeFromPage($page);
        }

        return $routes;
    }

    /**
     * Finds a Route by name.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return null|Route
     */
    public function getRouteByName($name, $parameters = array())
    {
        // TODO optimize by querying only required fields
        if (null !== $page = $this->pageRepository->findOneByRoute($name)) {
            return $this->routeFromPage($page);
        }
        return null;
    }

    /**
     * Creates a Route form the given Page.
     *
     * @param PageInterface $page
     *
     * @return Route
     */
    protected function routeFromPage(PageInterface $page)
    {
        $route = new Route($page->getPath());

        $route
            ->setDefault('_controller', $page->getController())
            ->setMethods(array('GET'))
        ;

        return $route;
    }
}
