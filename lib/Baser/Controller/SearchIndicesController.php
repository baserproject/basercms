<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('HttpSocket', 'Core.Network/Http');

/**
 * 検索インデックスコントローラー
 *
 * @package	Baser.Controller
 */
class SearchIndicesController extends AppController {

/**
 * クラス名
 *
 * @var array
 */
	public $name = 'SearchIndices';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('SearchIndex', 'Page');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcText', 'BcForm');

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// 認証設定
		$this->BcAuth->allow('search', 'mobile_search', 'smartphone_search', 'get_page_list_recursive');

		if (!empty($this->request->params['admin'])) {
			$this->subMenuElements = array('search_indices');
			$this->crumbs = array(
				array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
				array('name' => '検索インデックス管理', 'url' => array('controller' => 'search_indices', 'action' => 'index'))
			);
		}
	}

/**
 * コンテンツ検索
 *
 * @return void
 */
	public function search() {
		$datas = array();
		$query = array();

		$default = array('named' => array('num' => 10));
		$this->setViewConditions('SearchIndex', array('default' => $default, 'type' => 'get'));

		if (!empty($this->request->data['SearchIndex'])) {
			foreach ($this->request->data['SearchIndex'] as $key => $value) {
				$this->request->data['SearchIndex'][$key] = h($value);
			}
		}
		if (!empty($this->request->query['q'])) {
			$this->paginate = array(
				'conditions' => $this->_createSearchConditions($this->request->data),
				'order' => 'SearchIndex.priority DESC, SearchIndex.modified DESC, SearchIndex.id',
				'limit' => $this->passedArgs['num']
			);

			$datas = $this->paginate('SearchIndex');
			$query = $this->_parseQuery($this->request->query['q']);
		}
		$this->set('query', $query);
		$this->set('datas', $datas);
		$this->pageTitle = '検索結果一覧';
	}

/**
 * [MOBILE] コンテンツ検索
 */
	public function mobile_search() {
		$this->setAction('search');
	}
	
/**
 * [SMARTPHONE] コンテンツ検索
 */
	public function smartphone_search() {
		$this->setAction('search');
	}
	
/**
 * 検索キーワードを分解し配列に変換する
 *
 * @param string $query
 * @return array
 */
	protected function _parseQuery($query) {
		$query = str_replace('　', ' ', $query);
		if (strpos($query, ' ') !== false) {
			$query = explode(' ', $query);
		} else {
			$query = array($query);
		}
		return h($query);
	}

/**
 * 検索条件を生成する
 *
 * @param	array	$data
 * @return	array	$conditions
 * @access	protected
 */
	protected function _createSearchConditions($data) {
		$conditions = array('SearchIndex.status' => true);
		$query = '';
		unset($data['SearchIndex']['key']);
		unset($data['SearchIndex']['fields']);
		unset($data['SearchIndex']['_Token']);
		if (isset($data['SearchIndex']['q'])) {
			$query = $data['SearchIndex']['q'];
			unset($data['SearchIndex']['q']);
		}
		if (isset($data['SearchIndex']['c'])) {
			if ($data['SearchIndex']['c']) {
				$data['SearchIndex']['category'] = $data['SearchIndex']['c'];
			}
			unset($data['SearchIndex']['c']);
		}
		if (isset($data['SearchIndex']['m'])) {
			if ($data['SearchIndex']['m']) {
				$data['SearchIndex']['model'] = $data['SearchIndex']['m'];
			}
			unset($data['SearchIndex']['m']);
		}

		$conditions = am($conditions, $this->postConditions($data));

		if ($query) {
			$query = $this->_parseQuery($query);
			foreach ($query as $key => $value) {
				$conditions['and'][$key]['or'][] = array('SearchIndex.title LIKE' => "%{$value}%");
				$conditions['and'][$key]['or'][] = array('SearchIndex.detail LIKE' => "%{$value}%");
			}
		}

		return $conditions;
	}

