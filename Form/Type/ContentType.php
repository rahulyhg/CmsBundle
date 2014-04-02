<?php

namespace Ekyna\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ContentType
 */
class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('blocks', 'ekyna_block_collection', array(
                'label' => false,
                'types' => array(
                    'ekyna_text_block', // The first defined Type becomes the default
                    'ekyna_tinymce_block',
                    'ekyna_image_block',
                ),
                'allow_add' => true,
                'allow_delete' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'  => 'Ekyna\Bundle\CmsBundle\Entity\Content'));
    }

    public function getName()
    {
    	return 'ekyna_content';
    }
}