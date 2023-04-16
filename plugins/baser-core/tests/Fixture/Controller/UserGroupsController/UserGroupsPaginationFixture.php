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

namespace BaserCore\Test\Fixture\Controller\UserGroupsController;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class UserGroupsPaginationFixture
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
                'auth_prefix' => 'Admin',
                'use_move_contents' => false,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ];
        }

        parent::init();
    }
}
