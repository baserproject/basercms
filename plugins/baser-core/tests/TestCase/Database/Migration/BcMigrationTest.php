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

namespace BaserCore\Test\TestCase\Database\Migration;

use BaserCore\Database\Migration\BcMigration;
use BaserCore\TestSuite\BcTestCase;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Migrations\Db\Adapter\AdapterFactory;

/**
 * Class BcMigrationTest
 *
 */
class BcMigrationTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * スキーム
     *
     * @var BcMigration
     */
    protected $BcMigration;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcMigration = new BcMigration(20200101010101);
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
     * Test create
     *
     * プレフィックスを変更するテストを行う場合、
     * 他のテストで、トランザクション処理を行う際にデッドロックが発生してしまう様子。
     * 原因が不明のため、一旦、プレフィックスの変更テストは行わない
     */
    public function testTable()
    {
        //準備　Adapterをセットアップ（接続のプレフィックスを参照するため）
        // ドライバ名（mysql / postgres / sqlite など）をアダプタ種別として利用する
        $connection = ConnectionManager::get('test');
        $driverClass = get_class($connection->getDriver());
        $adapterType = strtolower(substr($driverClass, (int)strrpos($driverClass, '\\') + 1));
        $options = ['adapter' => $adapterType, 'connection' => $connection] + (array)$connection->config();
        $factory = AdapterFactory::instance();
        $adapter = $factory->getAdapter($adapterType, $options);
        $this->BcMigration->setAdapter($adapter);

        // 実行
        $rs = $this->BcMigration->table('test');
        $this->assertEquals('test', $rs->getName());
    }
}
