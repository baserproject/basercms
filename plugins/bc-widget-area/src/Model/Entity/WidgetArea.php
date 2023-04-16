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

namespace BcWidgetArea\Model\Entity;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\I18n\FrozenTime;

/**
 * WidgetArea
 *
 * @property int $id
 * @property string $name
 * @property string $widgets
 * @property array $widgets_array
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class WidgetArea extends \Cake\ORM\Entity
{

    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * バーチャルフィールド
     *
     * @var string[]
     */
    protected $_virtual = ['widgets_array'];

    /**
     * ウィジェットを取り出す
     *
     * 取り出す際に widgets フィールドの値をアンシリアライズする。
     *
     * @return array|mixed
     * @checked
     * @noTodo
     */
    protected function _getWidgetsArray()
    {
        if (!empty($this->_fields['widgets'])) {
            $value = BcUtil::unserialize($this->_fields['widgets']);
            usort($value, function($a, $b) {
                $aKey = key($a);
                $bKey = key($b);
                if ($a[$aKey]['sort'] == $b[$bKey]['sort']) {
                    return 0;
                }
                if ($a[$aKey]['sort'] < $b[$bKey]['sort']) {
                    return -1;
                }
                return 1;
            });
            return $value;
        } else {
            return [];
        }
    }

    /**
     * ウィジェットをセットする
     *
     * 保存する際に、配列をシリアライズする。
     *
     * @param $value
     * @return string|null
     * @checked
     * @noTodo
     */
    protected function _setWidgets($value)
    {
        if (is_array($value)) {
            if($value) {
                return BcUtil::serialize($value);
            } else {
                return null;
            }
        } else {
            return $value;
        }
    }

    /**
     * ウィジェット数を取得する
     * @return int
     * @checked
     * @noTodo
     */
    protected function _getCount()
    {
        if ($this->_fields['widgets']) {
            return count($this->widgets_array);
        } else {
            return 0;
        }
    }

}
