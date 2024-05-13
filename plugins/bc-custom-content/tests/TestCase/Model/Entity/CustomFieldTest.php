<?php

namespace BcCustomContent\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomField;

class CustomFieldTest extends BcTestCase
{
    public $CustomField;

    public function setUp(): void
    {
        parent::setUp();
        $this->CustomField = $this->getTableLocator()->get('BcCustomContent.CustomFields');
    }

    public function tearDown(): void
    {
        unset($this->CustomField);
        parent::tearDown();
    }

    /**
     * test getTypeTitle
     */
    public function test_getTypeTitle()
    {
        // type group
        $customField = new CustomField([
            'id' => 1,
            'custom_table_id' => 1,
            'type' => 'group',
        ]);
        $this->assertEquals('グループ', $customField->getTypeTitle());
        // type text
        $customField = new CustomField([
            'id' => 1,
            'custom_table_id' => 1,
            'type' => 'text',
        ]);
        $this->assertEmpty($customField->getTypeTitle());
    }

}