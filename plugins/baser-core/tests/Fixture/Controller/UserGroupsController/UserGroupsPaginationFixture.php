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

namespace BaserCore\Test\Fixture\Controller\UserGroupsController;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class UserGroupsPaginationFixture
 * @package BaserCore\Test\Fixture
 */
class UserGroupsPaginationFixture extends TestFixture
{
    public $import = ['table' => 'user_groups'];

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
                'name' => 'pagination' . $i,
                'title' => 'ページネーション' . $i,
                'auth_prefix' => 'admin',
                'use_move_contents' => false,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ];
        }

        parent::init();
    }
}
