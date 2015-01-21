<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Entity\MenuRepository;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Knp\Menu\Twig\Helper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CmsExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends \Twig_Extension
{
    /**
     * @var Editor
     */
    protected $editor;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Twig_Template
     */
    protected $template;


    /**
     * Constructor.
     *
     * @param Editor $editor
     * @param MenuRepository $menuRepository
     * @param Helper $helper
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $config
     */
    public function __construct(
        Editor $editor,
        MenuRepository $menuRepository,
        Helper $helper,
        EventDispatcherInterface $eventDispatcher,
        array $config = array()
    ) {
        $this->editor = $editor;
        $this->menuRepository = $menuRepository;
        $this->helper = $helper;
        $this->eventDispatcher = $eventDispatcher;

        $this->config = array_merge(array(
            'template' => 'EkynaCmsBundle:Editor:content.html.twig',
            'seo_no_follow' => true,
            'seo_no_index' => true,
        ), $config);
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->template = $twig->loadTemplate($this->config['template']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms_metas', array($this, 'renderMetas'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_seo', array($this, 'renderSeo'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_meta', array($this, 'renderMeta'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_title', array($this, 'renderTitle'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_content', array($this, 'renderContent'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_content_block', array($this, 'renderContentBlock'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_block', array($this, 'renderBlock'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_menu', array($this, 'renderMenu'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_breadcrumb', array($this, 'renderBreadcrumb'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_flashes', array($this, 'renderFlashes'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param SeoInterface $seo
     * @return string
     * @deprecated use renderSeo()
     */
    public function renderMetas(SeoInterface $seo = null)
    {
        return $this->renderSeo($seo);
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param SeoInterface $seo
     * @return string
     */
    public function renderSeo(SeoInterface $seo = null)
    {
        if (null === $seo && null !== $page = $this->editor->getCurrentPage()) {
            $seo = $page->getSeo();
        }

        if (null !== $seo) {
            $follow = !$this->config['seo_no_follow'] ? ($seo->getFollow() ? 'follow' : 'nofollow') : 'nofollow';
            $index = !$this->config['seo_no_index'] ? ($seo->getIndex() ?  'index'  : 'noindex') : 'noindex';

            $metas =
                $this->renderTitle('title', $seo->getTitle()) . "\n" .
                $this->renderMeta('description', $seo->getDescription()) . "\n" .
                $this->renderMeta('robots', $follow.','.$index)
            ;

            if (0 < strlen($canonical = $seo->getCanonical())) {
                $metas .= "\n" .$this->renderTag('link', null, array(
                    'rel' => 'canonical',
                    'href' => $canonical,
                ));
            }

            // Tags the response as Seo relative
            $this->eventDispatcher->dispatch(
                HttpCacheEvents::TAG_RESPONSE,
                new HttpCacheEvent('ekyna_cms.seo[id:'.$seo->getId().']')
            );
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
        return $this->renderTag('meta', null, array('name' => $name, 'content' => $content));
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
        if (null === $content && null !== $page = $this->editor->getCurrentPage()) {
            $content = $page->getTitle();

            // Tags the response as Page relative
            $this->eventDispatcher->dispatch(
                HttpCacheEvents::TAG_RESPONSE,
                new HttpCacheEvent('ekyna_cms.page[id:'.$page->getId().']')
            );
        }

        if (0 == strlen($content)) {
            $content = 'Undefined title';
        }

        return $this->renderTag($tag, $content);
    }

    /**
     * Renders the html tag.
     *
     * @param $tag
     * @param string $content
     * @param array $attributes
     *
     * @return string
     */
    private function renderTag($tag, $content = null, array $attributes = array())
    {
        $attr = [];

        foreach($attributes as $key => $value) {
            $attr[] = sprintf(' %s="%s"', $key, $value);
        }

        if (0 < strlen($content)) {
            return sprintf('<%s%s>%s</%s>', $tag, implode('', $attr), $content, $tag);
        } else {
            return sprintf('<%s%s />', $tag, implode('', $attr));
        }
    }

    /**
     * Generates html from given Content.
     *
     * @param mixed $subject
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContent($subject = null)
    {
        $content = null;

        if ($subject instanceOf ContentInterface) {
            $content = $subject;
        } elseif ($subject instanceof ContentSubjectInterface) {
            if (null === $content = $subject->getContent()) {
                $content = $this->editor->createDefaultContent($subject);
            }
        } elseif (null === $subject) {
            if (null !== $page = $this->editor->getCurrentPage()) {
                if (null === $content = $page->getContent()) {
                    if ($page->getAdvanced()) {
                        $content = $this->editor->createDefaultContent($page);
                    } elseif (0 < strlen($html = $page->getHtml())) {
                        return $html;
                    } else {
                        return '<p>Page en construction.</p>';
                    }
                }
            }
        }

        if (null === $content) {
            throw new \RuntimeException('Undefined content.');
        }

        // TODO : hasBlock() does not use template inheritance.
        if (!$this->template->hasBlock('cms_block_content')) {
            throw new \RuntimeException('Unable to find "cms_block_content" twig block.');
        }

        $this->editor->setEnabled(true);

        // Tag response as Content relative
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::TAG_RESPONSE,
            new HttpCacheEvent('ekyna_cms.content[id:'.$content->getId().']')
        );

        return $this->template->renderBlock('cms_block_content', array(
            'content' => $content,
            'editable' => $this->editor->getEnabled()
        ));
    }

    /**
     * Generates html from given Content Block.
     *
     * @param BlockInterface $block
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContentBlock(BlockInterface $block)
    {
        $blockName = sprintf('cms_block_%s', $block->getType());

        if (!$this->template->hasBlock($blockName)) {
            throw new \RuntimeException('Unable to find "%s" twig block.', $blockName);
        }

        return trim($this->template->renderBlock($blockName, array('block' => $block)));
    }

    /**
     * Generates html from given Block.
     *
     * @param string $name the block name
     * @param string $type the block type
     * @param array $datas the block datas
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock($name, $type = null, array $datas = array())
    {
        $block = $this->editor->findBlockByName($name, $type, $datas);

        $this->editor->setEnabled(true);

        // Tags the response as Block relative
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::TAG_RESPONSE,
            new HttpCacheEvent('ekyna_cms.block[id:'.$block->getId().']')
        );

        return $this->template->renderBlock('cms_block', array(
            'block' => $block,
            'editable' => $this->editor->getEnabled()
        ));
    }

    /**
     * Renders the menu by his name.
     *
     * @param MenuInterface|string $name
     * @param array  $options
     * @param string $renderer
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function renderMenu($name, array $options = array(), $renderer = null)
    {
        if (null === $menu = $this->menuRepository->findOneBy(array('name' => $name))) {
            throw new \InvalidArgumentException(sprintf('Menu named "%s" not found.', $name));
        }

        // Tags the response as Block relative
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::TAG_RESPONSE,
            new HttpCacheEvent('ekyna_cms.menu[id:'.$menu->getId().']')
        );

        return $this->helper->render($name, $options, $renderer);
    }

    /**
     * Renders the breadcrumb.
     *
     * @param array $options
     *
     * @return string
     */
    public function renderBreadcrumb(array $options = array())
    {
        return $this->helper->render('breadcrumb', array_merge(array(
            'template' => 'EkynaCmsBundle:Cms:breadcrumb.html.twig',
            //'currentAsLink' => false,
            'depth' => 1,
        ), $options));
    }

    /**
     * Renders the session flashes.
     *
     * @param array $options
     *
     * @return string
     */
    public function renderFlashes(array $options = array())
    {
        // TODO if esi not enabled, render flashes.html.twig

        return '<div id="flashes"></div>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms';
    }
}
