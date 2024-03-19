<?php

namespace BcCustomContent\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomLinkTest extends BcTestCase
{
    use ScenarioAwareTrait;

    protected $CustomLink;

    public function setUp(): void
    {
        parent::setUp();
        $this->CustomLink = $this->getTableLocator()->get('BcCustomContent.CustomLinks');
    }

    public function tearDown(): void
    {
        unset($this->CustomLink);
        parent::tearDown();
    }

    /**
     * test isGroupSelectable
     */
    public function test_isGroupSelectable()
    {
        //check is not custom_field
        $customLink = CustomLinkFactory::make([
            'id' => 1,
        ])->getEntity();
        $this->assertTrue($customLink->isGroupSelectable());
        //check if custom_field is not group
        $customLink = CustomLinkFactory::make([
            'id' => 1,
            'custom_field' => [
                'type' => 'text'
            ]
        ])->getEntity();
        $this->assertTrue($customLink->isGroupSelectable());
        //check if custom_field is group
        $customLink = CustomLinkFactory::make([
            'id' => 1,
            'custom_field' => [
                'type' => 'group'
            ]
        ])->getEntity();
        $this->assertFalse($customLink->isGroupSelectable());
    }
}