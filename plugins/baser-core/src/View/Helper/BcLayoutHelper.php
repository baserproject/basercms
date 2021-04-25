<?php
// TODO : コード確認要
use BaserCore\Event\BcEventDispatcherTrait;

return;
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
     * Trait
     */
    use BcEventDispatcherTrait;

	/**
	 * コンテンツヘッダー発火
	 *
	 * @return string
	 */
	public function dispatchContentsHeader()
	{
		$request = $this->_View->request;
		$id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
		$event = $this->dispatchLayerEvent('contentsHeader', [
			'id' => $id,
			'out' => ''
		], ['class' => 'BcLayout', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			$output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
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
		$id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
		$event = $this->dispatchLayerEvent('contentsFooter', [
			'id' => $id,
			'out' => ''
		], ['class' => 'BcLayout', 'plugin' => '']);
		$output = '';
		if ($event !== false) {
			$output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
		}
		return $output;
	}

}
