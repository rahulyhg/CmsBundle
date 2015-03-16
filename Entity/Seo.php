<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\SeoInterface;

/**
 * Class Seo
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Seo implements SeoInterface
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
    protected $changefreq;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var boolean
     */
    protected $follow;

    /**
     * @var boolean
     */
    protected $index;

    /**
     * @var string
     */
    protected $canonical;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changefreq = 'monthly';
        $this->priority = 0.5;
        $this->follow = true;
        $this->index = true;
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritDoc}
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;

        return $this;
    }

    /**
     * {@inheritDoc} 
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * {@inheritDoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritDoc}
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * {@inheritDoc}
     */
    public function setFollow($follow)
    {
        $this->follow = (bool)$follow;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function setIndex($index)
    {
        $this->index = (bool)$index;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * {@inheritDoc}
     */
    public function setCanonical($canonical)
    {
        $this->canonical = $canonical;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public static function getChangefreqs()
    {
        return array('hourly', 'monthly', 'yearly');
    }

    /**
     * Returns whether the exhibitor should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable()
    {
        return $this->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTag()
    {
        if (null === $this->getId()) {
            throw new \RuntimeException('Unable to generate entity tag, as the id property is undefined.');
        }
        return sprintf('ekyna_cms.seo[id:%s]', $this->getId());
    }
}
