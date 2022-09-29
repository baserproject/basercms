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

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Event\PagesControllerEventListener;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\EventManager;

/**
 * Class PagesControllerEventListenerTest
 */
class PagesControllerEventListenerTest extends BcTestCase
{

    /**
     * Fixtures
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Dblogs'
    ];

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
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $event = EventManager::instance();
        $event->on(new PagesControllerEventListener());
        $listeners = $event->listeners('Controller.BaserCore.Contents.afterMove');
        $listener = $listeners[0]['callable'][0];
        $this->assertEquals('BaserCore\Model\Table\PagesTable', get_class($listener->Pages));
    }

    /**
     * Contents After Move
     */
    public function testBaserCoreContentsAfterMove()
    {
        $token = $this->apiLoginAdmin();
        $data = [
            'origin' => [
                'id' => 5,
                'parentId' => 1
            ],
            'target' => [
                'id' => 5,
                'parentId' => 6,
                'siteId' => 1,
            ]
        ];
        $this->patch("/baser/api/baser-core/contents/move.json?token=" . $token['access_token'], $data);
        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertEquals(1, $searchIndexesTable->find()->where(['url' => '/service/about'])->count());
    }

    /**
     * Contents After Delete
     */
    public function testBaserCoreContentsAfterDelete()
    {
        $this->loginAdmin($this->getRequest());
        $this->enableCsrfToken();
        // 検索インデックスも連動して削除
        $this->post('/baser/admin/baser-core/contents/delete', ['Contents' => ['id' => 13]]);
        $this->assertResponseSuccess();
        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertEquals(0, $searchIndexesTable->find()->where(['url' => '/service/service3'])->count());
    }

    /**
     * Contents After Trash Return
     */
    public function testBaserCoreContentsAfterTrashReturn()
    {
        $this->loginAdmin($this->getRequest());
        $this->enableCsrfToken();
        // 検索インデックスを生成
        $this->post('/baser/admin/baser-core/contents/trash_return/7');
        $this->assertResponseSuccess();
        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertEquals(1, $searchIndexesTable->find()->where(['url' => '/sample'])->count());
    }

    /**
     * test baserCoreContentsAfterChangeStatus
     */
//    public function testBaserCoreContentsAfterChangeStatus()
//    {
//        $token = $this->apiLoginAdmin();
//        $data = [
//            'id' => 5,
//            'status' => 'unpublish'
//        ];
//        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
//        $this->assertTrue($searchIndexesTable->find()->where(['model' => 'Page', 'model_id' => 16])->first()->status);
//        $this->patch("/baser/api/baser-core/contents/change_status.json?token=" . $token['access_token'], $data);
//        $this->assertFalse($searchIndexesTable->find()->where(['model' => 'Page', 'model_id' => 16])->first()->status);
//    }

}
