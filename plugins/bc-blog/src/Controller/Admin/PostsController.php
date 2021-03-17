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

namespace BcBlog\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;

/**
 * Class PostsController
 * @package BcBlog\Controller\Admin
 */
class PostsController extends BcAdminAppController
{

    /**
     * 記事一覧
     */
    public function index() : void
    {
        $this->setTitle('ブログサンプル');
    }
}
