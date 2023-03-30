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
class UsersUserGroupsFixture extends TestFixture
{
    public $import = ['table' => 'users_user_groups'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'user_id' => 1,
            'user_group_id' => 1,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 2,
            'user_id' => 2,
            'user_group_id' => 2,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
        [
            'id' => 3,
            'user_id' => 3,
            'user_group_id' => 3,
            'created' => '2017-05-03 10:57:07',
            'modified' => '2017-05-03 10:57:07'
        ],
    ];
}
