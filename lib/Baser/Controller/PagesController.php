<?php
/**
 * 固定ページコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * 固定ページコントローラー
 *
 * @package Baser.Controller
 */
class PagesController extends AppController {

/**
 * コントローラー名
 *
 * @var string
 * @access public
 */
	public $name = 'Pages';

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html', 'Session', 'BcGooglemaps', 
		'BcXml', 'BcText',
		'BcFreeze', 'BcPage'
	);

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail');

/**
 * モデル
 *
 * @var array
 * @access	public
 */
	public $uses = array('Page', 'PageCategory', 'SiteConfig');

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		
		parent::beforeFilter();

		// 認証設定
		$this->BcAuth->allow('display', 'mobile_display', 'smartphone_display');

		if (!empty($this->request->params['admin'])) {
			$this->crumbs = array(array('name' => '固定ページ管理', 'url' => array('controller' => 'pages', 'action' => 'index')));
		}

		$user = $this->BcAuth->user();
		if ($user) {
			$newCatAddable = $this->PageCategory->checkNewCategoryAddable(
				$user['user_group_id'], $this->checkRootEditable()
			);
			$this->set('newCatAddable', $newCatAddable);
		}

		if (!empty($this->siteConfigs['editor']) && $this->siteConfigs['editor'] != 'none') {
			$this->helpers[] = $this->siteConfigs['editor'];
		}
	}

/**
 * [ADMIN] ページリスト
 *
 * @return void
 * @access public
 */
	public function admin_index() {

		$this->SiteConfig->resetContentsSortLastModified();

		/* 画面情報設定 */
		$default = array(
			'named' => array('num' => $this->siteConfigs['admin_list_num'], 'sortmode' => 0, 'view_type' => 1, 'page_type' => 1),
			'Page' => array('page_category_id' => '', 'page_type' => 1)
		);
		$this->setViewConditions('Page', array('default' => $default));

		// 並び替えモードの場合は、強制的にsortフィールドで並び替える
		if ($this->passedArgs['sortmode']) {
			$this->passedArgs['sort'] = 'sort';
			$this->passedArgs['direction'] = 'asc';
		}

		if (!isset($this->passedArgs['page_type'])) {
			if (!isset($this->request->data['Page']['page_type'])) {
				$this->request->data['Page']['page_type'] = 1;
				$this->request->data['ViewSetting']['page_type'] = 1;
			}
		} else {
			$this->request->data['ViewSetting']['page_type'] = $this->passedArgs['page_type'];
			$this->request->data['Page']['page_type'] = $this->passedArgs['page_type'];
		}
		if (!isset($this->passedArgs['view_type'])) {
			$this->request->data['ViewSetting']['view_type'] = 1;
		} else {
			$this->request->data['ViewSetting']['view_type'] = $this->passedArgs['view_type'];
		}

		if ($this->request->data['ViewSetting']['view_type'] == 1) {

			$this->search = 'pages_index';
			$template = 'index';

			// 並び替えモードの際は、ページタイプ以外の検索条件を除外する
			if(!empty($this->passedArgs['sortmode'])) {
				$this->request->data = array(
					'ViewSetting' => $this->request->data['ViewSetting'],
					'Page' => array('page_type' => $this->request->data['Page']['page_type'])
				);
				$this->search = null;
			}

			// 検索条件
			$conditions = $this->_createAdminIndexConditions($this->request->data);

			$this->paginate = array(
				'conditions' => $conditions,
				'fields' => array(),
				'order' => 'Page.sort',
				'limit' => $this->passedArgs['num']
			);

			$datas = $this->paginate('Page');
			foreach ($datas as $key => $data) {
				$path = $this->Page->PageCategory->getPath($data['Page']['page_category_id'], array('PageCategory.name', 'PageCategory.title'));
				if ($path) {
					$titlePath = Hash::extract($path, '{n}.PageCategory.title');
					$datas[$key]['PageCategory']['title'] = implode(' > ', $titlePath);
				}
			}

			$this->set('datas', $datas);
			$this->_setAdminIndexViewData();

			if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
				Configure::write('debug', 0);
				$this->render('ajax_index');
				return;
			}

			/* 表示設定 */
			$pageCategories = array('' => '指定しない', 'noncat' => 'カテゴリなし');
			$_pageCategories = $this->getCategorySource($this->request->data['Page']['page_type']);
			if ($_pageCategories) {
				$pageCategories += $_pageCategories;
			}

			$this->set('search', 'pages_index');
			$this->set('pageCategories', $pageCategories);

			
		} else {
			switch ($this->request->data['ViewSetting']['page_type']) {
				case '1':
					$cateogryId = null;
					break;
				case '2':
					$cateogryId = 1;
					break;
				case '3':
					$cateogryId = 2;
					break;
			}
			$datas = $this->Page->treeList($cateogryId);
			$this->set('datas', $datas);
			if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
				Configure::write('debug', 0);
				$this->render('ajax_index_tree');
				return;
			}
			$template = 'index_tree';
		}

		if (Configure::read('BcApp.mobile') && (!isset($this->siteConfigs['linked_pages_mobile']) || !$this->siteConfigs['linked_pages_mobile'])) {
			$reflectMobile = true;
		} else {
			$reflectMobile = false;
		}
		if (Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || !$this->siteConfigs['linked_pages_smartphone'])) {
			$reflectSmartphone = true;
		} else {
			$reflectSmartphone = false;
		}
		$this->set('reflectMobile', $reflectMobile);
		$this->set('reflectSmartphone', $reflectSmartphone);

		$this->subMenuElements = array('pages', 'page_categories');
		$this->pageTitle = '固定ページ一覧';
		$this->help = 'pages_index';
		$this->render($template);
	}

