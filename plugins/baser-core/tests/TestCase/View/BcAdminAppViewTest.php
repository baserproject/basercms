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

namespace BaserCore\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use ReflectionClass;

/**
 * Class BcAdminAppViewTest
 * @property BcAdminAppView $BcAdminAppView
 */
class BcAdminAppViewTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Sites'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminAppView = new BcAdminAppView();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcAdminAppView->BcAdminForm);
        $this->assertNotEmpty($this->BcAdminAppView->BcBaser);
        $this->assertNotEmpty($this->BcAdminAppView->BcAuth);
        $this->assertNotEmpty($this->BcAdminAppView->BcAdmin);
        $this->assertNotEmpty($this->BcAdminAppView->BcListTable);
        $this->assertNotEmpty($this->BcAdminAppView->BcText);
        $this->assertNotEmpty($this->BcAdminAppView->BcTime);
        $this->assertNotEmpty($this->BcAdminAppView->BcHtml);
        $this->assertNotEmpty($this->BcAdminAppView->BcUpload);
        $this->assertNotEmpty($this->BcAdminAppView->BcSiteConfig);
        $this->assertEquals($this->BcAdminAppView->get('title'), 'Undefined');
    }

    /**
     * _paths
     *
     * 管理画面のファイルを別のテーマのテンプレートで上書きするためのパスを追加する
     * 別のテーマは、 setting.php で、 BcApp.customAdminTheme として定義する
     */
    public function test_paths()
    {
        $pluginName = 'BcTestTestTest';
        $pluginDashName = Inflector::dasherize($pluginName);
        $this->BcAdminAppView->setRequest($this->getRequest('/baser/admin/baser-core/users/index'));
        $pluginDir = ROOT . DS . 'plugins' . DS . $pluginName . DS;
        $templateDir = $pluginDir . 'templates' . DS . 'Admin' . DS . 'element' . DS;
        $folder = new Folder();
        $folder->create($templateDir);
        $file = new File($templateDir . 'sidebar.php');
        $file->create();
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->save(new \BaserCore\Model\Entity\Plugin([
            'name' => $pluginName,
            'status' => true
        ]));
        Configure::write('BcApp.customAdminTheme', $pluginName);
        $reflection = new ReflectionClass($this->BcAdminAppView);
        $method = $reflection->getMethod('_paths');
        $method->setAccessible(true);
        $result = $method->invoke($this->BcAdminAppView, 'BaserCore', false);
        $this->assertEquals(ROOT . '/plugins/' . $pluginDashName . '/templates/', $result[0]);
        $this->assertEquals(ROOT . '/plugins/' . $pluginName . '/templates/', $result[1]);

        $method = $reflection->getMethod('_getElementFileName');
        $method->setAccessible(true);
        $result = $method->invoke($this->BcAdminAppView, 'sidebar');
        $this->assertEquals(ROOT . '/plugins/' . $pluginName . '/templates/Admin/element/sidebar.php', $result);
        $folder->delete($pluginDir);
    }

}
