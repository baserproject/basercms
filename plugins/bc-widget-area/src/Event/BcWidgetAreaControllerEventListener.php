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

namespace BcWidgetArea\Event;

use BaserCore\Event\BcControllerEventListener;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Controller;
use Cake\Event\Event;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcWidgetAreaControllerEventListener
 */
class BcWidgetAreaControllerEventListener extends BcControllerEventListener
{

    /**
     * Events
     *
     * @var string[]
     */
    public $events = ['startup'];

    /**
     * Startup
     *
     * @param Event $event
     */
    public function startup(Event $event)
    {
        if(BcUtil::isAdminSystem()) return;
        /** @var Controller $controller */
        $controller = $event->getSubject();
        $controller->set('currentWidgetAreaId', BcSiteConfig::get('widget_area'));
    }

}
