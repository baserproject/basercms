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

namespace BaserCore\Database\Schema;

use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcSchema
 *
 * バックアップで利用するスキーマで継承して利用する
 * 継承するクラスファイルは、BcDatabaseService::writeSchema() で生成する。
 */
class BcSchema extends TableSchema
{

    /**
     * テーブル名
     * @var string
     */
    public $table;

    /**
     * フィールド
     * @var array
     */
    public $fields;

    /**
     * コネクション名
     * @var string
     */
    public $connection;

    /**
     * Construct
     * @param string $table
     * @param array $columns
     */
    public function __construct(string $table = '', array $columns = [])
    {
        if (!$table && $this->table) $table = $this->table;
        parent::__construct($table, $columns);
        $this->init();
    }

    /**
     * コネクション名を取得
     * @return string
     */
    public function connection(): string
    {
        return $this->connection;
    }

    /**
     * 初期化処理
     */
    public function init()
    {
        $this->connection = TableRegistry::getTableLocator()->get('BaserCore.App')->getConnection()->configName();
        if (!empty($this->fields)) {
            $this->_schemaFromFields();
        }
    }

    /**
     * fields プロパティからスキーマを構築
     * Cake\TestSuite\Fixture\TestFixture::_schemaFromFields() を移植
     * @return void
     * @unitTest 移植のためテストをスキップ
     * @noTodo
     * @checked
     */
    protected function _schemaFromFields(): void
    {
        $connection = ConnectionManager::get($this->connection());
        foreach($this->fields as $field => $data) {
            if ($field === '_constraints' || $field === '_indexes' || $field === '_options') {
                continue;
            }
            $this->addColumn($field, $data);
        }
        if (!empty($this->fields['_constraints'])) {
            foreach($this->fields['_constraints'] as $name => $data) {
                if (!$connection->supportsDynamicConstraints() || $data['type'] !== TableSchema::CONSTRAINT_FOREIGN) {
                    $this->addConstraint($name, $data);
                } else {
                    $this->_constraints[$name] = $data;
                }
            }
        }
        if (!empty($this->fields['_indexes'])) {
            foreach($this->fields['_indexes'] as $name => $data) {
                $this->addIndex($name, $data);
            }
        }
        if (!empty($this->fields['_options'])) {
            $this->setOptions($this->fields['_options']);
        }
    }

    /**
     * テーブルを作成する
     * @return void
     */
    public function create()
    {
        $connection = ConnectionManager::get($this->connection());
        $queries = $this->createSql($connection);
        foreach($queries as $query) {
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $stmt->closeCursor();
        }
    }

    /**
     * テーブルを削除する
     * @return void
     */
    public function drop()
    {
        $connection = ConnectionManager::get($this->connection());
        $queries = $this->dropSql($connection);
        foreach($queries as $query) {
            $connection->execute($query)->closeCursor();
        }
    }

}
