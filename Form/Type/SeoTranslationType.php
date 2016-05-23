<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Ekyna\Bundle\CmsBundle\Entity\SeoTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SeoTranslationType
 * @package Ekyna\Bundle\CmsBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class, array(
                'label'        => 'ekyna_core.field.title',
                'required'     => true,
                'admin_helper' => 'CMS_SEO_TITLE',
            ))
            ->add('description', Type\TextareaType::class, array(
                'label'        => 'ekyna_core.field.description',
                'required'     => true,
                'admin_helper' => 'CMS_SEO_DESCRIPTION',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => SeoTranslation::class,
            ))
        ;
    }
}
