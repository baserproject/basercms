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

use BaserCore\Test\Factory\LoginStoreFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * UserScenario
 */
class UserScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        UserFactory::make([
            'id' => 1,
            'name' => 'baser admin',
            'password' => '$2y$10$x6WQstawmuyS7XrqutyDjOSOLxJp3dv72O73B7lhqzP8XvVlmcx4G',
            'real_name_1' => 'baser',
            'real_name_2' => 'admin',
            'email' => 'testuser1@example.com',
            'nickname' => 'ニックネーム1',
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07',
            'status' => true
        ])->persist();
        UserFactory::make([
            'id' => 2,
            'name' => 'baser operator',
            'password' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'baser',
            'real_name_2' => 'operator',
            'email' => 'testuser2@example.com',
            'nickname' => 'ニックネーム2',
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07',
            'status' => true
        ])->persist();
        UserFactory::make([
            'id' => 3,
            'name' => 'baser others',
            'password' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'baser',
            'real_name_2' => 'others',
            'email' => 'testuser3@example.com',
            'nickname' => 'ニックネーム3',
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07',
            'status' => false
        ])->persist();
    }

}
