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
use BaserCore\Model\Entity\Site;

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
     * テーマを適用する
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(Site $site, string $theme): array;

    /**
     * 初期データを読み込む
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadDefaultDataPattern(): bool;

    /**
     * コピーする
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(string $theme): bool;

    /**
     * 削除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(string $theme): bool;

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

    /**
     * 指定したテーマをダウンロード用のテーマとして一時フォルダに作成する
     * @param string $theme
     * @return string
     */
    public function createDownloadToTmp(string $theme): string;

}
