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
 * SingleBlogController
 *
 * @package SingleBlog.Controller
 * @property SingleBlogContent $SingleBlogContent
 */
class SingleBlogController extends AppController {

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents');

/**
 * モデル
 * @var array
 */
	public $uses = array('SingleBlog.SingleBlogPost', 'Content');

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
 * フロントの記事一覧を表示
 *
 * @return void
 */
	public function index () {
		$datas = $this->SingleBlogPost->find('all');
		$content = $this->Content->findByType('SingleBlog.SingleBlog');
		if($this->BcContents->preview && $this->request->data) {
			$content = $this->request->data;
		}
		$this->set('editLink', array('admin' => true, 'plugin' => '', 'controller' => 'contents', 'action' => 'edit', 'content_id' => $content['Content']['id']));
		$this->set('datas', $datas);
		$this->set('content', $content);
	}

/**
 * フロントの詳細を表示
 *
 * @param $id
 * @return void
 */
	public function view($blogPostId) {
		$this->crumbs[] = array('name' => $this->request->params['Content']['title'], 'url' => $this->request->params['Content']['url']);
		$data = $this->SingleBlogPost->find('first', array('conditions' => array(
			'SingleBlogPost.id' => $blogPostId
		)));
		$this->pageTitle = $data['SingleBlogPost']['title'];
		$this->set('editLink', array('admin' => true, 'plugin' => 'single_blog', 'controller' => 'single_blog_posts', 'action' => 'edit', $blogPostId));
		$this->set('data', $data);
	}

/**
 * 削除
 */
	public function admin_delete() {
		$this->SingleBlogPost->getDataSource()->begin();
		if($this->Content->deleteByType('SingleBlog.SingleBlog') && $this->SingleBlogPost->deleteAll("1=1")) {
			$this->SingleBlogPost->getDataSource()->commit();
			return true;
		}
		$this->SingleBlogPost->getDataSource()->rollback();
		return false;
	}

}