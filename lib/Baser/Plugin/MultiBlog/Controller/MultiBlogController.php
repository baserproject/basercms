<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.Controller
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * MultiBlogController
 *
 * @package MultiBlog.Controller
 * @property MultiBlogPost $MultiBlogPost
 */
class MultiBlogController extends AppController {

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcContents');

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('MultiBlog.MultiBlogPost');

/**
 * フロントの記事一覧を表示
 *
 * @return void
 */
	public function index ($blogContentId) {
		$datas = $this->MultiBlogPost->find('all', array('conditions' => array(
			'MultiBlogPost.blog_content_id' => $blogContentId
		)));
		$blogContent = $this->MultiBlogPost->MultiBlogContent->find('first', array('conditions' => array(
			'MultiBlogContent.id' => $blogContentId
		)));
		if($this->BcContents->preview == 'default' && $this->request->data) {
			$blogContent = $this->request->data;
		} elseif($this->BcContents->preview == 'alias') {
			$blogContent['Content'] = $this->request->data['Content'];
		}
		if(!$blogContent) {
			$this->notFound();
		}
		$this->set('blogContent', $blogContent);
		$this->set('datas', $datas);
		$this->set('editLink', array('plugin' => 'multi_blog', 'admin' => true, 'controller' => 'multi_blog_contents', 'action' => 'edit', $blogContentId));
	}

/**
 * フロントの詳細を表示
 *
 * @param $id
 * @return void
 */
	public function view($blogContentId, $no) {
		$data = $this->MultiBlogPost->find('first', array('conditions' => array(
			'MultiBlogPost.blog_content_id' => $blogContentId,
			'MultiBlogPost.no' => $no
		)));
		$blogContent = $this->MultiBlogPost->MultiBlogContent->find('first', array('conditions' => array(
			'MultiBlogContent.id' => $blogContentId
		)));
		$this->pageTitle = $data['MultiBlogPost']['title'];
		$this->set('blogContent', $blogContent);
		$this->set('data', $data);
		$this->set('editLink', array('plugin' => 'multi_blog', 'admin' => true, 'controller' => 'multi_blog_posts', 'action' => 'edit', $blogContentId, $data['MultiBlogPost']['id']));
	}

}