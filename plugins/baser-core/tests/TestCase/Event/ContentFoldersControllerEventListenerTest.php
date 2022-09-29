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

use BaserCore\Event\ContentFoldersControllerEventListener;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\EventManager;

/**
 * Class ContentFoldersControllerEventListenerTest
 */
class ContentFoldersControllerEventListenerTest extends BcTestCase
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
        $event->on(new ContentFoldersControllerEventListener());
        $listeners = $event->listeners('Controller.BaserCore.Contents.afterMove');
        $listener = $listeners[0]['callable'][0];
        $this->assertEquals('BaserCore\Model\Table\PagesTable', get_class($listener->Pages));
        $this->assertEquals('BaserCore\Model\Table\ContentFoldersTable', get_class($listener->ContentFolders));
    }

    /**
     * Contents after move
     */
    public function testBaserCoreContentsAfterMove()
    {
        $token = $this->apiLoginAdmin();
        $data = [
            'origin' => [
                'id' => 6,
                'parentId' => 1
            ],
            'target' => [
                'id' => 6,
                'parentId' => 21,
                'siteId' => 1,
            ]
        ];
        $this->patch("/baser/api/baser-core/contents/move.json?token=" . $token['access_token'], $data);
        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertEquals(1, $searchIndexesTable->find()->where(['url' => '/testEdit/service/service1'])->count());
        $this->assertEquals(1, $searchIndexesTable->find()->where(['url' => '/testEdit/service/service2'])->count());
        $this->assertEquals(1, $searchIndexesTable->find()->where(['url' => '/testEdit/service/service3'])->count());
    }

    /**
     * Contents Before Delete
     */
    public function testBaserCoreContentsBeforeDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loginAdmin($this->getRequest('/'));
        $this->post('/baser/admin/baser-core/contents/delete', ['Contents' => ['id' => 6]]);
        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertEquals(0, $searchIndexesTable->find()->where(['url' => '/service/service1'])->count());
        $this->assertEquals(0, $searchIndexesTable->find()->where(['url' => '/service/service2'])->count());
        $this->assertEquals(0, $searchIndexesTable->find()->where(['url' => '/service/service3'])->count());
    }

    /**
     * Contents After Change Status
     */
    public function testBaserCoreContentsAfterChangeStatus()
    {
        $token = $this->apiLoginAdmin();
        $url = '/about';
        $contentsTable = $this->getTableLocator()->get('BaserCore.Contents');
        $content = $contentsTable->find()->where(['url' => $url])->first();
        $data = [
            'id' => $content->id,
            'status' => 'unpublish'
        ];
        $this->patch("/baser/api/baser-core/contents/change_status.json?token=" . $token['access_token'], $data);
        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertFalse($searchIndexesTable->find()->where(['url' => $url])->first()->status);
    }


}
