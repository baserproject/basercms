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

namespace BaserCore\Controller;

use App\Controller\AppController as BaseController;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Event\EventInterface;

/**
 * Class AppController
 * @package BaserCore\Controller
 */
class AppController extends BaseController
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcMessage');
        $this->loadComponent('Security');
        $this->loadComponent('Paginator');
    }

    /**
     * Before Render
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     */
    public function beforeRender(EventInterface $event): void
    {
        if (!isset($this->RequestHandler) || !$this->RequestHandler->prefers('json')) {
            $this->viewBuilder()->setClassName('BaserCore.App');
            $site = $this->getRequest()->getParam('Site');
            if($site) {
                $this->viewBuilder()->setTheme($site->theme);
            }
        }
    }

}
