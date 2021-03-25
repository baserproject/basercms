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

use \Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcFormTableHelper
 * @package BaserCore\View\Helper
 * @uses BcFormTableHelper
 */
class BcFormTableHelper extends Helper
{

	/**
	 * テーブル前発火
	 *
	 * @return string
     * @checked
	 */
	public function dispatchBefore()
	{

	    // TODO 未実装のため代替措置
	    // >>>
	    return '';
	    // <<<

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
     * @checked
	 */
	public function dispatchAfter()
	{

	    // TODO 未実装のため代替措置
	    // >>>
	    return '';
	    // <<<

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
