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

namespace BcPluginSample\Event;

use BaserCore\Event\BcViewEventListener;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;

/**
 * BcPluginSampleViewEventListener
 */
class BcPluginSampleViewEventListener extends BcViewEventListener
{

    /**
     * Events
     *
     * @var string[]
     */
    public $events = ['rightOfToolbar'];

    /**
     * Right Of Toolbar
     *
     * @return void
     */
    public function rightOfToolbar() {
        $html = new HtmlHelper(new View());
        $img = $html->image('BcPluginSample.bassy.png', ['width' => '24px']);
        $link = $html->link($img, 'https://basercms.net', ['target' => '_blank', 'escape' => false]);
        echo '<li>' . $link . '</li>';
    }

}
