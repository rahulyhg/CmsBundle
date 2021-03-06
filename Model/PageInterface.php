<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model as RM;

/**
 * Class PageInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method PageTranslationInterface translate($locale = null, $create = false)
 */
interface PageInterface extends
    ContentSubjectInterface,
    SeoSubjectInterface,
    RM\TimestampableInterface,
    RM\TaggedEntityInterface,
    RM\TranslatableInterface
{
    /**
     * Set parent
     *
     * @param PageInterface $parent
     * @return PageInterface|$this
     */
    public function setParent(PageInterface $parent = null);

    /**
     * Get parent
     *
     * @return PageInterface|$this
     */
    public function getParent();

    /**
     * Set left
     *
     * @param integer $left
     * @return PageInterface|$this
     */
    public function setLeft($left);

    /**
     * Get left
     *
     * @return integer
     */
    public function getLeft();

    /**
     * Set right
     *
     * @param integer $right
     * @return PageInterface|$this
     */
    public function setRight($right);

    /**
     * Get right
     *
     * @return integer
     */
    public function getRight();

    /**
     * Set root
     *
     * @param integer $root
     * @return PageInterface|$this
     */
    public function setRoot($root = null);

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot();

    /**
     * Set level
     *
     * @param integer $level
     * @return PageInterface|$this
     */
    public function setLevel($level);

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel();

    /**
     * Returns whether the page has the child or not.
     *
     * @param PageInterface $child
     * @return bool
     */
    public function hasChild(PageInterface $child);

    /**
     * Add children
     *
     * @param PageInterface $child
     * @return PageInterface|$this
     */
    public function addChild(PageInterface $child);

    /**
     * Remove children
     *
     * @param PageInterface $child
     * @return PageInterface|$this
     */
    public function removeChild(PageInterface $child);

    /**
     * Has children
     *
     * @return boolean
     */
    public function hasChildren();

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|PageInterface[]
     */
    public function getChildren();

    /**
     * Set name
     *
     * @param string $name
     * @return PageInterface|$this
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set title
     *
     * @param string $title
     * @return PageInterface|$this
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set breadcrumb
     *
     * @param string $breadcrumb
     * @return PageInterface|$this
     */
    public function setBreadcrumb($breadcrumb);

    /**
     * Get breadcrumb
     *
     * @return string
     */
    public function getBreadcrumb();

    /**
     * Set html
     *
     * @param string $html
     *
     * @return PageInterface|$this
     */
    public function setHtml($html);

    /**
     * Return html
     *
     * @return string
     */
    public function getHtml();

    /**
     * Set path
     *
     * @param string $path
     * @return PageInterface|$this
     */
    public function setPath($path);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set route
     *
     * @param string $route
     * @return PageInterface|$this
     */
    public function setRoute($route = null);

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute();

    /**
     * Set static
     *
     * @param boolean $static
     * @return PageInterface|$this
     */
    public function setStatic($static);

    /**
     * Get static
     *
     * @return boolean
     */
    public function isStatic();

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return PageInterface|$this
     */
    public function setLocked($locked);

    /**
     * Get locked
     *
     * @return boolean
     */
    public function isLocked();

    /**
     * Set controller
     *
     * @param string $controller
     * @return PageInterface|$this
     */
    public function setController($controller = null);

    /**
     * Get controller
     *
     * @return string
     */
    public function getController();

    /**
     * Set advanced
     *
     * @param boolean $advanced
     * @return PageInterface|$this
     */
    public function setAdvanced($advanced);

    /**
     * Get advanced
     *
     * @return boolean
     */
    public function isAdvanced();

    /**
     * Sets the dynamicPath.
     *
     * @param boolean $dynamicPath
     * @return PageInterface|$this
     */
    public function setDynamicPath($dynamicPath);

    /**
     * Returns the dynamicPath.
     *
     * @return boolean
     */
    public function isDynamicPath();

    /**
     * Sets the enabled.
     *
     * @param boolean $enabled
     * @return PageInterface|$this
     */
    public function setEnabled($enabled);

    /**
     * Returns the enabled.
     *
     * @return boolean
     */
    public function isEnabled();

    /**
     * Returns whether the exhibitor should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable();
}
