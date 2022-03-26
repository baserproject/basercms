<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
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
        'plugin.BaserCore.SearchIndexes',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Pages'
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
        $searchIndexesTable = $this->getTableLocator()->get('BaserCore.SearchIndexes');
        $this->assertEquals(1, $searchIndexesTable->find()->where(['url' => '/service/about'])->count());
    }

    /**
     * Contents Before Delete
     */
    public function testContentsBeforeDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Contents After Trash Return
     */
    public function testContentsAfterTrashReturn()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


}
