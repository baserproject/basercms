<?php

/**
 * ブログコンテンツコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * ブログコンテンツコントローラー
 *
 * @package Blog.Controller
 */
class BlogContentsController extends BlogAppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'BlogContents';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('SiteConfig', 'Blog.BlogCategory', 'Blog.BlogContent');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcHtml', 'BcTime', 'BcForm', 'Blog.Blog');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	public $crumbs = array(
		array('name' => 'ブログ管理', 'url' => array('controller' => 'blog_contents', 'action' => 'index'))
	);

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * before_filter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			$this->subMenuElements = array('blog_common');
		}
	}

/**
 * [ADMIN] ブログコンテンツ一覧
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$datas = $this->BlogContent->find('all', array('order' => array('BlogContent.id')));
		$this->set('datas', $datas);

		if ($this->RequestHandler->isAjax() || !empty($this->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$this->pageTitle = 'ブログ一覧';
		$this->help = 'blog_contents_index';
	}

/**
 * [ADMIN] ブログコンテンツ追加
 *
 * @return void
 * @access public
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
				$this->redirect(array('action' => 'edit', $id));
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
 * @access public
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->BlogContent->read(null, $id);
			$this->request->data = $this->BlogContent->constructEyeCatchSize($this->request->data);
		} else {

			$this->request->data = $this->BlogContent->deconstructEyeCatchSize($this->request->data);
			$this->BlogContent->set($this->request->data);

			if ($this->BlogContent->save()) {

				$this->setMessage('ブログ「' . $this->request->data['BlogContent']['title'] . '」を更新しました。', false, true);

				if ($this->request->data['BlogContent']['edit_layout_template']) {
					$this->redirectEditLayout($this->request->data['BlogContent']['layout']);
				} elseif ($this->request->data['BlogContent']['edit_blog_template']) {
					$this->redirectEditBlog($this->request->data['BlogContent']['template']);
				} else {
					$this->redirect(array('action' => 'edit', $id));
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

			$this->request->data = $this->BlogContent->constructEyeCatchSize($this->request->data);
		}

		$this->set('publishLink', '/' . $this->request->data['BlogContent']['name'] . '/index');

		/* 表示設定 */
		$this->set('blogContent', $this->request->data);
		$this->subMenuElements = array('blog_posts', 'blog_categories', 'blog_common');
		$this->set('themes', $this->SiteConfig->getThemes());
		$this->pageTitle = 'ブログ設定編集：' . $this->request->data['BlogContent']['title'];
		$this->help = 'blog_contents_form';
		$this->render('form');
	}

/**
 * レイアウト編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	public function redirectEditLayout($template) {
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . 'Layouts' . DS . $template . $this->ext;
		$sorces = array(BASER_PLUGINS . 'blog' . DS . 'View' . DS . 'Layouts' . DS . $template . $this->ext,
			BASER_VIEWS . 'Layouts' . DS . $template . $this->ext);
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
			$this->redirect(array('plugin' => null, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'Layouts', $template . $this->ext));
		} else {
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * ブログテンプレート編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	public function redirectEditBlog($template) {
		$path = 'Blog' . DS . $template;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path;
		$sources = array(BASER_PLUGINS . 'Blog' . DS . 'View' . DS . $path);
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target . DS . 'index' . $this->ext)) {
				foreach ($sources as $source) {
					if (is_dir($source)) {
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(array('from' => $source, 'to' => $target, 'chmod' => 0777, 'skip' => array('_notes')));
						break;
					}
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(array_merge(array('plugin' => null, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'etc'), explode('/', $path . '/index' . $this->ext)));
		} else {
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * [ADMIN] 削除処理
 *
 * @param int $id
 * @return void
 * @access public
 * @deprecated
 */
	public function admin_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->BlogContent->read(null, $id);

		/* 削除処理 */
		if ($this->BlogContent->delete($id)) {
			$this->setMessage('ブログ「' . $post['BlogContent']['title'] . '」 を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] Ajax 削除処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	public function admin_ajax_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->BlogContent->read(null, $id);

		/* 削除処理 */
		if ($this->BlogContent->delete($id)) {
			$this->BlogContent->saveDbLog('ブログ「' . $post['BlogContent']['title'] . '」 を削除しました。');
			echo true;
		}

		exit();
	}

/**
 * [ADMIN] データコピー（AJAX）
 * 
 * @param int $id 
 * @return void
 * @access public
 */
	public function admin_ajax_copy($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$result = $this->BlogContent->copy($id);
		if ($result) {
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->BlogContent->validationErrors);
		}
	}

}
