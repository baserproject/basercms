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
use Cake\ORM\Query;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcArrayHelper
 * @uses BcArrayHelper
 */
class BcArrayHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * 配列の最初の要素かどうか調べる
     *
     * @param mixed $array 配列
     * @param int $key 現在のキー
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function first($array, int $key)
    {
        if($array instanceof Query) {
            $iterator = clone $array->getIterator();
            $iterator->first();
            $first = $iterator->key();
        } else {
            reset($array);
            $first = key($array);
        }
        if ($key === $first) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 配列の最後の要素かどうか調べる
     *
     * @param mixed $array 配列
     * @param int $key 現在のキー
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function last($array, $key)
    {
        if($array instanceof Query) {
            $end = $array->count() - 1;
        } else {
            end($array);
            $end = key($array);
        }
        if ($key === $end) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 配列にテキストを追加する
     *
     * @param array $array
     * @param string $prefix
     * @param string $suffix
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addText($array, $prefix = '', $suffix = '')
    {
        if ($prefix || $suffix) {
            array_walk($array, [$this, '__addText'], $prefix . ',' . $suffix);
        }
        return $array;
    }

    /**
     * addTextToArrayのコールバックメソッド
     *
     * @param string $value
     * @param string $key
     * @param string $add
     * @checked
     * @noTodo
     * @unitTest
     */
    private function __addText(&$value, $key, $add)
    {
        if ($add) {
            [$prefix, $suffix] = explode(',', $add);
        }
        $value = $prefix . $value . $suffix;
    }

}
