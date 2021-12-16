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

namespace BaserCore\Test\TestCase\Routing;

use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\Folder;
use Cake\Routing\Asset;

/**
 * Class AssetTest
 */
class AssetTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test webroot
     *
     * 適用テーマにアセットが存在する場合はそちらのパスを返し
     * 存在しない場合は、デフォルトテーマのアセットのパスを返す
     */
    public function testWebroot()
    {
        $result = Asset::webroot('css/style.css', ['theme' => 'BcSpaSample']);
        $this->assertEquals('/bc_front/css/style.css', $result);
        $cssDir = ROOT . DS . 'plugins' . DS . 'BcSpaSample' . DS . 'webroot' . DS . 'css' . DS;
        $folder = new Folder();
        $folder->create($cssDir);
        touch($cssDir . 'style.css');
        $result = Asset::webroot('css/style.css', ['theme' => 'BcSpaSample']);
        $this->assertEquals('/bc_spa_sample/css/style.css', $result);
        $folder->delete($cssDir);
    }

}
