<?php

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

/**
 * Class PageGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageGenerator
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var \Ekyna\Component\Resource\Operator\ResourceOperatorInterface
     */
    private $pageOperator;

    /**
     * @var \Ekyna\Component\Resource\Operator\ResourceOperatorInterface
     */
    private $menuOperator;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var string
     */
    private $routesTranslationDomain = 'routes'; // TODO DI

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\SeoRepository
     */
    private $seoRepository;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    private $pageRepository;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\MenuRepository
     */
    private $menuRepository;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var string
     */
    private $homeRouteName;

    /**
     * @var RouteDefinition
     */
    private $homeDefinition;


    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param OutputInterface    $output
     */
    public function __construct(ContainerInterface $container, OutputInterface $output)
    {
        $this->output = $output;

        /** @var \Symfony\Component\Routing\RouterInterface|\JMS\I18nRoutingBundle\Router\I18nRouter $router */
        $router = $container->get('router');
        $i18nRouterClass = 'JMS\I18nRoutingBundle\Router\I18nRouterInterface';
        if (interface_exists($i18nRouterClass) && $router instanceof $i18nRouterClass) {
            $this->routes = $router->getOriginalRouteCollection();
        } else {
            $this->routes = $router->getRouteCollection();
        }
        $this->homeRouteName = $container->getParameter('ekyna_cms.home_route');

        $this->pageOperator = $container->get('ekyna_cms.page.operator');
        $this->menuOperator = $container->get('ekyna_cms.menu.operator');
        $this->validator = $container->get('validator');
        $this->translator = $container->get('translator');
        $this->locales = $container->getParameter('locales');
        $this->seoRepository = $container->get('ekyna_cms.seo.repository');
        $this->pageRepository = $container->get('ekyna_cms.page.repository');
        $this->menuRepository = $container->get('ekyna_cms.menu.repository');
    }

    public function generatePages()
    {
        $this->configureOptionsResolver();
        $this->gatherRoutesDefinitions();

        $this->createPage($this->homeDefinition);

        $this->removeNonMappedPages();
    }

    private function configureOptionsResolver()
    {
        /**
         * Seo options
         */
        $seoOptionResolver = new OptionsResolver();
        $seoOptionResolver
            ->setDefaults([
                'changefreq' => 'monthly',
                'priority'   => 0.5,
                'follow'     => true,
                'index'      => true,
                'canonical'  => null,
            ])
            ->setAllowedTypes('changefreq', 'string')
            ->setAllowedTypes('priority', 'float')
            ->setAllowedTypes('follow', 'bool')
            ->setAllowedTypes('index', 'bool')
            ->setAllowedTypes('canonical', ['string', 'null'])
            ->setAllowedValues('changefreq', Seo::getChangefreqs())
            ->setNormalizer('priority', function (Options $options, $value) {
                if (0 > $value) {
                    return 0;
                }
                if (1 < $value) {
                    return 1;
                }

                return $value;
            });

        /**
         * Menus options
         */
        $menuOptionResolver = new OptionsResolver();
        $menuOptionResolver
            ->setDefaults([
                'options'    => [],
                'attributes' => [],
            ])
            ->setAllowedTypes('options', 'array')
            ->setAllowedTypes('attributes', 'array');

        /**
         * Page options
         */
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver
            ->setDefaults([
                'name'     => null,
                'path'     => null,
                'parent'   => null,
                'locked'   => true,
                'advanced' => false,
                'dynamic' => false,
                'position' => 0,
                'seo'      => null,
                'menus'    => [],
            ])
            ->setRequired(['name', 'path'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('path', 'string')
            ->setAllowedTypes('parent', ['string', 'null'])
            ->setAllowedTypes('locked', 'bool')
            ->setAllowedTypes('advanced', 'bool')
            ->setAllowedTypes('dynamic', 'bool')
            ->setAllowedTypes('position', 'int')
            ->setAllowedTypes('seo', ['null', 'array'])
            ->setAllowedTypes('menus', 'array')
            ->setNormalizer('locked', function (Options $options, $value) {
                // Lock pages with parameters in path
                if (preg_match('#\{.*\}#', $options['path'])) {
                    return true;
                }

                return $value;
            })
            ->setNormalizer('menus', function (Options $options, $value) use ($menuOptionResolver) {
                $normalized = [];
                if (!empty($value)) {
                    foreach ($value as $key => $val) {
                        if (is_scalar($val)) {
                            $normalized[$val] = [];
                        } elseif (is_scalar($key) && null == $val) {
                            $normalized[$key] = [];
                        } elseif (is_array($val)) {
                            $normalized[$key] = $menuOptionResolver->resolve((array)$val);
                        } else {
                            throw new \InvalidArgumentException("Unexpected menu options format.");
                        }
                    }
                }

                return $normalized;
            })
            ->setNormalizer('seo', function (Options $options, $value) use ($seoOptionResolver) {
                return $seoOptionResolver->resolve((array)$value);
            });
    }

    /**
     * Resolve route options.
     *
     * @param Route  $route
     * @param string $routeName
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function resolveRouteOptions(Route $route, $routeName)
    {
        if (null === $cmsOptions = $route->getOption('_cms')) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not have "_cms" defaults attributes.', $routeName));
        }

        $path = $route->getPath();

        $dynamic = false;
        if (preg_match_all('~\{([^\}]+)\}~', $path, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // If route parameter does not have a default value
                if (!array_key_exists($match[1], $route->getDefaults())) {
                    $dynamic = true;
                    break;
                }
            }
        }

        return $this->optionsResolver->resolve(array_merge($cmsOptions, [
            'path'    => $path,
            'dynamic' => $dynamic,
        ]));
    }

    /**
     * Creates a tree of RouteDefinition
     *
     * @throws \RuntimeException
     */
    private function gatherRoutesDefinitions()
    {
        $route = $this->findRouteByName($this->homeRouteName);
        $this->homeDefinition = new RouteDefinition($this->homeRouteName, $this->resolveRouteOptions($route, $this->homeRouteName));

        /** @var Route $route */
        foreach ($this->routes as $name => $route) {
            if ($this->homeRouteName !== $name && null !== $cms = $route->getOption('_cms')) {
                if (array_key_exists('name', $cms)) {
                    $this->createRouteDefinition($name, $route);
                }
            }
        }

        $this->homeDefinition->sortChildren();
    }

    /**
     * Creates a route definition
     *
     * @param string                           $routeName
     * @param \Symfony\Component\Routing\Route $route
     *
     * @return RouteDefinition
     */
    private function createRouteDefinition($routeName, Route $route)
    {
        if (null === $definition = $this->findRouteDefinitionByRouteName($routeName)) {
            $definition = new RouteDefinition($routeName, $this->resolveRouteOptions($route, $routeName));
            if (null === $parentRouteName = $definition->getParentRouteName()) {
                // If parent route name is null => home page child
                $definition->setParentRouteName($this->homeRouteName);
                $this->homeDefinition->appendChild($definition);
            } else {
                // Creates parent route definition if needed
                $parentRoute = $this->findRouteByName($parentRouteName);
                $parentDefinition = $this->createRouteDefinition($parentRouteName, $parentRoute);
                $parentDefinition->appendChild($definition);
            }
        }

        return $definition;
    }

    /**
     * Finds a route by name
     *
     * @param string $name
     *
     * @throws \RuntimeException
     *
     * @return \Symfony\Component\Routing\Route|NULL
     */
    private function findRouteByName($name)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new \RuntimeException(sprintf('Route "%s" not found.', $name));
        }

        return $route;
    }

    /**
     * Finds a RouteDefinition by route name
     *
     * @param string $routeName
     *
     * @return RouteDefinition
     */
    private function findRouteDefinitionByRouteName($routeName)
    {
        if ($routeName === $this->homeRouteName) {
            return $this->homeDefinition;
        }

        return $this->homeDefinition->findChildByRouteName($routeName);
    }

    /**
     * Finds a page by route
     *
     * @param string $routeName
     *
     * @return PageInterface|NULL
     */
    private function findPageByRouteName($routeName)
    {
        return $this->pageRepository->findOneBy(['route' => $routeName]);
    }

    /**
     * Creates a Page from given Route
     *
     * @param RouteDefinition $definition
     * @param PageInterface   $parentPage
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    private function createPage(RouteDefinition $definition, PageInterface $parentPage = null)
    {
        $routeName = $definition->getRouteName();
        if (null !== $page = $this->findPageByRouteName($routeName)) {

            $updated = false;
            if ($page->getName() !== $definition->getPageName()) {
                $page->setName($definition->getPageName());
                $updated = true;
            }
            if ($page->isLocked() !== $definition->isLocked()) {
                $page->setLocked($definition->isLocked());
                $updated = true;
            }
            if ($page->isAdvanced() !== $definition->isAdvanced()) {
                $page->setAdvanced($definition->isAdvanced());
                $updated = true;
            }
            if ($page->isDynamicPath() !== $definition->isDynamic()) {
                $page->setDynamicPath($definition->isDynamic());
                $updated = true;
            }

            // Watch for paths update
            foreach ($this->locales as $locale) {
                if ($routeName === $path = $this->translator->trans(
                        $routeName, [], $this->routesTranslationDomain, $locale
                    )
                ) {
                    $path = $definition->getPath();
                }

                $pageTranslation = $page->translate($locale, true);
                if ($pageTranslation->getPath() !== $path) {
                    $pageTranslation->setPath($path);
                    $updated = true;
                }
            }

            if ($updated) {
                if (!$this->validate($page)) {
                    return false;
                }
                $this->pageOperator->persist($page);
            }

            $this->outputPageAction($page->getName(), $updated ? 'updated' : 'already exists');

        } else {
            /** @var PageInterface $page */
            $page = $this->pageRepository->createNew();

            // Seo
            $seoDefinition = $definition->getSeo();
            /** @var SeoInterface $seo */
            $seo = $this->seoRepository->createNew();
            $seo
                ->setChangefreq($seoDefinition['changefreq'])
                ->setPriority($seoDefinition['priority'])
                ->setFollow($seoDefinition['follow'])
                ->setIndex($seoDefinition['index'])
                ->setCanonical($seoDefinition['canonical']);

            // Page
            $page
                ->setName($definition->getPageName())
                ->setRoute($routeName)
                ->setPath($definition->getPath())
                ->setStatic(true)
                ->setLocked($definition->isLocked())
                ->setAdvanced($definition->isAdvanced())
                ->setParent($parentPage)
                ->setSeo($seo);

            $dynamic = false;
            foreach ($this->locales as $locale) {
                $title = $seoTitle = $definition->getPageName();
                if (null !== $parentPage && $parentPage->getRoute() !== $this->homeRouteName) {
                    $seoTitle = sprintf('%s - %s', $parentPage->getSeo()->translate($locale)->getTitle(), $title);
                }

                $seoTranslation = $seo->translate($locale, true);
                $seoTranslation
                    ->setTitle($seoTitle);

                if ($routeName === $path = $this->translator->trans(
                        $routeName, [], $this->routesTranslationDomain, $locale
                    )
                ) {
                    $path = $definition->getPath();
                }

                $pageTranslation = $page->translate($locale, true);
                $pageTranslation
                    ->setTitle($title)
                    ->setBreadcrumb($title)
                    ->setPath($path);

                if (0 < preg_match('~\{.*\}~', $path)) {
                    $dynamic = true;
                }
            }

            if ($dynamic != $page->isDynamicPath()) {
                $page->setDynamicPath($dynamic);
            }

            if (!$this->validate($page)) {
                return false;
            }

            $this->pageOperator->persist($page);

            $this->outputPageAction($page->getName(), 'created');
        }

        if (!$this->createMenus($page, $definition->getMenus())) {
            return false;
        }

        // Creates children pages
        foreach ($definition->getChildren() as $child) {
            if (!$this->createPage($child, $page)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates the menus entries.
     *
     * @param PageInterface $page
     * @param array         $menus
     *
     * @return bool
     */
    private function createMenus(PageInterface $page, array $menus)
    {
        if (!empty($menus)) {
            foreach ($menus as $parentName => $config) {
                if (null === $parent = $this->menuRepository->findOneByName($parentName)) {
                    $this->output->writeln(sprintf(
                        '<error>Parent menu "%s" not found for route "%s".</error>',
                        $parentName,
                        $page->getRoute()
                    ));

                    return false;
                }

                $name = $page->getRoute();
                if (null === $this->menuRepository->findOneBy(['name' => $name, 'parent' => $parent])) {
                    /** @var \Ekyna\Bundle\CmsBundle\Model\MenuInterface $menu */
                    $menu = $this->menuRepository->createNew();
                    $menu
                        ->setParent($parent)
                        ->setName($name)
                        ->setRoute($name);

                    if (isset($config['options'])) {
                        $menu->setOptions((array)$config['options']);
                    }
                    if (isset($config['attributes'])) {
                        $menu->setAttributes((array)$config['attributes']);
                    }

                    foreach ($this->locales as $locale) {
                        $menuTranslation = $menu->translate($locale, true);
                        $menuTranslation
                            ->setTitle($page->translate($locale)->getTitle());
                    }

                    if (!$this->validate($menu)) {
                        return false;
                    }

                    $this->menuOperator->persist($menu);
                }
            }
        }

        return true;
    }

    /**
     * Removes static pages which are no longer mapped to the routing.
     */
    private function removeNonMappedPages()
    {
        /** @var PageInterface[] $staticPages */
        $staticPages = $this->pageRepository->findBy(['static' => true], ['left' => 'DESC']);
        foreach ($staticPages as $page) {
            if (null === $this->findRouteDefinitionByRouteName($page->getRoute())) {
                $this->outputPageAction($page->getName(), 'removed');
                $this->pageOperator->delete($page);
            }
        }
    }

    /**
     * Validates the element.
     *
     * @param object $element
     *
     * @return bool
     */
    private function validate($element)
    {
        $violationList = $this->validator->validate($element, null, ['Generator']);
        if (0 < $violationList->count()) {
            $this->output->writeln('<error>Invalid element</error>');
            /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
            foreach ($violationList as $violation) {
                $this->output->writeln(sprintf('<error>%s : %s</error>', $violation->getPropertyPath(), $violation->getMessage()));
            }

            return false;
        }

        return true;
    }

    /**
     * Outputs the page action.
     *
     * @param string $name
     * @param string $action
     */
    private function outputPageAction($name, $action)
    {
        $this->output->writeln(sprintf(
            '- <comment>%s</comment> %s %s.',
            $name,
            str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT),
            $action
        ));
    }
}
