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
use BaserCore\Vendor\Simplezip;

/**
 * UtilitiesServiceInterface
 */
interface UtilitiesServiceInterface
{

    /**
     * コンテンツツリーの構造をチェックする
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function verityContentsTree(): bool;

    /**
     * コンテンツツリーをリセットし全て同階層にする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetContentsTree();

    /**
     * クレジットを取得する
     * @return mixed|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCredit();

    /**
     * ログのZipファイルを作成する
     * @return Simplezip|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createLogZip();

    /**
     * ログを削除する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteLog();

    /**
     * DBバックアップを作成する
     * @param $encoding
     * @return Simplezip
     * @checked
     * @noTodo
     * @unitTest
     */
    public function backupDb($encoding): Simplezip;

    /**
     * スキーマ用の一時フォルダをリセットする
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetTmpSchemaFolder(): bool;

    /**
     * バックアップファイルよりレストアを行う
     * @param array $postData
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restoreDb(array $postData, array $uploaded): bool;

    public function writeSchema();

    public function loadScheme();

}
