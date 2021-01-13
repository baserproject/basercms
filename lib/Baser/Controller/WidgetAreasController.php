<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class WidgetAreasController
 *
 * ウィジェットエリアコントローラー
 *
 * @package Baser.Controller
 */
class WidgetAreasController extends AppController
{

	/**
	 * クラス名
	 * @var string
	 */
	public $name = 'WidgetAreas';

	/**
	 * コンポーネント
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler'];

	/**
	 * ヘルパー
	 * @var array
	 */
	public $helpers = ['BcForm'];

	/**
	 * モデル
	 * @var array
	 */
	public $uses = ['WidgetArea', 'Plugin'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = ['widget_areas'];

	/**
	 * WidgetAreasController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeRequest $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->crumbs = [
			['name' => __d('baser', 'ウィジェットエリア管理'), 'url' => ['controller' => 'widget_areas', 'action' => 'index']]
		];
	}

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		$this->BcAuth->allow('get_widgets');
		parent::beforeFilter();
	}

	/**
	 * 一覧
	 * @return void
	 */
	public function admin_index()
	{
		$this->pageTitle = __d('baser', 'ウィジェットエリア一覧');
		$widgetAreas = $this->WidgetArea->find('all');
		if ($widgetAreas) {
			foreach($widgetAreas as $key => $widgetArea) {
				$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
				if (!$widgets) {
					$widgetAreas[$key]['WidgetArea']['count'] = 0;
				} else {
					$widgetAreas[$key]['WidgetArea']['count'] = count($widgets);
				}
			}
		}
		$this->set('widgetAreas', $widgetAreas);
		$this->help = 'widget_areas_index';
	}

	/**
	 * 新規登録
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->pageTitle = __d('baser', '新規ウィジェットエリア登録');

		if ($this->request->data) {
			$this->WidgetArea->set($this->request->data);
			if (!$this->WidgetArea->save()) {
				$this->BcMessage->setError(__d('baser', '新しいウィジェットエリアの保存に失敗しました。'));
			} else {
				$this->BcMessage->setInfo(__d('baser', '新しいウィジェットエリアを保存しました。'));
				$this->redirect(['action' => 'edit', $this->WidgetArea->getInsertID()]);
			}
		}
		$this->help = 'widget_areas_form';
		$this->render('form');
	}

	/**
	 * 編集
	 *
	 * @return void
	 */
	public function admin_edit($id)
	{
		$this->pageTitle = __d('baser', 'ウィジェットエリア編集');

		$widgetArea = $this->WidgetArea->read(null, $id);
		if ($widgetArea['WidgetArea']['widgets']) {
			$widgetArea['WidgetArea']['widgets'] = $widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
			usort($widgetArea['WidgetArea']['widgets'], 'widgetSort');
			foreach($widgets as $widget) {
				$key = key($widget);
				$widgetArea[$key] = $widget[$key];
			}
		}
		$this->request->data = $widgetArea;

		$widgetInfos = [0 => ['title' => __d('baser', 'コアウィジェット'), 'plugin' => '', 'paths' => [BASER_VIEWS . 'Elements' . DS . 'admin' . DS . 'widgets']]];
		if (is_dir(APP . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'widgets')) {
			$widgetInfos[0]['paths'][] = APP . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'widgets';
		}

		$plugins = $this->Plugin->find('all', ['conditions' => ['status' => true]]);

		if ($plugins) {
			$pluginWidgets = [];
			$paths = App::path('Plugin');
			foreach($plugins as $plugin) {

				$pluginWidget['paths'] = [];
				foreach($paths as $path) {
					$path .= $plugin['Plugin']['name'] . DS . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'widgets';
					if (is_dir($path)) {
						$pluginWidget['paths'][] = $path;
					}
				}

				if (!$pluginWidget['paths']) {
					continue;
				}

				$pluginWidget['title'] = $plugin['Plugin']['title'] . 'ウィジェット';
				$pluginWidget['plugin'] = $plugin['Plugin']['name'];
				$pluginWidgets[] = $pluginWidget;
			}
			if ($pluginWidgets) {
				$widgetInfos = am($widgetInfos, $pluginWidgets);
			}
		}

		$this->set('widgetInfos', $widgetInfos);
		$this->help = 'widget_areas_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int ID
	 * @return void
	 */
	public function admin_ajax_delete($id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// メッセージ用にデータを取得
		$post = $this->WidgetArea->read(null, $id);

		/* 削除処理 */
		if ($this->WidgetArea->delete($id)) {
			$message = 'ウィジェットエリア「' . $post['WidgetArea']['name'] . '」 を削除しました。';
			exit(true);
		}
		clearViewCache('element_widget', '');
		exit();
	}

	/**
	 * 一括削除
	 *
	 * @param array $ids
	 * @return boolean
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				$data = $this->WidgetArea->read(null, $id);
				if ($this->WidgetArea->delete($id)) {
					$this->WidgetArea->saveDbLog('ウィジェットエリア: ' . $data['WidgetArea']['name'] . ' を削除しました。');
				}
			}
			clearViewCache('element_widget', '');
		}
		return true;
	}

	/**
	 * [AJAX] タイトル更新
	 *
	 * @return void
	 */
	public function admin_update_title()
	{
		if (!$this->request->data) {
			$this->notFound();
		}

		$this->WidgetArea->set($this->request->data);
		if ($this->WidgetArea->save()) {
			echo true;
		}
		exit();
	}

	/**
	 * [AJAX] ウィジェット更新
	 *
	 * @param int $widgetAreaId
	 * @return void
	 */
	public function admin_update_widget($widgetAreaId)
	{
		if (!$widgetAreaId || !$this->request->data) {
			exit();
		}

		$data = $this->request->data;
		if (isset($data['_Token'])) {
			unset($data['_Token']);
		}
		$dataKey = key($data);
		$widgetArea = $this->WidgetArea->read(null, $widgetAreaId);
		$update = false;
		if ($widgetArea['WidgetArea']['widgets']) {
			$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
			foreach($widgets as $key => $widget) {
				if (isset($data[$dataKey]['id']) && isset($widget[$dataKey]['id']) && $widget[$dataKey]['id'] == $data[$dataKey]['id']) {
					$widgets[$key] = $data;
					$update = true;
					break;
				}
			}
		} else {
			$widgets = [];
		}
		if (!$update) {
			$widgets[] = $data;
		}

		$widgetArea['WidgetArea']['widgets'] = BcUtil::serialize($widgets);

		$this->WidgetArea->set($widgetArea);
		if ($this->WidgetArea->save()) {
			echo true;
		}
		// 全てのキャッシュを削除しないと画面に反映できない。
		//clearViewCache('element_widget','');
		clearViewCache();

		exit();
	}

	/**
	 * 並び順を更新する
	 * @param int $widgetAreaId
	 * @return void
	 */
	public function admin_update_sort($widgetAreaId)
	{
		if (!$widgetAreaId || !$this->request->data) {
			exit();
		}
		$ids = explode(',', $this->request->data['WidgetArea']['sorted_ids']);
		$widgetArea = $this->WidgetArea->read(null, $widgetAreaId);
		if ($widgetArea['WidgetArea']['widgets']) {
			$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
			foreach($widgets as $key => $widget) {
				$widgetKey = key($widget);
				$widgets[$key][$widgetKey]['sort'] = array_search($widget[$widgetKey]['id'], $ids) + 1;
			}
			$widgetArea['WidgetArea']['widgets'] = BcUtil::serialize($widgets);
			$this->WidgetArea->set($widgetArea);
			if ($this->WidgetArea->save()) {
				echo true;
			}
		} else {
			echo true;
		}
		// 全てのキャッシュを削除しないと画面に反映できない。
		//clearViewCache('element_widget','');
		clearViewCache();
		exit();
	}

	/**
	 * [AJAX] ウィジェットを削除
	 *
	 * @param int $widgetAreaId
	 * @param int $id
	 * @return void
	 */
	public function admin_del_widget($widgetAreaId, $id)
	{
		$this->_checkSubmitToken();
		$widgetArea = $this->WidgetArea->read(null, $widgetAreaId);
		if (!$widgetArea['WidgetArea']['widgets']) {
			exit();
		}
		$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
		foreach($widgets as $key => $widget) {
			$type = key($widget);
			if ($id == $widget[$type]['id']) {
				unset($widgets[$key]);
				break;
			}
		}
		if ($widgets) {
			$widgetArea['WidgetArea']['widgets'] = BcUtil::serialize($widgets);
		} else {
			$widgetArea['WidgetArea']['widgets'] = '';
		}
		$this->WidgetArea->set($widgetArea);
		if ($this->WidgetArea->save()) {
			echo true;
		}
		// 全てのキャッシュを削除しないと画面に反映できない。
		//clearViewCache('element_widget','');
		clearViewCache();
		exit();
	}

	/**
	 * ウィジェットを並び替えた上で取得する
	 *
	 * @param int $id
	 * @return array $widgets
	 * @deprecated 4.1.0 since 4.0.0 BcWidgetAreaHelper::showWidgets() に移行
	 */
	public function get_widgets($id)
	{
		trigger_error(deprecatedMessage(__d('baser', 'メソッド：WidgetAreaController::get_widgets()'), '4.0.0', '4.1.0', __d('baser', 'このメソッドは非推奨となりました。BcWidgetAreaHelper::showWidgets() に移行してください。')), E_USER_DEPRECATED);
		$widgetArea = $this->WidgetArea->read(null, $id);
		if (empty($widgetArea['WidgetArea']['widgets'])) {
			return [];
		}

		$widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
		usort($widgets, 'widgetSort');
		return $widgets;
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
function widgetSort($a, $b)
{
	$aKey = key($a);
	$bKey = key($b);
	if ($a[$aKey]['sort'] == $b[$bKey]['sort']) {
		return 0;
	}
	if ($a[$aKey]['sort'] < $b[$bKey]['sort']) {
		return -1;
	}

	return 1;
}
