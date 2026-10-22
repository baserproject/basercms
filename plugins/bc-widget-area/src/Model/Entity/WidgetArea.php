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
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class WidgetArea extends \Cake\ORM\Entity
{

    /**
     * Accessible
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * バーチャルフィールド
     *
     * @var string[]
     */
    protected array $_virtual = ['widgets_array'];

    /**
     * ウィジェットを取り出す
     *
     * 取り出す際に widgets フィールドの値をアンシリアライズする。
     *
     * @return array|mixed
     * @checked
     * @noTodo
     * @unitTest
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
     * @unitTest
     */
    protected function _setWidgets($value)
    {
        if (is_array($value)) {
            if($value) {
                return BcUtil::serialize(self::stripObjects($value));
            } else {
                return null;
            }
        } elseif (is_string($value) && $value !== '') {
            // シリアライズ済み文字列を受け取った場合は、配列として復元できるものだけを
            // オブジェクトを除去したうえで再シリアライズして保存する。
            // 任意のシリアライズ文字列をそのまま保存させると、復元時のオブジェクトインジェクションに悪用される
            $decoded = BcUtil::unserialize($value);
            if (is_array($decoded) && $decoded) {
                return BcUtil::serialize(self::stripObjects($decoded));
            }
            return null;
        } else {
            return null;
        }
    }

    /**
     * 配列からオブジェクトを再帰的に除去する
     *
     * @param array $value
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function stripObjects(array $value): array
    {
        foreach($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = self::stripObjects($item);
            } elseif (is_object($item)) {
                unset($value[$key]);
            }
        }
        return $value;
    }

    /**
     * ウィジェット数を取得する
     * @return int
     * @checked
     * @noTodo
     * @unitTest
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
