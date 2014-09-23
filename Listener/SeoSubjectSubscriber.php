<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class SeoSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 * @see http://www.theodo.fr/blog/2013/11/dynamic-mapping-in-doctrine-and-symfony-how-to-extend-entities/
 */
class SeoSubjectSubscriber implements EventSubscriber
{
    const SEO_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Seo';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface';

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        $metadata->mapOneToOne(array(
            'fieldName'     => 'seo',
            'targetEntity'  => self::SEO_FQCN,
            'cascade'       => array('all'),
            'orphanRemoval' => true,
            'joinColumns' => array(
                array(
                    'name'                  => 'seo_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'RESTRICT',
                    'nullable'              => false,
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }
}