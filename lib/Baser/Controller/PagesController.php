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

/**
 * 固定ページコントローラー
 *
 * @package Baser.Controller
 * @property Page $Page
 * @property Content $Content
 * @property BcContentsComponent $BcContents
 */
class PagesController extends AppController {

/**
 * コントローラー名
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * ヘルパー
 *
 * @var array
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
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail', 'BcContents' => ['useForm' => true]);

/**
 * モデル
 *
 * @var array
 * @access	public
 */
	public $uses = array('Page', 'PageCategory');

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// 認証設定
		$this->BcAuth->allow('display');

		if (!empty($this->siteConfigs['editor']) && $this->siteConfigs['editor'] != 'none') {
			$this->helpers[] = $this->siteConfigs['editor'];
		}
	}

/**
 * 固定ページ情報登録
 * 
 * @return mixed json|false
 */
	public function admin_ajax_add() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->request->data['Page'] = $this->Page->getDefaultValue()['Page'];

		// EVENT Pages.beforeAdd
		$event = $this->dispatchEvent('beforeAdd', array(
			'data' => $this->request->data
		));
		if ($event !== false) {
			$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
		}
		
		if ($data = $this->Page->save($this->request->data)) {

			// EVENT Pages.afterAdd
			$this->dispatchEvent('afterAdd', array(
				'data' => $data
			));
			
			$message = '固定ページ「' . $this->request->data['Content']['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => $this->Page->id,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, $this->Page->validationErrors);
		}
		return false;
	}