/**
 * [ADMIN] 固定ページ情報登録
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		if (empty($this->request->data)) {
			$this->request->data = $this->Page->getDefaultValue();
			$this->request->data['Page']['page_type'] = 1;
		} else {

			/* 登録処理 */
			if ($this->request->data['Page']['page_type'] == 2 && !$this->request->data['Page']['page_category_id']) {
				$this->request->data['Page']['page_category_id'] = $this->PageCategory->getAgentId('mobile');
			} elseif ($this->request->data['Page']['page_type'] == 3 && !$this->request->data['Page']['page_category_id']) {
				$this->request->data['Page']['page_category_id'] = $this->PageCategory->getAgentId('smartphone');
			}
			$this->request->data['Page']['url'] = $this->Page->getPageUrl($this->request->data);
			
			/*			 * * Pages.beforeAdd ** */
			$event = $this->dispatchEvent('beforeAdd', array(
				'data' => $this->request->data
			));
			if ($event !== false) {
				$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
			}

			$this->Page->create($this->request->data);
			if ($this->Page->validates()) {

				if ($data = $this->Page->save($this->request->data, false)) {

					// キャッシュを削除する
					if ($this->Page->isPublish($this->request->data['Page']['status'], $this->request->data['Page']['publish_begin'], $this->request->data['Page']['publish_end'])) {
						clearViewCache();
					}

					// 完了メッセージ
					$this->setMessage('固定ページ「' . $this->request->data['Page']['name'] . '」を追加しました。', false, true);

					/*					 * * Pages.afterAdd ** */
					$this->dispatchEvent('afterAdd', array(
						'data' => $data
					));

					// 編集画面にリダイレクト
					$id = $this->Page->getInsertID();
					$this->redirect(array('controller' => 'pages', 'action' => 'edit', $id));
				} else {

					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			} else {

				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		if (Configure::read('BcApp.mobile') && (!isset($this->siteConfigs['linked_pages_mobile']) || !$this->siteConfigs['linked_pages_mobile'])) {
			$reflectMobile = true;
		} else {
			$reflectMobile = false;
		}
		if (Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || !$this->siteConfigs['linked_pages_smartphone'])) {
			$reflectSmartphone = true;
		} else {
			$reflectSmartphone = false;
		}

		$editorOptions = array('editorDisableDraft' => true);
		if (!empty($this->siteConfigs['editor_styles'])) {
			App::uses('CKEditorStyleParser', 'Vendor');
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorStyles = array('default' => $CKEditorStyleParser->parse($this->siteConfigs['editor_styles']));
			$editorOptions = array_merge($editorOptions, array(
				'editorStylesSet' => 'default',
				'editorStyles' => $editorStyles
			));
		}

		/* 表示設定 */
		$categories = $this->getCategorySource($this->request->data['Page']['page_type'], array('empty' => '指定しない', 'own' => true));
		$this->set('categories', $categories);
		$this->set('editable', true);
		$this->set('previewId', 'add_' . mt_rand(0, 99999999));
		$this->set('reflectMobile', $reflectMobile);
		$this->set('reflectSmartphone', $reflectSmartphone);
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('editorOptions', $editorOptions);
		$this->subMenuElements = array('pages', 'page_categories');
		$this->set('rootMobileId', $this->PageCategory->getAgentId('mobile'));
		$this->set('rootSmartphoneId', $this->PageCategory->getAgentId('smartphone'));
		$this->pageTitle = '新規固定ページ登録';
		$this->help = 'pages_form';
		$this->render('form');
	}

