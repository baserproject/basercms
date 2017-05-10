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
 * 検索ボックスヘルパ
 *
 * @package Baser.View.Helper
 */
class BcSearchBoxHelper extends AppHelper {

/**
 * 検索フィールド発火
 *
 * @return string
 */
	public function dispatchShowField() {
		$request = $this->_View->request;
		$id = Inflector::camelize($request->params['controller']) . '.' . Inflector::camelize($request->params['action']);
		$event = $this->dispatchEvent('showField', ['id' => $id, 'fields' => []], ['class' => 'BcSearchBox', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			if(!empty($event->data['fields'])) {
				foreach($event->data['fields'] as $field) {
					if(!empty($field['title'])) {
						$output .= "<span>" . $field['title'] . "</span>\n";
					}
					if(!empty($field['input'])) {
						$output .= "<span>" . $field['input'] . "</span>　\n";
					}
				}
			}
		}
		return $output;
	}

}