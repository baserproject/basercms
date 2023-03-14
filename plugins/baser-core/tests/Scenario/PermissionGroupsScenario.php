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

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Factory\PermissionGroupFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * PermissionGroupsScenario
 *
 * - plugin.BaserCore.Factory/Permissions
 * - plugin.BaserCore.Factory/PermissionGroups
 * - plugin.BaserCore.Factory/UserGroups
 */
class PermissionGroupsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        PermissionGroupFactory::make([
            'id' => 1,
            'name' => 'コンテンツフォルダ管理',
            'type' => 'Admin',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        PermissionGroupFactory::make([
            'id' => 2,
            'name' => '固定ページ管理',
            'type' => 'Admin',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        PermissionGroupFactory::make([
            'id' => 3,
            'name' => 'システム基本設定',
            'type' => 'Admin',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();

        PermissionFactory::make([
            'no' => 1,
            'sort' => 1,
            'name' => 'システム管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/*',
            'auth' => 0,
            'status' => 1,
            'created' => '2015-09-30 01:21:40',
            'method' => 'ALL',
            'modified' => null,
        ])->persist();

        PermissionFactory::make([
            'no' => 1,
            'sort' => 1,
            'name' => 'よく使う項目',
            'user_group_id' => 1,
            'permission_group_id' => 2,
            'url' => '/baser/admin/*',
            'auth' => 0,
            'status' => 1,
            'created' => '2015-09-30 01:21:40',
            'method' => 'ALL',
            'modified' => null,
        ])->persist();

        PermissionFactory::make([
            'no' => 1,
            'sort' => 1,
            'name' => 'ページ管理',
            'user_group_id' => 1,
            'permission_group_id' => 3,
            'url' => '/baser/admin/*',
            'auth' => 0,
            'status' => 1,
            'created' => '2015-09-30 01:21:40',
            'method' => 'ALL',
            'modified' => null,
        ])->persist();
    }

}
