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
     * @param $theme
     * @param $pattern
     * @param $excludes
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadDefaultDataPattern($theme, $pattern): bool;

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
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function resetTables($plugin = 'BaserCore', $excludes = []): bool;

    /**
     * テーブルのデータをリセットする
     * @param $table
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function truncate($table): bool;

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
     * メールメッセージテーブルを初期化する
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function initMessageTables(): bool;

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
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAppTableList($plugin = ''): array;

    /**
     * アプリケーションに関連するテーブルリストのキャッシュをクリアする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearAppTableList(): void;

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
     * @param $options
     * @return bool
     * @unitTest
     * @noTodo
     * @unitTest
     */
    public function loadSchema($options): bool;

}
