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
 * 記事モデル
 *
 * @package Blog.Model
 * @property BlogContent $BlogContent
 * @property BlogCategory $BlogCategory
 * @property BlogTag $BlogTag
 */
class BlogPost extends BlogAppModel
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'BlogPost';

	/**
	 * 検索テーブルへの保存可否
	 *
	 * @var boolean
	 */
	public $searchIndexSaving = true;

	/**
	 * ファインダーメソッド
	 *
	 * @var array
	 */
	public $findMethods = ['customParams' => true];

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = [
		'BcSearchIndexManager',
		'BcUpload' => [
			'subdirDateFormat' => 'Y/m/',
			'fields' => [
				'eye_catch' => [
					'type' => 'image',
					'namefield' => 'no',
					'nameformat' => '%08d'
				]
			]
		]
	];

	/**
	 * belongsTo
	 *
	 * @var array
	 */
	public $belongsTo = [
		'BlogCategory' => [
			'className' => 'Blog.BlogCategory',
			'foreignKey' => 'blog_category_id'],
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id'],
		'BlogContent' => [
			'className' => 'Blog.BlogContent',
			'foreignKey' => 'blog_content_id']
	];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = [
		'BlogComment' => [
			'className' => 'Blog.BlogComment',
			'order' => 'created',
			'foreignKey' => 'blog_post_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => '']
	];

	/**
	 * HABTM
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = [
		'BlogTag' => [
			'className' => 'Blog.BlogTag',
			'joinTable' => 'blog_posts_blog_tags',
			'foreignKey' => 'blog_post_id',
			'associationForeignKey' => 'blog_tag_id',
			'conditions' => '',
			'order' => '',
			'limit' => '',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => ''
		]];

	/**
	 * BlogPost constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'name' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'タイトルを入力してください。'), 'required' => true],
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'タイトルは255文字以内で入力してください。')]],
			'content' => [
				['rule' => 'containsScript', 'message' => __d('baser', '概要欄でスクリプトの入力は許可されていません。')]],
			'detail' => [
				['rule' => ['maxByte', 64000], 'message' => __d('baser', '本稿欄に保存できるデータ量を超えています。')],
				['rule' => 'containsScript', 'message' => __d('baser', '本稿欄でスクリプトの入力は許可されていません。')]],
			'detail_draft' => [
				['rule' => ['maxByte', 64000], 'message' => __d('baser', '草稿欄に保存できるデータ量を超えています。')],
				['rule' => 'containsScript', 'message' => __d('baser', '草稿欄でスクリプトの入力は許可されていません。')]],
			'publish_begin' => [
				['rule' => ['checkDate'], 'allowEmpty' => true, 'message' => __d('baser', '公開開始日の形式が不正です。')],
				['rule' => ['checkDateRenge', 'allowEmpty' => true, 'publish_begin', 'publish_end'], 'message' => __d('baser', '公開期間が不正です。')]],
			'publish_end' => [
				['rule' => ['checkDate'], 'allowEmpty' => true, 'message' => __d('baser', '公開終了日の形式が不正です。')],
				['rule' => ['checkDateRenge', 'allowEmpty' => true, 'publish_begin', 'publish_end'], 'message' => __d('baser', '公開期間が不正です。')]],
			'posts_date' => [
				['rule' => ['notBlank'], 'message' => __d('baser', '投稿日を入力してください。'), 'required' => true],
				['rule' => ['checkDate'], 'message' => __d('baser', '投稿日の形式が不正です。')]],
			'user_id' => [
				['rule' => ['notBlank'], 'message' => __d('baser', '投稿者を選択してください。')]],
			'eye_catch' => [
				['rule' => ['fileCheck', $this->convertSize(ini_get('upload_max_filesize'))], 'message' => __d('baser', 'ファイルのアップロードに失敗しました。')],
				['rule' => ['fileExt', ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png']], 'allowEmpty' => true, 'message' => __d('baser', '許可されていないファイルです。')]
			]
		];
	}

	/**
	 * アップロードビヘイビアの設定
	 *
	 * @param int $id ブログコンテンツID
	 */
	public function setupUpload($id)
	{
		$sizes = ['thumb', 'mobile_thumb'];
		$data = $this->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $id], 'recursive' => 0]);
		$data = $this->BlogContent->constructEyeCatchSize($data);
		$blogContent = $data['BlogContent'];

		$imagecopy = [];

		foreach($sizes as $size) {
			if (!isset($blogContent['eye_catch_size_' . $size . '_width']) || !isset($blogContent['eye_catch_size_' . $size . '_height'])) {
				continue;
			}
			$imagecopy[$size] = ['suffix' => '__' . $size];
			$imagecopy[$size]['width'] = $blogContent['eye_catch_size_' . $size . '_width'];
			$imagecopy[$size]['height'] = $blogContent['eye_catch_size_' . $size . '_height'];
		}

		$settings = $this->Behaviors->BcUpload->BcFileUploader['BlogPost']->settings;
		if (empty($settings['saveDir']) || !preg_match('/^' . preg_quote("blog" . DS . $blogContent['id'], '/') . '\//', $settings['saveDir'])) {
			$settings['saveDir'] = "blog" . DS . $blogContent['id'] . DS . "blog_posts";
		}

		$settings['fields']['eye_catch']['imagecopy'] = $imagecopy;
		$this->Behaviors->attach('BcUpload', $settings);
	}

	/**
	 * 初期値を取得する
	 *
	 * @return array $authUser 初期値データ
	 */
	public function getDefaultValue($authUser)
	{
		$data[$this->name]['user_id'] = $authUser['id'];
		$data[$this->name]['posts_date'] = date('Y/m/d H:i:s');
		$data[$this->name]['status'] = 0;
		return $data;
	}

	/**
	 * ブログの月別一覧を取得する
	 *
	 * @param int $blogContentId ブログコンテンツID
	 * @param array $options オプション
	 * @return array 月別リストデータ
	 */
	public function getPostedDates($blogContentId = null, $options = [])
	{
		$options = array_merge([
			'category' => false,
			'limit' => false,
			'viewCount' => false,
			'type' => 'month' // month Or year
		], $options);

		$conditions = [];
		if ($blogContentId) {
			$conditions = ['BlogPost.blog_content_id' => $blogContentId];
		}
		$conditions = array_merge($conditions, $this->getConditionAllowPublish());

		if ($options['category']) {
			$recursive = 1;
			$this->unbindModel([
				'belongsTo' => ['User', 'BlogContent'],
				'hasAndBelongsToMany' => ['BlogTag']
			]);
		} else {
			$recursive = -1;
		}

		// 毎秒抽出条件が違うのでキャッシュしない
		$posts = $this->find('all', [
			'conditions' => $conditions,
			'order' => 'BlogPost.posts_date DESC',
			'recursive' => $recursive,
		]);

		$dates = [];
		$counter = 0;

		foreach($posts as $post) {

			$exists = false;
			$_date = [];
			$year = date('Y', strtotime($post['BlogPost']['posts_date']));
			$month = date('m', strtotime($post['BlogPost']['posts_date']));
			$categoryId = $post['BlogPost']['blog_category_id'];

			foreach($dates as $key => $date) {

				if (!$options['category'] || $date['BlogCategory']['id'] == $categoryId) {
					if ($options['type'] == 'year' && $date['year'] == $year) {
						$exists = true;
					}
					if ($options['type'] == 'month' && $date['year'] == $year && $date['month'] == $month) {
						$exists = true;
					}
				}

				if ($exists) {
					if ($options['viewCount']) {
						$dates[$key]['count']++;
					}
					break;
				}
			}

			if (!$exists) {
				if ($options['type'] == 'year') {
					$_date['year'] = $year;
				} elseif ($options['type'] == 'month') {
					$_date['year'] = $year;
					$_date['month'] = $month;
				}
				if ($options['category']) {
					$_date['BlogCategory']['id'] = $categoryId;
					$_date['BlogCategory']['name'] = $post['BlogCategory']['name'];
					$_date['BlogCategory']['title'] = $post['BlogCategory']['title'];
				}
				if ($options['viewCount']) {
					$_date['count'] = 1;
				}
				$dates[] = $_date;
				$counter++;
			}

			if ($options['limit'] !== false && $options['limit'] <= $counter) {
				break;
			}
		}
		return $dates;
	}

	/**
	 * カレンダー用に指定した月で記事の投稿がある日付のリストを取得する
	 *
	 * @param int $blogContentId ブログコンテンツID
	 * @param int $year 年
	 * @param int $month 月
	 * @return array
	 */
	public function getEntryDates($blogContentId, $year, $month)
	{
		$entryDates = $this->find('all', [
			'fields' => ['BlogPost.posts_date'],
			'conditions' => $this->_getEntryDatesConditions($blogContentId, $year, $month),
			'recursive' => -1,
		]);
		$entryDates = Hash::extract($entryDates, '{n}.BlogPost.posts_date');
		foreach($entryDates as $key => $entryDate) {
			$entryDates[$key] = date('Y-m-d', strtotime($entryDate));
		}
		return $entryDates;
	}

	/**
	 * 投稿者の一覧を取得する
	 *
	 * @param int $blogContentId ブログコンテンツID
	 * @param array $options オプション
	 * @return array
	 */
	public function getAuthors($blogContentId, $options)
	{
		$options = array_merge([
			'viewCount' => false
		], $options);
		$users = $this->User->find('all', ['recursive' => -1, ['order' => 'User.id'], 'fields' => [
			'User.id', 'User.name', 'User.real_name_1', 'User.real_name_2', 'User.nickname'
		]]);
		$availableUsers = [];
		foreach($users as $key => $user) {
			$count = $this->find('count', ['conditions' => array_merge([
				'BlogPost.user_id' => $user['User']['id'],
				'BlogPost.blog_content_id' => $blogContentId
			], $this->getConditionAllowPublish())]);
			if ($count) {
				if ($options['viewCount']) {
					$user['count'] = $count;
				}
				$availableUsers[] = $user;
			}
		}
		return $availableUsers;
	}

	/**
	 * 指定した月の記事が存在するかチェックする
	 *
	 * @param int $blogContentId
	 * @param int $year
	 * @param int $month
	 * @return    boolean
	 */
	public function existsEntry($blogContentId, $year, $month)
	{
		if ($this->find('first', [
			'fields' => ['BlogPost.id'],
			'conditions' => $this->_getEntryDatesConditions($blogContentId, $year, $month),
			'recursive' => -1,
		])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 年月を指定した検索条件を生成
	 * データベースごとに構文が違う
	 *
	 * @param int $blogContentId
	 * @param int $year
	 * @param int $month
	 * @return array
	 */
	protected function _getEntryDatesConditions($blogContentId, $year, $month)
	{
		$datasource = $this->getDataSource()->config['datasource'];
		$conditions = [];
		switch($datasource) {
			case 'Database/BcMysql':
			case 'Database/BcCsv':
				if (!empty($year)) {
					$conditions["YEAR(`BlogPost`.`posts_date`)"] = $year;
				} else {
					$conditions["YEAR(`BlogPost`.`posts_date`)"] = date('Y');
				}
				if (!empty($month)) {
					$conditions["MONTH(`BlogPost`.`posts_date`)"] = $month;
				} else {
					$conditions["MONTH(`BlogPost`.`posts_date`)"] = date('m');
				}
				break;
			case 'Database/BcPostgres':
				if (!empty($year)) {
					$conditions["date_part('year', \"BlogPost\".\"posts_date\") ="] = $year;
				} else {
					$conditions["date_part('year', \"BlogPost\".\"posts_date\") ="] = date('Y');
				}
				if (!empty($month)) {
					$conditions["date_part('month', \"BlogPost\".\"posts_date\") ="] = $month;
				} else {
					$conditions["date_part('month', \"BlogPost\".\"posts_date\") ="] = date('m');
				}
				break;
			case 'Database/BcSqlite':
				if (!empty($year)) {
					$conditions["strftime('%Y',BlogPost.posts_date)"] = $year;
				} else {
					$conditions["strftime('%Y',BlogPost.posts_date)"] = date('Y');
				}
				if (!empty($month)) {
					$conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d', $month);
				} else {
					$conditions["strftime('%m',BlogPost.posts_date)"] = date('m');
				}
				break;
		}
		return am($conditions, ['BlogPost.blog_content_id' => $blogContentId], $this->getConditionAllowPublish());
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param array $options
	 * @return mixed false|array コントロールソース
	 */
	public function getControlSource($field, $options = [])
	{
		$options = array_merge([
			'blogContentId' => '',
			'userGroupId' => '',
			'blogCategoryId' => '',
			'empty' => ''
		], $options);

		$blogContentId = $options['blogContentId'];
		$empty = $options['empty'];
		unset($options['blogCategoryId']);
		unset($options['blogContentId']);
		unset($options['empty']);
		unset($options['userGroupId']);

		switch($field) {
			case 'blog_category_id':
				$catOption = ['blogContentId' => $blogContentId];
				$categories = $this->BlogCategory->getControlSource('parent_id', $catOption);
				// 「指定しない」追加
				if ($empty) {
					if ($categories) {
						$categories = ['' => $empty] + $categories;
					} else {
						$categories = ['' => $empty];
					}
				}
				$controlSources['blog_category_id'] = $categories;

				break;
			case 'user_id':
				$controlSources['user_id'] = $this->User->getUserList($options);
				break;
			case 'blog_tag_id':
				$controlSources['blog_tag_id'] = $this->BlogTag->find('list');
				break;
		}
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

	/**
	 * 公開状態を取得する
	 *
	 * @param array $data モデルデータ
	 * @return boolean 公開状態
	 */
	public function allowPublish($data)
	{
		if (isset($data['BlogPost'])) {
			$data = $data['BlogPost'];
		}

		$allowPublish = (int)$data['status'];

		if ($data['publish_begin'] == '0000-00-00 00:00:00') {
			$data['publish_begin'] = null;
		}
		if ($data['publish_end'] == '0000-00-00 00:00:00') {
			$data['publish_end'] = null;
		}

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
			($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}

		return $allowPublish;
	}

	/**
	 * 公開状態の記事を取得する
	 *
	 * @param array $options
	 * @return array
	 */
	public function getPublishes($options)
	{
		if (!empty($options['conditions'])) {
			$options['conditions'] = array_merge($this->getConditionAllowPublish(), $options['conditions']);
		} else {
			$options['conditions'] = $this->getConditionAllowPublish();
		}
		// 毎秒抽出条件が違うのでキャッシュしない
		$datas = $this->find('all', $options);
		return $datas;
	}

	/**
	 * After Validate
	 */
	public function afterValidate()
	{
		parent::afterValidate();
		if(empty($this->data['BlogPost']['blog_content_id'])) {
			throw new BcException('blog_content_id が指定されていません。');
		}
		if (empty($this->validationErrors) && empty($this->data['BlogPost']['id'])) {
			$this->data['BlogPost']['no'] = $this->getMax('no', ['BlogPost.blog_content_id' => $this->data['BlogPost']['blog_content_id']]) + 1;
		}
	}

	/**
	 * afterSave
	 *
	 * @param boolean $created
	 * @param array $options
	 */
	public function afterSave($created, $options = [])
	{
		// 検索用テーブルへの登録・削除
		if ($this->searchIndexSaving && empty($this->data['BlogPost']['exclude_search'])) {
			$this->saveSearchIndex($this->createSearchIndex($this->data));
		} else {
			if (!empty($this->data['BlogPost']['id'])) {
				$this->deleteSearchIndex($this->data['BlogPost']['id']);
			} elseif (!empty($this->id)) {
				$this->deleteSearchIndex($this->id);
			} else {
				$this->cakeError('Not found pk-value in BlogPost.');
			}
		}
	}

	/**
	 * 検索用データを生成する
	 *
	 * @param array $data
	 * @return array|false
	 */
	public function createSearchIndex($data)
	{
		if (isset($data['BlogPost'])) {
			$data = $data['BlogPost'];
		}
		$content = $this->BlogContent->Content->findByType('Blog.BlogContent', $data['blog_content_id']);
		if (!$content) {
			return false;
		}

		$status = $data['status'];
		$publishBegin = $data['publish_begin'];
		$publishEnd = $data['publish_end'];
		// コンテンツのステータスを優先する
		if (!$content['Content']['status']) {
			$status = false;
		}

		if ($publishBegin) {
			if ((!empty($content['Content']['publish_begin']) && $content['Content']['publish_begin'] > $publishBegin)) {
				// コンテンツの公開開始の方が遅い場合
				$publishBegin = $content['Content']['publish_begin'];
			} elseif (!empty($content['Content']['publish_end']) && $content['Content']['publish_end'] < $publishBegin) {
				// 記事の公開開始より、コンテンツの公開終了が早い場合
				$publishBegin = $content['Content']['publish_end'];
			}
		} else {
			if (!empty($content['Content']['publish_begin'])) {
				// 記事の公開開始が定められていない
				$publishBegin = $content['Content']['publish_begin'];
			}
		}
		if ($publishEnd) {
			if (!empty($content['Content']['publish_end']) && $content['Content']['publish_end'] < $publishEnd) {
				// コンテンツの公開終了の方が早い場合
				$publishEnd = $content['Content']['publish_end'];
			} elseif (!empty($content['Content']['publish_begin']) && $content['Content']['publish_begin'] < $publishEnd) {
				// 記事の公開終了より、コンテンツの公開開始が早い場合
				$publishEnd = $content['Content']['publish_begin'];
			}
		} else {
			if (!empty($content['Content']['publish_end'])) {
				// 記事の公開終了が定められていない
				$publishEnd = $content['Content']['publish_end'];
			}
		}

		return ['SearchIndex' => [
			'type' => __d('baser', 'ブログ'),
			'model_id' => $this->id,
			'content_filter_id' => !empty($data['blog_category_id'])? $data['blog_category_id'] : '',
			'content_id' => $content['Content']['id'],
			'site_id' => $content['Content']['site_id'],
			'title' => $data['name'],
			'detail' => $data['content'] . ' ' . $data['detail'],
			'url' => $content['Content']['url'] . 'archives/' . $data['no'],
			'status' => $status,
			'publish_begin' => $publishBegin,
			'publish_end' => $publishEnd
		]];

	}

	/**
	 * beforeDelete
	 *
	 * @return boolean
	 */
	public function beforeDelete($cascade = true)
	{
		return $this->deleteSearchIndex($this->id);
	}

	/**
	 * コピーする
	 *
	 * @param int $id
	 * @param array $data
	 * @return mixed page Or false
	 */
	public function copy($id = null, $data = [])
	{
		if ($id) {
			$data = $this->find('first', ['conditions' => ['BlogPost.id' => $id]]);
		}
		$oldData = $data;

		// EVENT BlogPost.beforeCopy
		$event = $this->dispatchEvent('beforeCopy', [
			'data' => $data,
			'id' => $id,
		]);
		if ($event !== false) {
			$data = $event->result === true? $event->data['data'] : $event->result;
		}

		$sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
		if (!empty($_SESSION['Auth'][$sessionKey])) {
			$data['BlogPost']['user_id'] = $_SESSION['Auth'][$sessionKey]['id'];
		}

		$data['BlogPost']['name'] .= '_copy';
		$data['BlogPost']['no'] = $this->getMax('no', ['BlogPost.blog_content_id' => $data['BlogPost']['blog_content_id']]) + 1;
		$data['BlogPost']['status'] = '0'; // TODO intger の為 false では正常に保存できない（postgreSQLで確認）
		$data['BlogPost']['posts_date'] = date('Y-m-d H:i:s');

		unset($data['BlogPost']['id']);
		unset($data['BlogPost']['created']);
		unset($data['BlogPost']['modified']);

		// 一旦退避(afterSaveでリネームされてしまうのを避ける為）
		$eyeCatch = $data['BlogPost']['eye_catch'];
		unset($data['BlogPost']['eye_catch']);

		if (!empty($data['BlogTag'])) {
			foreach($data['BlogTag'] as $key => $tag) {
				$data['BlogTag'][$key] = $tag['id'];
			}
		}

		$this->create($data);
		$result = $this->save();

		if ($result) {
			if ($eyeCatch) {
				$result['BlogPost']['eye_catch'] = $eyeCatch;
				$this->set($result);
				$result = $this->renameToBasenameFields(true);
				$this->set($result);    // 内部でリネームされたデータが再セットされる
				$result = $this->save();
			}
			// EVENT BlogPost.afterCopy
			$this->dispatchEvent('afterCopy', [
				'id' => $result['BlogPost']['id'],
				'data' => $result,
				'oldId' => $id,
				'oldData' => $oldData,
			]);
			return $result;
		} else {
			if (isset($this->validationErrors['name'])) {
				return $this->copy(null, $data);
			} else {
				return false;
			}
		}
	}

	/**
	 * プレビュー用のデータを生成する
	 *
	 * @param array $data
	 */
	public function createPreviewData($data)
	{
		$post['BlogPost'] = $data['BlogPost'];
		if (isset($post['BlogPost']['detail_tmp'])) {
			$post['BlogPost']['detail'] = $post['BlogPost']['detail_tmp'];
		}

		if ($data['BlogPost']['blog_category_id']) {
			$blogCategory = $this->BlogCategory->find('first', [
				'conditions' => ['BlogCategory.id' => $data['BlogPost']['blog_category_id']],
				'recursive' => -1
			]);
			$post['BlogCategory'] = $blogCategory['BlogCategory'];
		}

		if ($data['BlogPost']['user_id']) {
			$author = $this->User->find('first', [
				'conditions' => ['User.id' => $data['BlogPost']['user_id']],
				'recursive' => -1
			]);
			$post['User'] = $author['User'];
		}

		if (!empty($data['BlogTag']['BlogTag'])) {
			$tags = $this->BlogTag->find('all', [
				'conditions' => ['BlogTag.id' => $data['BlogTag']['BlogTag']],
				'recursive' => -1
			]);
			if ($tags) {
				$tags = Hash::extract($tags, '{n}.BlogTag');
				$post['BlogTag'] = $tags;
			}
		}

		// BlogPostキーのデータは作り直しているため、元データは削除して他のモデルキーのデータとマージする
		unset($data['BlogPost']);
		unset($data['BlogTag']); // プレビュー時に、フロントでの利用データの形式と異なるため削除
		$post = Hash::merge($data, $post);

		return $post;
	}

	/**
	 * Before Find
	 *
	 * @param array $options
	 * @return array
	 */
	public function beforeFind($options)
	{
		// ================================================================
		// 日付等全く同じ値のレコードが複数存在する場合の並び替え処理を安定する為、
		// IDが order に入っていない場合、IDを追加する
		// PostgreSQLの場合、max min count sum を利用している際に、order を
		// 指定するとエラーとなってしまうので、追加するのは最小限にする
		// ================================================================
		$idRequire = false;
		if (!empty($options['order']) && isset($options['order'][0]) && $options['order'][0] !== false) {
			$idRequire = true;
			if (is_array($options['order'])) {
				foreach($options['order'] as $key => $value) {
					if (strpos($value, ',') !== false) {
						$orders = explode(',', $value);
						foreach($orders as $order) {
							if (strpos($order, 'BlogPost.id') !== false) {
								$idRequire = false;
							}
						}
					} else {
						if (strpos($key, 'BlogPost.id') !== false) {
							$idRequire = false;
						}
					}
				}
			} else {
				if (strpos('BlogPost.id', $options['sort']) === false) {
					$idRequire = false;
				}
			}
		}
		if ($idRequire) {
			$options['order']['BlogPost.id'] = 'DESC';
		}
		return $options;
	}

	/**
	 * カスタムパラメーター検索
	 * ※ カスタムファインダーメソッド
	 *
	 * @param string $state
	 * @param array $query
	 * @param array $results
	 * @return array
	 */
	public function _findCustomParams($state, $query, $results = [])
	{
		if ($state == 'before') {
			$expects = ['BlogContent', 'BlogCategory', 'User', 'BlogTag'];
			$assocContent = false;
			$query = array_merge([
				'conditions' => [],        // 検索条件のベース
				'listCount' => null,    // 件数（非推奨）
				'num' => null,            // 件数
				'limit' => null,        // 件数
				'direction' => 'DESC',    // 並び方向
				'sort' => 'posts_date',    // 並び順対象のフィールド
				'page' => 1,            // ページ数
				'contentId' => null,    // 《条件》ブログコンテンツID
				'category' => null,        // 《条件》カテゴリ
				'tag' => null,            // 《条件》タグ
				'year' => null,            // 《条件》年
				'month' => null,        // 《条件》月
				'day' => null,            // 《条件》日
				'id' => null,            // 《条件》記事NO
				'no' => null,            // 《条件》記事NO
				'keyword' => null,        // 《条件》キーワード
				'author' => null,        // 《条件》作成者
				'postId' => null,        // 《条件》記事ID
				'siteId' => null,        // 《条件》サイトID
				'contentUrl' => null,    // 《条件》コンテンツURL
				'preview' => false,        // プレビュー
				'recursive' => 2,        // 取得範囲
				'cache' => false,        // キャッシュ
				'force' => false,        // 強制取得
			], $query);

			// 取得件数
			// TODO num に統一する
			if ($query['listCount'] && !$query['num']) {
				$query['num'] = $query['listCount'];
			}
			if ($query['num']) {
				$query['limit'] = $query['num'];
			}
			// 並び順
			if ($query['sort']) {
				$order = $this->createOrder($query['sort'], $query['direction']);
				if (empty($query['order'])) {
					$query['order'] = $order;
				} else {
					if (is_array($query['order'])) {
						$query['order'] = array_merge([$order], $query['order']);
					} else {
						$query['order'] = $order . ',' . $query['order'];
					}
				}
			}

			if (is_null($query['conditions'])) {
				$conditions = [];
			} else {
				$conditions = $query['conditions'];
			}

			// ブログコンテンツID
			if ($query['contentId']) {
				$conditions['BlogPost.blog_content_id'] = $query['contentId'];
			}
			// カテゴリ
			if ($query['category']) {
				$conditions = $this->createCategoryCondition($conditions, $query['category'], $query['contentId'], $query['contentUrl'], $query['force']);
			}
			// タグ
			if ($query['tag']) {
				$conditions = $this->createTagCondition($conditions, $query['tag']);
			}
			// キーワード
			if ($query['keyword']) {
				$conditions = $this->createKeywordCondition($conditions, $query['keyword']);
			}
			// 年月日
			if ($query['year'] || $query['month'] || $query['day']) {
				$conditions = $this->createYearMonthDayCondition($conditions, $query['year'], $query['month'], $query['day']);
			}
			// 作成者
			if ($query['author']) {
				$conditions = $this->createAuthorCondition($conditions, $query['author']);
			}
			// ID
			if ($query['postId']) {
				$conditions["BlogPost.id"] = $query['postId'];
				$expects[] = 'BlogComment';
				$this->hasMany['BlogComment']['conditions'] = ['BlogComment.status' => true];
			}
			// NO
			if ($query['id'] || $query['no']) {
				if (!$query['contentId'] && !$query['contentUrl'] && !$query['force']) {
					trigger_error(__d('baser', 'contentId を指定してください。'), E_USER_WARNING);
				}
				if ($query['no'] && !$query['id']) {
					$query['id'] = $query['no'];
				}
				$conditions["BlogPost.no"] = $query['id'];
				$expects[] = 'BlogComment';
				$this->hasMany['BlogComment']['conditions'] = ['BlogComment.status' => true];
			}
			// サイトID
			if (!is_null($query['siteId'])) {
				$conditions['Content.site_id'] = $query['siteId'];
				$assocContent = true;
			}
			// URL
			if ($query['contentUrl']) {
				$conditions['Content.url'] = $query['contentUrl'];
				$assocContent = true;
			}
			if ($assocContent) {
				$query['joins'] = [[
					'type' => 'LEFT',
					'table' => 'contents',
					'alias' => 'Content',
					'conditions' => "Content.entity_id=BlogContent.id AND Content.type='BlogContent'",
				]];
			}
			// 公開条件
			if (empty($query['preview'])) {
				$conditions = array_merge($conditions, $this->getConditionAllowPublish());
				$query['cache'] = false;
			}
			$query['conditions'] = $conditions;

			unset($query['contentId'], $query['category'], $query['tag'], $query['keyword'],
				$query['year'], $query['month'], $query['day'], $query['author'], $query['id'],
				$query['preview'], $query['sort'], $query['direction'], $query['num'],
				$query['force'], $query['no'], $query['siteId'], $query['contentUrl']);

			$this->reduceAssociations($expects, false);

			$this->BlogContent->unbindModel([
				'hasMany' => ['BlogPost', 'BlogCategory']
			]);
			$this->BlogCategory->unbindModel([
				'hasMany' => ['BlogPost']
			]);
			$this->User->unbindModel([
				'hasMany' => ['Favorite']
			]);
			return $query;
		}
		return $results;
	}

	/**
	 * カテゴリ条件を生成する
	 *
	 * @param array $conditions
	 * @param string $category
	 * @param int $contentId
	 * @param bool $force
	 * @return array
	 */
	public function createCategoryCondition($conditions, $category, $contentId = null, $contentUrl = null, $force = false)
	{
		$categoryConditions = ['BlogCategory.name' => $category];
		if ($contentId) {
			$categoryConditions['BlogCategory.blog_content_id'] = $contentId;
		} elseif ($contentUrl) {
			$categoryConditions['BlogCategory.blog_content_id'] = $this->BlogContent->Content->field('entity_id', ['Content.url' => $contentUrl]);
		} elseif (!$force) {
			trigger_error(__d('baser', 'contentId を指定してください。'), E_USER_WARNING);
		}

		$categoryData = $this->BlogCategory->find('all', [
			'conditions' => $categoryConditions,
		]);
		$categoryIds = Hash::extract($categoryData, '{n}.BlogCategory.id');
		if (!$categoryIds) {
			$categoryIds = false;
		} else {
			// 指定したカテゴリ名にぶら下がる子カテゴリを取得
			foreach($categoryIds as $categoryId) {
				$catChildren = $this->BlogCategory->children($categoryId);
				if ($catChildren) {
					$categoryIds = am($categoryIds, Hash::extract($catChildren, '{n}.BlogCategory.id'));
				}
			}
		}
		if ($categoryIds === false) {
			$conditions['BlogPost.id'] = null;
		} else {
			$conditions['BlogPost.blog_category_id'] = $categoryIds;
		}
		return $conditions;
	}

	/**
	 * タグ条件を生成する
	 *
	 * @param array $conditions
	 * @param mixed $tag タグ（配列可）
	 * @return array
	 */
	public function createTagCondition($conditions, $tag)
	{
		if (!is_array($tag)) {
			$tag = [$tag];
		}
		foreach($tag as $key => $value) {
			$tag[$key] = urldecode($value);
		}
		$tags = $this->BlogTag->find('all', [
			'conditions' => ['BlogTag.name' => $tag],
			'recursive' => 1,
			'cache' => false,
		]);
		if (isset($tags[0]['BlogPost'][0]['id'])) {
			$conditions['BlogPost.id'] = Hash::extract($tags, '{n}.BlogPost.{n}.id');
		} else {
			$conditions['BlogPost.id'] = null;
		}
		return $conditions;
	}

	/**
	 * キーワード条件を生成する
	 *
	 * @param array $conditions
	 * @param string $keyword
	 * @return array
	 */
	public function createKeywordCondition($conditions, $keyword)
	{
		$keyword = str_replace('　', ' ', $keyword);
		if (strpos($keyword, ' ') !== false) {
			$keywords = explode(" ", $keyword);
		} else {
			$keywords = [$keyword];
		}
		foreach($keywords as $key => $value) {
			$value = h(urldecode($value));
			$conditions['and'][$key]['or'][] = ['BlogPost.name LIKE' => "%{$value}%"];
			$conditions['and'][$key]['or'][] = ['BlogPost.content LIKE' => "%{$value}%"];
			$conditions['and'][$key]['or'][] = ['BlogPost.detail LIKE' => "%{$value}%"];
		}
		return $conditions;
	}

	/**
	 * 年月日条件を生成する
	 *
	 * @param array $conditions
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @return array
	 */
	public function createYearMonthDayCondition($conditions, $year, $month, $day)
	{
		$datasouce = strtolower(preg_replace('/^Database\/Bc/', '', ConnectionManager::getDataSource($this->useDbConfig)->config['datasource']));
		switch($datasouce) {
			case 'mysql':
				if ($year) $conditions["YEAR(BlogPost.posts_date)"] = $year;
				if ($month) $conditions["MONTH(BlogPost.posts_date)"] = $month;
				if ($day) $conditions["DAY(BlogPost.posts_date)"] = $day;
				break;
			case 'postgres':
				if ($year) $conditions["date_part('year',BlogPost.posts_date) = "] = $year;
				if ($month) $conditions["date_part('month',BlogPost.posts_date) = "] = $month;
				if ($day) $conditions["date_part('day',BlogPost.posts_date) = "] = $day;
				break;
			case 'sqlite':
				if ($year) $conditions["strftime('%Y',BlogPost.posts_date)"] = (string)$year;
				if ($month) $conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d', $month);
				if ($day) $conditions["strftime('%d',BlogPost.posts_date)"] = sprintf('%02d', $day);
				break;
		}
		return $conditions;
	}

	/**
	 * 作成者の条件を作成する
	 *
	 * @param array $conditions
	 * @param string $author
	 * @return array
	 */
	public function createAuthorCondition($conditions, $author)
	{
		$userId = ClassRegistry::init('User')->field('id', ['User.name' => $author]);
		$conditions['BlogPost.user_id'] = $userId;
		return $conditions;
	}

	/**
	 * 並び替え設定を生成する
	 *
	 * @param string $sort
	 * @param string $direction
	 * @return string
	 */
	public function createOrder($sort, $direction)
	{
		$order = '';
		if (strtoupper($direction) == 'RANDOM') {
			$datasouce = strtolower(preg_replace('/^Database\/Bc/', '', ConnectionManager::getDataSource($this->useDbConfig)->config['datasource']));
			switch($datasouce) {
				case 'mysql':
					$order = 'RAND()';
					break;
				case 'postgres':
					$order = 'RANDOM()';
					break;
				case 'sqlite':
					$order = 'RANDOM()';
					break;
			}
		} else {
			$order = "BlogPost.{$sort} {$direction}, BlogPost.id {$direction}";
		}
		return $order;
	}

}
