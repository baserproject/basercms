<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Event;

use BaserCore\Event\BcControllerEventListener;
use Cake\Event\EventInterface;

/**
 * Class BcSeoControllerEventListener
 */
class BcSeoControllerEventListener extends BcControllerEventListener
{
    public $events = [
        'beforeRender',
    ];

    /**
     * beforeRender
     */
    public function beforeRender(EventInterface $event)
    {
        $controller = $event->getSubject();
        $controller->viewBuilder()->addHelper('BcSeo.Seo');
        $controller->viewBuilder()->addHelper('BaserCore.BcHtml');
    }
}
