<?php
/* SVN FILE: $Id$ */
/**
 * コンテンツコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * コンテンツコントローラー
 *
 * @package       cake
 * @subpackage    cake.baser.controllers
 */
class ContentsController extends AppController {
/**
 * クラス名
 *
 * @var		array
 * @access	public
 */
	var $name = 'Contents';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	var $uses = array('Content', 'Page');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ヘルパー
 *
 * @var		array
 * @access	public
 */
	var $helpers = array('TextEx', 'FormEx');
/**
 * beforeFilter
 *
 * @return	void
 * @access	public
 */
	function beforeFilter() {
		
		parent::beforeFilter();
		
		$this->Security->enabled = false;
		
		// 認証設定
		$this->AuthEx->allow('search','get_page_list_recursive');
		
		if(!empty($this->params['admin'])) {
			$this->subMenuElements = array('site_configs', 'contents');
			$this->navis = array('システム設定'=>'/admin/site_configs/form', '検索インデックス管理' => '/admin/contents/index');
		}
		
	}
/**
 * コンテンツ検索
 *
 * @return	void
 * @access	public
 */
	function search() {
		
		$datas = array();
		$query = array();

		$default = array('named' => array('num' => 10));
		$this->setViewConditions('Content', array('default' => $default, 'type' => 'get'));

		if(!empty($this->params['url']['q'])) {
		
			$this->paginate = array(
				'conditions'=> $this->_createSearchConditions($this->data),
				'order'		=> 'Content.priority DESC, Content.modified DESC, Content.id',
				'limit'		=> $this->passedArgs['num']
			);

			$datas = $this->paginate('Content');
			$query = $this->_parseQuery($this->params['url']['q']);
			
		}

		$this->set('query', $query);
		$this->set('datas', $datas);
		$this->pageTitle = '検索結果一覧';

	}
/**
 * 検索キーワードを分解し配列に変換する
 *
 * @param string $query
 * @return array
 * @access protected
 */
	function _parseQuery($query) {
		
		$query = str_replace('　', ' ', $query);
		if(strpos($query, ' ') !== false) {
			$query = explode(' ', $query);
		} else {
			$query = array($query);
		}
		return $query;
		
	}
/**
 * 検索条件を生成する
 *
 * @param	array	$data
 * @return	array	$conditions
 * @access	protected
 */
	function _createSearchConditions($data) {
		
		$conditions = array('Content.status' => true);
		$query = '';
		if(isset($data['Content']['q'])) {
			$query = $data['Content']['q'];
			unset($data['Content']['q']);
		}
		if(isset($data['Content']['c'])) {
			if($data['Content']['c']) {
				$data['Content']['category'] = $data['Content']['c'];
			}
			unset($data['Content']['c']);
		}
		if(isset($data['Content']['m'])) {
			if($data['Content']['m']) {
				$data['Content']['model'] = $data['Content']['m'];
			}
			unset($data['Content']['m']);
		}
		
		$conditions = am($conditions, $this->postConditions($data));

		if($query) {
			$query = $this->_parseQuery($query);
			foreach($query as $key => $value) {
				$conditions['and'][$key]['or'][] = array('Content.title LIKE' => "%{$value}%");
				$conditions['and'][$key]['or'][] = array('Content.detail LIKE' => "%{$value}%");
			}
		}
		
		return $conditions;
		
	}
/**
 * ページリストを取得する
 * 
 * @param mixid $parentCategoryId / '' / 0
 * @return type 
 */
	function get_page_list_recursive($parentCategoryId = null, $recursive = null) {
		
		return $this->__getPageListRecursive($parentCategoryId, $recursive);
		
	}
/**
 * ページリストを取得する（再帰）
 * 
 * @param mixid $parentCategoryId / '' / 0
 * @return string 
 */
	function __getPageListRecursive($parentCategoryId = null, $recursive = null, $level = 0) {

		$direct = false;
		$mobileId = $this->Page->PageCategory->getMobileId();
		if($parentCategoryId === 0) {
			$direct = true;
			$parentCategoryId = null;
		}elseif(!$parentCategoryId && Configure::read('Mobile.on')) {
			$parentCategoryId = $mobileId;
		}
		
		// ページリスト取得
		$conditions = array('Page.page_category_id' => $parentCategoryId);
		$conditions = am($conditions, $this->Page->getConditionAllowPublish());
		$pages = $this->Page->find('all', array(
			'conditions'=> $conditions,
			'fields'	=> array('name', 'title', 'url'), 
			'order'		=> 'Page.sort',
			'recursive' => -1,
			'cache'		=> false
		));

		foreach($pages as $key => $page) {
			$pages[$key]['Page']['url'] = preg_replace('/^\/mobile/', '/m', $page['Page']['url']);
		}
		
		if(!$direct) {
			// カテゴリリスト取得
			$conditions = array('PageCategory.parent_id' => $parentCategoryId);
			if(!$parentCategoryId) {
				$conditions['PageCategory.id <>'] = $mobileId;
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
		if($pageCategories) {
			$level++;
			foreach ($pageCategories as $key => $pageCategory) {
				$children = $this->__getPageListRecursive($pageCategory['PageCategory']['id'], $recursive, $level);
				if($children && (is_null($recursive) || $recursive > $level)) {
					$pageCategories[$key]['children'] = $children;
				}
				if(isset($children['pages'])) {
					$paths = Set::extract('/Page/name', $children['pages']);
					if(in_array('index', $paths)) {
						$cats = $this->Page->PageCategory->getPath($pageCategory['PageCategory']['id'], array('name'), -1);
						$cats = Set::extract('/PageCategory/name', $cats);
						if($cats) {
							$parentCategoryPath = '/'.implode('/', $cats).'/';
						} else {
							$parentCategoryPath = '/';
						}
						$parentCategoryPath = preg_replace('/^\/mobile/', '/m', $parentCategoryPath);
						$pageCategories[$key]['PageCategory']['url'] = $parentCategoryPath.'index';
					}
				}
			}
		}

		$result = array();
		if($pages) {
			$result['pages'] = $pages;
		}
		if($pageCategories) {
			$result['pageCategories'] = $pageCategories;
		}
		
		return $result;
		
	}
/**
 * [ADMIN] 検索インデックス
 * 
 * @return void
 * @access public
 */
	function admin_index() {
		
		$this->pageTitle = '検索インデックス コンテンツ一覧';

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('Content', array('default' => $default));
		$conditions = $this->_createAdminIndexConditions($this->data);
		$this->paginate = array(
				'conditions' => $conditions,
				'fields' => array(),
				'order' =>'Content.priority DESC, Content.modified DESC, Content.id',
				'limit' => $this->passedArgs['num']
		);
		$this->set('datas', $this->paginate('Content'));

	}
/**
 * [ADMIN] 検索インデックス登録
 * 
 * @return	void
 * @access 	public
 */
	function admin_add() {
		
		$this->pageTitle = '検索インデックス コンテンツ登録';
		
		if($this->data) {
			$url = $this->data['Content']['url'];
			$url = str_replace(FULL_BASE_URL.$this->base, '', $url);

			if(!$this->Content->find('count', array('conditions' => array('Content.url' => $url)))) {

				// ルーティングのデフォルト設定を再読み込み（requestActionでルーティング設定がダブって登録されてしまう為）
				Router::reload();
				// URLのデータを取得
				$content = $this->requestAction($url, array('return' => 1));
				$View =& ClassRegistry::getObject('View');
				// requestActionでインスタンス化されたViewを削除
				// （管理システムではなく公開ページのView情報になっている可能性がある為）
				ClassRegistry::removeObject('View');
				// ルーティングのデフォルト設定を再読み込み（元の設定に復元する為）
				Router::reload();
				// 元の設定を復元
				Router::setRequestInfo(array($this->params, array('base' => $this->base, 'webroot' => $this->webroot)));
				$title = '';
				
				if(!is_a($content, 'ErrorHandler')) {
					$content = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $content);
					$title = $View->pageTitle;
				} elseif (preg_match('/\.html/', $url)) {
					App::import('Core', 'HttpSocket');
					$socket = new HttpSocket();
					// ※ Router::url() では、スマートURLオフの場合、/app/webroot/ 内のURLが正常に取得できない
					$content = $socket->get(siteUrl().$url);
					$code = $socket->response['status']['code'];
					if($code != 200) {
						unset($content);
					} else {
						if(preg_match('/<title>([^<]+)<\/title>/', $content, $matches)) {
							$title = $matches[1];
							$content = preg_replace('/<title>[^<]+<\/title>/', '', $content);
						}
					}
				} else {
					unset($content);
				}
				
				if(isset($content)) {
					$content = Sanitize::stripAll($content);
					$content = strip_tags($content);
					$data = array('Content' => array(
						'title'		=> $title,
						'detail'	=> $content,
						'url'		=> $url,
						'type'		=> 'その他',
						'status'	=> true,
						'priority'	=> 0.5
					));
					$this->Content->create($data);
					if($this->Content->save()) {
						$this->Session->setFlash('検索インデックスに '.$url.' を追加しました。');
						$this->redirect('index');
					} else {
						$this->Session->setFlash('保存中にエラーが発生しました。');
					}
				} else {
					$this->Content->invalidate('url', '入力したURLは存在しないか、検索インデックスに登録できるURLではありません。');
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
				
			} else {
				$this->Content->invalidate('url', '既に登録済のURLです。');
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
			
		}
		
	}
/**
 * [ADMIN] 検索インデックス削除
 *
 * @param	int		$id
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		/* 削除処理 */
		if($this->Content->del($id)) {
			$message = '検索インデックスより NO.'.$id.' を削除しました。';
			$this->Session->setFlash($message);
			$this->Content->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * [AJAX] 優先順位を変更する
 * 
 * @return boolean
 * @access public
 */
	function admin_ajax_change_priority() {
		
		if($this->data) {
			$this->Content->set($this->data);
			if($this->Content->save()) {
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
	function _createAdminIndexConditions($data){
		
		/* 条件を生成 */
		$conditions = array();
		
		$type = $data['Content']['type'];
		$category = $data['Content']['category'];
		$status = $data['Content']['status'];
		$keyword = $data['Content']['keyword'];

		unset($data['Content']['type']);
		unset($data['Content']['category']);
		unset($data['Content']['status']);
		unset($data['Content']['keyword']);
		unset($data['Content']['open']);
		if(!$data['Content']['priority']) {
			unset($data['Content']['priority']);
		}
		foreach($data['Content'] as $key => $value) {
			if(preg_match('/priority_[0-9]+$/', $key)) {
				unset($data['Content'][$key]);
			}
		}

		if($data['Content']) {
			$conditions = $this->postConditions($data);
		}
		
		if($type) {
			$conditions['Content.type'] = $type;
		}
		if($category) {
			if($category == 'none') {
				$conditions['Content.category'] = '';
			} else {
				$conditions['Content.category'] = $category;
			}
		}
		if($status != '') {
			$conditions['Content.status'] = $status;
		}
		if($keyword) {
			$conditions['and']['or'] = array(
				'Content.title LIKE' => '%'.$keyword.'%',
				'Content.detail LIKE' => '%'.$keyword.'%'
			);
		}
		
		return $conditions;

	}

}
?>