/**
 * [ADMIN] 固定ページ情報編集
 *
 * @param int $id (page_id)
 * @return void
 * @access public
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->Page->read(null, $id);
			$mobileIds = $this->PageCategory->getAgentCategoryIds('mobile');
			$smartphoneIds = $this->PageCategory->getAgentCategoryIds('smartphone');
			if (in_array($this->request->data['Page']['page_category_id'], $mobileIds)) {
				$this->request->data['Page']['page_type'] = 2;
			} elseif (in_array($this->request->data['Page']['page_category_id'], $smartphoneIds)) {
				$this->request->data['Page']['page_type'] = 3;
			} else {
				$this->request->data['Page']['page_type'] = 1;
			}
		} else {

			$before = $this->Page->find('first', array('conditions' => array('Page.id' => $id)));
			if (empty($this->request->data['Page']['page_type'])) {
				$this->request->data['Page']['page_type'] = 1;
			}
			/* 更新処理 */
			if ($this->request->data['Page']['page_type'] == 2 && !$this->request->data['Page']['page_category_id']) {
				$this->request->data['Page']['page_category_id'] = $this->PageCategory->getAgentId('mobile');
			} elseif ($this->request->data['Page']['page_type'] == 3 && !$this->request->data['Page']['page_category_id']) {
				$this->request->data['Page']['page_category_id'] = $this->PageCategory->getAgentId('smartphone');
			}
			$this->request->data['Page']['url'] = $this->Page->getPageUrl($this->request->data);

			/*			 * * Pages.beforeEdit ** */
			$event = $this->dispatchEvent('beforeEdit', array(
				'data' => $this->request->data
			));
			if ($event !== false) {
				$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
			}

			$this->Page->set($this->request->data);

			if ($this->Page->validates()) {

				if ($data = $this->Page->save(null, false)) {

					// タイトル、URL、公開状態が更新された場合、全てビューキャッシュを削除する
					$beforeStatus = $this->Page->isPublish($before['Page']['status'], $before['Page']['publish_begin'], $before['Page']['publish_end']);
					$afterStatus = $this->Page->isPublish($this->request->data['Page']['status'], $this->request->data['Page']['publish_begin'], $this->request->data['Page']['publish_end']);
					if ($beforeStatus != $afterStatus || $before['Page']['title'] != $this->request->data['Page']['title'] || $before['Page']['url'] != $this->request->data['Page']['url']) {
						clearViewCache();
					} else {
						clearViewCache($this->request->data['Page']['url']);
					}

					// 完了メッセージ
					$this->setMessage('固定ページ「' . $this->request->data['Page']['name'] . '」を更新しました。', false, true);

					/*					 * * Pages.afterEdit ** */
					$this->dispatchEvent('afterEdit', array(
						'data' => $data
					));

					// 同固定ページへリダイレクト
					$this->redirect(array('action' => 'edit', $id));
				} else {

					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$currentPageCategoryId = '';
		if (!empty($this->request->data['PageCategory']['id'])) {
			$currentPageCategoryId = $this->request->data['PageCategory']['id'];
		}

		if (empty($this->request->data['PageCategory']['id']) || $this->request->data['PageCategory']['name'] == 'mobile' || $this->request->data['PageCategory']['name'] == 'smartphone') {
			$currentCatOwnerId = $this->siteConfigs['root_owner_id'];
		} else {
			$currentCatOwnerId = $this->request->data['PageCategory']['owner_id'];
		}

		$categories = $this->getCategorySource($this->request->data['Page']['page_type'], array(
			'currentOwnerId' => $currentCatOwnerId,
			'currentPageCategoryId' => $currentPageCategoryId,
			'own' => true,
			'empty' => '指定しない'
		));

		$url = $this->Page->convertViewUrl($this->request->data['Page']['url']);

		if ($this->request->data['Page']['url']) {
			$this->set('publishLink', $url);
		}

		if (Configure::read('BcApp.mobile') && (!isset($this->siteConfigs['linked_pages_mobile']) || !$this->siteConfigs['linked_pages_mobile'])) {
			$reflectMobile = true;
		} else {
			$reflectMobile = false;
		}
		if (Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || !$this->siteConfigs['linked_pages_smartphone'])) {
			$reflectSmartphone = true;
		} else {
			$reflectSmartphone = false;
		}

		$editorOptions = array('editorDisableDraft' => false);
		if (!empty($this->siteConfigs['editor_styles'])) {
			App::uses('CKEditorStyleParser', 'Vendor');
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorStyles = array('default' => $CKEditorStyleParser->parse($this->siteConfigs['editor_styles']));
			$editorOptions = array_merge($editorOptions, array(
				'editorStylesSet' => 'default',
				'editorStyles' => $editorStyles
			));
		}

		$this->set('currentCatOwnerId', $currentCatOwnerId);
		$this->set('categories', $categories);
		$this->set('editable', $this->checkCurrentEditable($currentPageCategoryId, $currentCatOwnerId));
		$this->set('previewId', $this->request->data['Page']['id']);
		$this->set('reflectMobile', $reflectMobile);
		$this->set('reflectSmartphone', $reflectSmartphone);
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('editorOptions', $editorOptions);
		$this->set('url', $url);
		$this->set('mobileExists', $this->Page->agentExists('mobile', $this->request->data));
		$this->set('smartphoneExists', $this->Page->agentExists('smartphone', $this->request->data));
		$this->set('rootMobileId', $this->PageCategory->getAgentId('mobile'));
		$this->set('rootSmartphoneId', $this->PageCategory->getAgentId('smartphone'));
		$this->subMenuElements = array('pages', 'page_categories');
		if (!empty($this->request->data['Page']['title'])) {
			$this->pageTitle = '固定ページ情報編集：' . $this->request->data['Page']['title'];
		} else {
			$this->pageTitle = '固定ページ情報編集：' . Inflector::Classify($this->request->data['Page']['name']);
		}
		$this->help = 'pages_form';
		$this->render('form');
	}

