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

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * InitAppScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Sites
 * - plugin.BaserCore.Factory/SiteConfigs
 * - plugin.BaserCore.Factory/Users
 * - plugin.BaserCore.Factory/UsersUserGroups
 * - plugin.BaserCore.Factory/UserGroups
 */
class InitAppScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        SiteFactory::make()->main()->persist();
        UserFactory::make()->admin()->persist();
    }

}
