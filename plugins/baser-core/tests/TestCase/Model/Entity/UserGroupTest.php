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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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