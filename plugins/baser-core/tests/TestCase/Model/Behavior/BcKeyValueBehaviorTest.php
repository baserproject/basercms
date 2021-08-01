<?php

namespace BaserCore\Test\TestCase\Model\Behavior;

use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcKeyValueBehaviorTest
 * @package BaserCore\Test\TestCase\Model\Behavior
 * @property SiteConfigsTable $SiteConfigs
 */
class BcKeyValueBehaviorTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * @var SiteConfigsTable
     */
    public $SiteConfigs;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteConfigs = $this->getTableLocator()->get('BaserCore.SiteConfigs');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SiteConfigs);
        parent::tearDown();
    }

    /**
     * test getKeyValue
     */
    public function testGetKeyValue()
    {
        $result = $this->SiteConfigs->getKeyValue();
        $this->assertArrayHasKey('version', $result);
    }

    /**
     * test saveKeyValue And getValue
     */
    public function testSaveKeyValueAndGetValue()
    {
        $expected = '5.0.0';
        $this->SiteConfigs->saveKeyValue(['version' => $expected]);
        $this->assertEquals($expected, $this->SiteConfigs->getValue('version'));
    }

    /**
     * test saveValue And getValue
     */
    public function testSaveValueAndGetValue()
    {
        $expected = '5.0.0';
        $this->SiteConfigs->saveValue('version', $expected);
        $this->assertEquals($expected, $this->SiteConfigs->getValue('version'));
    }
}