/**
 * DBに保存されているURLをビュー用のURLに変換する
 * 
 * @param string $url
 * @return string
 */
	public function convertViewUrl($url) {
		$url = preg_replace('/\/index$/', '/', $url);
		if (preg_match('/^\/' . Configure::read('BcAgent.mobile.prefix') . '\//is', $url)) {
			$url = preg_replace('/^\/' . Configure::read('BcAgent.mobile.prefix') . '\//is', '/' . Configure::read('BcAgent.mobile.alias') . '/', $url);
		} elseif (preg_match('/^\/' . Configure::read('BcAgent.smartphone.prefix') . '\//is', $url)) {
			$url = preg_replace('/^\/' . Configure::read('BcAgent.smartphone.prefix') . '\//is', '/' . Configure::read('BcAgent.smartphone.alias') . '/', $url);
		}
		return $url;
	}

/**
 * [ADMIN] 固定ページ情報削除
 *
 * @param int $id (page_id)
 * @return void
 * @access public
 * @deprecated admin_ajax_delete で Ajax化
 */
	public function admin_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$page = $this->Page->read(null, $id);

		/* 削除処理 */
		if ($this->Page->delete($id)) {

			// 完了メッセージ
			$this->setMessage('固定ページ: ' . $page['Page']['name'] . ' を削除しました。', false, true);
		} else {

			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] 固定ページファイルを登録する
 *
 * @return void
 * @access public
 */
	public function admin_entry_page_files() {
		// 現在のテーマの固定ページファイルのパスを取得
		$pagesPath = getViewPath() . 'Pages';
		$result = $this->Page->entryPageFiles($pagesPath);
		clearAllCache();
		$this->setMessage($result['all'] . ' ページ中 ' . $result['insert'] . ' ページの新規登録、 ' . $result['update'] . ' ページの更新に成功しました。');
		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] 固定ページファイルを登録する
 *
 * @return void
 * @access public
 */
	public function admin_write_page_files() {
		if ($this->Page->createAllPageTemplate()) {
			$this->setMessage('固定ページテンプレートの書き出しに成功しました。');
		} else {
			$this->setMessage('固定ページテンプレートの書き出しに失敗しました。<br />表示できないページは固定ページ管理より更新処理を行ってください。', true);
		}
		clearViewCache();
		$this->redirect(array('action' => 'index'));
	}

