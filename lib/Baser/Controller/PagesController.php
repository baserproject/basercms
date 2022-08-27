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
 * Class PagesController
 *
 * 固定ページコントローラー
 *
 * @package Baser.Controller
 * @property Page $Page
 * @property Content $Content
 * @property BcContentsComponent $BcContents
 */
class PagesController extends AppController
{

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
	public $helpers = [
		'Html', 'Session', 'BcGooglemaps',
		'BcXml', 'BcText',
		'BcFreeze', 'BcPage'
	];

	/**
	 * コンポーネント
	 *
	 * @var array
	 * @deprecated useViewCache 5.0.0 since 4.0.0
	 *    CakePHP3では、ビューキャッシュは廃止となるため、別の方法に移行する
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail', 'BcContents' => ['useForm' => true, 'useViewCache' => true]];

	/**
	 * モデル
	 *
	 * @var array
	 * @access    public
	 */
	public $uses = ['Page', 'Content'];

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();

		// 認証設定
		$this->BcAuth->allow('display');

		if (empty($this->siteConfigs['editor']) || $this->siteConfigs['editor'] === 'none') {
			return;
		}
		$this->helpers[] = $this->siteConfigs['editor'];
	}

	/**
	 * 固定ページ情報登録
	 *
	 * @return mixed json|false
	 */
	public function admin_ajax_add()
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// EVENT Pages.beforeAdd
		$event = $this->dispatchEvent('beforeAdd', [
			'data' => $this->request->data
		]);
		if ($event !== false) {
			$this->request->data = $event->result === true? $event->data['data'] : $event->result;
		}

		$data = $this->Page->save($this->request->data);
		if (!$data) {
			$this->ajaxError(500, $this->Page->validationErrors);
			return false;
		}
		// EVENT Pages.afterAdd
		$this->dispatchEvent('afterAdd', [
			'data' => $data
		]);
		$site = BcSite::findById($data['Content']['site_id']);
		$url = $this->Content->getUrl($data['Content']['url'], true, $site->useSubDomain);
		$message = sprintf(
			__d(
				'baser',
				"固定ページ「%s」を追加しました。\n%s"
			),
			$this->request->data['Content']['title'],
			urldecode($url)
		);
		$this->BcMessage->setSuccess($message, true, false);
		return json_encode($data['Content']);
	}

	/**
	 * 固定ページ新規追加
	 *
	 * @param int $parentContentId 親コンテンツID
	 * @return void
	 */
	public function admin_add($parentContentId, $name = '')
	{
		if (!$parentContentId) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
		}

		if ($this->request->is(['post', 'put'])) {
			if ($this->Page->isOverPostSize()) {
				$this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
				$this->redirect(['contents', 'action' => 'index']);
			}

			// EVENT Pages.beforeAdd
			$event = $this->dispatchEvent('beforeAdd', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true? $event->data['data'] : $event->result;
			}

			$this->Page->set($this->request->data);
			if ($data = $this->Page->save()) {

				// 完了メッセージ
				$site = BcSite::findById($data['Content']['site_id']);
				$url = $this->Content->getUrl($data['Content']['url'], true, $site->useSubDomain);
				$this->BcMessage->setSuccess(sprintf(__d('baser', "固定ページ「%s」を登録しました。\n%s"), $data['Content']['name'], urldecode($url)));

				// EVENT Pages.afterAdd
				$this->dispatchEvent('afterAdd', [
					'data' => $data
				]);

				// 同固定ページへリダイレクト
				$this->redirect(['action' => 'edit', $this->Page->id]);
				return;
			}

			$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
		} else {
			$this->request->data = $this->Page->getDefaultValue($parentContentId, $name);
			if(!$this->request->data) {
				$this->BcMessage->setError(__d('baser', '無効な処理です。'));
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
				return;
			}
		}

		// エディタオプション
		$editorOptions = ['editorDisableDraft' => true];
		if (!empty($this->siteConfigs['editor_styles'])) {
			App::uses('CKEditorStyleParser', 'Vendor');
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet' => 'default',
				'editorStyles' => [
					'default' => $CKEditorStyleParser->parse($this->siteConfigs['editor_styles'])
				]
			]);
		}

		// ページテンプレートリスト
		$theme = [$this->siteConfigs['theme']];
		$site = BcSite::findById($this->request->data['Content']['site_id']);
		if (!empty($site) && $site->theme && $site->theme != $this->siteConfigs['theme']) {
			$theme[] = $site->theme;
		}
		$pageTemplateList = [];
		$publishLink = '';
		$this->set(compact('editorOptions', 'pageTemplateList', 'publishLink'));

		$this->pageTitle = __d('baser', '固定ページ情報新規追加');
		$this->help = 'pages_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 固定ページ情報編集
	 *
	 * @param int $id (page_id)
	 * @return void
	 */
	public function admin_edit($id)
	{
		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
		}

		if ($this->request->is(['post', 'put'])) {
			if ($this->Page->isOverPostSize()) {
				$this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
				$this->redirect(['action' => 'edit', $id]);
			}
			$isChangedStatus = $this->Content->isChangedStatus($id, $this->request->data);

			// EVENT Pages.beforeEdit
			$event = $this->dispatchEvent('beforeEdit', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true? $event->data['data'] : $event->result;
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
				$site = BcSite::findById($data['Content']['site_id']);
				$url = $this->Content->getUrl($data['Content']['url'], true, $site->useSubDomain);
				$this->BcMessage->setSuccess(sprintf(__d('baser', "固定ページ「%s」を更新しました。\n%s"), rawurldecode($data['Content']['name']), urldecode($url)));

				// EVENT Pages.afterEdit
				$this->dispatchEvent('afterEdit', [
					'data' => $data
				]);

				// 同固定ページへリダイレクト
				$this->redirect(['action' => 'edit', $id]);
				return;
			}

			$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
		} else {
			$this->Page->recursive = 2;
			$this->request->data = $this->Page->read(null, $id);
			if (!$this->request->data) {
				$this->BcMessage->setError(__d('baser', '無効な処理です。'));
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
			}
		}

		// 公開リンク
		$publishLink = $this->Content->getPublishUrl($this->request->data['Content']);
		// エディタオプション
		$editorOptions = ['editorDisableDraft' => false];
		if (!empty($this->siteConfigs['editor_styles'])) {
			App::uses('CKEditorStyleParser', 'Vendor');
			$CKEditorStyleParser = new CKEditorStyleParser();
			$editorOptions = array_merge($editorOptions, [
				'editorStylesSet' => 'default',
				'editorStyles' => [
					'default' => $CKEditorStyleParser->parse($this->siteConfigs['editor_styles'])
				]
			]);
		}
		// ページテンプレートリスト
		$theme = [$this->siteConfigs['theme']];
		$site = BcSite::findById($this->request->data['Content']['site_id']);
		if (!empty($site) && $site->theme && $site->theme != $this->siteConfigs['theme']) {
			$theme[] = $site->theme;
		}
		$pageTemplateList = $this->Page->getPageTemplateList($this->request->data['Content']['id'], $theme);
		$this->set(compact('editorOptions', 'pageTemplateList', 'publishLink'));

		$this->pageTitle = __d('baser', '固定ページ情報編集');
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
	public function admin_delete()
	{
		if (empty($this->request->data['entityId'])) {
			return false;
		}
		if ($this->Page->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
	}

	/**
	 * [ADMIN] 固定ページファイルを登録する
	 *
	 * @return void
	 */
	public function admin_entry_page_files()
	{
		$this->_checkSubmitToken();
		$pagesPath = APP . 'View' . DS . 'Pages';
		$result = $this->Page->entryPageFiles($pagesPath);
		clearAllCache();
		$this->BcMessage->setInfo(
			sprintf(__d('baser', '%s ページ中 %s ページの新規登録、 %s ページの更新、 %s フォルダの新規登録、 %s フォルダの更新に成功しました。'), $result['all'], $result['insert'], $result['update'], $result['insert_folder'], $result['update_folder'])
		);
		$this->redirect(['controller' => 'tools', 'action' => 'index']);
	}

	/**
	 * [ADMIN] 固定ページファイルを登録する
	 *
	 * @return void
	 */
	public function admin_write_page_files()
	{
		$this->_checkSubmitToken();
		if ($this->Page->createAllPageTemplate()) {
			$this->BcMessage->setInfo(__d('baser', '固定ページテンプレートの書き出しに成功しました。'));
		} else {
			$this->BcMessage->setError(__d('baser', "固定ページテンプレートの書き出しに失敗しました。\n表示できないページは固定ページ管理より更新処理を行ってください。"));
		}
		clearViewCache();
		$this->redirect(['controller' => 'tools', 'action' => 'index']);
	}

	/**
	 * ビューを表示する
	 *
	 * @return void
	 * @throws ForbiddenException When a directory traversal attempt.
	 * @throws NotFoundException When the view file could not be found
	 *   or MissingViewException in debug mode.
	 */
	public function display()
	{
		// CUSTOMIZE DELETE 2016/10/05 ryuring
		$path = func_get_args();

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		if ($this->request->params['Content']['alias_id']) {
			$urlTmp = $this->Content->field('url', ['Content.id' => $this->request->params['Content']['alias_id']]);
		} else {
			$urlTmp = $this->request->params['Content']['url'];
		}

		if ($this->request->params['Site']['alias']) {
			$site = BcSite::findByUrl($urlTmp);
			if ($site && ($site->alias == $this->request->params['Site']['alias'])) {
				$urlTmp = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/' . $this->request->params['Site']['name'] . '/', $urlTmp);
			}
		}

		if (isset($urlTmp)) {
			$urlTmp = preg_replace('/^\//', '', $urlTmp);
			$path = explode('/', $urlTmp);
		}
		// <<<

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		if (in_array('..', $path, true) || in_array('.', $path, true)) {
			throw new ForbiddenException();
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>

		$previewCreated = false;
		if ($this->request->data) {
			// POSTパラメータのコードに含まれるscriptタグをそのままHTMLに出力するとブラウザによりXSSと判定される
			// 一度データをセッションに退避する
			if ($this->BcContents->preview === 'default') {
				// 入力validation
				$check = ['content_tmp' => $this->request->data['Page']['contents_tmp']];
				if (!$this->Page->containsScript($check)) {
					$this->BcMessage->setError(__d('baser', '本稿欄でスクリプトの入力は許可されていません。'));
					$this->notFound();
				}
				
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->data['Content']['entity_id'];
				$this->request->data = $this->Content->saveTmpFiles($this->request->data, mt_rand(0, 99999999));
				
				$this->Session->write($sessionKey, $this->request->data);
				$query = [];
				if ($this->request->query) {
					foreach($this->request->query as $key => $value) {
						$query[] = $key . '=' . $value;
					}
				}
				$redirectUrl = '/';
				if ($this->request->url) {
					$redirectUrl .= $this->request->url;
				}
				if ($query) {
					$redirectUrl .= '?' . implode('&', $query);
				}
				$this->redirect($redirectUrl);
				return;
			}

			if ($this->BcContents->preview === 'draft') {
				// 入力validation
				$check = ['content_tmp' => $this->request->data['Page']['contents_tmp']];
				if (!$this->Page->containsScript($check)) {
					$this->BcMessage->setError(__d('baser', '本稿欄でスクリプトの入力は許可されていません。'));
					$this->notFound();
				}
				
				$this->request->data = $this->Content->saveTmpFiles($this->request->data, mt_rand(0, 99999999));
				$this->request->params['Content']['eyecatch'] = $this->request->data['Content']['eyecatch'];

				$uuid = $this->_createPreviewTemplate($this->request->data);
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . $this->ext);
				$previewCreated = true;
			}

		} else {

			// プレビューアクセス
			if ($this->BcContents->preview === 'default') {
				$sessionKey = __CLASS__ . '_preview_default_' . $this->request->params['Content']['entity_id'];
				$previewData = $this->Session->read($sessionKey);
				$this->request->params['Content']['eyecatch'] = $previewData['Content']['eyecatch'];

				if (!is_null($previewData)) {
					$this->Session->delete($sessionKey);
					$uuid = $this->_createPreviewTemplate($previewData);
					$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . $this->ext);
					$previewCreated = true;
				}
			}

			// 草稿アクセス
			if ($this->BcContents->preview === 'draft') {
				$data = $this->Page->find('first', ['conditions' => ['Page.id' => $this->request->params['Content']['entity_id']]]);
				$uuid = $this->_createPreviewTemplate($data, true);
				$this->set('previewTemplate', TMP . 'pages_preview_' . $uuid . $this->ext);
				$previewCreated = true;
			}
		}

		$page = $this->Page->find('first', ['conditions' => ['Page.id' => $this->request->params['Content']['entity_id']], 'recursive' => -1]);
		$template = $page['Page']['page_template'];

		// 固定ページのプレビュー時、保存前の選択している固定ページテンプレートを適用する
		if ($previewCreated) {
			if ($previewData['Page']['page_template']) {
				$template = $previewData['Page']['page_template'];
			}
		}

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
			if ($previewCreated) {
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
	 * @param $data
	 * @param bool $isDraft
	 * @return string uuid
	 */
	protected function _createPreviewTemplate($data, $isDraft = false)
	{
		if (!$isDraft) {
			// postで送信される前提
			if (!empty($data['Page']['contents_tmp'])) {
				$contents = $data['Page']['contents_tmp'];
			} else {
				$contents = $data['Page']['contents'];
			}
		} else {
			$contents = $data['Page']['draft'];
		}
		$contents = $this->Page->addBaserPageTag(
			null,
			$contents,
			$data['Content']['title'],
			$data['Content']['description'],
			$data['Page']['code']
		);
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
	public function admin_ajax_copy()
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$user = $this->BcAuth->user();
		$data = $this->Page->copy(
			$this->request->data['entityId'],
			$this->request->data['parentId'],
			$this->request->data['title'],
			$user['id'],
			$this->request->data['siteId']
		);
		if (!$data) {
			$this->ajaxError(500, $this->Page->validationErrors);
			return false;
		}

		$message = sprintf(__d('baser', '固定ページのコピー「%s」を追加しました。'), $this->request->data['title']);
		$this->BcMessage->setSuccess($message, true, false);
		return json_encode($data['Content']);
	}

	/**
	 * 一覧の表示用データをセットする
	 *
	 * @return void
	 */
	protected function _setAdminIndexViewData()
	{
		$user = $this->BcAuth->user();
		$allowOwners = [];
		if (!empty($user)) {
			$allowOwners = ['', $user['user_group_id']];
		}
		if (!isset($this->passedArgs['sortmode'])) {
			$this->passedArgs['sortmode'] = false;
		}
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('allowOwners', $allowOwners);
		$this->set('sortmode', $this->passedArgs['sortmode']);
	}

}
