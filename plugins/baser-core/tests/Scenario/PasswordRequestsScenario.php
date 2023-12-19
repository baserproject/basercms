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
 * PasswordRequestsScenario
 *
 */
class PasswordRequestsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        PasswordRequestFactory::make(
            [
                'id' => 1,
                'user_id' => 1,
                'request_key' => 'testkey1',
                'used' => 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => '2021-02-20 12:54:00'
                // ����: �����؂�
            ]
        )->persist();
        PasswordRequestFactory::make(
            [
                'id' => 2,
                'user_id' => 1,
                'request_key' => 'testkey1',
                'used' => 0,
                'created' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'modified' => '2021-02-20 12:54:00'
                // �L��
            ])->persist();
            PasswordRequestFactory::make(
            [
                'id' => 3,
                'user_id' => 1,
                'request_key' => 'testkey1',
                'used' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => '2021-02-20 12:54:00'
                // �L��
            ])->persist();
                PasswordRequestFactory::make(
            [
                'id' => 4,
                'user_id' => 2,
                'request_key' => 'testkey4',
                'used' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => '2021-02-20 12:54:00'
            ])->persist();
        return null;
    }

}
