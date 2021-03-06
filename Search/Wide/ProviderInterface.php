<?php

namespace Ekyna\Bundle\CmsBundle\Search\Wide;

/**
 * Interface ProviderInterface
 * @package Ekyna\Bundle\CmsBundle\Search\Wide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ProviderInterface
{
    /**
     * Returns the results for the given expression.
     *
     * @param string  $expression
     * @param integer $limit
     *
     * @return array|Result[]
     */
    public function search($expression, $limit = 10);

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();
}