/**
 * ビューを表示する
 *
 * @param mixed
 * @return void
 * @access public
 */
	public function display() {
		$path = func_get_args();

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		if (is_array($path) && count($path) == 1) {
			$path = explode('/', $path[0]);
		}

		$url = '/' . implode('/', $path);

		// モバイルディレクトリへのアクセスは Not Found
		if (isset($path[0]) && ($path[0] == Configure::read('BcAgent.mobile.prefix') || $path[0] == Configure::read('BcAgent.smartphone.prefix'))) {
			$this->notFound();
		}
		// <<<
		
		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $titleForLayout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$titleForLayout = Inflector::humanize($path[$count - 1]);
		}

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		$agentAlias = Configure::read('BcRequest.agentAlias');
		$agent = Configure::read('BcRequest.agent');

		if ($agentAlias) {
			if($agent && $this->siteConfigs['linked_pages_' . $agent]) {
				$checkUrl = $url;
			} else {
				$checkUrl = '/' . $agentAlias . $url;
			}
		} else {
			$checkUrl = $url;
		}

		// 固定ページを保存する際、非公開の場合でも、検索用データを作成時に
		// requestAction で呼ばれる為、requestAction時には無視する仕様とする
		if(empty($this->request->params['requested']) && !$this->Page->checkPublish($checkUrl)) {
			$this->notFound();
		}
		
		// キャッシュ設定
		// TODO 手法検討要
		// Consoleから requestAction で呼出された場合、getCacheTimeがうまくいかない
		// Consoleの場合は実行しない
		if (!isset($_SESSION['Auth']['User']) && !isConsole()) {
			$this->helpers[] = 'BcCache';
			$this->cacheAction = $this->Page->getCacheTime($checkUrl);
		}

		// ナビゲーションを取得
		$this->crumbs = $this->_getCrumbs($url);

		$this->subMenuElements = array('default');
		// <<<
		
		$this->set(array(
			'page' => $page,
			'subpage' => $subpage,
			'title_for_layout' => $titleForLayout
		));

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		$data = $this->Page->findByUrl($checkUrl);

		$template = $layout = $agent = '';

		if (Configure::read('BcRequest.agent')) {
			$agent = '_' . Configure::read('BcRequest.agent');
		}

		if (empty($data['PageCategory']['id'])) {
			if (!empty($this->siteConfigs['root_layout_template' . $agent])) {
				$layout = $this->siteConfigs['root_layout_template' . $agent];
			}
			if (!empty($this->siteConfigs['root_content_template' . $agent])) {
				$template = 'templates/' . $this->siteConfigs['root_content_template' . $agent];
			} else {
				$template = join('/', $path);
			}
		} else {
			if (!empty($data['PageCategory']['layout_template'])) {
				$layout = $data['PageCategory']['layout_template'];
			}
			if (!empty($data['PageCategory']['content_template'])) {
				$template = 'templates/' . $data['PageCategory']['content_template'];
			} else {
				$template = join('/', $path);
			}
		}

		if ($layout) {
			$this->layout = $layout;
		}

		if ($template) {
			$this->set('pagePath', implode('/', $path));
		} else {
			$template = implode('/', $path);
		}
		// <<<
		
		try {
			// CUSTOMIZE MODIFY 2014/07/02 ryuring
			// >>>
			//$this->render(implode('/', $path));
			// ---
			$this->render($template);
			// <<<
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

/**
 * パンくずナビ用の配列を取得する
 *
 * @param string	$url
 * @return array
 * @access protected
 */
	protected function _getCrumbs($url) {
		if (Configure::read('BcRequest.agent')) {
			$url = '/' . Configure::read('BcRequest.agentAlias') . $url;
		}

		// 直属のカテゴリIDを取得
		$pageCategoryId = $this->Page->field('page_category_id', array('Page.url' => $url));

		// 関連カテゴリを取得（関連固定ページも同時に取得）
		$pageCategorires = array();
		if ($pageCategoryId) {
			$pageCategorires = $this->Page->PageCategory->getPath($pageCategoryId, array('PageCategory.name', 'PageCategory.title'), 1);
		}

		$crumbs = array();
		if ($pageCategorires) {
			// index 固定ページの有無によりリンクを判別
			foreach ($pageCategorires as $pageCategory) {
				if (!empty($pageCategory['Page'])) {
					$categoryUrl = '';
					foreach ($pageCategory['Page'] as $page) {
						if ($page['name'] == 'index') {
							$categoryUrl = $page['url'];
							break;
						}
					}
					if ($categoryUrl) {
						$crumbs[] = array('name' => $pageCategory['PageCategory']['title'], 'url' => $categoryUrl);
					} else {
						$crumbs[] = array('name' => $pageCategory['PageCategory']['title'], 'url' => '');
					}
				} else {
					$crumbs[] = array('name' => $pageCategory['PageCategory']['title'], 'url' => '');
				}
			}
		}

		return $crumbs;
	}

/**
 * [MOBILE] ビューを表示する
 *
 * @param mixed
 * @return void
 * @access public
 */
	public function mobile_display() {
		$path = func_get_args();
		call_user_func_array(array($this, 'display'), $path);
	}

/**
 * [SMARTPHONE] ビューを表示する
 *
 * @param mixed
 * @return void
 * @access public
 */
	public function smartphone_display() {
		$path = func_get_args();
		call_user_func_array(array($this, 'display'), $path);
	}

/**
 * [ADMIN] 固定ページをプレビュー
 *
 * @param mixed	$id (blog_post_id)
 * @return void
 * @access public
 */
	public function admin_create_preview($id) {
		if (isset($this->request->data['Page'])) {
			$page = $this->request->data;
			if (empty($page['Page']['page_category_id']) && $page['Page']['page_type'] == 2) {
				$page['Page']['page_category_id'] = $this->Page->PageCategory->getAgentId('mobile');
			} elseif (empty($page['Page']['page_category_id']) && $page['Page']['page_type'] == 3) {
				$page['Page']['page_category_id'] = $this->Page->PageCategory->getAgentId('smartphone');
			}

			$page['Page']['url'] = $this->Page->getPageUrl($page);
		} else {
			$conditions = array('Page.id' => $id);
			$page = $this->Page->find($conditions);
		}

		if (!$page) {
			echo false;
			exit();
		}

		Cache::write('page_preview_' . $id, $page, '_cake_core_');

		$settings = Configure::read('BcAgent');
		foreach ($settings as $key => $setting) {
			if (preg_match('/^\/' . $setting['prefix'] . '\//is', $page['Page']['url'])) {
				Configure::write('BcRequest.agent', $key);
				Configure::write('BcRequest.agentPrefix', $setting['prefix']);
				Configure::write('BcRequest.agentAlias', $setting['alias']);
				break;
			}
		}

		// 一時ファイルとしてビューを保存
		// タグ中にPHPタグが入る為、ファイルに保存する必要がある
		$contents = $this->Page->addBaserPageTag(null, $page['Page']['contents'], $page['Page']['title'], $page['Page']['description'], $page['Page']['code']);
		$path = TMP . 'pages_preview_' . $id . $this->ext;
		$file = new File($path);
		$file->open('w');
		$file->append($contents);
		$file->close();
		unset($file);
		@chmod($path, 0666);
		echo true;
		exit();
	}

/**
 * プレビューを表示する
 *
 * @return void
 * @access public
 */
	public function admin_preview($id) {
		$page = Cache::read('page_preview_' . $id, '_cake_core_');
		
		// 直接previewにアクセスした場合
		if (empty($page) || !file_exists(TMP . 'pages_preview_' . $id . $this->ext)) {
			$page = $this->Page->find('first', array('conditions' => array('Page.id' => $id), 'recursive' => -1));
			$contents = $this->Page->addBaserPageTag(null, $page['Page']['contents'], $page['Page']['title'], $page['Page']['description'], $page['Page']['code']);
			$path = TMP . 'pages_preview_' . $id . $this->ext;
			$file = new File($path);
			$file->open('w');
			$file->append($contents);
			$file->close();
			unset($file);
		}

		$settings = Configure::read('BcAgent');
		foreach ($settings as $key => $setting) {
			if (preg_match('/^\/' . $setting['prefix'] . '\//is', $page['Page']['url'])) {
				Configure::write('BcRequest.agent', $key);
				Configure::write('BcRequest.agentPrefix', $setting['prefix']);
				Configure::write('BcRequest.agentAlias', $setting['alias']);
				break;
			}
		}
		$agent = Configure::read('BcRequest.agent');
		if ($agent) {
			$this->layoutPath = Configure::read('BcAgent.' . $agent . '.prefix');
			if ($agent == 'mobile') {
				$this->helpers[] = 'BcMobile';
			} elseif ($agent == 'smartphone') {
				$this->helpers[] = 'BcSmartphone';
			}
		} else {
			$this->layoutPath = '';
		}

		$url = $page['Page']['url'];
		$url = preg_replace('/^\/mobile\//is', '/' . Configure::read('BcAgent.mobile.alias') . '/', $url);
		$url = preg_replace('/^\/smartphone\//is', '/' . Configure::read('BcAgent.smartphone.alias') . '/', $url);
		$url = preg_replace('/^\//i', '', $url);

		$this->preview = true;
		$this->subDir = '';
		$this->request->params['prefix'] = '';
		$this->request->params['admin'] = '';
		$this->request->params['controller'] = 'pages';
		$this->request->params['action'] = 'display';

		$this->request->url = $url;
		Configure::write('BcRequest.pureUrl', $url);
		$this->here = $this->base . '/' . $url;
		$this->crumbs = $this->_getCrumbs('/' . $url);
		$this->theme = $this->siteConfigs['theme'];
		if(!empty($page['Page']['page_category_id'])) {
			$this->layout = $this->Page->PageCategory->field('layout_template', array('PageCategory.id' => $page['Page']['page_category_id']));
		}
		$this->render(TMP . 'pages_preview_' . $id . $this->ext);
		@unlink(TMP . 'pages_preview_' . $id . $this->ext);
		Cache::delete('page_preview_' . $id, '_cake_core_');
	}

/**
 * 並び替えを更新する [AJAX]
 *
 * @access public
 * @return boolean
 */
	public function admin_ajax_update_sort() {
		if ($this->request->data) {

			if($this->SiteConfig->isChangedContentsSortLastModified($this->request->data('listDisplayed'))) {
				$this->ajaxError(500, "コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。");
			}

			$this->setViewConditions('Page', array('action' => 'admin_index'));
			$conditions = $this->_createAdminIndexConditions($this->request->data);
			$this->Page->fileSave = false;
			$this->Page->contentSaving = false;
			if ($this->Page->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'], $conditions)) {
				clearViewCache();
				clearDataCache();
				$this->SiteConfig->updateContentsSortLastModified();
				echo true;
			} else {
				$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}
		exit();
	}

/**
 * 管理画面固定ページ一覧の検索条件を取得する
 *
 * @param array $data
 * @return string
 * @access protected
 */
	protected function _createAdminIndexConditions($data) {
		/* 条件を生成 */
		$conditions = array();
		$pageCategoryId = '';

		// 固定ページカテゴリ

		if (isset($data['Page']['page_category_id'])) {
			$pageCategoryId = $data['Page']['page_category_id'];
		}

		$name = '';
		$pageType = 1;
		if (isset($data['Page']['name'])) {
			$name = $data['Page']['name'];
		}
		if (isset($data['ViewSetting']['page_type'])) {
			$pageType = $data['ViewSetting']['page_type'];
		}

		unset($data['_Token']);
		unset($data['Page']['name']);
		unset($data['Page']['page_category_id']);
		unset($data['Sort']);
		unset($data['Page']['open']);
		unset($data['Page']['page_type']);
		unset($data['ViewSetting']);

		if ($pageType == 1 && !$pageCategoryId) {
			$pageCategoryId = 'pconly';
		}
		if ($pageType == 2 && !$pageCategoryId) {
			$pageCategoryId = $this->PageCategory->getAgentId('mobile');
		}
		if ($pageType == 3 && !$pageCategoryId) {
			$pageCategoryId = $this->PageCategory->getAgentId('smartphone');
		}

		// 条件指定のないフィールドを解除
		if(!empty($data['Page'])) {
			foreach ($data['Page'] as $key => $value) {
				if ($value === '') {
					unset($data['Page'][$key]);
				}
			}
		}

		if (!empty($data['Page'])) {
			$conditions = $this->postConditions($data);
		}

		if (isset($data['Page'])) {
			$data = $data['Page'];
		}

		// 固定ページカテゴリ
		if (!empty($pageCategoryId)) {

			if ($pageCategoryId == 'pconly') {

				// PCのみ
				$agentCategoryIds = am($this->PageCategory->getAgentCategoryIds('mobile'), $this->PageCategory->getAgentCategoryIds('smartphone'));
				if ($agentCategoryIds) {
					$conditions['or'] = array('not' => array('Page.page_category_id' => $agentCategoryIds),
						array('Page.page_category_id' => null));
				} else {
					$conditions['or'] = array(array('Page.page_category_id' => null));
				}
			} elseif ($pageCategoryId != 'noncat') {

				// カテゴリ指定
				// 子カテゴリも検索条件に入れる
				$pageCategoryIds = array($pageCategoryId);
				$children = $this->PageCategory->children($pageCategoryId);
				if ($children) {
					foreach ($children as $child) {
						$pageCategoryIds[] = $child['PageCategory']['id'];
					}
				}
				$conditions['Page.page_category_id'] = $pageCategoryIds;
			} elseif ($pageCategoryId == 'noncat') {

				//カテゴリなし
				if ($pageType == 1) {
					$conditions['or'] = array(array('Page.page_category_id' => ''), array('Page.page_category_id' => null));
				} elseif ($pageType == 2) {
					$conditions['Page.page_category_id'] = $this->PageCategory->getAgentId('mobile');
				} elseif ($pageType == 3) {
					$conditions['Page.page_category_id'] = $this->PageCategory->getAgentId('smartphone');
				}
			}
		} else {
			if (!Configure::read('BcApp.mobile') || !Configure::read('BcApp.smartphone')) {
				$conditions['or'] = array(
					array('Page.page_category_id' => ''),
					array('Page.page_category_id' => null));
			}
			if (!Configure::read('BcApp.mobile')) {
				$conditions['or'][] = array('Page.page_category_id <>' => $this->PageCategory->getAgentId('mobile'));
			}
			if (!Configure::read('BcApp.smartphone')) {
				$conditions['or'][] = array('Page.page_category_id <>' => $this->PageCategory->getAgentId('smartphone'));
			}
		}

		if ($name) {
			$conditions['and']['or'] = array(
				'Page.name LIKE' => '%' . $name . '%',
				'Page.title LIKE' => '%' . $name . '%'
			);
		}

		return $conditions;
	}

/**
 * PC用のカテゴリIDを元にモバイルページが作成する権限があるかチェックする
 * 
 * @param int $type
 * @param int $id
 * @return boolean
 * @access public
 */
	public function admin_check_agent_page_addable($type, $id = null) {
		$user = $this->BcAuth->user();
		$userGroupId = $user['user_group_id'];
		$result = false;
		while (true) {
			$agentId = $this->PageCategory->getAgentRelativeId($type, $id);
			if ($agentId) {
				if ($agentId == 1 || $agentId == 2) {
					$ownerId = $this->siteConfigs['root_owner_id'];
				} else {
					$pageCategory = $this->PageCategory->find('first', array(
						'conditions' => array('PageCategory.id' => $agentId),
						'field' => array('owner_id')
					));
					$ownerId = $pageCategory['PageCategory']['owner_id'];
				}
				if ($ownerId) {
					if ($userGroupId == $ownerId) {
						$result = true;
					} else {
						$result = false;
					}
				} else {
					$result = true;
				}
				break;
			}
			$pageCategory = $this->PageCategory->find('first', array(
				'conditions' => array('PageCategory.id' => $id),
				'field' => array('parent_id')
			));

			$id = $pageCategory['PageCategory']['parent_id'];
		}

		if ($result) {
			echo 1;
		}
		exit();
	}

/**
 * [AJAX] カテゴリリスト用のデータを取得する
 * 
 * @param int $type
 * @param boolean $empty
 * @return array
 * @access public
 */
	public function admin_ajax_category_source($type) {
		$option = array();
		if (!empty($this->request->data['Option'])) {
			$option = $this->request->data['Option'];
		}
		$categorySource = $this->getCategorySource($type, $option);
		$this->set('categorySource', $categorySource);
	}

/**
 * カテゴリリスト用のデータを取得する
 * 
 * @param int $type
 * @param int $options
 * @param boolean $empty
 * @return array
 * @access public
 */
	public function getCategorySource($type, $options = array()) {
		$editable = true;

		if (isset($options['currentPageCategoryId']) && isset($options['currentOwnerId'])) {
			$editable = $this->checkCurrentEditable($options['currentPageCategoryId'], $options['currentOwnerId']);
		}

		$mobileId = $this->Page->PageCategory->getAgentId('mobile');
		$smartphoneId = $this->Page->PageCategory->getAgentId('smartphone');

		switch ($type) {
			case '1': // PC
				$parentId = '';
				$excludeParentId = array($mobileId, $smartphoneId);
				break;
			case '2': // モバイル
				$parentId = $mobileId;
				$excludeParentId = '';
				break;
			case '3': // スマホ
				$parentId = $smartphoneId;
				$excludeParentId = '';
				break;
			default:
				$parentId = '';
				$excludeParentId = '';
		}

		$_options = array(
			'rootEditable'		=> $this->checkRootEditable(),
			'pageEditable'		=> $editable,
			'agentRoot'			=> false,
			'parentId'			=> $parentId,
			'excludeParentId'	=> $excludeParentId
		);
		$_options['currentPageCategoryId'] = 58;
		if (isset($options['currentPageCategoryId'])) {
			$_options['pageCategoryId'] = $options['currentPageCategoryId'];
		}
		if (!empty($options['excludeParentId'])) {
			if ($_options['excludeParentId']) {
				$_options['excludeParentId'][] = $options['excludeParentId'];
			} else {
				$_options['excludeParentId'] = $options['excludeParentId'];
			}
		}
		if (isset($options['empty'])) {
			$_options['empty'] = $options['empty'];
		}
		if (!empty($options['own'])) {
			$user = $this->BcAuth->user();
			$_options['userGroupId'] = $user['user_group_id'];
		}

		return $this->Page->getControlSource('page_category_id', $_options);
	}

/**
 * 現在のページが書込可能かチェックする
 * 
 * @param int $pageCategoryId
 * @param int $ownerId
 * @return boolean
 * @access public
 */
	public function checkCurrentEditable($pageCategoryId, $ownerId) {
		$user = $this->BcAuth->user();

		$mobileId = $this->Page->PageCategory->getAgentId('mobile');
		$smartphoneId = $this->Page->PageCategory->getAgentId('smartphone');

		if (!$pageCategoryId || $pageCategoryId == $mobileId || $pageCategoryId == $smartphoneId) {
			$currentCatOwner = $this->siteConfigs['root_owner_id'];
		} else {
			$currentCatOwner = $ownerId;
		}

		return ($currentCatOwner == $user['user_group_id'] ||
			$user['user_group_id'] == Configure::read('BcApp.adminGroupId') || !$currentCatOwner);
	}

/**
 * 一括削除
 * 
 * @param array $ids
 * @return boolean
 * @access protected
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$data = $this->Page->read(null, $id);
				if ($this->Page->delete($id)) {
					$this->Page->saveDbLog('固定ページ: ' . $data['Page']['name'] . ' を削除しました。');
				}
			}
		}
		return true;
	}

/**
 * [ADMIN] 無効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	public function admin_ajax_unpublish($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->Page->validationErrors);
		}
		exit();
	}

/**
 * [ADMIN] 有効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	public function admin_ajax_publish($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->Page->validationErrors);
		}
		exit();
	}

/**
 * 一括公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	protected function _batch_publish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_changeStatus($id, true);
			}
		}
		return true;
	}

/**
 * 一括非公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	protected function _batch_unpublish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_changeStatus($id, false);
			}
		}
		return true;
	}

/**
 * ステータスを変更する
 * 
 * @param int $id
 * @param boolean $status
 * @return boolean 
 */
	protected function _changeStatus($id, $status) {
		$statusTexts = array(0 => '非公開', 1 => '公開');
		$data = $this->Page->find('first', array('conditions' => array('Page.id' => $id), 'recursive' => -1));
		$data['Page']['status'] = $status;
		if ($status) {
			$data['Page']['publish_begin'] = '';
			$data['Page']['publish_end'] = '';
		}
		$this->Page->set($data);
		if ($this->Page->save()) {
			clearViewCache($data['Page']['url']);
			$statusText = $statusTexts[$status];
			$this->Page->saveDbLog('固定ページ「' . $data['Page']['name'] . '」 を' . $statusText . 'にしました。');
			return true;
		} else {
			return false;
		}
	}

/**
 * [ADMIN] 固定ページ情報削除
 *
 * @param int $id (page_id)
 * @return void
 * @access public
 */
	public function admin_ajax_delete($id = null) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$page = $this->Page->read(null, $id);

		if ($this->Page->delete($id)) {
			clearViewCache($page['Page']['url']);
			$this->Page->saveDbLog('固定ページ: ' . $page['Page']['name'] . ' を削除しました。');
			echo true;
		}
		exit();
	}

/**
 * [ADMIN] 固定ページコピー
 * 
 * @param int $id 
 * @return void
 * @access public
 */
	public function admin_ajax_copy($id = null) {
		$result = $this->Page->copy($id);
		if ($result) {
			$result['Page']['id'] = $this->Page->getInsertID();
			$this->setViewConditions('Page', array('action' => 'admin_index'));
			$this->_setAdminIndexViewData();
			ClassRegistry::removeObject('View'); // Page 保存時に requestAction で 固定ページテンプレート生成用に初期化される為
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->Page->validationErrors);
		}
	}

/**
 * 一覧の表示用データをセットする
 * 
 * @return void
 * @access protected
 */
	protected function _setAdminIndexViewData() {
		$user = $this->BcAuth->user();
		$allowOwners = array();
		if (!empty($user)) {
			$allowOwners = array('', $user['user_group_id']);
		}
		if (!isset($this->passedArgs['sortmode'])) {
			$this->passedArgs['sortmode'] = false;
		}
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('allowOwners', $allowOwners);
		$this->set('sortmode', $this->passedArgs['sortmode']);
	}

}
