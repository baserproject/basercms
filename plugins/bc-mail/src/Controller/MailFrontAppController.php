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

namespace BcMail\Controller;

use BaserCore\Controller\BcFrontAppController;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールコントローラー基底クラス
 */
class MailFrontAppController extends BcFrontAppController
{

    /**
     * Before Render
     * @param EventInterface $event
     * @return void
     * @checked
     * @noTodo
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        if (isset($this->RequestHandler) && $this->RequestHandler->prefers('json')) return;
        $this->viewBuilder()->setClassName('BcMail.MailFrontApp');
    }

}
