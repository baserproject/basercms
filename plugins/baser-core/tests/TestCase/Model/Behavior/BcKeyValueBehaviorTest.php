<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Test\TestCase\Model\Behavior;

use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Test\Factory\SiteConfigFactory;
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

    /**
     * test getKeyValue
     * @return void
     */
    public function test_getKeyValue(){
        $newData = SiteConfigFactory::make(["name"=>"type","value"=>"ハガキ"])->persist();
        $result = $this->SiteConfigs->getKeyValue();
        $this->assertEquals($newData["value"], $result[$newData["name"]]);
    }

}
