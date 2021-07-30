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

namespace BaserCore\Event;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

trait BcEventDispatcherTrait
{

    /**
     * View
     *
     * @var \Cake\View\View
     */
    protected $_View;

    /**
     * イベントを発火
     *
     * @param string $name
     * @param array $data
     * @return bool|\Cake\Event\Event
     * @checked
     * @unitTest
     * @noTodo
     */
    public function dispatchLayerEvent($name, $data = [], $options = [])
    {
        $plugin = method_exists($this, 'getPlugin')? $this->getPlugin() : '';
        $class = method_exists($this, 'getName')? $this->getName() : get_class($this);
        $subject = $this;
        $layer = '';
        if (is_a($this, 'Cake\Controller\Controller')) {
            $layer = 'Controller';
        } elseif (is_a($this, 'Cake\ORM\Table')) {
            $classArray = explode('\\', $class);
            $class = str_replace('Table', '', $classArray[count($classArray) - 1]);
            $layer = 'Model';
            $alias = $this->getRegistryAlias();
            if(strpos($alias, '.') !== false) {
                $plugin = explode('.', $alias)[0];
            }
        } elseif (is_a($this, 'Cake\View\View')) {
            $layer = 'View';
        } elseif (is_a($this, 'Cake\View\Helper')) {
            $layer = 'Helper';
            $class = str_replace('Helper', '', $class);
            $subject = $this->_View;
        }
        $options = array_merge([
            'modParams' => 0,
            'plugin' => $plugin,
            'layer' => $layer,
            'class' => $class
        ], $options);
        return BcEventDispatcher::dispatch($name, $subject, $data, $options);
    }
}
