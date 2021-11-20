<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Model\Entity\SiteConfig;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Interface SiteConfigServiceInterface
 * @package BaserCore\Service
 */
interface SiteConfigServiceInterface
{

    /**
     * フィールドの値を取得する
     * @param $fieldName
     * @return string|null
     */
    public function getValue($fieldName): ?string;

    /**
     * サイト全体の設定値を更新する
     *
     * @param  string $name
     * @param  string $value
     * @return SiteConfig
     */
    public function setValue($name, $value);

    /**
     * サイト全体の設定値をリセットする
     *
     * @param  string $name
     * @return SiteConfig
     */
    public function resetValue($name);


    /**
     * データをキーバリュー形式で取得する
     * @return array
     */
    public function get(): SiteConfig;

    /**
     * データをキーバリュー形式で保存する
     * @return EntityInterface
     */
    public function update(array $postData): SiteConfig;

    /**
     * .env が書き込み可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isWritableEnv(): bool;

    /**
     * .env に設定値を書き込む
     * @param $key
     * @param $value
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function putEnv($key, $value): bool;

    /**
     * アプリケーションモードリストを取得
     * @return array
     */
    public function getModeList(): array;

}
