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

use BaserCore\Database\Migration\BcSeed;
use BaserCore\TestSuite\BcTestCase;
use Cake\Datasource\ConnectionManager;
use Phinx\Db\Adapter\AdapterFactory;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Class BcSeedTest
 *
 */
class BcSeedTest extends BcTestCase
{

    /**
     * スキーム
     *
     * @var BcSeed
     */
    private $BcSeed;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcSeed = new BcSeed();
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
     * Test table
     */
    public function testTable()
    {
        //準備　Adapterをセットアップ
        $input = new ArgvInput(['cli.php', 'foo']);
        $this->BcSeed->setInput($input);
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
        $this->BcSeed->setAdapter($adapter);

        // prefixをセットアップ
        $config = ConnectionManager::getConfig('test');
        ConnectionManager::drop('test');
        $config['prefix'] = 'my_prefix_';
        ConnectionManager::setConfig('test', $config);

        // 実行
        $rs = $this->BcSeed->table('test');
        $this->assertEquals('my_prefix_test', $rs->getName());

        // 後処理
        $config['prefix'] = '';
        ConnectionManager::drop('test');
        ConnectionManager::setConfig('test', $config);
    }
}
