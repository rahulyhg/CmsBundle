<?php


namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Editor\Model\LayoutInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface RowInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface RowInterface
    extends LayoutInterface,
            RM\SortableInterface,
            RM\TimestampableInterface,
            RM\TaggedEntityInterface
{
    /**
     * Set container
     *
     * @param ContainerInterface $container
     *
     * @return RowInterface|$this
     */
    public function setContainer(ContainerInterface $container = null);

    /**
     * Get container
     *
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * Sets the name
     *
     * @param string $name
     *
     * @return RowInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName();

    /**
     * Set blocks
     *
     * @param ArrayCollection|BlockInterface[] $blocks
     *
     * @return RowInterface|$this
     */
    public function setBlocks(ArrayCollection $blocks);

    /**
     * Add block
     *
     * @param BlockInterface $block
     *
     * @return RowInterface|$this
     */
    public function addBlock(BlockInterface $block);

    /**
     * Remove block
     *
     * @param BlockInterface $block
     *
     * @return RowInterface|$this
     */
    public function removeBlock(BlockInterface $block);

    /**
     * Get blocks
     *
     * @return ArrayCollection|BlockInterface[]
     */
    public function getBlocks();
}
