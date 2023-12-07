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

use BaserCore\Test\Factory\DblogFactory;
use BaserCore\Test\Factory\PasswordRequestFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * UserGroupsPaginationsScenario
 *
 */
class UserGroupsPaginationsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        for($i = 1; $i <= 21; $i++) {
            UserFactory::make(
                [
                    'name' => 'pagination' . $i,
                    'title' => 'ページネーション' . $i,
                    'auth_prefix' => 'Admin',
                    'use_move_contents' => false,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s')
                ]
            )->persist();
        }
    }

}