/**
 * [ADMIN] 固定ページ情報編集
 *
 * @param int $id (page_id)
 * @return void
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Page->read(null, $id);
		} else {
			$isChangedStatus = $this->Content->isChangedStatus($id, $this->request->data);
			if (empty($this->request->data['Page']['page_type'])) {
				$this->request->data['Page']['page_type'] = 1;
			}

			// EVENT Pages.beforeEdit
			$event = $this->dispatchEvent('beforeEdit', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
			}

			$this->Page->set($this->request->data);
			if ($this->Page->validates()) {

				if ($data = $this->Page->save(null, false)) {
					// タイトル、URL、公開状態が更新された場合、全てビューキャッシュを削除する
					if ($isChangedStatus) {
						clearViewCache();
					} else {
						clearViewCache($this->request->data['Content']['url']);
					}

					// 完了メッセージ
					$this->setMessage('固定ページ「' . $this->request->data['Content']['name'] . '」を更新しました。', false, true);

					// EVENT Pages.afterEdit
					$this->dispatchEvent('afterEdit', [
						'data' => $data
					]);

					// 同固定ページへリダイレクト
					$this->redirect(['action' => 'edit', $id]);
				} else {
					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		if (empty($this->request->data['PageCategory']['id']) || $this->request->data['PageCategory']['name'] == 'mobile' || $this->request->data['PageCategory']['name'] == 'smartphone') {
			$currentCatOwnerId = $this->siteConfigs['root_owner_id'];
		} else {
			$currentCatOwnerId = $this->request->data['PageCategory']['owner_id'];
		}

		if ($this->request->data['Content']['url']) {
			$this->set('publishLink', $this->request->data['Content']['url']);
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

		$editorOptions = ['editorDisableDraft' => false];
		if (!empty($this->siteConfigs['editor_styles'])) {
			App::uses('CKEditorStyleParser', 'Vendor');
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorStyles = ['default' => $CKEditorStyleParser->parse($this->siteConfigs['editor_styles'])];
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet'	=> 'default',
				'editorStyles' 		=> $editorStyles
			]);
		}

		$this->set('currentCatOwnerId', $currentCatOwnerId);
		$this->set('previewId', $this->request->data['Page']['id']);
		$this->set('reflectMobile', $reflectMobile);
		$this->set('reflectSmartphone', $reflectSmartphone);
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('editorOptions', $editorOptions);
		if (!empty($this->request->data['Content']['title'])) {
			$this->pageTitle = '固定ページ情報編集：' . $this->request->data['Content']['title'];
		} else {
			$this->pageTitle = '固定ページ情報編集：' . Inflector::Classify($this->request->data['Content']['name']);
		}
		$this->help = 'pages_form';
		$this->render('form');
	}

/**
 * 削除
 * 
 * Controller::requestAction() で呼び出される
 *
 * @return bool
 */
	public function admin_delete() {
		if(empty($this->request->data['entityId'])) {
			return false;
		}
		if($this->Page->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
	}

/**
 * [ADMIN] 固定ページファイルを登録する
 *
 * @return void
 */
	public function admin_entry_page_files() {
		// 現在のテーマの固定ページファイルのパスを取得
		$pagesPath = APP . 'View' . DS . 'Pages';
		$result = $this->Page->entryPageFiles($pagesPath);
		clearAllCache();
		$this->setMessage($result['all'] . ' ページ中 ' . $result['insert'] . ' ページの新規登録、 ' . $result['update'] . ' ページの更新に成功しました。');
		$this->redirect(array('controller' => 'tools', 'action' => 'index'));
	}

/**
 * [ADMIN] 固定ページファイルを登録する
 *
 * @return void
 */
	public function admin_write_page_files() {
		if ($this->Page->createAllPageTemplate()) {
			$this->setMessage('固定ページテンプレートの書き出しに成功しました。');
		} else {
			$this->setMessage('固定ページテンプレートの書き出しに失敗しました。<br />表示できないページは固定ページ管理より更新処理を行ってください。', true);
		}
		clearViewCache();
		$this->redirect(array('controller' => 'tools', 'action' => 'index'));
	}

/**
 * ビューを表示する
 *
 * @param mixed
 * @return void
 */
	public function display() {
		$path = func_get_args();

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		if(!empty($this->request->params['requested'])) {
			if($this->request->params['Content']['alias_id']) {
				$urlTmp = $this->Content->field('url', ['Content.id' => $this->request->params['Content']['alias_id']]);
			} else {
				$urlTmp = $this->request->params['Content']['url'];
			}
			$urlTmp = preg_replace('/^\//', '', $urlTmp);
			$path = explode('/', $urlTmp);
		}

		if($this->request->params['Site']['alias']) {
			if($path[0] == $this->request->params['Site']['alias']) {
				$path[0] = $this->request->params['Site']['name'];
			}
		}

		$url = '/' . implode('/', $path);

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
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

		$agentAlias = Configure::read('BcRequest.agentAlias');
		if ($agentAlias) {
			$checkUrl = '/' . $agentAlias . $url;
		} else {
			$checkUrl = $url;
		}
		
		// キャッシュ設定
		// TODO 手法検討要
		// Consoleから requestAction で呼出された場合、getCacheTimeがうまくいかない
		// Consoleの場合は実行しない
		if (!isset($_SESSION['Auth'][Configure::read('BcAuthPrefix.admin.sessionKey')]) && !isConsole()) {
			$this->helpers[] = 'BcCache';
			$this->cacheAction = $this->Page->getCacheTime($checkUrl);
		}

		$this->subMenuElements = array('default');
		// <<<
		
		$this->set(array(
			'page' => $page,
			'subpage' => $subpage,
			'title_for_layout' => $titleForLayout
		));

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>

		$previewCreated = false;
		if($this->request->data) {
			if($this->BcContents->preview == 'default') {
				$this->set('previewTemplate', TMP . 'pages_preview_' . $this->createPreviewTemplate($this->request->data) . $this->ext);
				$previewCreated = true;
			}
		}
		
		// TODO コンテンツテンプレート未実装
		$template = '';
		$pagePath = implode('/', $path);
		if ($template) {
			$this->set('pagePath', $pagePath);
		} else {
			$template = $pagePath;
		}

		// <<<
		
		try {
			// CUSTOMIZE MODIFY 2014/07/02 ryuring
			// >>>
			//$this->render(implode('/', $path));
			// ---
			$this->render($template);
			if($previewCreated) {
				@unlink(TMP . 'pages_preview_' . $uuid . $this->ext);
			}
			// <<<
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

/**
 * プレビュー用テンプレートを生成する
 *
 * @param mixed	$id 固定ページID
 * @return string uuid
 */
	public function createPreviewTemplate($data) {
		// 一時ファイルとしてビューを保存
		// タグ中にPHPタグが入る為、ファイルに保存する必要がある
		$contents = $this->Page->addBaserPageTag(null, $data['Page']['contents_tmp'], $data['Content']['title'], $data['Content']['description'], $data['Page']['code']);
		$uuid = CakeText::uuid();
		$path = TMP . 'pages_preview_' . $uuid . $this->ext;
		$file = new File($path);
		$file->open('w');
		$file->append($contents);
		$file->close();
		unset($file);
		@chmod($path, 0666);
		return $uuid;
	}

/**
 * コピー
 *
 * @return bool
 */
	public function admin_ajax_copy() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$user = $this->BcAuth->user();
		if ($this->Page->copy($this->request->data['entityId'], $this->request->data['parentId'], $this->request->data['title'], $user['id'], $this->request->data['siteId'])) {
			$message = '固定ページのコピー「' . $this->request->data['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => $this->Page->id,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, $this->Page->validationErrors);
		}
		return false;
	}

/**
 * 一覧の表示用データをセットする
 * 
 * @return void
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
