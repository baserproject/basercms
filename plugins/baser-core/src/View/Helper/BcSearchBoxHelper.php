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

use Cake\View\Helper;
use Cake\Utility\Inflector;
use BaserCore\Event\BcEventDispatcherTrait;

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
     */
    public function dispatchShowField($request)
    {
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
