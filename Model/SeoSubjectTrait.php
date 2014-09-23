<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Seo;

/**
 * Trait SeoSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait SeoSubjectTrait
{
    /**
     * @var Seo
     */
    protected $seo;

    /**
     * Returns the seo.
     *
     * @return Seo
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Sets the seo.
     *
     * @param Seo $seo
     * @return SeoSubjectTrait|$this
     */
    public function setSeo(Seo $seo)
    {
        $this->seo = $seo;

        return $this;
    }
}