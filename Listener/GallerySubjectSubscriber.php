<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class ImageSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GallerySubjectSubscriber implements EventSubscriber
{
    const GALLERY_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Gallery';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\GallerySubjectInterface';

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        // Prevent doctrine:generate:entities bug
        if (!class_exists($metadata->getName())) {
            return;
        }

        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        $metadata->mapManyToOne(array(
            'fieldName'     => 'gallery',
            'targetEntity'  => self::GALLERY_FQCN,
            'cascade'       => array('persist', 'refresh', 'detach', 'merge'),
            //'orphanRemoval' => true,
            'joinColumns' => array(
                array(
                    'name'                  => 'gallery_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'RESTRICT',
                    'nullable'              => true,
                ),
            ),
            // TODO fetch => ?
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