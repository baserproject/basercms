<?php

namespace BcBurgerEditor\Event;

use App\Controller\ErrorController;
use BaserCore\Event\BcControllerEventListener;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */
class BcBurgerEditorControllerEventListener extends BcControllerEventListener
{

    /**
     * 登録イベント
     *
     * @var array
     */
    public $events = [
        'initialize',
    ];

    /**
     * initialize
     * 利用Helperの追加
     *
     * @param EventInterface $event
     */
    public function initialize(EventInterface $event)
    {
        /** @var Controller $Controller */
        $Controller = $event->getSubject();
        if($Controller instanceof ErrorController) return;
        $Controller->viewBuilder()->addHelper('BcBurgerEditor.BurgerEditor');
        if(!$this->isAction('BlogPosts.Add')
            && !$this->isAction('BlogPosts.Edit')
            && !$this->isAction('Pages.Add')
            && !$this->isAction('Pages.Edit')
            && !$this->isAction('CustomEntries.Add')
            && !$this->isAction('CustomEntries.Edit')
        ){
            return;
        }
        // BurgerEditor は編集フォーム内に FormHelper を経由しない input を出力するため、
        // それらのみを検証対象から除外する。validate 自体は有効に保つ。
        $Controller->FormProtection->setConfig('unlockedFields', BurgerEditorUtil::getEditorFieldNames());
    }

}