/**
 * ページリストを取得する
 * 
 * @param mixid $parentCategoryId / '' / 0
 * @return type
 * @access public 
 */
	public function get_page_list_recursive($parentCategoryId = null, $recursive = null) {
		if (isConsole()) {
			$this->Page = ClassRegistry::init('Page');
		}
		return $this->__getPageListRecursive($parentCategoryId, $recursive);
	}

/**
 * ページリストを取得する（再帰）
 * TODO スマートフォン未対応
 * @param mixid $parentCategoryId / '' / 0
 * @return string
 * @access private 
 */
	private function __getPageListRecursive($parentCategoryId = null, $recursive = null, $level = 0) {
		if (empty($this->Page->PageCategory)) {
			// インストールの段階で呼出された場合 ClassRegistry::init() だと AppModelで初期化されてしまう
			$this->Page->PageCategory = new PageCategory(false, null, 'baser');
		}
		$direct = false;
		$currentAgentId = $this->Page->PageCategory->getAgentId(Configure::read('BcRequest.agent'));
		$mobileId = $this->Page->PageCategory->getAgentId('mobile');
		$smartphoneId = $this->Page->PageCategory->getAgentId('smartphone');
		if ($parentCategoryId === 0) {
			$direct = true;
			$parentCategoryId = null;
		} elseif (!$parentCategoryId && Configure::read('BcRequest.agent') == Configure::read('BcAgent.mobile.prefix')) {
			$parentCategoryId = $currentAgentId;
		}

		// ページリスト取得
		$conditions = array('Page.page_category_id' => $parentCategoryId);
		$conditions = am($conditions, $this->Page->getConditionAllowPublish());
		$pages = $this->Page->find('all', array(
			'conditions' => $conditions,
			'fields' => array('name', 'title', 'url'),
			'order' => 'Page.sort',
			'recursive' => -1,
			'cache' => false
		));

		foreach ($pages as $key => $page) {
			$pages[$key]['Page']['url'] = $page['Page']['url'] = preg_replace('/^\/mobile/', '/' . Configure::read('BcAgent.mobile.alias'), $page['Page']['url']);
			$pages[$key]['Page']['url'] = preg_replace('/^\/smartphone/', '/' . Configure::read('BcAgent.smartphone.alias'), $page['Page']['url']);
		}

		if (!$direct) {
			// カテゴリリスト取得
			$conditions = array('PageCategory.parent_id' => $parentCategoryId);
			if (!$parentCategoryId) {
				$conditions = am($conditions, array(array('PageCategory.id <>' => $mobileId), array('PageCategory.id <>' => $smartphoneId)));
			} elseif ($parentCategoryId == $mobileId) {
				$conditions = am($conditions, array(array('PageCategory.id <>' => ''), array('PageCategory.id <>' => $smartphoneId)));
			} elseif ($parentCategoryId == $smartphoneId) {
				$conditions = am($conditions, array(array('PageCategory.id <>' => ''), array('PageCategory.id <>' => $mobileId)));
			}

			$pageCategories = $this->Page->PageCategory->find('all', array(
				'conditions' => $conditions,
				'fields' => array('id', 'title'),
				'order' => 'PageCategory.lft',
				'recursive' => -1
			));
		} else {
			$pageCategories = array();
		}

		// カテゴリごとの子カテゴリ取得
		$children = array();
		if ($pageCategories) {
			$level++;
			foreach ($pageCategories as $key => $pageCategory) {
				$children = $this->__getPageListRecursive($pageCategory['PageCategory']['id'], $recursive, $level);
				if ($children && (is_null($recursive) || $recursive > $level)) {
					$pageCategories[$key]['children'] = $children;
				}
				if (isset($children['pages'])) {
					$paths = Hash::extract($children['pages'], '{n}.Page.name');
					if (in_array('index', $paths)) {
						$cats = $this->Page->PageCategory->getPath($pageCategory['PageCategory']['id'], array('name'), -1);
						$cats = Hash::extract($cats, '{n}.PageCategory.name');
						if ($cats) {
							$parentCategoryPath = '/' . implode('/', $cats) . '/';
						} else {
							$parentCategoryPath = '/';
						}
						$parentCategoryPath = preg_replace('/^\/mobile/', '/m', $parentCategoryPath);
						$pageCategories[$key]['PageCategory']['url'] = $parentCategoryPath . 'index';
					}
				}
			}
		}

		$result = array();
		if ($pages) {
			$result['pages'] = $pages;
		}
		if ($pageCategories) {
			$result['pageCategories'] = $pageCategories;
		}

		return $result;
	}

