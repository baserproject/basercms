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
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcListTableHelper
 * @package BaserCore\View\Helper
 * @uses BcListTableHelper
 */
class BcListTableHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * カラム数
     *
     * @var int
     */
    protected $_columnNumber = 0;

    /**
     * カラム数を設定する
     *
     * @param int $number カラム数
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setColumnNumber($number)
    {
        $this->_columnNumber = $number;
    }

    /**
     * カラム数を取得する
     *
     * @return int カラム数
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getColumnNumber()
    {
        return $this->_columnNumber;
    }

    /**
     * リスト見出し発火
     *
     * @return string
     */
    public function dispatchShowHead()
    {

        // TODO 未実装のため代替措置
        // >>>
        return '';
        // <<<

        $request = $this->_View->request;
        $id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
        $event = $this->dispatchLayerEvent('showHead', ['id' => $id, 'fields' => []], ['class' => 'BcListTable', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            if (!empty($event->getData('fields'))) {
                foreach($event->getData('fields') as $field) {
                    $output .= "<th class=\"bca-table-listup__thead-th\">" . $field . "</th>\n";
                }
                $this->_columnNumber += count($event->getData('fields'));
            }
        }
        return $output;
    }

    /**
     * リスト行発火
     *
     * @param $data
     * @return string
     */
    public function dispatchShowRow($data)
    {

        // TODO 未実装のため代替措置
        // >>>
        return '';
        // <<<

        $request = $this->_View->request;
        $id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
        $event = $this->dispatchLayerEvent('showRow', ['id' => $id, 'data' => $data, 'fields' => []], ['class' => 'BcListTable', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            if (!empty($event->getData('fields'))) {
                foreach($event->getData('fields') as $field) {
                    $output .= "<td class=\"bca-table-listup__tbody-td\">" . $field . "</td>\n";
                }
            }
        }
        return $output;
    }

    /**
     * Row Class
     *
     * @param bool $isPublish 公開しているかどうか
     * @param array $record レコード
     * @param array $options オプション
     *    - `class` : 追加するクラス
     */
    public function rowClass($isPublish, $record = [], $options = [])
    {

        // TODO 未実装のため代替措置
        // >>>
        return '';
        // <<<

        $options = array_merge([
            'class' => ['bca-table-listup__tbody-tr']
        ], $options);
        if (!$isPublish) {
            $classies = ['unpublish', 'disablerow'];
        } else {
            $classies = ['publish'];
        }
        if (!empty($options['class'])) {
            $classies = array_merge($classies, $options['class']);
        }

        // EVENT BcListTable.rowClass
        $event = $this->dispatchLayerEvent('rowClass', [
            'classies' => $classies,
            'record' => $record
        ], ['class' => 'BcListTable', 'plugin' => '']);
        if ($event !== false) {
            $classies = ($event->getResult() === null || $event->getResult() === true)? $event->getData('classies') : $event->getResult();
        }
        echo ' class="' . implode(' ', $classies) . '"';
    }

}
