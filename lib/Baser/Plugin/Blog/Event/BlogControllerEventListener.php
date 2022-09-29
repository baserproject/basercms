<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Event
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * Class BlogControllerEventListener
 *
 * @package Baser.Event
 * @property Page $Page
 */
class BlogControllerEventListener extends BcControllerEventListener
{

	/**
	 * イベント
	 *
	 * @var array
	 */
	public $events = [
		'Contents.beforeDelete',
		'Contents.afterTrashReturn',
		'Contents.afterChangeStatus'
	];

	/**
	 * モデル
	 *
	 * @var bool|null|object
	 */
	public $BlogContent = null;
	public $BlogPost = null;

	/**
	 * BlogControllerEventListener constructor.
	 */
	public function __construct() {

		parent::__construct();

		// DB接続ができない場合、処理がコントローラーまで行き着かない為、try で実行
		try {
			$this->BlogContent = ClassRegistry::init('Blog.BlogContent');
			$this->BlogPost = ClassRegistry::init('Blog.BlogPost');
		} catch (Exception $e) {
		}
	}

	/**
	 * Contents Before Delete
	 *
	 * ゴミ箱に入れた場合に検索インデックスを削除する事が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsBeforeDelete(CakeEvent $event) {

		set_time_limit(0);
		$id = $event->data['data'];
		$data = $this->BlogContent->find('first', ['conditions' => ['Content.id' => $id]]);
		if ($data) {
			$dataSource = $this->BlogContent->getDataSource();
			$dataSource->begin();
			$this->BlogContent->deleteSearchIndex($data['BlogContent']['id']);

			$posts = $this->BlogPost->find('all', [
				'conditions' => [
					'BlogPost.blog_content_id' => $data['BlogContent']['id'],
				],
				'recursive' => -1,
			]);
			foreach ($posts as $post) {
				$this->BlogPost->id = $post['BlogPost']['id'];
				$this->BlogPost->deleteSearchIndex($post['BlogPost']['id']);
			}
			$dataSource->commit();
		}
	}

	/**
	 * Contents After Trash Return
	 *
	 * ゴミ箱から戻した場合に検索インデックスを更新する事が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsAfterTrashReturn(CakeEvent $event) {

		set_time_limit(0);
		$id = $event->data;
		$data = $this->BlogContent->find('first', ['conditions' => ['Content.id' => $id]]);
		if ($data) {
			$dataSource = $this->BlogContent->getDataSource();
			$dataSource->begin();
			if (empty($data['Content']['exclude_search'])) {
				$this->BlogContent->saveSearchIndex($this->BlogContent->createSearchIndex($data));
			} else {
				$this->BlogContent->deleteSearchIndex($data['BlogContent']['id']);
			}

			$posts = $this->BlogPost->find('all', [
				'conditions' => [
					'BlogPost.blog_content_id' => $data['BlogContent']['id'],
				],
				'recursive' => -1,
			]);
			foreach ($posts as $post) {
				$this->BlogPost->id = $post['BlogPost']['id'];
				if (empty($post['BlogPost']['exclude_search'])) {
					$this->BlogPost->saveSearchIndex($this->BlogPost->createSearchIndex($post));
				} else {
					$this->BlogPost->deleteSearchIndex($post['BlogPost']['id']);
				}
			}
			$dataSource->commit();
		}
	}

	/**
	 * Contents After Change Status
	 *
	 * 一覧から公開設定を変更した場合に検索インデックスを更新する事が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsAfterChangeStatus(CakeEvent $event) {

		set_time_limit(0);
		if (empty($event->data['result'])) {
			return;
		}
		$dataSource = $this->BlogContent->getDataSource();
		$dataSource->begin();
		$id = $event->data['id'];
		$data = $this->BlogContent->find('first', ['conditions' => ['Content.id' => $id]]);
		if (empty($data['Content']['exclude_search'])) {
			$this->BlogContent->saveSearchIndex($this->BlogContent->createSearchIndex($data));
		} else {
			$this->BlogContent->deleteSearchIndex($data['BlogContent']['id']);
		}

		if (empty($data['BlogContent']['id'])) {
			$dataSource->commit();
			return;
		}

		$posts = $this->BlogPost->find('all', [
			'conditions' => [
				'BlogPost.blog_content_id' => $data['BlogContent']['id'],
			],
			'recursive' => -1,
		]);
		foreach ($posts as $post) {
			$this->BlogPost->id = $post['BlogPost']['id'];
			if (empty($post['BlogPost']['exclude_search'])) {
				$this->BlogPost->saveSearchIndex($this->BlogPost->createSearchIndex($post));
			} else {
				$this->BlogPost->deleteSearchIndex($post['BlogPost']['id']);
			}
		}
		$dataSource->commit();
	}

}
