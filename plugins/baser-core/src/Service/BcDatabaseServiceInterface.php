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
     * @param $theme
     * @param $pattern
     */
    public function loadDefaultDataPattern($theme, $pattern);

    /**
     * CSVファイルをDBに読み込む
     *
     * @param array $options
     *  - `path`: 読み込み元のCSVのパス
     *  - `encoding: CSVファイルのエンコード
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function loadCsv($options);

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
    public function resetAllTables($excludes = []);

    /**
     * 複数のテーブルをリセットする
     *
     * @param string $plugin
     * @param array $excludes
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function resetTables($plugin = 'BaserCore', $excludes = []);

    /**
     * テーブルのデータをリセットする
     * @param $table
     * @noTodo
     * @checked
     * @unitTest
     */
    public function truncate($table);

    /**
     * システムデータを初期化する
     *
     * @param string $dbConfigKeyName
     * @param array $dbConfig
     * @noTodo
     * @checked
     * @unitTest
     */
    public function initSystemData($options = []);

    /**
     * メールメッセージテーブルを初期化する
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function initMessageTables();

    /**
     * データベースシーケンスをアップデートする
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
     * @noTodo
     * @checked
     * @unitTest
     */
    public function loadCsvToArray($path, $encoding = null);

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
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getEncoding();

    /**
     * アプリケーションに関連するテーブルリストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    function getAppTableList($plugin = ''): array;

}
