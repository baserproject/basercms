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

namespace BcWidgetArea\Test\TestCase\Service\Admin;

use BaserCore\Test\Factory\PluginFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcWidgetArea\Model\Entity\WidgetArea;
use BcWidgetArea\Service\Admin\WidgetAreasAdminService;
use BcWidgetArea\Service\Admin\WidgetAreasAdminServiceInterface;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
 * WidgetAreasAdminServiceTest
 * @property WidgetAreasAdminService $WidgetAreasAdminService
 */
class WidgetAreasAdminServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->WidgetAreasAdminService = $this->getService(WidgetAreasAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {

    }

    /**
     * test getWidgetInfos
     */
    public function test_getWidgetInfos()
    {
        //EnablePluginsがないケース
        $result = $this->execPrivateMethod($this->WidgetAreasAdminService, 'getWidgetInfos');
        $this->assertIsArray($result);
        $this->assertEquals([
            'title' => __d('baser_core', 'コアウィジェット'),
            'plugin' => 'BaserCore',
            'paths' => ['/var/www/html/plugins/bc-admin-third/templates/Admin/element/widget']
        ], $result[0]);
        // テスト用のプラグインフォルダ作成
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $file = new File($pluginPath . DS . 'config.php');
        $file->write("<?php return ['type' => 'Plugin'];");
        $file->close();
        Configure::write('BcRequest.isInstalled', true);
        //
        $result = $this->execPrivateMethod($this->WidgetAreasAdminService, 'getWidgetInfos');
        $this->assertCount(2, $result);

    }


}
