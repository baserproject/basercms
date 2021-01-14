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
 * フォームテーブルヘルパ
 *
 * @package Baser.View.Helper
 */
class BcFormTableHelper extends AppHelper
{

	/**
	 * テーブル前発火
	 *
	 * @return string
	 */
	public function dispatchBefore()
	{
		$event = $this->dispatchEvent('before', [
			'id' => $this->_View->BcForm->getId(),
			'out' => ''
		], ['class' => 'BcFormTable', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			$output = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}
		return $output;
	}

	/**
	 * テーブル後発火
	 *
	 * @return string
	 */
	public function dispatchAfter()
	{
		$event = $this->dispatchEvent('after', [
			'id' => $this->_View->BcForm->getId(),
			'out' => ''
		], ['class' => 'BcFormTable', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			$output = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}
		return $output;
	}

}
