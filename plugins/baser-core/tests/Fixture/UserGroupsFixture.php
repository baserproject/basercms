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
class UserGroupsFixture extends TestFixture
{
    public $import = ['table' => 'user_groups'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'admins',
            'title' => 'システム管理',
            'auth_prefix' => 'admin',
            'use_move_contents' => true,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 2,
            'name' => 'operators',
            'title' => 'サイト運営者',
            'auth_prefix' => 'admin',
            'use_move_contents' => false,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 3,
            'name' => 'others',
            'title' => 'その他のグループ',
            'auth_prefix' => 'admin',
            'use_move_contents' => false,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
    ];
}
