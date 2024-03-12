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
        //check if the table is content table
        $table = new CustomTable();
        $table->type = '1';
        $table->custom_content = new CustomContent();
        $this->assertTrue($table->isContentTable());
        //check if the table is not content table
        $table = new CustomTable();
        $table->type = '0';
        $table->custom_content = new CustomContent();
        $this->assertFalse($table->isContentTable());
    }
}