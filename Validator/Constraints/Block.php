<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Block
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Block extends Constraint
{
    public $rowOrNameButNotBoth = 'ekyna_cms.block.row_or_name_but_not_both';

    public $tooSmallBlock       = 'ekyna_cms.block.too_small_block';

    /**
     * {@inheritdoc}
     */
    /*public function validatedBy()
    {
        return BlockValidator::class;
    }*/

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}