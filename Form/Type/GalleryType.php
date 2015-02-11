<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['name_field']) {
            $builder
                ->add('name', 'text', array(
                    'label' => 'ekyna_core.field.name',
                ))
            ;
        }

        $builder
            ->add('images', 'collection', array(
                'label'        => 'ekyna_core.field.images',
                'type'         => 'ekyna_cms_gallery_image',
                'allow_add'    => $options['allow_add'],
                'allow_delete' => $options['allow_delete'],
                'allow_sort'   => $options['allow_sort'],
                'by_reference' => false,
                'options'      => array(
                    'label'        => false,
                    'required'     => false,
                    'image_path'   => $options['image_path'],
                    'thumb_col'    => $options['thumb_col'],
                    'rename_field' => $options['rename_field'],
                    'alt_field'    => $options['alt_field'],
                    'attr'         => array(
                        'widget_col' => 12
                    ),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'    => 'Ekyna\Bundle\CmsBundle\Entity\Gallery',
                'allow_add'     => true,
                'allow_delete'  => true,
                'allow_sort'    => true,
                'name_field'    => false,
                'image_path'    => 'path',
                'thumb_col'     => 3,
                'rename_field'  => true,
                'alt_field'     => true,
            ))
            ->setRequired(array('data_class'))
            ->setOptional(array('image_path'))
            ->setAllowedTypes(array(
                'allow_add'     => 'bool',
                'allow_delete'  => 'bool',
                'allow_sort'    => 'bool',
                'name_field'    => 'bool',
                'image_path'    => array('null', 'string'),
                'thumb_col'     => 'int',
                'rename_field'  => 'bool',
                'alt_field'     => 'bool',
            ))
            ->setNormalizers(array(
                'thumb_col' => function($options, $value) {
                    if (0 == strlen($options['image_path'])) {
                        return 0;
                    }
                    if ($value > 4) {
                        return 4;
                    }
                    return $value;
                },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_gallery';
    }
}
