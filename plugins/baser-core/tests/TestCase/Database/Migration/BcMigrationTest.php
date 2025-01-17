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
use Phinx\Db\Adapter\AdapterFactory;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

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
        $this->BcMigration = new BcMigration('localhost', 20200101010101);
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
        //準備　Adapterをセットアップ
        $input = new ArgvInput(['cli.php', 'foo']);
        $input->bind(new InputDefinition([new InputArgument('name')]));
        $this->BcMigration->setInput($input);
        $options = [
            'adapter' => 'mysql',
            'host' => 'bc-db',
            'user' => 'root',
            'pass' => 'root',
            'port' => '3306',
            'name' => 'test_basercms'
        ];
        $factory = AdapterFactory::instance();
        $adapter = $factory->getAdapter('mysql', $options);
        $this->BcMigration->setAdapter($adapter);

        // 実行
        $rs = $this->BcMigration->table('test');
        $this->assertEquals('test', $rs->getName());
    }
}
