<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ウィジェットエリアヘルパー
 *
 * @package Baser.View.Helper
 * @property BcAppView $_View
 */
class BcWidgetAreaHelper extends AppHelper
{

	/**
	 * ウィジェットエリアを表示する
	 *
	 * @param int $no ウィジェットエリアNO
	 * @param array $options オプション
	 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
	 *  ※ その他のパラメータについては、View::element() を参照
	 */
	public function show($no, $options = [])
	{

		$options = array_merge([
			'subDir' => true,
			'cache' => false,
		], $options);
		if ($options['cache'] === false) {
			unset($options['cache']);
		}
		$WidgetArea = ClassRegistry::init('WidgetArea');
		$widgetArea = $WidgetArea->find('first', ['conditions' => ['WidgetArea.id' => $no]]);

		if (empty($widgetArea['WidgetArea']['widgets'])) {
			return;
		}

		if ($this->_View->BcBaser->isAdminUser() && Configure::read('BcWidget.editLinkAtFront')) {
			$editLink = $this->url(['admin' => true, 'plugin' => null, 'controller' => 'widget_areas', 'action' => 'edit', $no]);
			$this->_View->BcBaser->element('admin/widget_link', ['editLink' => $editLink], ['subDir' => false]);
		}

		$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
		usort($widgets, ['BcWidgetAreaHelper', '_widgetSort']);

		foreach($widgets as $key => $widget) {
			$key = key($widget);
			if ($widget[$key]['status']) {
				$params = [];
				$plugin = '';
				$params['widget'] = true;
				$params = am($params, $widget[$key]);
				$params[$no . '_' . $widget[$key]['id']] = $no . '_' . $widget[$key]['id']; // 同じタイプのウィジェットでキャッシュを特定する為に必要
				if (!empty($params['plugin'])) {
					$plugin = Inflector::camelize($params['plugin']) . '.';
					unset($params['plugin']);
				}
				$this->_View->BcBaser->element($plugin . 'widgets/' . $widget[$key]['element'], $params, $options);
			}
		}

	}

	/**
	 * ウィジェットの並べ替えを行う
	 * usortのコールバックメソッド
	 *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function _widgetSort($a, $b)
	{
		$aKey = key($a);
		$bKey = key($b);
		if ($a[$aKey]['sort'] == $b[$bKey]['sort']) {
			return 0;
		}
		if ($a[$aKey]['sort'] < $b[$bKey]['sort']) {
			return -1;
		} else {
			return 1;
		}
	}

}
