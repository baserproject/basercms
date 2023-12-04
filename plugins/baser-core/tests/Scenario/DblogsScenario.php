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
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * DblogsScenario
 *
 */
class DblogsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        DblogFactory::make(
            [
                'id' => 1,
                'message' => 'dblogs test message1',
                'user_id' => '1',
            ]
        )->persist();
        DblogFactory::make(
        [
            'id' => 2,
            'message' => 'dblogs test message2',
            'user_id' => '2',
        ])->persist();
        DblogFactory::make(
        [
            'id' => 3,
            'message' => 'dblogs test message3',
            'user_id' => '3',
        ])->persist();
    }

}
