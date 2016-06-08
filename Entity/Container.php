<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Container
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Container implements Cms\ContainerInterface
{
    use Core\SortableTrait,
        Core\TimestampableTrait,
        Core\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Cms\ContentInterface
     */
    protected $content;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ArrayCollection|Cms\RowInterface[]
     */
    protected $rows;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
        $this->rows = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setContent(Cms\ContentInterface $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setRows(ArrayCollection $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRow(Cms\RowInterface $row)
    {
        $row->setContainer($this);
        $this->rows->add($row);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRow(Cms\RowInterface $row)
    {
        $row->setContainer(null);
        $this->rows->removeElement($row);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * {@inheritdoc}
     */
    public function sortRows()
    {
        $iterator = $this->rows->getIterator();
        $iterator->uasort(function (Cms\RowInterface $a, Cms\RowInterface $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });
        $this->rows = new ArrayCollection(iterator_to_array($iterator));

        return $this;
    }

    /**
     * {@inheritdoc}
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents()
    {
        $contents = [];

        /* TODO foreach ($this->blocks as $block) {
            if ($block->isIndexable()) {
                foreach ($block->getIndexableContents() as $locale => $content) {
                    if (!array_key_exists($locale, $contents)) {
                        $contents[$locale] = array('content' => '');
                    }
                    $contents[$locale]['content'] .= $content;
                }
            }
        }*/

        return $contents;
    }

    /**
     * Returns the entity tag.
     *
     * @return string
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.container';
    }
}
