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

namespace BaserCore\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class BcFormTableHelper
 * @uses BcFormTableHelper
 */
class BcFormTableHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * テーブル前発火
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function dispatchBefore()
    {
        $event = $this->dispatchLayerEvent('before', [
            'id' => $this->_View->BcAdminForm->getId(),
            'out' => ''
        ], ['class' => 'BcFormTable', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
    }

    /**
     * テーブル後発火
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function dispatchAfter()
    {
        $event = $this->dispatchLayerEvent('after', [
            'id' => $this->_View->BcAdminForm->getId(),
            'out' => ''
        ], ['class' => 'BcFormTable', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
    }

}
