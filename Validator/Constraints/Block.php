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
    public $row_or_name_but_not_both = 'ekyna_cms.block.row_or_name_but_not_both';

    public $invalid_position         = 'ekyna_cms.block.invalid_position';


    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
