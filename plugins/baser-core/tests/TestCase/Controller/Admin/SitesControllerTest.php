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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Controller\Admin\SitesController;
use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class SitesControllerTest
 * @package BaserCore\Test\TestCase\Controller\Admin
 */
class SitesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites'
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin($this->getRequest());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * サイト一覧
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/sites/');
        $this->assertResponseOk();
        // イベントテスト
        $this->entryControllerEventToMock('Controller.Sites.searchIndex', function(Event $event) {
            $request = $event->getData('request');
            return $request->withQueryParams(['num' => 1]);
        });
        // アクション実行（requestの変化を判定するため $this->get() ではなくクラスを直接利用）
        $sitesController = new SitesController($this->getRequest());
        $sitesController->index($this->getService(SiteManageServiceInterface::class));
        $this->assertEquals(1, $sitesController->getRequest()->getQuery('num'));
    }

    /**
     * サイト追加
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * サイト情報編集
     */
    public function testEdit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 削除する
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
