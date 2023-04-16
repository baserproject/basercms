<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Favorite Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Favorite Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcFavorite\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcFavorite\Model\Table\FavoritesTable;
use BaserCore\Utility\BcUtil;

/**
 * Class FavoriteTableTest
 */
class FavoritesTableTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BcFavorite.Favorites',
    ];

    /**
     * @var Favorites
     */
    public $Favorites;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Favorites = $this->getTableLocator()->get('BcFavorite.Favorites');
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        BcUtil::includePluginClass('BcFavorite');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Favorites);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals('favorites', $this->Favorites->getTable());
        $this->assertEquals('name', $this->Favorites->getDisplayField());
        $this->assertEquals('id', $this->Favorites->getPrimaryKey());
        $this->assertTrue($this->Favorites->hasBehavior('Timestamp'));
        $this->assertEquals('Users', $this->Favorites->getAssociation('Users')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($fields, $messages): void
    {
        $this->loginAdmin($this->getRequest('/baser/admin'), 2);
        $favorite = $this->Favorites->newEntity($fields);
        $this->assertSame($messages, $favorite->getErrors());
    }

    public function validationDefaultDataProvider()
    {
        return [
            [
                ['name' => ''],
                ['name' => ['_empty' => 'タイトルは必須です。']]
            ],
            [
                ['url' => 1],
                [
                    'name' => ['_required' => 'タイトルは必須です。'],
                    'url' => ['isPermitted' => 'このURLの登録は許可されていません。']
                ]
            ],
            [
                ['name' => 'hoge', 'url' => '/baser/admin/favorites/add'],
                []
            ],
        ];
    }

    /**
     * 偽装ログイン処理
     *
     * @param $id ユーザーIDとユーザーグループID
     * - 1 システム管理者
     * - 2 サイト運営
     */
    public function login($id)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Favorite->setSession(new SessionComponent(new ComponentCollection()));
        $prefix = BcUtil::authSessionKey('Admin');
        $this->Favorite->_Session->write('Auth.' . $prefix . '.id', $id);
        $this->Favorite->_Session->write('Auth.' . $prefix . '.user_group_id', $id);
    }

    /**
     * validate
     */
    public function test権限チェック異常系()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Favorite->create([
            'Favorite' => [
                'url' => '/admin/hoge',
            ]
        ]);

        $this->login(2);

        $this->assertFalse($this->Favorite->validates());
        $this->assertArrayHasKey('url', $this->Favorite->validationErrors);
        $this->assertEquals('このURLの登録は許可されていません。', current($this->Favorite->validationErrors['url']));
    }

    public function test権限チェックシステム管理者正常系()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Favorite->create([
            'Favorite' => [
                'url' => '/admin/hoge',
            ]
        ]);

        $this->login(1);

        $this->assertTrue($this->Favorite->validates());
    }

    public function test権限チェックサイト運営者正常系()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Favorite->create([
            'Favorite' => [
                'url' => '/hoge',
            ]
        ]);

        $this->login(2);

        $this->assertTrue($this->Favorite->validates());
    }

}
