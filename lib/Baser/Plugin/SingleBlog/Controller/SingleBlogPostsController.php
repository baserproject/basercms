<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.Controller
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * SingleBlogPostsController
 *
 * @package SingleBlog.Controller
 * @property SingleBlogPost $SingleBlogPost
 */
class SingleBlogPostsController extends AppController {

/**
 * パンくず
 */
	public $crumbs = array(array(
		'name' => 'コンテンツ一覧', 'url' => array('plugin' => null, 'controller' => 'contents', 'action' => 'index')
	));

/**
 * コンポーネント
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('type' => 'SingleBlog.SingleBlog'));

/**
 * モデル
 * @var array
 */
	public $uses = array('SingleBlog.SingleBlogPost', 'Content');
	
/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = array('single_blog_posts');

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BcAuth->allow('index', 'view');
	}

/**
 * 記事一覧
 *
 * @return void
 */
	public function admin_index() {
		$this->pageTitle = '記事一覧';
		$this->paginate = array(
			'limit' => $this->siteConfigs['admin_list_num'],
			'recursive' => 2
		);
		$datas = $this->paginate('SingleBlogPost');
		$this->set('datas', $datas);
		$this->set('content', $this->Content->findByType('SingleBlog.SingleBlog'));
	}

/**
 * 記事編集
 *
 * @param int $id
 * @return void
 */
	public function admin_add() {
		$this->pageTitle = '記事新規登録';
		if($this->request->data) {
			if($this->SingleBlogPost->save($this->request->data)) {
				$this->setMessage('保存しました。', false, true);
				$this->redirect(array(
					'plugin'	=> 'single_blog',
					'controller'=> 'single_blog_posts',
					'action'	=> 'edit',
					$this->SingleBlogPost->id
				));
			} else {
				$this->setMessage('保存に失敗しました。', true, true);
			}
		}
		$this->crumbs[] = array(
			'name' => '記事一覧', 'url' => array('plugin' => 'single_blog', 'controller' => 'single_blog_posts', 'action' => 'index')
		);
	}

/**
 * 記事編集
 *
 * @param int $id
 * @return void
 */
	public function admin_edit($id) {
		$this->pageTitle = '記事編集';
		if(!$this->request->data) {
			$this->request->data = $this->SingleBlogPost->read(null, $id);
		} else {
			if($this->SingleBlogPost->save($this->request->data)) {
				$this->setMessage('保存しました。', false, true);
				$this->redirect(array(
					'plugin'	=> 'single_blog',
					'controller'=> 'single_blog_posts',
					'action'	=> 'edit',
					$id
				));
			} else {
				$this->setMessage('保存に失敗しました。', true, true);
			}
		}
		$this->crumbs[] = array(
			'name' => '記事一覧', 'url' => array('plugin' => 'single_blog', 'controller' => 'single_blog_posts', 'action' => 'index')
		);
		$content = $this->BcContents->getContent();
		$this->set('publishLink', $content['Content']['url'] . '/view/' . $id);
	}

/**
 * 記事を削除
 *
 * @param int $blogContentId
 * @param int $id
 */
	public function admin_delete($blogContentId, $id) {
		if($this->SingleBlogPost->delete($id)) {
			$this->setMessage('削除しました。', false, true);
		} else {
			$this->setMessage('削除に失敗しました。', false, true);
		}
		$this->redirect(
			array(
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_posts',
				'action'	=> 'index',
				$blogContentId
		));
	}

}