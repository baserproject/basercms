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

use BaserCore\Event\BcHelperEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Event\Event;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcWidgetAreaControllerEventListener
 */
class BcWidgetAreaHelperEventListener extends BcHelperEventListener
{

    /**
     * Events
     *
     * @var string[]
     */
    public $events = ['Form.afterForm'];

    /**
     * Startup
     *
     * @param Event $event
     */
    public function formAfterForm(Event $event)
    {
        if(!BcUtil::isAdminSystem()) return;
        if($event->getData('id') !== 'SiteConfigFormForm') return;
        $view = $event->getSubject();
        $event->setData(['fields' => [[
            'title' => $view->BcAdminForm->label('widget_area', __d('baser', '標準ウィジェットエリア')),
            'input' => $view->BcAdminForm->control('widget_area', [
                'type' => 'select',
                'options' => $view->BcAdminForm->getControlSource('BcWidgetArea.WidgetAreas.id'), 'empty' => __d('baser', 'なし')
            ]) .
            '&nbsp;<i class="bca-icon--question-circle bca-help"></i>' .
            '<div class="bca-helptext">' .
                __d(
                    'baser',
                    'ウィジェットエリアは「{0}」より追加できます。',
                    $view->BcBaser->getLink(__d('baser', 'ウィジェットエリア管理'), [
                        'plugin' => 'BcWidgetArea',
                        'controller' => 'widget_areas',
                        'action' => 'index'
                    ])
                ) .
            '</div>'
        ]]]);
    }

}
