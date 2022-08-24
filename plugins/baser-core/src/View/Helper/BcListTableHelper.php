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
        $request = $this->_View->getRequest();
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
     * テーブルの行用のクラス文字列を生成する
     *
     * @param bool $isPublish 公開しているかどうか
     * @param array $record レコード
     * @param array $options オプション
     *    - `class` : 追加するクラス
     * @checked
     * @unitTest
     * @noTodo
     */
    public function rowClass($isPublish, $record = [], $options = [])
    {
        $options = array_merge([
            'class' => array_merge(
                ['bca-table-listup__tbody-tr'],
                ($isPublish)? ['publish'] : ['unpublish', 'disablerow']
            )
        ], $options);

        // EVENT BcListTable.rowClass
        $event = $this->dispatchLayerEvent('rowClass', [
            'class' => $options['class'],
            'record' => $record
        ], ['class' => 'BcListTable', 'plugin' => '']);
        if ($event !== false) {
            $options['class'] = ($event->getResult() === null || $event->getResult() === true)? $event->getData('class') : $event->getResult();
        }

        echo ' class="' . implode(' ', $options['class']) . '"';
    }

}
