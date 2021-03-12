<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BlogAppModel', 'Blog.Model');

/**
 * ブログコンテンツモデル
 *
 * @package Blog.Model
 * @property BlogPost $BlogPost
 */
class BlogContent extends BlogAppModel
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'BlogContent';

	/**
	 * behaviors
	 *
	 * @var array
	 */
	public $actsAs = [
		'BcSearchIndexManager',
		'BcContents'
	];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = [
		'BlogPost' => [
			'className' => 'Blog.BlogPost',
			'order' => 'id DESC',
			'limit' => 10,
			'foreignKey' => 'blog_content_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => ''],
		'BlogCategory' => [
			'className' => 'Blog.BlogCategory',
			'order' => 'id',
			'limit' => 10,
			'foreignKey' => 'blog_content_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => '']
	];

	/**
	 * BlogContent constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'layout' => [
				['rule' => 'halfText', 'message' => __d('baser', 'レイアウトテンプレート名は半角で入力してください。'), 'allowEmpty' => false],
				['rule' => ['maxLength', 20], 'message' => __d('baser', 'レイアウトテンプレート名は20文字以内で入力してください。')]],
			'template' => [
				['rule' => 'halfText', 'message' => __d('baser', 'コンテンツテンプレート名は半角で入力してください。'), 'allowEmpty' => false],
				['rule' => ['maxLength', 20], 'message' => __d('baser', 'レイアウトテンプレート名は20文字以内で入力してください。')]],
			'list_count' => [
				['rule' => 'halfText', 'message' => __d('baser', '一覧表示件数は半角で入力してください。'), 'allowEmpty' => false],
				['rule' => ['range', 0, 101], 'message' => __d('baser', '一覧表示件数は100までの数値で入力してください。')]
			],
			'list_direction' => [
				['rule' => ['notBlank'], 'message' => __d('baser', '一覧に表示する順番を指定してください。')]],
			'eye_catch_size' => [
				['rule' => ['checkEyeCatchSize'], 'message' => __d('baser', 'アイキャッチ画像のサイズが不正です。')]]
		];
	}

	/**
	 * アイキャッチ画像サイズバリデーション
	 *
	 * @return boolean
	 */
	public function checkEyeCatchSize()
	{
		$data = $this->constructEyeCatchSize($this->data);
		if (empty($data['BlogContent']['eye_catch_size_thumb_width']) ||
			empty($data['BlogContent']['eye_catch_size_thumb_height']) ||
			empty($data['BlogContent']['eye_catch_size_mobile_thumb_width']) ||
			empty($data['BlogContent']['eye_catch_size_mobile_thumb_height'])) {
			return false;
		}

		return true;
	}

	/**
	 * 英数チェック
	 *
	 * @param string $check チェック対象文字列
	 * @return boolean
	 */
	public function alphaNumeric($check)
	{
		if (preg_match("/^[a-z0-9]+$/", $check[key($check)])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string フィールド名
	 * @return array コントロールソース
	 */
	public function getControlSource($field = null, $options = [])
	{

		switch($field) {
			case 'id':
				$ContentModel = ClassRegistry::init('Content');
				$controlSources['id'] = $ContentModel->find('list', [
					'fields' => [
						'entity_id',
						'title',
					],
					'conditions' => [
						'plugin' => 'Blog',
						'type' => 'BlogContent',
					],
					'recursive' => -1,
				]);
				break;
			default:
				break;
		}

		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

	/**
	 * afterSave
	 *
	 * @return boolean
	 */
	public function afterSave($created, $options = [])
	{
		if (empty($this->data['BlogContent']['id'])) {
			$this->data['BlogContent']['id'] = $this->getInsertID();
		}

		// 検索用テーブルへの登録・削除
		if (!$this->data['Content']['exclude_search'] && $this->data['Content']['status']) {
			$this->saveSearchIndex($this->createSearchIndex($this->data));
			clearDataCache();
			$datas = $this->BlogPost->find('all', [
				'conditions' => ['BlogPost.blog_content_id' => $this->data['BlogContent']['id']],
				'recursive' => -1
			]);
			foreach($datas as $data) {
				$this->BlogPost->set($data);
				$this->BlogPost->afterSave(true);
			}
		} else {
			$this->deleteSearchIndex($this->data['BlogContent']['id']);
		}
	}

	/**
	 * beforeDelete
	 *
	 * @return    boolean
	 * @access    public
	 */
	public function beforeDelete($cascade = true)
	{
		return $this->deleteSearchIndex($this->id);
	}

	/**
	 * 検索用データを生成する
	 *
	 * @param array $data
	 * @return array|false
	 */
	public function createSearchIndex($data)
	{
		if (!isset($data['BlogContent']) || !isset($data['Content'])) {
			return false;
		}
		$blogContent = $data['BlogContent'];
		$content = $data['Content'];
		return ['SearchIndex' => [
			'type' => __d('baser', 'ブログ'),
			'model_id' => (!empty($blogContent['id']))? $blogContent['id'] : $this->id,
			'content_id' => $content['id'],
			'site_id' => $content['site_id'],
			'title' => $content['title'],
			'detail' => $blogContent['description'],
			'url' => $content['url'],
			'status' => $content['status'],
			'publish_begin' => $content['publish_begin'],
			'publish_end' => $content['publish_end']
		]];
	}

	/**
	 * ブログコンテンツをコピーする
	 *
	 * @param int $id ページID
	 * @param int $newParentId 新しい親コンテンツID
	 * @param string $newTitle 新しいタイトル
	 * @param int $newAuthorId 新しいユーザーID
	 * @param int $newSiteId 新しいサイトID
	 * @return mixed blogContent|false
	 */
	public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null)
	{

		$data = $this->find('first', ['conditions' => ['BlogContent.id' => $id], 'recursive' => 0]);
		$oldData = $data;

		// EVENT BlogContent.beforeCopy
		$event = $this->dispatchEvent('beforeCopy', [
			'data' => $data,
			'id' => $id,
		]);
		if ($event !== false) {
			$data = $event->result === true? $event->data['data'] : $event->result;
		}

		$url = $data['Content']['url'];
		$siteId = $data['Content']['site_id'];
		$name = $data['Content']['name'];
		$eyeCatch = $data['Content']['eyecatch'];
		unset($data['BlogContent']['id']);
		unset($data['BlogContent']['created']);
		unset($data['BlogContent']['modified']);
		unset($data['Content']);
		$data['Content'] = [
			'name' => $name,
			'parent_id' => $newParentId,
			'title' => $newTitle,
			'author_id' => $newAuthorId,
			'site_id' => $newSiteId,
			'exclude_search' => false
		];
		if (!is_null($newSiteId) && $siteId != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->Content->copyContentFolderPath($url, $newSiteId);
		}
		$this->getDataSource()->begin();
		$this->create($data);
		if ($result = $this->save()) {
			$result['BlogContent']['id'] = $this->getInsertID();
			$data = $result;

			$blogPosts = $this->BlogPost->find('all', ['conditions' => ['BlogPost.blog_content_id' => $id], 'order' => 'BlogPost.id', 'recursive' => -1]);
			foreach($blogPosts as $blogPost) {
				$blogPost['BlogPost']['blog_category_id'] = null;
				$blogPost['BlogPost']['blog_content_id'] = $result['BlogContent']['id'];
				$this->BlogPost->copy(null, $blogPost);
			}
			if ($eyeCatch) {
				$result['Content']['id'] = $this->Content->getLastInsertID();
				$result['Content']['eyecatch'] = $eyeCatch;
				$this->Content->set(['Content' => $result['Content']]);
				$result = $this->Content->renameToBasenameFields(true);
				$this->Content->set($result);
				$result = $this->Content->save();
				$data['Content'] = $result['Content'];
			}

			// EVENT BlogContent.afterCopy
			$event = $this->dispatchEvent('afterCopy', [
				'id' => $data['BlogContent']['id'],
				'data' => $data,
				'oldId' => $id,
				'oldData' => $oldData,
			]);

			$this->getDataSource()->commit();
			return $result;
		}
		$this->getDataSource()->rollback();
		return false;
	}

	/**
	 * フォームの初期値を取得する
	 *
	 * @return void
	 */
	public function getDefaultValue()
	{
		$data['BlogContent']['comment_use'] = true;
		$data['BlogContent']['comment_approve'] = false;
		$data['BlogContent']['layout'] = 'default';
		$data['BlogContent']['template'] = 'default';
		$data['BlogContent']['list_count'] = 10;
		$data['BlogContent']['list_direction'] = 'DESC';
		$data['BlogContent']['feed_count'] = 10;
		$data['BlogContent']['auth_captcha'] = 1;
		$data['BlogContent']['tag_use'] = false;
		$data['BlogContent']['status'] = false;
		$data['BlogContent']['eye_catch_size_thumb_width'] = Configure::read("Blog.eye_catch_size_thumb_width");
		$data['BlogContent']['eye_catch_size_thumb_height'] = Configure::read("Blog.eye_catch_size_thumb_height");
		$data['BlogContent']['eye_catch_size_mobile_thumb_width'] = Configure::read("Blog.eye_catch_size_mobile_thumb_width");
		$data['BlogContent']['eye_catch_size_mobile_thumb_height'] = Configure::read("Blog.eye_catch_size_mobile_thumb_height");
		$data['BlogContent']['use_content'] = true;

		return $data;
	}

	/**
	 * アイキャッチサイズフィールドの値をDB用に変換する
	 *
	 * @param array $data
	 * @return array
	 */
	public function deconstructEyeCatchSize($data)
	{
		$data['BlogContent']['eye_catch_size'] = BcUtil::serialize([
			'thumb_width' => $data['BlogContent']['eye_catch_size_thumb_width'],
			'thumb_height' => $data['BlogContent']['eye_catch_size_thumb_height'],
			'mobile_thumb_width' => $data['BlogContent']['eye_catch_size_mobile_thumb_width'],
			'mobile_thumb_height' => $data['BlogContent']['eye_catch_size_mobile_thumb_height'],
		]);
		unset($data['BlogContent']['eye_catch_size_thumb_width']);
		unset($data['BlogContent']['eye_catch_size_thumb_height']);
		unset($data['BlogContent']['eye_catch_size_mobile_thumb_width']);
		unset($data['BlogContent']['eye_catch_size_mobile_thumb_height']);

		return $data;
	}

	/**
	 * アイキャッチサイズフィールドの値をフォーム用に変換する
	 *
	 * @param array $data
	 * @return array
	 */
	public function constructEyeCatchSize($data)
	{
		$eyeCatchSize = BcUtil::unserialize($data['BlogContent']['eye_catch_size']);
		$data['BlogContent']['eye_catch_size_thumb_width'] = $eyeCatchSize['thumb_width'];
		$data['BlogContent']['eye_catch_size_thumb_height'] = $eyeCatchSize['thumb_height'];
		$data['BlogContent']['eye_catch_size_mobile_thumb_width'] = $eyeCatchSize['mobile_thumb_width'];
		$data['BlogContent']['eye_catch_size_mobile_thumb_height'] = $eyeCatchSize['mobile_thumb_height'];
		return $data;
	}

}
