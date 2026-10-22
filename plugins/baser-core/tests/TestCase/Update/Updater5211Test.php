<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.11
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Update;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUpdateLog;
use Cake\Core\Plugin;

/**
 * 5.2.11 アップデーターのテスト
 */
class Updater5211Test extends BcTestCase
{

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        // アップデーターが記録したログを後続のテストに残さない
        BcUpdateLog::clear();
        parent::tearDown();
    }

    /**
     * キャッシュクリアのアクセスルールの method が GET から POST に書き換わること
     * それ以外のルールは変更しないこと
     */
    public function test_updateClearCachePermissionMethod()
    {
        PermissionFactory::make(['id' => 1, 'user_group_id' => 2, 'url' => '/baser/admin/baser-core/utilities/clear_cache', 'method' => 'GET', 'auth' => true])->persist();
        PermissionFactory::make(['id' => 2, 'user_group_id' => 2, 'url' => '/baser/api/admin/baser-core/utilities/clear_cache.json', 'method' => 'GET', 'auth' => true])->persist();
        PermissionFactory::make(['id' => 3, 'user_group_id' => 2, 'url' => '/baser/admin/baser-core/utilities/credit', 'method' => 'GET', 'auth' => true])->persist();
        PermissionFactory::make(['id' => 4, 'user_group_id' => 3, 'url' => '/baser/admin/baser-core/utilities/clear_cache', 'method' => '*', 'auth' => true])->persist();

        include Plugin::path('BaserCore') . 'config' . DS . 'update' . DS . '5.2.11' . DS . 'updater.php';

        $permissions = $this->getTableLocator()->get('BaserCore.Permissions');
        $this->assertEquals('POST', $permissions->get(1)->method);
        $this->assertEquals('POST', $permissions->get(2)->method);
        $this->assertEquals('GET', $permissions->get(3)->method);
        $this->assertEquals('*', $permissions->get(4)->method);

        // 再実行しても変わらない
        include Plugin::path('BaserCore') . 'config' . DS . 'update' . DS . '5.2.11' . DS . 'updater.php';
        $this->assertEquals('POST', $permissions->get(1)->method);
    }

}
