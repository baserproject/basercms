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

namespace BaserCore\Test\TestCase\Model;

use BaserCore\Model\AppTable;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\PermissionsTable;
use BaserCore\Model\Table\PermissionsTable as TablePermissionsTable;

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
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Permissions'
    ];
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('AppTable')? [] : ['className' => 'BaserCore\Model\AppTable'];
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
     * @param string $url
     * @param array $expect
     * @return void
     * @dataProvider getUrlPatternDataProvider
     */
    public function testGetUrlPattern($url, $expect)
    {
        $this->assertEquals($expect, $this->App->getUrlPattern($url));
    }

    public function getUrlPatternDataProvider()
    {
        return [
            ['/news', ['/news']],
            ['/news/', ['/news/', '/news/index']],
            ['/news/index', ['/news/index', '/news/']],
            ['/news/archives/1', ['/news/archives/1']],
            ['/news/archives/index', ['/news/archives/index', '/news/archives/']]
        ];
    }

    /**
     * Test getMax
     *
     * @return void
     */
    public function testGetMax()
    {
        $Permission = new TablePermissionsTable();
        $max = $Permission->getMax('no', []);
        $this->assertEquals(20, $max);
    }


}
