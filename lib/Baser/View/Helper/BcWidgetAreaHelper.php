<?php
/**
 * BcWidgetAreaHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
class BcWidgetAreaHelper extends AppHelper {

/**
 * ウィジェットエリアを表示する
 *
 * @param $no ウィジェットエリアNO
 * @param array $options オプション
 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
 *  ※ その他のパラメータについては、View::element() を参照
 */
	public function show ($no, $options = array()) {

		$options = array_merge(array(
			'subDir' => true
		), $options);

		$WidgetArea = ClassRegistry::init('WidgetArea');
		$widgetArea = $WidgetArea->find('first', array('conditions' => array('WidgetArea.id' => $no)));

		if (empty($widgetArea['WidgetArea']['widgets'])) {
			return;
		}

		$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
		usort($widgets, array('BcWidgetAreaHelper', '_widgetSort'));

		foreach ($widgets as $key => $widget) {
			$key = key($widget);
			if ($widget[$key]['status']) {
				$params = array();
				$plugin = '';
				$params['widget'] = true;
				if (empty($_SESSION['Auth']['User']) && !isset($cache)) {
					$params['cache'] = '+1 month';
				}
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
	protected function _widgetSort($a, $b) {
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