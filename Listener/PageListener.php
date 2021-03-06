<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\SettingBundle\Event\BuildRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\DiscardRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PageListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @todo    resource (persistence) event subscriber ?
 */
class PageListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RouterInterface|\JMS\I18nRoutingBundle\Router\I18nRouter
     */
    private $router;

    /**
     * @var array
     */
    private $pageConfig;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;


    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param RouterInterface          $router
     * @param array                    $pageConfig
     * @param array                    $locales
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        RouterInterface $router,
        array $pageConfig,
        array $locales
    ) {
        $this->dispatcher = $dispatcher;
        $this->router = $router;
        $this->pageConfig = $pageConfig;
        $this->locales = $locales;
    }

    /**
     * Pre persist event handler.
     *
     * @param PageInterface $page
     */
    public function prePersist(PageInterface $page)
    {
        $this->handlePage($page);
    }

    /**
     * Pre update event handler.
     *
     * @param PageInterface      $page
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PageInterface $page, PreUpdateEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($this->handlePage($page)) {
            $metadata = $em->getClassMetadata(get_class($page));
            $uow->recomputeSingleEntityChangeSet($metadata, $page);
        }

        /*$changeSet = $event->getEntityChangeSet();
        if (array_key_exists('enabled', $changeSet)) {
            if ($page->isEnabled()) {
                $this->discardPageRedirections($page);
            } else {
                $this->buildPageRedirections($page);
            }
        }*/
    }

    /**
     * Post update event handler.
     *
     * @param PageInterface      $page
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(PageInterface $page, LifecycleEventArgs $event)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $event->getObjectManager();
        $uow = $em->getUnitOfWork();

        $changeSet = $uow->getEntityChangeSet($page);
        if (array_key_exists('enabled', $changeSet)) {
            if ($page->isEnabled()) {
                $this->discardPageRedirections($page);
            } else {
                $this->buildPageRedirections($page);
            }
        }
    }

    /**
     * Post remove event handler.
     *
     * @param PageInterface $page
     */
    public function postRemove(PageInterface $page)
    {
        $this->buildPageRedirections($page);
    }

    /**
     * Handles the page.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function handlePage(PageInterface $page)
    {
        $doRecompute = false;

        $dynamicPath = $this->hasDynamicPath($page);
        if ($dynamicPath != $page->isDynamicPath()) {
            $page->setDynamicPath($dynamicPath);
            $doRecompute = true;
        }

        $advanced = $this->isAdvanced($page);
        if (null !== $advanced && $advanced != $page->isAdvanced()) {
            $page->setAdvanced($advanced);
            $doRecompute = true;
        }

        return $doRecompute;
    }

    /**
     * Returns whether the page has dynamic path or not.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function hasDynamicPath(PageInterface $page)
    {
        if (empty($route = $page->getRoute())) {
            return false;
        }

        if (null === $route = $this->findRoute($route)) {
            return false;
        }

        if (preg_match_all('~\{([^\}]+)\}~', $route->getPath(), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // If route parameter does not have a default value
                if (!array_key_exists($match[1], $route->getDefaults())) {
                    return true;
                }
            }
        }


        return false;
    }

    /**
     * Finds the route for the given name.
     *
     * @param string $name
     *
     * @return \Symfony\Component\Routing\Route|null
     */
    private function findRoute($name)
    {
        if (null === $this->routes) {
            $i18nRouterClass = 'JMS\I18nRoutingBundle\Router\I18nRouterInterface';
            if (interface_exists($i18nRouterClass) && $this->router instanceof $i18nRouterClass) {
                $this->routes = $this->router->getOriginalRouteCollection();
            } else {
                $this->routes = $this->router->getRouteCollection();
            }
        }

        return $this->routes->get($name);
    }

    /**
     * Builds redirections for the page.
     *
     * @param PageInterface $page
     */
    private function buildPageRedirections(PageInterface $page)
    {
        if (!$page->isEnabled()) {
            $redirections = [];

            // Store "from" paths for each locale
            $locales = [];
            /** @var \Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface $translation */
            foreach ($page->getTranslations() as $locale => $translation) {
                $locales[$locale] = $locale;
                $redirections[$locale] = [
                    'from' => $translation->getPath(),
                ];
            }

            // Find the first enabled ancestor
            $parentPage = $page;
            while (null !== $parentPage = $parentPage->getParent()) {
                if ($parentPage->isEnabled()) {
                    // Store "to" paths for each locale
                    foreach ($parentPage->getTranslations() as $locale => $translation) {
                        if (array_key_exists($locale, $redirections)) {
                            $redirections[$locale]['to'] = $translation->getPath();
                            unset($locales[$locale]);
                        }
                        // Check that all locales has been handled
                        if (empty($locales)) {
                            break 2;
                        }
                    }
                }
            }

            if (!empty($redirections)) {
                foreach ($redirections as $locale => $redirection) {
                    if (!(array_key_exists('from', $redirection) && array_key_exists('to', $redirection))) {
                        continue;
                    }
                    // TODO use url generator or i18n routing prefix strategy
                    $localePrefix = $locale != 'fr' ? '/' . $locale : '';
                    $event = new BuildRedirectionEvent($localePrefix . $redirection['from'], $localePrefix . $redirection['to'], true);
                    $this->dispatcher->dispatch(RedirectionEvents::BUILD, $event);
                }
            }
        }
    }

    /**
     * Discards redirections for the page.
     *
     * @param PageInterface $page
     */
    private function discardPageRedirections(PageInterface $page)
    {
        if ($page->isEnabled()) {
            /** @var \Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface $translation */
            foreach ($page->getTranslations() as $locale => $translation) {
                // TODO use url generator or i18n routing prefix strategy
                $localePrefix = $locale != 'fr' ? '/' . $locale : '';
                $event = new DiscardRedirectionEvent($localePrefix . $translation->getPath());
                $this->dispatcher->dispatch(RedirectionEvents::DISCARD, $event);
            }
        }
    }


    /**
     * Returns whether the page is advanced or not.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function isAdvanced(PageInterface $page)
    {
        if (null !== $controller = $page->getController()) {
            if (array_key_exists($controller, $this->pageConfig['controllers'])) {
                return $this->pageConfig['controllers'][$controller]['advanced'];
            }
            throw new \RuntimeException("Undefined page controller '{$controller}'.");
        }

        return null;
    }
}
