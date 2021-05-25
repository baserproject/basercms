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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Model\Table\LoginStoresTable Test Case
 *
 * @property LoginStoresTable $Users
 */
class LoginStoresTableTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * @var LoginStoresTable
     */
    public $LoginStores;

    /**
     * @var Users
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.LoginStores',
        // 'plugin.BaserCore.Users',
        // 'plugin.BaserCore.UsersUserGroups',
        // 'plugin.BaserCore.UserGroups',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('LoginStores')?
            [] : ['className' => 'BaserCore\Model\Table\LoginStoresTable'];
        $this->LoginStores = $this->getTableLocator()->get('LoginStores', $config);

    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->LoginStores);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->LoginStores->hasBehavior('Timestamp'));
    }

    /**
     * Test buildRules
     */
    public function testBuildRules()
    {
        $key = "samekey";
        $loginStore = $this->LoginStores->newEntity([
            'user_id' => '1',
            'prefix' => 'Admin',
            'store_key' => $key,
        ]);
        $this->LoginStores->save($loginStore);

        $loginStore = $this->LoginStores->newEntity([
            'user_id' => '2',
            'prefix' => 'Admin',
            'store_key' => $key,
        ]);
        $this->LoginStores->save($loginStore);

        $this->assertSame([
            'store_key' => ['_isUnique' => 'キーが重複しています。'],
        ], $loginStore->getErrors());

    }

    /**
     * Test addKey
     */
    public function testAddKey()
    {
        $beforeCount = $this->LoginStores->find('all')->count();
        $loginStore = $this->LoginStores->addKey('Admin', 1);
        $afterCount = $this->LoginStores->find('all')->count();
        $this->assertSame($beforeCount + 1, $afterCount);

        // 同一prexi user_idの場合リフレッシュとなり追加されない
        $loginStore2 = $this->LoginStores->addKey('Admin', 1);
        $loginStore2 = $this->LoginStores->addKey('Admin', 1);
        $this->assertSame($beforeCount + 1, $afterCount);

        // キーは変更されている
        $this->assertNotSame($loginStore->store_key, $loginStore2->store_key);

        // キー長を変更し衝突データを発生させた場合Exeptionが発生
        $reflection = new \ReflectionClass($this->LoginStores);
        $property = $reflection->getProperty('keyLength');
        $property->setAccessible(true);
        $property->setValue($this->LoginStores, 1);
        $errorMesage = "";
        try {
            for($i = 0; $i < 20; $i++) {
                $this->LoginStores->addKey('Admin', $i);
            }
        } catch (\Exception $e) {
            $errorMesage = $e->getMessage();
        }
        $this->assertSame($errorMesage, "不明なエラー");

    }

    /**
     * Test removeKey
     */
    public function testRemoveKey()
    {
        $loginStore = $this->LoginStores->addKey('Admin', 1);
        $createdKey = $loginStore->store_key;

        $this->LoginStores->removeKey('Admin', 1);
        $deletedStore = $this->LoginStores->find('all')
            ->where(['store_key' => $createdKey])
            ->first();

        $this->assertNull($deletedStore);
    }

    /**
     * Test getEnableLoginStore
     */
    public function testGetEnableLoginStore()
    {
        // 古いデータの生成
        $loginStore = $this->LoginStores->newEntity([
            'user_id' => '1',
            'prefix' => 'Admin',
            'store_key' => "oldkey",
            'created' => date('Y-m-d h:i:s', strtotime("-366 day")),
            'modified' => date('Y-m-d h:i:s', strtotime("-366 day")),
        ]);
        $this->LoginStores->save($loginStore);

        // 有効データの取得
        $loginStore = $this->LoginStores->addKey('Admin', 1);
        $enableLoginStore = $this->LoginStores->getEnableLoginStore($loginStore->store_key);
        $this->assertNotNull($enableLoginStore);

        // 古いデータの削除
        $disableLoginStore = $this->LoginStores->find('all')
            ->where(['created <=' => date('Y-m-d h:i:s', strtotime("-365 day"))])
            ->first();
        $this->assertNull($disableLoginStore);
    }

    /**
     * Test refresh
     */
    public function testRefresh()
    {
        $loginStore1 = $this->LoginStores->addKey('Admin', 1);
        $loginStore2 = $this->LoginStores->addKey('Admin', 1);
        $loginStore3 = $this->LoginStores->refresh('Admin', 1);
        $rowCount = $this->LoginStores->find('all')
            ->where(['prefix' => 'Admin', 'user_id' => 1])
            ->count();
        $this->assertSame($rowCount, 1);
        $this->assertFalse(in_array(
            $loginStore3->store_key,
            [
                $loginStore1->store_key,
                $loginStore2->store_key
            ]
        ));
    }

}
