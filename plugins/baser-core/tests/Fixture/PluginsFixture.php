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
 * Class PluginsFixture
 * @package BaserCore\Test\Fixture
 */
class PluginsFixture extends TestFixture
{
    public $import = ['table' => 'plugins'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'BcBlog',
            'title' => 'ブログ',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '1',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ],
        [
            'id' => 2,
            'name' => 'BcMail',
            'title' => 'メール',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '2',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ],
        [
            'id' => 3,
            'name' => 'BcUploader',
            'title' => 'アップローダー',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '3',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ],
    ];
}
