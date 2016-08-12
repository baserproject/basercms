<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller.Component
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * baserCMS Contents Component
 *
 * 階層コンテンツと連携したフォーム画面を作成する為のコンポーネント
 *
 * 《役割》
 * - コンテンツ一覧へのパンくずを自動追加
 * - フロントエンドでコンテンツデータを設定
 * 		Controller / View にて、$this->request->params['Content'] で参照できる
 * - コンテンツ保存フォームを自動表示
 * - コンテンツ保存フォームのデータソースを設定
 * - コンテンツ保存フォームの初期値を設定
 *
 * @package Baser.Controller.Component
 */
class BcContentsComponent extends Component {

/**
 * Content 保存フォームをコントローラーで利用するかどうか
 * settings で指定する
 *
 * @var bool
 */
 	public $useForm = false;

/**
 * コンテンツ編集用のアクション名
 * 判定に利用
 * settings で指定する
 *
 * @var string
 */
	public $editAction = 'admin_edit';

/**
 * コンテンツタイプ
 * settings で指定する
 *
 * @var string
 */
	public $type = null;

/**
 * コントローラー
 *
 * @var Controller
 */
	protected $_Controller = null;

/**
 * プレビューモード
 *
 * @var string default Or alias
 */
	public $preview = null;

/**
 * Initialize
 *
 * @param Controller $controller Controller with components to initialize
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_Controller = $controller;
		$controller->uses[] = 'Content';
		if(!$this->type) {
			if($controller->plugin) {
				$this->type = $controller->plugin . '.' . $controller->modelClass;
			} else {
				$this->type = $controller->modelClass;
			}
		}
		// フロントで現在のページが関連している Content を設定する
		if(!BcUtil::isAdminSystem()) {
			if(!empty($controller->request->params['requested']) && !empty($controller->request->params['path'])) {
				$urlAry = $controller->request->params['path'];
				$url = '/' . implode('/', $urlAry);
				$data = $controller->Content->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => 0]);
				if($data) {
					$controller->request->params['Content'] = $data['Content'];
					$controller->request->params['Site'] = $data['Site'];
				}
			}
			if(!empty($controller->request->query['preview']) && !empty($controller->request->data['Content'])) {
				$controller->request->params['Content'] = $controller->request->data['Content'];
				$controller->Security->validatePost = false;
				$controller->Security->csrfCheck = false;
			}
			if(!empty($controller->request->params['Content'])) {
				// レイアウトテンプレート設定
				$controller->layout = $controller->request->params['Content']['layout_template'];
				if(!$controller->layout) {
					$controller->layout = $this->getParentLayoutTemplate($controller->request->params['Content']['id']);
				}
				// パンくず
				$controller->crumbs = $this->getCrumbs($controller->request->params['Content']['id']);
				// 説明文
				$controller->set('description', $controller->request->params['Content']['description']);
				$controller->pageTitle = $controller->request->params['Content']['title'];
			}
		}

		if(!empty($this->_Controller->request->query['preview'])) {
			$this->preview = $this->_Controller->request->query['preview'];
		}

		$items = Configure::read('BcContents.items');

		// シングルコンテンツの存在チェック
		$conditions = array();
		foreach($items as $name => $settings) {
			foreach ($settings as $type => $setting) {
				if(empty($setting['multiple'])) {
					$conditions['or'][] = array(
						'Content.plugin' => $name,
						'Content.type' => $type,
						'Content.alias_id'=> null
					);
				}
			}
		}
		$Content = ClassRegistry::init('Content');
		$Content->Behaviors->unload('SoftDelete');
		$_existContents = $Content->find('all', array('fields' => array('plugin', 'type', 'title'), 'conditions' => $conditions, 'recursive' => -1));
		$Content->Behaviors->load('SoftDelete');
		$existContents = array();
		foreach($_existContents as $existContent) {
			$existContents[$existContent['Content']['plugin'] . '.' . $existContent['Content']['type']] = $existContent['Content']['title'];
		}

		foreach($items as $name => $settings) {
			foreach ($settings as $type => $setting) {
				$setting['plugin'] = $name;
				$setting['type'] = $type;

				if(empty($setting['multiple'])) {
					if(array_key_exists($name . '.' . $type, $existContents)) {
						$setting['exists'] = true;
						$setting['existsTitle'] = $existContents[$name . '.' . $type];
					} else {
						$setting['exists'] = false;
						$setting['existsTitle'] = '';
					}
					$setting['multiple'] = false;
				}

				// routes
				foreach (array('manage', 'add', 'edit', 'delete', 'index', 'view', 'copy') as $action) {
					if (empty($setting['routes'][$action]) && $action != 'copy') {
						$setting['routes'][$action] = array('controller' => 'contents', 'action' => $action);
					}
				}
				foreach ($setting['routes'] as $action => $route) {
					$setting['routes'][$action] = Router::url($route);
					// index アクションの際、index が省略されてしまうので強制的に補完
					if($route['action'] == 'index') {
						// 規定以外の引数がないかチェック
						unset($route['admin'], $route['plugin'], $route['prefix'], $route['controller'], $route['action']);
						if(count($route) == 0) {
							$setting['routes'][$action] .= '/index';
						}
					}
				}

				// title
				if (empty($setting['title'])) {
					$setting['title'] = $type;
				}
				$this->settings['items'][$type] = $setting;
			}
		}
	}

/**
 * パンくず用のデータを取得する
 *
 * @param $id
 * @return array
 */
	public function getCrumbs($id) {
		$contents = $this->_Controller->Content->getPath($id, [], -1);
		unset($contents[count($contents) -1]);
		$crumbs = [];
		foreach($contents as $content) {
			if(!$content['Content']['site_root']) {
				$crumb = [
					'name'	=> $content['Content']['title'],
					'url'	=> $content['Content']['url']
				];
				$crumbs[] = $crumb;
			}
		}
		if(!empty($this->_Controller->request->query['preview'])) {
			$crumbs[count($crumbs) - 1] = array(
				'name' => $this->_Controller->request->params['Content']['title'],
				'url' => $this->_Controller->request->params['Content']['url']
			);
		}
		return $crumbs;
	}

/**
 * Content データを取得する
 *
 * @param int $entityId
 * @return array
 */
	public function getContent($entityId = null) {
		return $this->_Controller->Content->findByType($this->type, $entityId);
	}
	
/**
 * Before render
 *
 * @param Controller $controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		parent::beforeRender($controller);
		$controller->set('contentsSettings', $this->settings['items']);
		if(BcUtil::isAdminSystem()) {
			// パンくずをセット
			array_unshift($controller->crumbs, array('name' => 'コンテンツ一覧', 'url' => array('plugin' => null, 'controller' => 'contents', 'action' => 'index')));
			if($controller->subMenuElements && !in_array('contents', $controller->subMenuElements)) {
				array_unshift($controller->subMenuElements, 'contents');
			} else {
				$controller->subMenuElements =  ['contents'];	
			}
			if ($this->useForm && $controller->request->action == $this->editAction && !empty($controller->request->data['Content'])) {
				// フォームをセット
				$this->settingForm($controller, $controller->request->data['Content']['site_id'], $controller->request->data['Content']['id']);
			}
		}
	}

/**
 * コンテンツ保存フォームを設定する
 *
 * @param Controller $controller
 * @return void
 */
	public function settingForm(Controller $controller, $currentSiteId, $currentContentId = null) {

		// コントロールソースを設定
		$options = array();
		if($controller->name == 'ContentFolders') {
			$options['excludeId'] = $currentContentId;
		}
		$data = $controller->request->data;
		$controller->set('isPublishByParents', $controller->Content->isPublishByParents($data['Content']['id']));
		$templates = BcUtil::getTemplateList('Layouts', $this->_Controller->plugin, $this->_Controller->siteConfigs['theme']);
		if($data['Content']['id'] != 1) {
			$parentTemplate = $this->getParentLayoutTemplate($data['Content']['id']);
			array_unshift($templates, array('' => '親フォルダの設定に従う（' . $parentTemplate . '）'));
		}
		$data['Content']['name'] = urldecode($data['Content']['name']);
		$controller->set('layoutTemplates', $templates);
		$controller->set('parentContents', $controller->Content->getContentFolderList($currentSiteId, $options));
		$controller->set('authors', $controller->User->getUserList());
		$Site = ClassRegistry::init('Site');
		$site = $controller->Content->find('first', ['conditions' => ['Content.id' => $data['Content']['id']]]);
    	if(!is_null($site['Site']['main_site_id'])){
			$mainSiteId = $site['Site']['main_site_id'];
		} else {
			$mainSiteId = 0;
		}
		$controller->set('sites', ['' => '', 0 => 'Home'] + $controller->Content->Site->find('list', ['fields' => ['id', 'display_name']]));
		$data['Site'] = $site['Site'];
		$controller->set('mainSiteId', $mainSiteId);
		$controller->set('relatedContents', $Site->getRelatedContents($data['Content']['id']));
		$related = false;
		if(($data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['alias_id']) ||
			$data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['type'] == 'ContentFolder') {
			$related = true;
		}
		$controller->set('related', $related);
		$controller->request->data = $data;
		// フォームを読み込む為のイベントを設定
        App::uses('BcContentsEventListener', 'Event');
		CakeEventManager::instance()->attach(new BcContentsEventListener());

	}

/**
 * レイアウトテンプレートを取得する
 *
 * @param $id
 * @return string $parentTemplate|false
 */
	public function getParentLayoutTemplate($id) {
		if(!$id) {
			return false;
		}
		$contents = $this->_Controller->Content->getPath($id);
		$contents = array_reverse($contents);
		unset($contents[0]);
		if(!$contents) {
			return false;
		}
		$parentTemplates = Hash::extract($contents, '{n}.Content.layout_template');
		foreach($parentTemplates as $parentTemplate) {
			if($parentTemplate) {
				break;
			}
		}
		return $parentTemplate;
	}

}