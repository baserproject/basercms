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

/**
 * ThemesServiceInterface
 */
interface ThemesServiceInterface
{

    /**
     * 単一データ取得
     */
    public function get(): array;

    /**
     * 一覧データ取得
     */
    public function getIndex(): array;

    /**
     * 新しいテーマをアップロードする
     */
    public function add(): bool;

    /**
     * インストール
     */
    public function apply(): bool;

    /**
     * 初期データを読み込む
     */
    public function loadDefaultDataPattern(): bool;

    /**
     * コピーする
     */
    public function copy(): bool;

    /**
     * 削除する
     */
    public function delete(): bool;

    /**
     * 利用中のテーマをダウンロードする
     */
    public function download();

    /**
     * 初期データをダウンロードする
     */
    public function downloadDefaultDataPattern();

    /**
     * baserマーケットのテーマ一覧を取得する
     */
    public function getMarketThemes(): array;

    /**
     * コアの初期データを読み込む
     */
    public function resetData(): bool;

}
