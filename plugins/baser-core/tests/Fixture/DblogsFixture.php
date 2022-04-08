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
 * Class DblogsFixture
 * @package BaserCore\Test\Fixture
 */
class DblogsFixture extends TestFixture
{
    public $import = ['table' => 'dblogs'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'message' => 'dblogs test message1',
            'user_id' => '1',
        ], [
            'id' => 2,
            'message' => 'dblogs test message2',
            'user_id' => '2',
        ], [
            'id' => 3,
            'message' => 'dblogs test message3',
            'user_id' => '3',
        ],
    ];
}
