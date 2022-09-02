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
 * ThemesServiceInterface
 */
interface ThemesServiceInterface
{

    /**
     * 単一データ取得
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(): array;

    /**
     * 一覧データ取得
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(): array;

    /**
     * 新しいテーマをアップロードする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(): bool;

    /**
     * インストール
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(): bool;

    /**
     * 初期データを読み込む
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadDefaultDataPattern(): bool;

    /**
     * コピーする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(): bool;

    /**
     * 削除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(): bool;

    /**
     * 利用中のテーマをダウンロードする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download();

    /**
     * 初期データをダウンロードする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function downloadDefaultDataPattern();

    /**
     * baserマーケットのテーマ一覧を取得する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMarketThemes(): array;

    /**
     * コアの初期データを読み込む
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetData(): bool;

}
