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

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class UsersFixture
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
            'auth_prefix' => 'Admin,Api/Admin',
            'auth_prefix_settings' => '',
            'use_move_contents' => true,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 2,
            'name' => 'operators',
            'title' => 'サイト運営者',
            'auth_prefix' => 'Admin',
            'auth_prefix_settings' => '{"Admin":{"type":"2"},"Api/Admin":{"type":"2"}}',
            'use_move_contents' => false,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 3,
            'name' => 'others',
            'title' => 'その他のグループ',
            'auth_prefix' => 'Admin',
            'auth_prefix_settings' => '{"Admin":{"type":"2"},"Api":{"type":"2"}}',
            'use_move_contents' => false,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
    ];
}
