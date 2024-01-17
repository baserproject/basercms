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
namespace BcBlog\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogAdminAppController
 */
class BlogAdminAppController extends BcAdminAppController
{

    /**
     * Before Render
     * @param EventInterface $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        if ($this->getRequest()->getQuery('preview')) return;
        $this->viewBuilder()->setClassName('BcBlog.BlogAdminApp');
    }

}
