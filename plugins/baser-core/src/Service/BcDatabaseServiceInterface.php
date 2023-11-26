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

namespace BaserCore\Service;

use Cake\ORM\Table;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcDatabaseServiceInterface
 */
interface BcDatabaseServiceInterface
{

    /**
     * 初期データを読み込む
     *
     * @param string $theme
     * @param string $pattern
     * @param string $dbConfigKeyName
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadDefaultDataPattern(string $theme, string $pattern, string $dbConfigKeyName = 'default'): bool;

    /**
     * CSVファイルをDBに読み込む
     *
     * @param array $options
     *  - `path`: 読み込み元のCSVのパス
     *  - `encoding: CSVファイルのエンコード
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadCsv($options): bool;

    /**
     * プラグインも含めて全てのテーブルをリセットする
     *
     * プラグインは有効となっているもののみ
     * 現在のテーマでないテーマの梱包プラグインを検出できない為
     *
     * @param array $dbConfig
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function resetAllTables($excludes = []): bool;

    /**
     * 複数のテーブルをリセットする
     *
     * @param string $plugin
     * @param array $excludes
     * @param string $dbConfigKeyName
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function resetTables($plugin = 'BaserCore', $excludes = [], string $dbConfigKeyName = 'default'): bool;

    /**
     * テーブルのデータをリセットする
     *
     * @param string $table
     * @param string $dbConfigKeyName
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function truncate(string $table, string $dbConfigKeyName = 'default'): bool;

    /**
     * システムデータを初期化する
     *
     * @param array $options
     * @noTodo
     * @checked
     * @unitTest
     */
    public function initSystemData($options = []): bool;

    /**
     * データベースシーケンスをアップデートする
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function updateSequence();

    /**
     * CSVよりデータを配列として読み込む
     *
     * @param string $path
     * @return false|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadCsvToArray($path, $encoding = 'auto');

    /**
     * DBのデータをCSVファイルとして書きだす
     *
     * @param array $options
     * -`path`: CSVの出力先となるパス
     * -`encoding`: 出力エンコーディング
     * -`table`: テーブル名
     * -`init`: id、created、modified を初期化する（初期値：false）
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function writeCsv($table, $options): bool;

    /**
     * Gets the database encoding
     *
     * @return string The database encoding
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEncoding(): string;

    /**
     * アプリケーションに関連するテーブルリストを取得する
     *
     * @param string $plugin
     * @param string $dbConfigKeyName
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAppTableList($plugin = '', string $dbConfigKeyName = 'default'): array;

    /**
     * アプリケーションに関連するテーブルリストのキャッシュをクリアする
     *
     * @checked
     * @noTodo
     * @unitTest
     * @param string $dbConfigKeyName
     */
    public function clearAppTableList(string $dbConfigKeyName = 'default'): void;

    /**
     * モデル名を指定してスキーマファイルを生成する
     *
     * @param Table テーブルオブジェクト名
     * @param array $options
     *  - `path` : スキーマファイルの生成場所
     * @return string|false スキーマファイルの内容
     * @unitTest
     * @noTodo
     * @unitTest
     */
    public function writeSchema($table, $options);

    /**
     * スキーマを読み込む
     *
     * @param $options
     * @return bool
     * @unitTest
     * @noTodo
     * @unitTest
     */
    public function loadSchema($options): bool;

}
