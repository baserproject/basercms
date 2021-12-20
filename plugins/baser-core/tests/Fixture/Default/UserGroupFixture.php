<?php
// TODO : コード確認要
return;

/**
 * UserGroupFixture
 */
class UserGroupFixture extends BaserTestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'name' => 'admins',
            'title' => 'システム管理',
            'auth_prefix' => 'admin',
            'use_move_contents' => 1,
            'modified' => null,
            'created' => '2015-01-27 12:56:53'
        ],
        [
            'id' => '2',
            'name' => 'operators',
            'title' => 'サイト運営',
            'auth_prefix' => 'operator',
            'use_move_contents' => 0,
            'modified' => null,
            'created' => '2015-01-27 12:56:53'
        ],
    ];

}
