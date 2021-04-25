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
use Cake\TestSuite\TestCase;

/**
 * BaserCore\Controller\UserGroupsController Test Case
 */
class UserGroupsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Controller/UserGroupsController/UserGroupsPagination',
    ];

    public $autoFixtures = false;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures('UsersUserGroups', 'Users');
        if ($this->getName() == 'testIndex_pagination') {
            $this->loadFixtures('Controller\UserGroupsController\UserGroupsPagination');
        } else {
            $this->loadFixtures('UserGroups');
        }

        $config = $this->getTableLocator()->exists('UserGroups')? [] : ['className' => 'BaserCore\Model\Table\UserGroupsTable'];
        $UserGroups = $this->getTableLocator()->get('UserGroups', $config);
        $this->session(['AuthAdmin' => $UserGroups->get(1)]);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/admin/user_groups/');
        $this->assertResponseOk();
    }

    /**
     * Test index pagination
     *
     * @return void
     */
    public function testIndex_pagination()
    {
        $this->get('/baser/admin/user_groups/?num=1&page=21');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->enableSecurityToken();

        $this->get('/baser/admin/user_groups/add');
        $this->assertResponseOk();

        $this->post('/baser/admin/user_groups/add', [
            'name' => 'addtestgroup',
            'title' => 'テストグループ',
            'use_move_contents' => '1',
        ]);
        $this->assertFlashMessage('新規ユーザーグループ「addtestgroup」を追加しました。');

        $userGroups = $this->getTableLocator()->get('UserGroups');
        $userGroup = $userGroups
            ->find()
            ->where([
                'name' => 'addtestgroup',
            ])
            ->first();
        $this->assertEquals($userGroup['name'], 'addtestgroup');
        $this->assertEquals($userGroup['title'], 'テストグループ');
        $this->assertEquals($userGroup['use_move_contents'], 1);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test copy method
     *
     * @return void
     */
    public function testCopy()
    {
        $this->enableSecurityToken();
        $this->post('/baser/admin/user_groups/copy/1');
        $this->assertResponseSuccess();
        $userGroups = $this->getTableLocator()->get('UserGroups');
        $originalUserGroup = $userGroups->get(1);
        $query = $userGroups->find()->where(['name' => $originalUserGroup->name . '_copy']);
        $this->assertEquals(1, $query->count());
    }

	/**
	 * beforeFilter
	 */
	public function testBeforeFilter()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 削除処理 (ajax)
	 */
	public function testAjax_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] データコピー（AJAX）
	 */
	public function testAjax_copy()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ユーザーグループのよく使う項目の初期値を登録する
	 */
	public function testSet_default_favorites()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
}
