<?php

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\UserGroup;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class UserGroupTest extends BcTestCase
{
    use ScenarioAwareTrait;
    public function setUp(): void
    {
        parent::setUp();
        $this->UserGroup = $this->getTableLocator()->get('BaserCore.UserGroups');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIsAdmin()
    {
        $this->loadFixtureScenario(InitAppScenario::class);

        //userGroup is admin
        $userGroup = $this->UserGroup->get(1);
        $this->assertTrue($userGroup->isAdmin());

        //userGroup is not admin
        $userGroup = UserGroupFactory::make(['id' => 999])->getEntity();
        $this->assertFalse($userGroup->isAdmin());
    }

    public function testIsAuthPrefixAvailabled()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetAuthPrefixSettingsArray()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);
        
        //the with auth_prefix_settings is empty
        $userGroup = UserGroupFactory::get(1);
        $rs = $userGroup->getAuthPrefixSettingsArray();
        $this->assertEquals([], $rs);

        //the with auth_prefix_settings is not empty
        $userGroup = UserGroupFactory::get(2);
        $rs = $userGroup->getAuthPrefixSettingsArray();
        $this->assertEquals(['Admin' => ['type' => '2'], 'Api/Admin' => ['type' => '2']], $rs);
    }

    public function testGetAuthPrefixSettings()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function test_getAuthPrefixSetting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}