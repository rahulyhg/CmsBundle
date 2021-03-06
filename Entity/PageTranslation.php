<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Model\AbstractTranslation;
use Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface;

/**
 * Class PageTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageTranslation extends AbstractTranslation implements PageTranslationInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $breadcrumb;

    /**
     * @var string
     */
    protected $html;

    /**
     * @var string
     */
    protected $path;


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setBreadcrumb($breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }
}
