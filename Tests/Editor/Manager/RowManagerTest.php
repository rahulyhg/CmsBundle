<?php

namespace Ekyna\Bundle\CmsBundle\Tests\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Manager\RowManager;
use Ekyna\Bundle\CmsBundle\Entity;
use phpunit\framework\TestCase;

/**
 * Class RowManagerTest
 * @package Ekyna\Bundle\CmsBundle\Tests\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowManagerTest extends TestCase
{
    public function test_it_compresses_a_too_large_layout()
    {
        $row = new Entity\Row();

        $block1 = new Entity\Block();
        $block1->setSize(6)->setColumn(0);
        $row->addBlock($block1);

        $block2 = new Entity\Block();
        $block2->setSize(4)->setColumn(0);
        $row->addBlock($block2);

        $block3 = new Entity\Block();
        $block3->setSize(4)->setColumn(0);
        $row->addBlock($block3);

        $mng = new RowManager();
        $mng->fixLayout($row);

        $size = 0;
        foreach ($row->getBlocks() as $block) {
            $size += $block->getSize();
        }

        $this->assertEquals(12, $size);
    }

    public function test_it_expands_a_too_small_layout()
    {
        $row = new Entity\Row();

        $block1 = new Entity\Block();
        $block1->setSize(4)->setColumn(0);
        $row->addBlock($block1);

        $block2 = new Entity\Block();
        $block2->setSize(2)->setColumn(0);
        $row->addBlock($block2);

        $block3 = new Entity\Block();
        $block3->setSize(2)->setColumn(0);
        $row->addBlock($block3);

        $mng = new RowManager();
        $mng->fixLayout($row);

        $size = 0;
        foreach ($row->getBlocks() as $block) {
            $size += $block->getSize();
        }

        $this->assertEquals(12, $size);
    }
}
