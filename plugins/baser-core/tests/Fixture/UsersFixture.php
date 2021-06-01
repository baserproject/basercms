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

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class UsersFixture
 * @package BaserCore\Test\Fixture
 */
class UsersFixture extends TestFixture
{
    public $import = ['table' => 'users'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'baser admin',
            'password' => '$2y$10$x6WQstawmuyS7XrqutyDjOSOLxJp3dv72O73B7lhqzP8XvVlmcx4G',
            'real_name_1' => 'baser',
            'real_name_2' => 'admin',
            'email' => 'testuser1@example.com',
            'nickname' => 'Lorem ipsum dolor sit amet',
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 2,
            'name' => 'baser operator',
            'password' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'baser',
            'real_name_2' => 'operator',
            'email' => 'testuser2@example.com',
            'nickname' => 'Lorem ipsum dolor sit amet',
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
    ];
}
