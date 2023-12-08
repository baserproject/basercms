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
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcKeyValueBehaviorTest
 * @property SiteConfigsTable $SiteConfigs
 */
class BcKeyValueBehaviorTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(SiteConfigsScenario::class);
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

    /**
     * test getValue
     * @return void
     */
    public function test_getValue(){
        $data = ["themes"=>"夏"];
        $result = $this->SiteConfigs->saveKeyValue($data);
        $rs = $this->SiteConfigs->getValue('themes');
        $this->assertEquals('夏',$rs);
    }

    /**
     * test getValue false
     * @return void
     */
    public function test_getValue_false(){
        $result = $this->SiteConfigs->getValue("noValue");
        $this->assertFalse($result);
    }

    /**
     * test saveKeyValue
     * @return void
     */

    public function test_saveKeyValue(){
        $siteConfigs = ['level'=>'admin', 'position'=>'top', 'address'=>'東京'];
        $result = $this->SiteConfigs->saveKeyValue($siteConfigs);

        foreach ($siteConfigs as $key => $value){
            $rs = $this->SiteConfigs->getValue($key);
            $this->assertEquals($value,$rs);
        }

        $this->assertTrue($result);
    }

    /**
     * test saveValue
     * @return void
     */
    public function test_saveValue(){
        $expected = '町田';
        $key = 'address';
        $result = $this->SiteConfigs->saveValue($key, $expected);

        $rs = $this->SiteConfigs->getValue($key);
        $this->assertEquals($rs, $expected);
        $this->assertTrue($result);
    }

}
