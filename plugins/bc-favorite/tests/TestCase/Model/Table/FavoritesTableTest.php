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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcFavorite\Test\Scenario\FavoritesScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class FavoriteTableTest
 */
class FavoritesTableTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(InitAppScenario::class);
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
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $favorite = $this->Favorites->newEntity($fields);
        $this->assertSame($messages, $favorite->getErrors());
    }

    public static function validationDefaultDataProvider()
    {
        return [
            [
                ['name' => ''],
                ['name' => ['_empty' => 'タイトルは必須です。']]
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
