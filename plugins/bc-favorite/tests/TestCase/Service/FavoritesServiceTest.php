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

namespace BcFavorite\Test\TestCase\Service;

use BaserCore\Utility\BcUtil;
use BaserCore\TestSuite\BcTestCase;
use BcFavorite\Service\FavoritesService;

/**
 * Class FavoritesServiceTest
 * @property FavoritesService $FavoritesService
 */
class FavoritesServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BcFavorite.Favorites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * FavoritesService
     *
     * @var FavoritesService
     */
    public $FavoritesService;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->FavoritesService = new FavoritesService();
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
        unset($this->FavoritesService);
        parent::tearDown();
    }

    /**
     * testGet
     *
     * @return void
     */
    public function testGet(): void
    {
        $this->expectException("Cake\Datasource\Exception\RecordNotFoundException");
        $result = $this->FavoritesService->get(0);
        $this->assertEmpty($result);

        $result = $this->FavoritesService->get(1);
        $this->assertEquals("固定ページ管理", $result->name);
    }

    /**
     * testGetIndex
     *
     * @return void
     */
    public function testGetIndex(): void
    {
        $result = $this->FavoritesService->getIndex(['num' => 2]);
        $this->assertEquals(2, $result->all()->count());
    }

    /**
     * testGetNew
     *
     * @return void
     */
    public function testGetNew(): void
    {
        $result = $this->FavoritesService->getNew();
        $this->assertInstanceOf("Cake\Datasource\EntityInterface", $result);
    }

    /**
     * testCreate
     *
     * @return void
     */
    public function testCreate(): void
    {
        $this->loginAdmin($this->getRequest());
        $result = $this->FavoritesService->create([
            'user_id' => '1',
            'name' => 'テスト新規登録',
            'url' => '/baser/admin/test/index/1',
        ]);
        $expected = $this->FavoritesService->Favorites->find('all')->last();
        $this->assertEquals($expected->name, $result->name);
    }

    /**
     * test update
     */
    public function testUpdate(): void
    {
        $favorite = $this->FavoritesService->get(1);
        $this->FavoritesService->update($favorite, [
            'name' => 'ucmitz',
        ]);
        $favorite = $this->FavoritesService->get(1);
        $this->assertEquals('ucmitz', $favorite->name);
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->FavoritesService->delete(2);
        $users = $this->FavoritesService->getIndex([]);
        $this->assertEquals(5, $users->all()->count());
    }

}
