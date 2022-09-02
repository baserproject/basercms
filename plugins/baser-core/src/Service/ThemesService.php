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

class ThemesService implements ThemesServiceInterface
{

    /**
     * 単一データ取得
     */
    public function get(): array
    {
        return [];
    }

    /**
     * 一覧データ取得
     */
    public function getIndex(): array
    {
        return [];
    }

    /**
     * 新しいテーマをアップロードする
     */
    public function add(): bool
    {
        return true;
    }

    /**
     * インストール
     */
    public function apply(): bool
    {
        return true;
    }

    /**
     * 初期データを読み込む
     */
    public function loadDefaultDataPattern(): bool
    {
        return true;
    }

    /**
     * コピーする
     */
    public function copy(): bool
    {
        return true;
    }

    /**
     * 削除する
     */
    public function delete(): bool
    {
        return true;
    }

    /**
     * 利用中のテーマをダウンロードする
     */
    public function download()
    {
        return true;
    }

    /**
     * 初期データをダウンロードする
     */
    public function downloadDefaultDataPattern()
    {
        return true;
    }

    /**
     * baserマーケットのテーマ一覧を取得する
     */
    public function getMarketThemes(): array
    {
        return [];
    }

    /**
     * コアの初期データを読み込む
     */
    public function resetData(): bool
    {
        return true;
    }

}
