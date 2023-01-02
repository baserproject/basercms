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
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PermissionGroupsServiceInterface
 */
interface PermissionGroupsServiceInterface
{

    /**
     * Constructor
     * @noTodo
     * @checked
     * @unitTest
     */
    public function __construct();

    /**
     * アクセスルールグループを単一取得
     *
     * @param int $id
     * @param int $userGroupId
     * @return EntityInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function get(int $id, int $userGroupId);

    /**
     * アクセスルールグループを更新する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function update(EntityInterface $entity, array $postData);

    /**
     * アクセスルールグループの一覧を取得する
     *
     * @param int $userGroupId
     * @param array $queryParams
     * @return \Cake\Datasource\ResultSetInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getIndex(int $userGroupId, array $queryParams);

    /**
     * アクセスルールグループのリストを取得する
     *
     * @param array $options
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getList(array $options = []);

    /**
     * プラグインを指定してアクセスルールを構築する
     *
     * @param string $plugin
     * @noTodo
     * @checked
     * @unitTest
     */
    public function buildByPlugin(string $plugin);

    /**
     * ユーザーグループを指定してアクセスグループを構築する
     *
     * @param int $userGroupId
     * @noTodo
     * @checked
     * @unitTest
     */
    public function buildByUserGroup(int $userGroupId);

    /**
     * デフォルトの拒否ルールを構築する
     *
     * @param int $userGroupId
     * @param string $type
     * @param string $name
     * @noTodo
     * @checked
     * @unitTest
     */
    public function buildDefaultDenyRule(int $userGroupId, string $type, string $name);

    /**
     * ユーザーを指定してアクセスルールを再構築する
     *
     * @param int $userGroupId
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function rebuildByUserGroup(int $userGroupId);

    /**
     * ユーザーを指定してアクセスルールを削除する
     *
     * @param int $userGroupId
     * @noTodo
     * @checked
     * @unitTest
     */
    public function deleteByUserGroup(int $userGroupId);

    /**
     * プラグインを指定してアクセスルールを削除する
     *
     * @param string $plugin
     * @noTodo
     * @checked
     * @unitTest
     */
    public function deleteByPlugin(string $plugin);

    /**
     * アクセスルールを全て構築する
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function buildAll();

    /**
     * デフォルトのその他のルールグループを作成する
     *
     * タイプを指定してタイプごとに作る
     *
     * @param string $type
     * @param string $name
     * @return EntityInterface|false
     * @noTodo
     * @checked
     * @unitTest
     */
    public function buildDefaultEtcRuleGroup(string $type, string $name);

    /**
     * アクセスルールを構築する
     *
     * @param int $userGroupId
     * @param string $plugin
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function build(int $userGroupId, string $plugin);

    /**
     * 指定したプラグインについて全てを許可するアクセスルールを構築する
     *
     * @param int $userGroupId
     * @param string $plugin
     * @param string $type
     * @param string $typeName
     * @noTodo
     * @checked
     * @unitTest
     */
    public function buildAllowAllMethodByPlugin(int $userGroupId, string $plugin, string $type, string $typeName);

}
