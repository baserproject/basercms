<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログコンテンツコントローラー
 *
 * @package Blog.Controller
 * @property BlogContent $BlogContent
 * @property BlogCategory $BlogCategory
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 * @property Content $Content
 */
class BlogContentsController extends BlogAppController {

/**
 * モデル
 *
 * @var
 */
	public $uses = ['Blog.BlogContent', 'SiteConfig', 'Blog.BlogCategory'];

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = ['BcHtml', 'BcTime', 'BcForm', 'Blog.Blog'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents' => ['useForm' => true]];

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = [];

/**
 * before_filter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			$this->subMenuElements = ['blog_common'];
		}
	}

/**
 * ブログ登録
 *
 * @return mixed json|false
 */
	public function admin_ajax_add() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->request->data['BlogContent'] = $this->BlogContent->getDefaultValue()['BlogContent'];
		$this->request->data = $this->BlogContent->deconstructEyeCatchSize($this->request->data);
		$data = $this->BlogContent->save($this->request->data);
		if ($data) {
			$message = 'ブログ「' . $this->request->data['Content']['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode($data['Content']);
		} else {
			$this->ajaxError(500, $this->BlogContent->validationErrors);
		}
		return false;
	}
	
/**
 * [ADMIN] ブログコンテンツ追加
 *
 * @return void
 */
	public function admin_add() {
		$this->pageTitle = '新規ブログ登録';

		if (!$this->request->data) {

			$this->request->data = $this->BlogContent->getDefaultValue();
		} else {

			$this->request->data = $this->BlogContent->deconstructEyeCatchSize($this->request->data);
			$this->BlogContent->create($this->request->data);

			if ($this->BlogContent->save()) {

				$id = $this->BlogContent->getLastInsertId();
				$this->setMessage('新規ブログ「' . $this->request->data['BlogContent']['title'] . '」を追加しました。', false, true);
				$this->redirect(['action' => 'edit', $id]);
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

			$this->request->data = $this->BlogContent->constructEyeCatchSize($this->request->data);
		}

		// テーマの一覧を取得
		$this->set('themes', $this->SiteConfig->getThemes());
		$this->render('form');
	}

/**
 * [ADMIN] 編集処理
 *
 * @param int $id
 * @return void
 */
	public function admin_edit($id) {
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->BlogContent->constructEyeCatchSize($this->BlogContent->read(null, $id));
			if(!$this->request->data) {
				$this->setMessage('無効な処理です。', true);
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
			}
		} else {
			$this->request->data = $this->BlogContent->deconstructEyeCatchSize($this->request->data);
			$this->BlogContent->set($this->request->data);

			if ($this->BlogContent->save()) {
				$this->setMessage('ブログ「' . $this->request->data['Content']['title'] . '」を更新しました。', false, true);
				if ($this->request->data['BlogContent']['edit_blog_template']) {
					$this->redirectEditBlog($this->request->data['BlogContent']['template']);
				} else {
					$this->redirect(['action' => 'edit', $id]);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
			$this->request->data = $this->BlogContent->constructEyeCatchSize($this->request->data);
		}

		if($this->request->data['Content']['status']) {
			$this->set('publishLink', $this->request->data['Content']['url']);
		}
		$this->request->params['Content'] = $this->BcContents->getContent($id)['Content'];
		$this->set('blogContent', $this->request->data);
		$this->subMenuElements = ['blog_posts'];
		$this->set('themes', $this->SiteConfig->getThemes());
		$this->pageTitle = 'ブログ設定編集：' . $this->request->data['Content']['title'];
		$this->help = 'blog_contents_form';
		$this->render('form');
	}

/**
 * レイアウト編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 */
	protected function redirectEditLayout($template) {
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . 'Layouts' . DS . $template . $this->ext;
		$sorces = [BASER_PLUGINS . 'blog' . DS . 'View' . DS . 'Layouts' . DS . $template . $this->ext,
			BASER_VIEWS . 'Layouts' . DS . $template . $this->ext];
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target)) {
				foreach ($sorces as $source) {
					if (file_exists($source)) {
						copy($source, $target);
						chmod($target, 0666);
						break;
					}
				}
			}
			$this->redirect(['plugin' => null, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'Layouts', $template . $this->ext]);
		} else {
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(['action' => 'index']);
		}
	}

/**
 * ブログテンプレート編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 */
	protected function redirectEditBlog($template) {
		$path = 'Blog' . DS . $template;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path;
		$sources = [BASER_PLUGINS . 'Blog' . DS . 'View' . DS . $path];
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target . DS . 'index' . $this->ext)) {
				foreach ($sources as $source) {
					if (is_dir($source)) {
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(['from' => $source, 'to' => $target, 'chmod' => 0777, 'skip' => ['_notes']]);
						break;
					}
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(array_merge(['plugin' => null, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'etc'], explode('/', $path . '/index' . $this->ext)));
		} else {
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(['action' => 'index']);
		}
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
		if($this->BlogContent->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
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
		$data = $this->BlogContent->copy($this->request->data['entityId'], $this->request->data['parentId'], $this->request->data['title'], $user['id'], $this->request->data['siteId']);
		if ($data) {
			$message = 'ブログのコピー「' . $this->request->data['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode($data['Content']);
		} else {
			$this->ajaxError(500, $this->BlogContent->validationErrors);
		}
		return false;
	}
	
}
