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

namespace BaserCore\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\BcAppController;
use Cake\Event\Event;

/**
 * BaserCore\Controller\BcAppController Test Case
 */
class BcAppControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAppController = new BcAppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcAppController);
    }

    /**
     * Test beforeFilter
     *
     * @return void
     */
    public function testBeforeFilter(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test saveDblog
     *
     * @return void
     * @dataProvider saveDblogDataProvider
     */
    public function testSaveDblog(string $message, int $userId = null): void
    {
        $request =$this->getRequest('/baser/admin/baser-core/users/');
        if (isset($userId)) $this->loginAdmin($request, $userId);

        $result = $this->execPrivateMethod($this->BcAppController, 'saveDblog', [$message]);

        $where = [
            'message' => $message,
            'controller' => 'Users',
            'action' => 'index'
        ];
        if (isset($userId)) {
            $where['user_id'] = $userId;
        } else {
            $where['user_id IS'] = null;
        }

        $dblogs = $this->getTableLocator()->get('Dblogs');
        $query = $dblogs->find()->where($where);
        $this->assertSame(1, $query->count());
    }

    public function saveDblogDataProvider(): array
    {
        return [
            ['dblogs testSaveDblog message guest', null],
            ['dblogs testSaveDblog message login', 1]
        ];
    }

}
