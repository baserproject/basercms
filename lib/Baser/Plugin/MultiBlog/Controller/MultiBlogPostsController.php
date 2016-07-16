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
 * MultiBlogPostsController
 *
 * @package MultiBlog.Controller
 * @property MultiBlogPost $MultiBlogPost
 */
class MultiBlogPostsController extends AppController {

/**
 * コンポーネント
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('type' => 'MultiBlog.MultiBlogContent'));

/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = array('multi_blog_contents');

/**
 * beforeFilter
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->crumbs = array(array(
			'name' => '記事管理', 'url' => array('plugin' => 'multi_blog', 'controller' => 'multi_blog_posts', 'action' => 'index', $this->request->pass[0])
		));
	}

/**
 * 記事一覧
 *
 * @return void
 */
	public function admin_index($blogContentId) {
		$this->pageTitle = '記事一覧';
		$this->paginate = array(
			'conditions' => array(
				'MultiBlogPost.blog_content_id' => $blogContentId
			),
			'limit' => $this->siteConfigs['admin_list_num'],
			'recursive' => 2
		);
		$datas = $this->paginate();
		$this->set('blogContentId', $blogContentId);
		$this->set('datas', $datas);
	}

/**
 * 記事編集
 *
 * @param int $id
 * @return void
 */
	public function admin_add($blogContentId) {
		$this->pageTitle = '記事新規登録';
		if($this->request->data) {
			$this->request->data['MultiBlogPost']['no'] = $this->MultiBlogPost->getMax('no', array('MultiBlogPost.blog_content_id' => $blogContentId)) + 1;
			$this->request->data['MultiBlogPost']['blog_content_id'] = $blogContentId;
			if($this->MultiBlogPost->save($this->request->data)) {
				$this->setMessage('保存しました。', false, true);
				$this->redirect(array(
					'plugin'	=> 'multi_blog',
					'controller'=> 'multi_blog_posts',
					'action'	=> 'edit',
					$blogContentId,
					$this->MultiBlogPost->id
				));
			} else {
				$this->setMessage('保存に失敗しました。', true, true);
			}
		}
		$this->crumbs[] = array(
			'name' => '記事一覧', 'url' => array('plugin' => 'multi_blog', 'controller' => 'multi_blog_posts', 'action' => 'index', $blogContentId)
		);
	}

/**
 * 記事編集
 *
 * @param int $id
 * @return void
 */
	public function admin_edit($blogContentId, $id) {
		$this->pageTitle = '記事編集';
		if(!$this->request->data) {
			$this->request->data = $this->MultiBlogPost->read(null, $id);
		} else {
			if($this->MultiBlogPost->save($this->request->data)) {
				$this->setMessage('保存しました。', false, true);
				$this->redirect(array(
					'plugin'	=> 'multi_blog',
					'controller'=> 'multi_blog_posts',
					'action'	=> 'edit',
					$blogContentId,
					$id
				));
			} else {
				$this->setMessage('保存に失敗しました。', true, true);
			}
		}
		$this->crumbs[] = array(
			'name' => '記事一覧', 'url' => array('plugin' => 'multi_blog', 'controller' => 'multi_blog_posts', 'action' => 'index', $blogContentId)
		);
		$content = $this->BcContents->getContent();
		$this->set('publishLink', $content['Content']['url'] . '/view/' . $this->request->data['MultiBlogPost']['no']);
	}

/**
 * 記事を削除
 *
 * @param int $blogContentId
 * @param int $id
 */
	public function admin_delete($blogContentId, $id) {
		if($this->MultiBlogPost->delete($id)) {
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