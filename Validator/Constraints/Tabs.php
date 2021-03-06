<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Tabs
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Tabs extends Constraint
{
    public $media_must_be_null = 'ekyna_cms.block.tab.media_must_be_null';
    public $locales_miss_match = 'ekyna_cms.block.tab.locales_miss_match';


    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
