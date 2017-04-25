<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Entity\Menu;
use Ekyna\Bundle\CmsBundle\Entity\SeoRepository;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Menu\MenuProvider;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Ekyna\Bundle\CmsBundle\Service\Renderer\TagRenderer;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Ekyna\Bundle\SettingBundle\Manager\SettingsManagerInterface;
use Knp\Menu\Twig\Helper;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * Class CmsExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends \Twig_Extension
{
    /**
     * @var SettingsManagerInterface
     */
    protected $settings;

    /**
     * @var MenuProvider
     */
    protected $menuProvider;

    /**
     * @var Helper
     */
    protected $menuHelper;

    /**
     * @var PageHelper
     */
    protected $pageHelper;

    /**
     * @var SeoRepository
     */
    protected $seoRepository;

    /**
     * @var TagManager
     */
    protected $tagManager;

    /**
     * @var FragmentHandler
     */
    protected $fragmentHandler;

    /**
     * @var TagRenderer
     */
    protected $tagRenderer;

    /**
     * @var array
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param SettingsManagerInterface $settings
     * @param MenuProvider             $menuProvider
     * @param Helper                   $menuHelper
     * @param PageHelper               $pageHelper
     * @param SeoRepository            $seoRepository
     * @param TagManager               $tagManager
     * @param FragmentHandler          $fragmentHandler
     * @param array                    $config
     */
    public function __construct(
        SettingsManagerInterface $settings,
        MenuProvider $menuProvider,
        Helper $menuHelper,
        PageHelper $pageHelper,
        SeoRepository $seoRepository,
        TagManager $tagManager,
        FragmentHandler $fragmentHandler,
        array $config
    ) {
        $this->settings = $settings;
        $this->menuProvider = $menuProvider;
        $this->menuHelper = $menuHelper;
        $this->pageHelper = $pageHelper;
        $this->tagManager = $tagManager;
        $this->seoRepository = $seoRepository;
        $this->fragmentHandler = $fragmentHandler;

        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('cms_tags', [$this, 'renderTags'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_page', [$this, 'getPage']),
            new \Twig_SimpleFunction('cms_metas', [$this, 'renderMetas'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_seo', [$this, 'renderSeo'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_meta', [$this, 'renderMeta'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_title', [$this, 'renderTitle'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_breadcrumb', [$this, 'renderBreadcrumb'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_flashes', [$this, 'renderFlashes'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_page_controller', [$this, 'getPageControllerTitle'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Returns the current page.
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\PageInterface|null
     */
    public function getPage()
    {
        return $this->pageHelper->getCurrent();
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param mixed $seoOrSubject
     *
     * @return string
     * @deprecated use renderSeo()
     */
    public function renderMetas($seoOrSubject = null)
    {
        if ($seoOrSubject instanceof SeoSubjectInterface) {
            $seoOrSubject = $seoOrSubject->getSeo();
        }

        return $this->renderSeo($seoOrSubject);
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param SeoInterface $seo
     *
     * @return string
     */
    public function renderSeo(SeoInterface $seo = null)
    {
        if (null === $seo) {
            if (null !== $page = $this->getPage()) {
                $seo = $page->getSeo();
            } else {
                /** @var SeoInterface $seo */
                $seo = $this->seoRepository->createNew();
                $seo
                    ->setTitle($this->settings->getParameter('seo.title'))
                    ->setDescription($this->settings->getParameter('seo.description'))
                    ->setIndex(!$this->config['seo']['no_index'])
                    ->setFollow(!$this->config['seo']['no_follow']);
            }
        }

        if (null !== $seo) {
            $follow = !$this->config['seo']['no_follow'] ? ($seo->getFollow() ? 'follow' : 'nofollow') : 'nofollow';
            $index = !$this->config['seo']['no_index'] ? ($seo->getIndex() ? 'index' : 'noindex') : 'noindex';

            $metas =
                $this->renderTitle('title', $seo->getTitle() . $this->config['seo']['title_append']) . "\n" .
                $this->renderMeta('description', $seo->getDescription()) . "\n" .
                $this->renderMeta('robots', $follow . ',' . $index);

            if (0 < strlen($canonical = $seo->getCanonical())) {
                $metas .= "\n" . $this->renderTag('link', null, [
                        'rel'  => 'canonical',
                        'href' => $canonical,
                    ]);
            }

            // Tags the response as Seo relative
            if ($seo->getId()) {
                $this->tagManager->addTags($seo->getEntityTag());
            }
        } else {
            $metas = '<title>Undefined</title>' . $this->renderMeta('robots', 'follow,noindex');
        }

        return $metas;
    }

    /**
     * Generates a meta tag.
     *
     * @param string $name
     * @param string $content
     *
     * @return string
     */
    public function renderMeta($name, $content)
    {
        return $this->renderTag('meta', null, ['name' => $name, 'content' => $content]);
    }

    /**
     * Returns current page's title.
     *
     * @param string $tag
     * @param string $content
     *
     * @return string
     */
    public function renderTitle($tag = 'h1', $content = null)
    {
        if (null === $content && null !== $page = $this->getPage()) {
            $content = $page->getTitle();

            // Tags the response as Page relative
            $this->tagManager->addTags($page->getEntityTag());
        }

        if (0 == strlen($content)) {
            $content = 'Undefined title';
        }

        return $this->renderTag($tag, $content);
    }

    /**
     * Renders the menu by his name.
     *
     * @param string $name
     * @param array  $options
     * @param string $renderer
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function renderMenu($name, array $options = [], $renderer = null)
    {
        if (null === $menu = $this->menuProvider->findByName($name)) {
            throw new \InvalidArgumentException(sprintf('Menu named "%s" not found.', $name));
        }

        // Tags the response as Menu relative
        $this->tagManager->addTags([
            Menu::getEntityTagPrefix(),
            sprintf('%s[id:%s]', Menu::getEntityTagPrefix(), $menu['id']),
        ]);

        return $this->menuHelper->render($name, $options, $renderer);
    }

    /**
     * Renders the breadcrumb.
     *
     * @param array $options
     *
     * @return string
     */
    public function renderBreadcrumb(array $options = [])
    {
        return $this->menuHelper->render('breadcrumb', array_merge([
            'template' => 'EkynaCmsBundle:Cms:breadcrumb.html.twig',
            //'currentAsLink' => false,
            'depth'    => 1,
        ], $options));
    }

    /**
     * Renders the tags.
     *
     * @param mixed $subjectOrTags
     * @param array $options
     *
     * @return string
     */
    public function renderTags($subjectOrTags, array $options = [])
    {
        if (null === $this->tagRenderer) {
            $this->tagRenderer = new TagRenderer();
        }

        return $this->tagRenderer->renderTags($subjectOrTags, $options);
    }

    /**
     * Renders the session flashes.
     *
     * @return string
     */
    public function renderFlashes()
    {
        if ($this->config['esi_flashes']) {
            return $this->fragmentHandler->render(new ControllerReference('EkynaCmsBundle:Cms:flashes'), 'esi');
        }

        return '<div id="cms-flashes"></div>';
    }

    /**
     * Returns the page controller title.
     *
     * @param string $name
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getPageControllerTitle($name)
    {
        if (!array_key_exists($name, $this->config['page']['controllers'])) {
            throw new \InvalidArgumentException(sprintf('Undefined controller "%s".', $name));
        }

        return $this->config['page']['controllers'][$name]['title'];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ekyna_cms';
    }

    /**
     * Renders the html tag.
     *
     * @param        $tag
     * @param string $content
     * @param array  $attributes
     *
     * @return string
     */
    private function renderTag($tag, $content = null, array $attributes = [])
    {
        $attr = [];

        foreach ($attributes as $key => $value) {
            $attr[] = sprintf(' %s="%s"', $key, $value);
        }

        if (0 < strlen($content)) {
            return sprintf('<%s%s>%s</%s>', $tag, implode('', $attr), $content, $tag);
        } else {
            return sprintf('<%s%s />', $tag, implode('', $attr));
        }
    }

    /**
     * Returns the default configuration.
     *
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'home_route'  => 'home',
            'seo'         => [
                'no_follow'    => true,
                'no_index'     => true,
                'title_append' => null,
            ],
            'page'        => [
                'controllers' => [],
            ],
            'esi_flashes' => false,
        ];
    }
}
