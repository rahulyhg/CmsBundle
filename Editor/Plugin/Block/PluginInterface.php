<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginInterface as BaseInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Interface PluginInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface PluginInterface extends BaseInterface
{
    /**
     * Creates a new block.
     *
     * @param BlockInterface $block
     * @param array          $data
     */
    public function create(BlockInterface $block, array $data = []);

    /**
     * Updates a block.
     *
     * @param BlockInterface $block
     * @param Request        $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function update(BlockInterface $block, Request $request);

    /**
     * Removes a block.
     *
     * @param BlockInterface $block
     */
    public function remove(BlockInterface $block);

    /**
     * Validates the block (data).
     *
     * @param BlockInterface            $block
     * @param ExecutionContextInterface $context
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context);

    /**
     * Returns the block content.
     *
     * @param BlockInterface $block
     * @param BlockView      $view
     *
     * @return string
     */
    public function render(BlockInterface $block, BlockView $view);

    /**
     * Returns whether the block is supported.
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function supports(BlockInterface $block);
}