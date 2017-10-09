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
 * @deprecated useViewCache 5.0.0 since 4.0.0
 * 	CakePHP3では、ビューキャッシュは廃止となる為、別の方法に移行する
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail', 'BcContents' => ['useForm' => true, 'useViewCache' => true]);

/**
 * モデル
 *
 * @var array
 * @access	public
 */
	public $uses = array('Page');

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

		// EVENT Pages.beforeAdd
		$event = $this->dispatchEvent('beforeAdd', array(
			'data' => $this->request->data
		));
		if ($event !== false) {
			$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
		}

		$data = $this->Page->save($this->request->data);
		if ($data) {

			// EVENT Pages.afterAdd
			$this->dispatchEvent('afterAdd', array(
				'data' => $data
			));
			
			$message = '固定ページ「' . $this->request->data['Content']['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode($data['Content']);
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
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->Page->recursive = 2;
			$this->request->data = $this->Page->read(null, $id);
			if(!$this->request->data) {
				$this->setMessage('無効な処理です。', true);
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
			}
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
			if ($data = $this->Page->save()) {
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
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		// 公開リンク
		$publishLink = '';
		if ($this->request->data['Content']['status']) {
			$publishLink = $this->request->data['Content']['url'];
		}
		// エディタオプション
		$editorOptions = ['editorDisableDraft' => false];
		if (!empty($this->siteConfigs['editor_styles'])) {
			App::uses('CKEditorStyleParser', 'Vendor');
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet'	=> 'default',
				'editorStyles' 		=> [
					'default' => $CKEditorStyleParser->parse($this->siteConfigs['editor_styles'])
				]
			]);
		}
		// ページテンプレートリスト
		$theme = [$this->siteConfigs['theme']];
		$site = BcSite::findById($this->request->data['Content']['site_id']);
		if(!empty($site) && $site->theme && $site->theme != $this->siteConfigs['theme']) {
			$theme[] = $site->theme;
		}
		$pageTemplateList = $this->Page->getPageTemplateList($this->request->data['Content']['id'], $theme);
		$this->set(compact('editorOptions', 'pageTemplateList', 'publishLink'));
		
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
		$this->_checkSubmitToken();
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
		$this->_checkSubmitToken();
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
		// CUSTOMIZE DELETE 2016/10/05 ryuring
		 $path = func_get_args();

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		if($this->request->params['Content']['alias_id']) {
			$urlTmp = $this->Content->field('url', ['Content.id' => $this->request->params['Content']['alias_id']]);
		} else {
			$urlTmp = $this->request->params['Content']['url'];
		}

		if($this->request->params['Site']['alias']) {
			$site = BcSite::findByUrl($urlTmp);
			if($site && ($site->alias == $this->request->params['Site']['alias'])) {
				$urlTmp = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/' . $this->request->params['Site']['name'] . '/', $urlTmp);
			}
		}

		$urlTmp = preg_replace('/^\//', '', $urlTmp);
		$path = explode('/', $urlTmp);

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

			// POSTパラメータのコードに含まれるscriptタグをそのままHTMLに出力するとブラウザによりXSSと判定される
			// 一度データをセッションに退避する
			if($this->BcContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->data['Content']['entity_id'];
				$this->Session->write($sessionKey,  $this->request->data);
				$query = [];
				if($this->request->query) {
					foreach($this->request->query as $key => $value) {
						$query[] = $key . '=' . $value;
					}
				}
				$this->redirect($this->request->here . '?' . implode('&', $query));
				return;
			}

			if($this->BcContents->preview == 'draft') {
				$uuid = $this->_createPreviewTemplate($this->request->data);
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . $this->ext);
				$previewCreated = true;
			}

		} else {

			// プレビューアクセス
			if($this->BcContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->params['Content']['entity_id'];
				$previewData = $this->Session->read($sessionKey);

				if(!is_null($previewData)) {
					$this->Session->delete($sessionKey);
					$uuid = $this->_createPreviewTemplate($previewData);
					$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . $this->ext);
					$previewCreated = true;
				}
			}

			// 草稿アクセス
			if($this->BcContents->preview == 'draft') {
				$data = $this->Page->find('first', ['conditions' => ['Page.id' => $this->request->params['Content']['entity_id']]]);
				$uuid = $this->_createPreviewTemplate($data, true);
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . $this->ext);
				$previewCreated = true;
			}
		}
		
		$page = $this->Page->find('first', ['conditions' => ['Page.id' => $this->request->params['Content']['entity_id']], 'recursive' => -1]);
		$template = $page['Page']['page_template'];
		$pagePath = implode('/', $path);
		if (!$template) {
			$ContentFolder = ClassRegistry::init('ContentFolder');
			$template = $ContentFolder->getParentTemplate($this->request->params['Content']['id'], 'page');
		}
		$this->set('pagePath', $pagePath);
		
		// <<<
		
		try {
			// CUSTOMIZE MODIFY 2014/07/02 ryuring
			// >>>
			//$this->render(implode('/', $path));
			// ---
			$this->render('templates/' . $template);
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
 * 一時ファイルとしてビューを保存
 * タグ中にPHPタグが入る為、ファイルに保存する必要がある
 *
 * @param mixed	$id 固定ページID
 * @return string uuid
 */
	protected function _createPreviewTemplate($data, $isDraft = false) {
		if(!$isDraft) {
			// postで送信される前提
			if(!empty($data['Page']['contents_tmp'])) {
				$contents = $data['Page']['contents_tmp'];
			} else {
				$contents = $data['Page']['contents'];
			}
		} else {
			$contents = $data['Page']['draft'];
		}
		$contents = $this->Page->addBaserPageTag(null, $contents, $data['Content']['title'], $data['Content']['description'], @$data['Page']['code']);
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
		$data = $this->Page->copy($this->request->data['entityId'], $this->request->data['parentId'], $this->request->data['title'], $user['id'], $this->request->data['siteId']);
		if ($data) {
			$message = '固定ページのコピー「' . $this->request->data['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode($data['Content']);
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
