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

namespace BcThemeConfig\Event;

use BaserCore\Event\BcControllerEventListener;
use Cake\Event\Event;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcThemeConfigControllerEventListener
 */
class BcThemeConfigControllerEventListener extends BcControllerEventListener
{

    /**
     * イベント
     * @var string[]
     */
    public $events = [
        'BaserCore.Themes.afterApply'
    ];

    /**
     * テーマ適用後イベント
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreThemesAfterApply(Event $event)
    {
        $path = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css';
        if(file_exists($path)) {
            unlink($path);
        }
    }

}
