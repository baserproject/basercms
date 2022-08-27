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

use Cake\View\Helper;
use Cake\Utility\Inflector;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * 検索ボックスヘルパ
 * Class BcSearchBoxHelper
 * @package BaserCore\View\Helper
 */
class BcSearchBoxHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * 検索フィールド発火
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function dispatchShowField()
    {
        $request = $this->_View->getRequest();
        $id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
        $event = $this->dispatchLayerEvent('showField', ['id' => $id, 'fields' => []], ['class' => 'BcSearchBox', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            if (!empty($event->getData('fields'))) {
                foreach($event->getData('fields') as $field) {
                    $output .= "<span class=\"bca-search__input-item\">";
                    if (!empty($field['title'])) {
                        $output .= $field['title'] . "&nbsp;";
                    }
                    if (!empty($field['input'])) {
                        $output .= $field['input'] . "&nbsp;";
                    }
                    $output .= "</span>\n";
                }
            }
        }
        return $output;
    }

}
