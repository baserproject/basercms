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
use Cake\Utility\Inflector;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;


/**
 * レイアウトヘルパ
 *
 */
class BcLayoutHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * コンテンツヘッダー発火
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function dispatchContentsHeader()
    {
        $request = $this->_View->request;
        $id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
        // EVENT BcLayout.contentsHeader
        $event = $this->dispatchLayerEvent('contentsHeader', [
            'id' => $id,
            'out' => ''
        ], ['class' => 'BcLayout', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
    }

    /**
     * コンテンツフッター発火
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function dispatchContentsFooter()
    {
        $request = $this->_View->request;
        $id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
        // EVENT BcLayout.contentsFooter
        $event = $this->dispatchLayerEvent('contentsFooter', [
            'id' => $id,
            'out' => ''
        ], ['class' => 'BcLayout', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
    }

}
