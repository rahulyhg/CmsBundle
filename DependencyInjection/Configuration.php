<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_cms');

        /** @noinspection PhpUndefinedMethodInspection */
        $rootNode
            ->children()
                ->scalarNode('output_dir')->defaultValue('')->cannotBeEmpty()->end()
                ->booleanNode('esi_flashes')->defaultFalse()->end()
                ->scalarNode('home_route')->defaultNull()->end()
            ->end()
        ;

        $this->addSeoSection($rootNode);
        $this->addPageSection($rootNode);
        $this->addMenuSection($rootNode);
        $this->addEditorSection($rootNode);
        $this->addPoolsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `seo` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addSeoSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('seo')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('no_follow')->defaultFalse()->end()
                        ->booleanNode('no_index')->defaultFalse()->end()
                        ->scalarNode('title_append')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `page` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPageSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('page')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('controllers')
                            ->defaultValue(['default' => [
                                'title' => 'Par défaut',
                                'value' => 'EkynaCmsBundle:Cms:default',
                                'advanced' => true,
                            ]])
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('value')->isRequired()->cannotBeEmpty()->end()
                                    ->booleanNode('advanced')->defaultFalse()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cookie_consent')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enable')->defaultTrue()->end()
                                ->scalarNode('controller')->defaultValue('EkynaCmsBundle:Cms:default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('wide_search')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enable')->defaultTrue()->end()
                                ->scalarNode('controller')->defaultValue('EkynaCmsBundle:Cms:search')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `menu` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addMenuSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('menu')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('roots')
                            ->defaultValue(['main' => [
                                'title' => 'Navigation principale',
                                'description' => 'Barre de navigation principale',
                            ]])
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('description')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `editor` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addEditorSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('editor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('plugin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('block')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('ekyna_block_tinymce')->end()
                                        ->integerNode('min_size')
                                            ->defaultValue(2)
                                            ->validate()
                                            ->ifTrue(function($value) { return $value < 1 || 12 < $value; })
                                                ->thenInvalid('Minimum block size must be greater than 0 and smaller than 13.')
                                            ->end()
                                        ->end()
                                        ->arrayNode('tinymce')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default_content')
                                                    ->defaultValue('<p>Default content.</p>')
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('image')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default_path')
                                                    ->defaultValue('/bundles/ekynacms/img/default-image.gif')
                                                ->end()
                                                ->scalarNode('default_alt')
                                                    ->defaultValue('Default image')
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('container')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('ekyna_container_background')->end()
                                        ->arrayNode('background')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default_color')->defaultValue('')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `pools` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPoolsSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('pools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('seo')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Seo')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('operator')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\SeoRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\SeoType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\SeoType')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\SeoTranslation')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title', 'description'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html'     => 'EkynaCmsBundle:Admin/Page:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Admin/Page:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Admin/Page:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Admin/Page:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Admin/Page:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Admin/Page:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Admin/Page:remove.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Page')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\PageController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\PageRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\PageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\PageType')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('event')->defaultValue('Ekyna\Bundle\CmsBundle\Event\PageEvent')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\PageTranslation')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title', 'html', 'path'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('menu')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html'     => 'EkynaCmsBundle:Admin/Menu:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Admin/Menu:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Admin/Menu:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Admin/Menu:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Admin/Menu:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Admin/Menu:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Admin/Menu:remove.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Menu')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\MenuController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\MenuRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\MenuType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\MenuType')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('event')->defaultValue('Ekyna\Bundle\CmsBundle\Event\MenuEvent')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\MenuTranslation')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultNull()->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Tag')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\TagType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\TagType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
