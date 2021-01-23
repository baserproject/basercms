<?php
namespace BcBlog\Controller\Admin;
use BaserCore\Controller\Admin\BcAdminAppController;

class PostsController extends BcAdminAppController {
    public function index() {
        $this->setTitle('ブログサンプル');
    }
}
