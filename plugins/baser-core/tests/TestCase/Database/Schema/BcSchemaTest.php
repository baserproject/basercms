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
use Cake\Filesystem\File;

/**
 * Class BcSchemaTest
 *
 * @package BaserCore\Test\TestCase\Database\Schema
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
     * @var File
     */
    private $schemaFile;

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
        $this->schemaFile = new File($schemaFilePath, true);
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
     * Test connection
     */
    public function test_connection()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test init
     */
    public function test_init()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
