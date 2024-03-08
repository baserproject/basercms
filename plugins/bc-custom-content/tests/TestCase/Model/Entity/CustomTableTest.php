<?php

namespace BcCustomContent\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomContent;
use BcCustomContent\Model\Entity\CustomTable;

class CustomTableTest extends BcTestCase
{
    /**
     * test isContentTable
     */
    public function testIsContentTable()
    {
        $table = new CustomTable();
        $table->type = '1';
        $table->custom_content = new CustomContent();
        $this->assertTrue($table->isContentTable());
    }
}