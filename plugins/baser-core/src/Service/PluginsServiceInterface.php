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

use Cake\Datasource\EntityInterface;
use Exception;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Interface PluginsServiceInterface
 */
interface PluginsServiceInterface
{

    /**
     * プラグインを取得する
     * 
     * @param int $id
     * @return EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function get($id): EntityInterface;

    /**
     * プラグイン一覧を取得
     * 
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getIndex(string $sortMode): array;

    /**
     * プラグインをインストールする
     * 
     * @param string $name プラグイン名
     * @param string $connection test connection指定用
     * @return bool|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function install($name, $connection = 'default'): ?bool;

    /**
     * プラグインをアップデートする
     * 
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function update($name, $connection = 'default'): ?bool;

    /**
     * バージョンを取得する
     * 
     * @param $name
     * @return mixed|string
     * @checked
     * @unitTest
     * @noTodo
     */
	public function getVersion($name);

    /**
     * プラグインを無効にする
     * 
     * @param string $name
     * @checked
     * @unitTest
     * @noTodo
     */
    public function detach(string $name): bool;

    /**
     * プラグインを有効にする
     * 
     * @param string $name
     * @checked
     * @unitTest
     * @noTodo
     */
    public function attach(string $name): bool;

    /**
     * プラグイン名からプラグインエンティティを取得
     * 
     * @param string $name
     * @return array|EntityInterface|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getByName(string $name);

    /**
     * データベースをリセットする
     * 
     * @param string $name
     * @param array $connection
     * @throws Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function resetDb(string $name, $connection = 'default'): void;

    /**
     * プラグインを削除する
     * 
     * @param string $name
     * @param string $connection
     * @checked
     * @unitTest
     * @noTodo
     */
    public function uninstall(string $name, $connection = 'default'): void;

    /**
     * 優先度を変更する
     * 
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function changePriority(int $id, int $offset, array $conditions = []): bool;

    /**
     * baserマーケットのプラグイン一覧を取得する
     * 
     * @return array|mixed
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getMarketPlugins(): array;

    /**
     * ユーザーグループにアクセス許可設定を追加する
     *
     * @param array $data リクエストデータ
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function allow($data): void;

    /**
     * インストール時の状態を返す
     * 
     * @param string $pluginName
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getInstallStatusMessage($pluginName): string;

    /**
     * 一括処理
     * 
     * @param array $ids
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function batch(string $method, array $ids): bool;

    /**
     * IDを指定して名前リストを取得する
     * 
     * @param $ids
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getNamesById($ids): array;

}
