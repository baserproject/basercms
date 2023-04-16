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

namespace BcFavorite\Event;

use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;

/**
 * BcFavoriteViewEventListener
 */
class BcFavoriteViewEventListener extends \BaserCore\Event\BcViewEventListener
{

    /**
     * Event
     * @var string[]
     */
    public $events = ['beforeAdminMenu', 'beforeContentsMenu', 'afterRender'];

    /**
     * 管理画面メニュー上部
     */
    public function beforeAdminMenu(EventInterface $event)
    {
        if(!BcUtil::isAdminSystem()) {
            return;
        }
        /* @var \BaserCore\View\BcAdminAppView $viewClass */
        $viewClass = $event->getSubject();
        echo $viewClass->element('BcFavorite.favorite_menu');
    }

    /**
     * beforeContentsMenu
     *
     * @param  EventInterface $event
     * @return void
     */
    public function beforeContentsMenu(EventInterface $event)
    {
        if(!BcUtil::isAdminSystem()) {
            return;
        }
        $contentsMenu = $event->getData('contentsMenu');
        $view = $event->getSubject();
        $contentsMenu[] = $view->BcBaser->getLink(__d('baser_core', 'お気に入りに追加'), 'javascript:void(0)', [
            'id' => 'BtnFavoriteAdd',
            'data-bca-fn' => 'BtnFavoriteAdd',
            'class' => 'bca-content-menu__link bca-icon--plus-square'
        ]);
        $event->setData('contentsMenu', $contentsMenu);
    }

    /**
     * beforeRender
     *
     * @param EventInterface $event
     * @note(value="できたら、favorite_menuに移動する")
     */
    public function afterRender(EventInterface $event)
    {
        if(!BcUtil::isAdminSystem()) return;
        $request = Router::getRequest();
        if($request->getParam('controller') === 'PasswordRequests') return;
        $view = $event->getSubject();
        $view->set('currentPageName', h($view->BcAdmin->getTitle()));
        $view->set('currentPageUrl', h($view->getRequest()->getRequestTarget()));
    }

}
