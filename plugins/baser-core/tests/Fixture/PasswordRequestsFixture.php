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
 * Class PasswordRequestsFixture
 * @package BaserCore\Test\Fixture
 */
class PasswordRequestsFixture extends TestFixture
{
    public $import = ['table' => 'password_requests'];

    /**
     * Records
     *
     * @var array
     */
    public $records;

    public function init(): void
    {
        $this->records = [
            // 無効: 使用済み
            [
                'id' => 1,
                'user_id' => 1,
                'request_key' => 'testkey1',
                'used' => 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => '2021-02-20 12:54:00'
                // 無効: 期限切れ
            ], [
                'id' => 2,
                'user_id' => 1,
                'request_key' => 'testkey1',
                'used' => 0,
                'created' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'modified' => '2021-02-20 12:54:00'
                // 有効
            ], [
                'id' => 3,
                'user_id' => 1,
                'request_key' => 'testkey1',
                'used' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => '2021-02-20 12:54:00'
                // 有効
            ], [
                'id' => 4,
                'user_id' => 2,
                'request_key' => 'testkey4',
                'used' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => '2021-02-20 12:54:00'
            ],
        ];
        parent::init();
    }
}
