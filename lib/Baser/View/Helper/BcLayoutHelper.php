<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * レイアウトヘルパ
 *
 * @package Baser.View.Helper
 */
class BcLayoutHelper extends AppHelper
{

	/**
	 * コンテンツヘッダー発火
	 *
	 * @return string
	 */
	public function dispatchContentsHeader()
	{
		$request = $this->_View->request;
		$id = Inflector::camelize($request->params['controller']) . '.' . Inflector::camelize($request->params['action']);
		$event = $this->dispatchEvent('contentsHeader', [
			'id' => $id,
			'out' => ''
		], ['class' => 'BcLayout', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			$output = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}
		return $output;
	}

	/**
	 * コンテンツフッター発火
	 *
	 * @return string
	 */
	public function dispatchContentsFooter()
	{
		$request = $this->_View->request;
		$id = Inflector::camelize($request->params['controller']) . '.' . Inflector::camelize($request->params['action']);
		$event = $this->dispatchEvent('contentsFooter', [
			'id' => $id,
			'out' => ''
		], ['class' => 'BcLayout', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			$output = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}
		return $output;
	}

}
