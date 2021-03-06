<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Model\AbstractTranslation;
use Ekyna\Bundle\CmsBundle\Model\SeoTranslationInterface;

/**
 * Class SeoTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoTranslation extends AbstractTranslation implements SeoTranslationInterface
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
    protected $description;

    /**
     * @var string
     */
    protected $keywords;


    /**
     * Clones the seo translation.
     */
    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
            $this->translatable = null;
        }
    }

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
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        return 0 == strlen($this->title);
    }
}