/**
 * [ADMIN] 検索インデックス
 * 
 * @return void
 */
	public function admin_index() {
		$this->pageTitle = '検索インデックス一覧';

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('SearchIndex', array('default' => $default));
		$conditions = $this->_createAdminIndexConditions($this->request->data);
		$this->paginate = array(
			'conditions' => $conditions,
			'fields' => array(),
			'order' => 'SearchIndex.priority DESC, SearchIndex.modified DESC, SearchIndex.id',
			'limit' => $this->passedArgs['num']
		);
		$this->set('datas', $this->paginate('SearchIndex'));

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$this->search = 'search_indices_index';
		$this->help = 'search_indices_index';
	}

/**
 * [ADMIN] 検索インデックス登録
 * 
 * TODO 2013/8/8 ryuring
 * この機能は、URLより、baserCMSで管理されたコンテンツのタイトルとコンテンツ本体を取得し、検索インデックスに登録する為の機能だったが、
 * CakePHP２より、Viewの扱いが変更となった（ClassRegistryで管理されなくなった）為、requestAction 時のタイトルを取得できなくなった。
 * よって機能自体を一旦廃止する事とする。
 * 実装の際は、自動取得ではなく、手動で、タイトルとコンテンツ本体等を取得する仕様に変更する。
 * 
 * @return	void
 * @access 	public
 */
	public function admin_add() {
		$this->pageTitle = '検索インデックス登録';

		if ($this->request->data) {
			$url = $this->request->data['SearchIndex']['url'];
			$url = str_replace(FULL_BASE_URL . $this->request->base, '', $url);

			if (!$this->SearchIndex->find('count', array('conditions' => array('SearchIndex.url' => $url)))) {

				// ルーティングのデフォルト設定を再読み込み（requestActionでルーティング設定がダブって登録されてしまう為）
				Router::reload();
				// URLのデータを取得
				try {
					$searchIndex = $this->requestAction($url, array('return' => 1));
				} catch (Exception $e) {
					$searchIndex = $e;
				}

				Router::reload();
				// 元の設定を復元
				Router::setRequestInfo($this->request);

				if (!is_a($searchIndex, 'Exception')) {
					$searchIndex = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $searchIndex);
				} elseif (preg_match('/\.html/', $url)) {
					App::uses('HttpSocket', 'Network/Http');
					$socket = new HttpSocket();
					// ※ Router::url() では、スマートURLオフの場合、/app/webroot/ 内のURLが正常に取得できない
					$HttpSocketResponse = $socket->get(siteUrl() . preg_replace('/^\//', '', $url));
					$code = $HttpSocketResponse->code;
					if ($code != 200) {
						unset($searchIndex);
					} else {
						if (preg_match('/<body>(.*?)<\/body>/is', $HttpSocketResponse->body, $matches)) {
							$searchIndex = $matches[1];
						} else {
							$searchIndex = '';
						}
					}
				} else {
					unset($searchIndex);
				}

				if (isset($searchIndex)) {
					$searchIndex = Sanitize::stripAll($searchIndex);
					$searchIndex = strip_tags($searchIndex);
					$data = array('SearchIndex' => array(
							'title'		=> $this->request->data['SearchIndex']['title'],
							'detail'	=> $searchIndex,
							'url'		=> $url,
							'type'		=> 'その他',
							'status'	=> true,
							'priority'	=> 0.5
					));
					$this->SearchIndex->create($data);
					if ($this->SearchIndex->save()) {
						$this->setMessage('検索インデックスに ' . $url . ' を追加しました。');
						$this->redirect(array('action' => 'index'));
					} else {
						$this->setMessage('保存中にエラーが発生しました。', true);
					}
				} else {
					$this->SearchIndex->invalidate('url', '入力したURLは存在しないか、検索インデックスに登録できるURLではありません。');
					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			} else {
				$this->SearchIndex->invalidate('url', '既に登録済のURLです。');
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}
		$this->help = 'search_indices_add';
	}

/**
 * [ADMIN] 検索インデックス削除　(ajax)
 *
 * @param	int		$id
 * @return	void
 * @access 	public
 */
	public function admin_ajax_delete($id = null) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		/* 削除処理 */
		if ($this->SearchIndex->delete($id)) {
			$message = '検索インデックスより NO.' . $id . ' を削除しました。';
			$this->SearchIndex->saveDbLog($message);
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] 検索インデックス削除
 *
 * @param	int		$id
 * @return	void
 * @access 	public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		/* 削除処理 */
		if ($this->SearchIndex->delete($id)) {
			$this->setMessage('検索インデックスより NO.' . $id . ' を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] 検索インデックス一括削除
 *
 * @param	int		$id
 * @return	void
 * @access 	public
 */
	protected function _batch_del($ids) {
		if ($ids) {

			foreach ($ids as $id) {

				/* 削除処理 */
				if ($this->SearchIndex->delete($id)) {
					$message = '検索インデックスより NO.' . $id . ' を削除しました。';
					$this->SearchIndex->saveDbLog($message);
				}
			}
		}
		return true;
	}

/**
 * [AJAX] 優先順位を変更する
 * 
 * @return boolean
 */
	public function admin_ajax_change_priority() {
		if ($this->request->data) {
			$this->SearchIndex->set($this->request->data);
			if ($this->SearchIndex->save()) {
				echo true;
			}
		}
		exit();
	}

/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param	array		$data
 * @return	string
 * @access	protected
 */
	protected function _createAdminIndexConditions($data) {
		if (empty($data['SearchIndex'])) {
			return array();
		}

		/* 条件を生成 */
		$conditions = array();

		$type = $data['SearchIndex']['type'];
		$category = $data['SearchIndex']['category'];
		$status = $data['SearchIndex']['status'];
		$keyword = $data['SearchIndex']['keyword'];

		unset($data['SearchIndex']['type']);
		unset($data['SearchIndex']['category']);
		unset($data['SearchIndex']['status']);
		unset($data['SearchIndex']['keyword']);
		unset($data['SearchIndex']['open']);
		if (!$data['SearchIndex']['priority']) {
			unset($data['SearchIndex']['priority']);
		}
		foreach ($data['SearchIndex'] as $key => $value) {
			if (preg_match('/priority_[0-9]+$/', $key)) {
				unset($data['SearchIndex'][$key]);
			}
		}

		if ($data['SearchIndex']) {
			$conditions = $this->postConditions($data);
		}

		if ($type) {
			$conditions['SearchIndex.type'] = $type;
		}
		if ($category) {
			if ($category == 'none') {
				$conditions['SearchIndex.category'] = '';
			} else {
				$conditions['SearchIndex.category'] = $category;
			}
		}
		if ($status != '') {
			$conditions['SearchIndex.status'] = $status;
		}
		if ($keyword) {
			$conditions['and']['or'] = array(
				'SearchIndex.title LIKE' => '%' . $keyword . '%',
				'SearchIndex.detail LIKE' => '%' . $keyword . '%'
			);
		}

		return $conditions;
	}

}
