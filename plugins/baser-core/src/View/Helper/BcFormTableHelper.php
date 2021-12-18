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

namespace BaserCore\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use \Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class BcFormTableHelper
 * @package BaserCore\View\Helper
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
     */
    public function dispatchBefore()
    {

        // TODO ucmitz 未実装のため代替措置
        // >>>
        return '';
        // <<<

        $event = $this->dispatchLayerEvent('before', [
            'id' => $this->_View->BcForm->getId(),
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
     */
    public function dispatchAfter()
    {

        // TODO ucmitz 未実装のため代替措置
        // >>>
        return '';
        // <<<

        $event = $this->dispatchLayerEvent('after', [
            'id' => $this->_View->BcForm->getId(),
            'out' => ''
        ], ['class' => 'BcFormTable', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
    }

}
