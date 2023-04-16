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

use BaserCore\Model\Entity\SiteConfig;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Interface SiteConfigsServiceInterface
 */
interface SiteConfigsServiceInterface
{

    /**
     * フィールドの値を取得する
     * 
     * @param $fieldName
     * @return string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getValue($fieldName): ?string;

    /**
     * サイト全体の設定値を更新する
     *
     * @param  string $name
     * @param  string $value
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValue($name, $value);

    /**
     * サイト全体の設定値をリセットする
     *
     * @param  string $name
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetValue($name);


    /**
     * データをキーバリュー形式で取得する
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(): SiteConfig;

    /**
     * データをキーバリュー形式で保存する
     * 
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(array $postData);

    /**
     * .env が書き込み可能かどうか
     * 
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isWritableEnv(): bool;

    /**
     * .env に設定値を書き込む
     * 
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
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getModeList(): array;

    /**
     * baserCMSのDBのバージョンを取得する
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getVersion():string;

    /**
     * キャッシュ用 Entity を削除
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearCache();

}
