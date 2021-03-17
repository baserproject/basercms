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

namespace BaserCore\Test\Fixture\Controller\UsersController;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class UsersPaginationFixture
 * @package BaserCore\Test\Fixture
 */
class UsersPaginationFixture extends TestFixture
{
    public $import = ['table' => 'users'];

    /**
     * Initialize the fixture.
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [];

        for($i = 1; $i <= 21; $i++) {
            $this->records[] = [
                'name' => 'Lorem ipsum dolor sit amet Pagination' . $i,
                'password' => 'Lorem ipsum dolor sit amet',
                'real_name_1' => 'Lorem ipsum dolor sit amet',
                'real_name_2' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet Pagination',
                'nickname' => 'Lorem ipsum dolor sit amet',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ];
        }

        parent::init();
    }
}
