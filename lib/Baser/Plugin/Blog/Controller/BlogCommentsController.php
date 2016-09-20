<?php

/**
 * ブログコメントコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * ブログコメントコントローラー
 *
 * @package Blog.Controller
 */
class BlogCommentsController extends BlogAppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'BlogComments';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Blog.BlogCategory', 'Blog.BlogComment', 'Blog.BlogPost');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array();

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler', 'BcEmail', 'Security', 'BcCaptcha');

/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
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
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->BcAuth->allow('add', 'captcha', 'smartphone_add', 'smartphone_captcha', 'get_token');

		$crumbs = array();
		if (!empty($this->params['pass'][1])) {

			$dbDatas = $this->BlogPost->read(null, $this->params['pass'][1]);

			if (!$dbDatas) {
				$this->notFound();
			}

			$this->blogPost = array('BlogPost' => $dbDatas['BlogPost']);
			$this->blogContent = array('BlogContent' => $dbDatas['BlogContent']);

			$crumbs[] = array('name' => $this->blogContent['BlogContent']['title'] . '管理', 'url' => array('controller' => 'blog_posts', 'action' => 'index', $this->blogContent['BlogContent']['id']));
			$crumbs[] = array('name' => $this->blogPost['BlogPost']['name'], 'url' => array('controller' => 'blog_posts', 'action' => 'edit', $this->blogContent['BlogContent']['id'], $this->blogPost['BlogPost']['id']));
		} elseif (!empty($this->params['pass'][0])) {

			$dbDatas = $this->BlogPost->BlogContent->read(null, $this->params['pass'][0]);
			$this->blogContent = array('BlogContent' => $dbDatas['BlogContent']);
			$crumbs[] = array('name' => $this->blogContent['BlogContent']['title'] . '管理', 'url' => array('controller' => 'blog_posts', 'action' => 'index', $this->blogContent['BlogContent']['id']));
		}

		$this->crumbs = am($this->crumbs, $crumbs);
		if (!empty($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			$this->subMenuElements = array('blog_posts', 'blog_categories', 'blog_common');
		}

		if (empty($this->params['admin'])) {
			$this->Security->enabled = true;
			$this->Security->requireAuth('add');
		}
	}

/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	public function beforeRender() {
		parent::beforeRender();
		if (!empty($this->blogContent)) {
			$this->set('blogContent', $this->blogContent);
		}
	}

/**
 * [ADMIN] ブログを一覧表示する
 *
 * @return void
 * @access public
 */
	public function admin_index($blogContentId, $blogPostId = null) {
		if (!$blogContentId || empty($this->blogContent['BlogContent'])) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('controller' => 'blog_contents', 'action' => 'index'));
		}

		/* 検索条件 */
		if ($blogPostId) {
			$conditions['BlogComment.blog_post_id'] = $blogPostId;
			$this->pageTitle = '記事 [' . $this->blogPost['BlogPost']['name'] . '] のコメント一覧';
		} else {
			$conditions['BlogComment.blog_content_id'] = $blogContentId;
			$this->pageTitle = 'ブログ [' . $this->blogContent['BlogContent']['title'] . '] のコメント一覧';
		}

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('BlogPost', array('group' => $blogContentId, 'default' => $default));

		// データを取得
		$this->paginate = array('conditions' => $conditions,
			'fields' => array(),
			'order' => 'BlogComment.created DESC',
			'limit' => $this->passedArgs['num']
		);

		$dbDatas = $this->paginate('BlogComment');
		$this->set('dbDatas', $dbDatas);
		$this->help = 'blog_comments_index';
	}

/**
 * [ADMIN] 一括削除
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 * @access public
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_del($id);
			}
		}
		return true;
	}

/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 * @access public
 */
	public function admin_ajax_delete($blogContentId, $blogPostId, $id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		if ($this->_del($id)) {
			exit(true);
		} else {
			exit();
		}
	}

/**
 * 削除処理
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 * @access public
 */
	protected function _del($id = null) {
		/* 削除処理 */
		if ($this->BlogComment->delete($id)) {
			if (isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「' . $this->blogPost['BlogPost']['name'] . '」へのコメントを削除しました。';
			} else {
				$message = '記事「' . $this->blogContent['BlogContent']['title'] . '」へのコメントを削除しました。';
			}
			$this->BlogComment->saveDbLog($message);
			return true;
		} else {
			return false;
		}
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
	public function admin_ajax_unpublish($blogContentId, $blogPostId, $id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->BlogComment->validationErrors);
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
	public function admin_ajax_publish($blogContentId, $blogPostId, $id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->BlogComment->validationErrors);
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
		clearViewCache();
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
		clearViewCache();
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
		$statusTexts = array(0 => '公開状態', 1 => '非公開状態');
		$data = $this->BlogComment->find('first', array('conditions' => array('BlogComment.id' => $id), 'recursive' => -1));
		$data['BlogComment']['status'] = $status;
		$this->BlogComment->set($data);

		if ($this->BlogComment->save()) {
			$statusText = $statusTexts[$status];
			if (isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「' . $this->blogPost['BlogPost']['name'] . '」へのコメントを' . $statusText . 'に設定しました。';
			} else {
				$message = '記事「' . $this->blogContent['BlogContent']['title'] . '」へのコメントを' . $statusText . 'に設定しました。';
			}
			$this->BlogComment->saveDbLog($message);
			return true;
		} else {
			return false;
		}
	}

/**
 * [AJAX] ブログコメントを登録する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @return boolean
 * @access public
 */
	public function add($blogContentId, $blogPostId) {
		Configure::write('debug', 0);

		if (!$this->request->data || !$blogContentId || !$blogPostId || empty($this->blogContent) || !$this->blogContent['BlogContent']['comment_use']) {
			$this->notFound();
		} else {

			// 画像認証を行う
			$captchaResult = true;
			if ($this->blogContent['BlogContent']['auth_captcha']) {
				$captchaResult = $this->BcCaptcha->check($this->request->data['BlogComment']['auth_captcha']);
				if (!$captchaResult) {
					$this->set('dbData', false);
					return false;
				} else {
					unset($this->request->data['BlogComment']['auth_captcha']);
				}
			}

			$result = $this->BlogComment->add($this->request->data, $blogContentId, $blogPostId, $this->blogContent['BlogContent']['comment_approve']);
			if ($result && $captchaResult) {
				$this->_sendCommentAdmin($blogPostId, $this->request->data);
				// コメント承認機能を利用していない場合は、公開されているコメント投稿者にアラートを送信
				if (!$this->blogContent['BlogContent']['comment_approve']) {
					$this->_sendCommentContributor($blogPostId, $this->request->data);
				}
				$this->set('dbData', $result['BlogComment']);
			} else {
				$this->set('dbData', false);
			}
		}
	}

/**
 * [AJAX] ブログコメントを登録する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @return boolean
 * @access public
 */
	public function smartphone_add($blogContentId, $blogPostId) {
		$this->setAction('add', $blogContentId, $blogPostId);
	}

/**
 * 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
	public function captcha() {
		$this->BcCaptcha->render();
		exit();
	}

/**
 * [SMARTPHONE] 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
	public function smartphone_captcha() {
		$this->BcCaptcha->render();
		exit();
	}

/**
 * コメント送信用にAjax経由でトークンを取得する
 */
	public function get_token() {
		if (!preg_match('/^' . preg_quote(Configure::read('BcEnv.siteUrl'), '/') . '/', $_SERVER['HTTP_REFERER'])) {
			$this->notFound();
		}
		echo $this->Session->read('_Token.key');
		exit();
	}

}
