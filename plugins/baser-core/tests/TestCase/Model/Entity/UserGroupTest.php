<?php

namespace BaserCore\Test\TestCase\Model\Entity;

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
        $this->loadFixtureScenario(UserGroupsScenario::class);

        //userGroup is not available
        $userGroup = UserGroupFactory::get(2);
        $this->assertTrue($userGroup->isAuthPrefixAvailabled('Admin'));

        //userGroup is available
        $userGroup = UserGroupFactory::get(1);
        $this->assertFalse($userGroup->isAuthPrefixAvailabled('Api'));
    }

    /**
     * test getAuthPrefixArray
     */
    public function test_getAuthPrefixArray()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);

        //the with auth_prefix empty
        $userGroup = UserGroupFactory::make(['auth_prefix' => ''])->getEntity();
        $rs = $userGroup->getAuthPrefixArray();
        $this->assertEquals([], $rs);

        //the with auth_prefix not empty
        $userGroup = UserGroupFactory::get(1);
        $rs = $userGroup->getAuthPrefixArray();
        $this->assertEquals([0 => 'Admin', 1 => 'Api/Admin'], $rs);
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

    /**
     * test getAuthPrefixSettings
     */
    public function testGetAuthPrefixSettings()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);

        //with prefixSetting empty
        $userGroup = UserGroupFactory::get(1);
        $rs = $userGroup->getAuthPrefixSettings('Admin');
        $this->assertEquals([], $rs);

        //with prefixSetting
        $userGroup = UserGroupFactory::get(2);
        $rs = $userGroup->getAuthPrefixSettings('Admin');
        $this->assertEquals(['type' => 2], $rs);
    }

    /**
     * test getAuthPrefixSetting
     */
    public function test_getAuthPrefixSetting()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);

        //the with auth_prefix_settings is empty
        $userGroup = UserGroupFactory::get(1);
        $rs = $userGroup->getAuthPrefixSetting('Admin', 'setting');
        $this->assertEmpty($rs);

        //the with auth_prefix_settings is not empty
        $userGroup = UserGroupFactory::get(2);
        $rs = $userGroup->getAuthPrefixSetting('Admin', 'type');
        $this->assertEquals('2', $rs);
    }
}
