<?php

namespace BcCustomContent\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomLinkTest extends BcTestCase
{
    use ScenarioAwareTrait;


    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test isGroupSelectable
     */
    public function test_isGroupSelectable()
    {
        //check if custom_field is not group
        $customLink = new CustomLink([
            'id' => 1,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
        ]);
        $result = $this->execPrivateMethod($customLink, 'isGroupSelectable', []);
        $this->assertTrue($result);
        //check if custom_field is group
        $customField = new CustomField([
            'id' => 1,
            'name' => 'test',
            'type' => 'group'
        ]);
        $customLink = new CustomLink([
            'id' => 1,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'custom_field' => $customField
        ]);
        $result = $this->execPrivateMethod($customLink, 'isGroupSelectable', []);
        $this->assertFalse($result);
    }
}