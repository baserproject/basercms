<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 4.0.5
 * @license			http://basercms.net/license/index.html
 */

/**
 * リストテーブルヘルパ
 *
 * @package Baser.View.Helper
 */
class BcListTableHelper extends AppHelper {

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
 */
	public function setColumnNumber($number) {
		$this->_columnNumber = $number;
	}

/**
 * カラム数を取得する
 *
 * @return int カラム数
 */
	public function getColumnNumber() {
		return $this->_columnNumber;
	}

/**
 * リスト見出し発火
 *
 * @return string
 */
	public function dispatchShowHead() {
		$request = $this->_View->request;
		$id = Inflector::camelize($request->params['controller']) . '.' . Inflector::camelize($request->params['action']);
		$event = $this->dispatchEvent('showHead', ['id' => $id, 'fields' => []], ['class' => 'BcListTable', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			if(!empty($event->data['fields'])) {
				foreach($event->data['fields'] as $field) {
					$output .= "<th>" . $field . "</th>\n";
				}
				$this->_columnNumber += count($event->data['fields']);
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
	public function dispatchShowRow($data) {
		$request = $this->_View->request;
		$id = Inflector::camelize($request->params['controller']) . '.' . Inflector::camelize($request->params['action']);
		$event = $this->dispatchEvent('showRow', ['id' => $id, 'data' => $data, 'fields' => []], ['class' => 'BcListTable', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			if(!empty($event->data['fields'])) {
				foreach($event->data['fields'] as $field) {
					$output .= "<td>" . $field . "</td>\n";
				}
			}
		}
		return $output;
	}

	public function rowClass($isPublish, $record = []) {
		if (!$isPublish) {
			$classies = ['unpublish', 'disablerow'];
		} else {
			$classies = ['publish'];
		}
		$event = $this->dispatchEvent('rowClass', ['classies' => $classies, 'record' => $record], ['class' => 'BcListTable', 'plugin' => '']);
		if ($event !== false) {
			$classies = ($event->result === null || $event->result === true) ? $event->data['classies'] : $event->result;
		}
		echo ' class="' . implode(' ', $classies) . '"';
	}

}