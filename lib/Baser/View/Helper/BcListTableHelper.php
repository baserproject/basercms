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

}