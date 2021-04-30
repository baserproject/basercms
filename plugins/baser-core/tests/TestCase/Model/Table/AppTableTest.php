<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\AppTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class AppTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 */
class AppTableTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var AppTable
     */
    public $App;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('AppTable')? [] : ['className' => 'BaserCore\Model\Table\AppTable'];
        $this->App = $this->getTableLocator()->get('BaserCore.AppTable', $config);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->App);
        parent::tearDown();
    }

    /**
     * Test deleteModelCache
     *
     * @return void
     */
    public function testDeleteModelCache()
    {
        $path = CACHE . 'models' . DS . 'dummy';

        if (touch($path)) {
            $this->App->deleteModelCache();
            $result = !file_exists($path);
            $this->assertTrue($result, 'Modelキャッシュを削除できません');
        } else {
            $this->markTestIncomplete('ダミーのキャッシュファイルの作成に失敗しました。');
        }
    }

    /**
     * Test getUrlPattern
     *
     * @return void
     */
    public function testGetUrlPattern()
    {
        $expectedPaths1 = [
            '/test'
        ];
        $this->assertEquals($expectedPaths1, $this->App->getUrlPattern('test'));
        $this->assertEquals($expectedPaths1, $this->App->getUrlPattern('/test'));

        $expectedPaths2 = [
            '/test/',
            '/test/index'
        ];
        $this->assertEquals($expectedPaths2, $this->App->getUrlPattern('test/'));
        $this->assertEquals($expectedPaths2, $this->App->getUrlPattern('/test/'));

        $expectedPaths3 = [
            '/test/index',
            '/test/'
        ];
        $this->assertEquals($expectedPaths3, $this->App->getUrlPattern('test/index'));
        $this->assertEquals($expectedPaths3, $this->App->getUrlPattern('/test/index'));

        $expectedPaths4 = [
            '/test.html',
            '/test'
        ];
        $this->assertEquals($expectedPaths4, $this->App->getUrlPattern('test.html'));
        $this->assertEquals($expectedPaths4, $this->App->getUrlPattern('/test.html'));

        $expectedPaths5 = [
            '/test/index.html',
            '/test/index',
            '/test/'
        ];
        $this->assertEquals($expectedPaths5, $this->App->getUrlPattern('test/index.html'));
        $this->assertEquals($expectedPaths5, $this->App->getUrlPattern('/test/index.html'));
    }

    /**
     * Test convertSize
     *
     * @return void
     */
    public function testConvertSize()
    {
        $this->assertEquals(1, $this->App->convertSize('1B'));
        $this->assertEquals(1024, $this->App->convertSize('1K'));
        $this->assertEquals(1048576, $this->App->convertSize('1M'));
        $this->assertEquals(1073741824, $this->App->convertSize('1G'));
        $this->assertEquals(1099511627776, $this->App->convertSize('1T'));
        $this->assertEquals(1099511627776, $this->App->convertSize('1T', 'B'));
        $this->assertEquals(1073741824, $this->App->convertSize('1T', 'K'));
        $this->assertEquals(1073741824, $this->App->convertSize('1', 'K', 'T'));
        $this->assertEquals(0, $this->App->convertSize(null));
    }

}
