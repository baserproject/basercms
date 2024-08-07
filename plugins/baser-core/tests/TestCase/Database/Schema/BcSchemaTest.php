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

namespace BaserCore\Test\TestCase\Database\Schema;

use BaserCore\Database\Schema\BcSchema;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;

/**
 * Class BcSchemaTest
 *
 */
class BcSchemaTest extends BcTestCase
{

    /**
     * スキーム
     *
     * @var BcSchema
    */
    private $schema;

    /**
     * スキームファイル
     *
     * @var BcFile
     */
    private BcFile $schemaFile;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $path = TMP . 'schema' . DS;
        $schemaName = 'UserActionsSchema';
        $schemaFilePath = $path . $schemaName . '.php';
        $this->schemaFile = new BcFile($schemaFilePath);
        $this->schemaFile->create();
        $table = 'user_actions';
        // スキーマファイルを生成
        $this->schemaFile->write("<?php
use BaserCore\Database\Schema\BcSchema;
class UserActionsSchema extends BcSchema
{
    public \$table = '$table';
    public \$fields = [
        'id' => ['type' => 'integer', 'autoIncrement' => true],
        'contents' => ['type' => 'text', 'length' => 100],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ]
    ];
}");
        require_once $schemaFilePath;
        $this->schema = new $schemaName();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->schemaFile->delete();
    }

    /**
     * Test __construct
     */
    public function test_construct()
    {
        // fields プロパティに値が設定される事を確認
        $this->assertEquals('integer', $this->schema->fields['id']['type']);
        $this->assertEquals('text', $this->schema->fields['contents']['type']);
    }

    /**
     * test setTable
     */
    public function testSetTable()
    {
        $this->schema->setTable('table_test');
        $this->assertEquals('table_test', $this->schema->name());
    }

    /**
     * Test connection
     */
    public function test_connection()
    {
        $conn = $this->schema->connection();
        $this->assertEquals('test', $conn);
    }

    /**
     * Test init
     */
    public function test_init()
    {
        $this->schema->init();
        $this->assertEquals('integer', $this->schema->fields['id']['type']);
        $this->assertEquals('text', $this->schema->fields['contents']['type']);
        $this->assertEquals('primary', $this->schema->fields['_constraints']['primary']['type']);
        $this->assertEquals('InnoDB', $this->schema->fields['_options']['engine']);
    }

    /**
     * Test create
     */
    public function test_create_and_drop()
    {
        // createを実行
        $this->schema->create();
        // DBに存在する事を確認
        $tableList = $this->getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        $this->assertContains('user_actions', $tableList);
        // dropを実行
        $this->schema->drop();
        // DBに存在しない事を確認
        $tableList = $this->getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        $this->assertNotContains('user_actions', $tableList);
    }
}
