<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BlogAppModel', 'Blog.Model');

/**
 * 記事モデル
 *
 * @package Blog.Model
 * @property BlogCategory $BlogCategory
 */
class BlogPost extends BlogAppModel {

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
 * ビヘイビア
 *
 * @var array
 */
	public $actsAs = [
		'BcSearchIndexManager',
		'BcCache',
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
 * validate
 *
 * @var array
 */
	public $validate = [
		'name' => [
			['rule' => ['notBlank'],
				'message' => 'タイトルを入力してください。',
				'required' => true],
			['rule' => ['maxLength', 255],
				'message' => 'タイトルは255文字以内で入力してください。']
		],
		'detail' => [
			['rule' => ['maxByte', 64000],
			'message' => '本稿欄に保存できるデータ量を超えています。']
		],
		'detail_draft' => [
			['rule' => ['maxByte', 64000],
			'message' => '草稿欄に保存できるデータ量を超えています。']
		],
		'publish_begin' => [
			['rule' => ['checkDate'],
				'message' => '公開開始日の形式が不正です。'],
			['rule' => ['checkDateRenge', 'publish_begin', 'publish_end'],
				'message' => '公開期間が不正です。']
			
		],
		'publish_end' => [
			['rule' => ['checkDate'],
				'message' => '公開終了日の形式が不正です。'],
			['rule' => ['checkDateRenge', 'publish_begin', 'publish_end'],
				'message' => '公開期間が不正です。']
		],
		'posts_date' => [
			['rule' => ['notBlank'],
				'message' => '投稿日を入力してください。',
				'required' => true],
			['rule' => ['checkDate'],
				'message' => '投稿日の形式が不正です。']
		],
		'user_id' => [
			['rule' => ['notBlank'],
				'message' => '投稿者を選択してください。']
		]
	];

/**
 * アップロードビヘイビアの設定
 *
 * @param	int $id ブログコンテンツID
 */
	public function setupUpload($id) {
		$sizes = ['thumb', 'mobile_thumb'];
		$data = $this->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $id], 'recursive' => 0]);
		$data = $this->BlogContent->constructEyeCatchSize($data);
		$blogContent = $data['BlogContent'];

		$imagecopy = [];

		foreach ($sizes as $size) {
			if (!isset($blogContent['eye_catch_size_' . $size . '_width']) || !isset($blogContent['eye_catch_size_' . $size . '_height'])) {
				continue;
			}
			$imagecopy[$size] = ['suffix' => '__' . $size];
			$imagecopy[$size]['width'] = $blogContent['eye_catch_size_' . $size . '_width'];
			$imagecopy[$size]['height'] = $blogContent['eye_catch_size_' . $size . '_height'];
		}

		$settings = $this->Behaviors->BcUpload->settings['BlogPost'];
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
	public function getDefaultValue($authUser) {
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
	public function getPostedDates($blogContentId = null, $options = []) {
		$options = array_merge([
			'category' => false,
			'limit' => false,
			'viewCount' => false,
			'type' => 'month' // month Or year
			], $options);

		extract($options);
		if($blogContentId) {
			$conditions = ['BlogPost.blog_content_id' => $blogContentId];	
		}
		$conditions = array_merge($conditions, $this->getConditionAllowPublish());
		// TODO CSVDBではGROUP BYが実装されていない為、取り急ぎPHPで処理
		/* $dates = $this->find('all',array('fields'=>array('YEAR(posts_date) as year','MONTH(posts_date) as month','COUNT(id)' as count),
		  $conditions,
		  'group'=>array('YEAR(posts_date)','MONTH(posts_date)')))); */

		if ($category) {
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
			'cache' => false
		]);

		$dates = [];
		$counter = 0;

		foreach ($posts as $post) {

			$exists = false;
			$_date = [];
			$year = date('Y', strtotime($post['BlogPost']['posts_date']));
			$month = date('m', strtotime($post['BlogPost']['posts_date']));
			$categoryId = $post['BlogPost']['blog_category_id'];

			foreach ($dates as $key => $date) {

				if (!$category || $date['BlogCategory']['id'] == $categoryId) {
					if ($type == 'year' && $date['year'] == $year) {
						$exists = true;
					}
					if ($type == 'month' && $date['year'] == $year && $date['month'] == $month) {
						$exists = true;
					}
				}

				if ($exists) {
					if ($viewCount) {
						$dates[$key]['count'] ++;
					}
					break;
				}
			}

			if (!$exists) {
				if ($type == 'year') {
					$_date['year'] = $year;
				} elseif ($type == 'month') {
					$_date['year'] = $year;
					$_date['month'] = $month;
				}
				if ($category) {
					$_date['BlogCategory']['id'] = $categoryId;
					$_date['BlogCategory']['name'] = $post['BlogCategory']['name'];
					$_date['BlogCategory']['title'] = $post['BlogCategory']['title'];
				}
				if ($viewCount) {
					$_date['count'] = 1;
				}
				$dates[] = $_date;
				$counter++;
			}

			if ($limit !== false && $limit <= $counter) {
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
	public function getEntryDates($blogContentId, $year, $month) {
		$entryDates = $this->find('all', [
			'fields' => ['BlogPost.posts_date'],
			'conditions' => $this->_getEntryDatesConditions($blogContentId, $year, $month),
			'recursive' => -1,
			'cache' => false
		]);
		$entryDates = Hash::extract($entryDates, '{n}.BlogPost.posts_date');
		foreach ($entryDates as $key => $entryDate) {
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
	public function getAuthors($blogContentId, $options) {
		$options = array_merge([
			'viewCount' => false
			], $options);
		extract($options);

		$users = $this->User->find('all', ['recursive' => -1, ['order' => 'User.id'], 'fields' => [
				'User.id', 'User.name', 'User.real_name_1', 'User.real_name_2', 'User.nickname'
		]]);
		$availableUsers = [];
		foreach ($users as $key => $user) {
			$count = $this->find('count', ['conditions' => array_merge([
					'BlogPost.user_id' => $user['User']['id'],
					'BlogPost.blog_content_id' => $blogContentId
					], $this->getConditionAllowPublish())]);
			if ($count) {
				if ($viewCount) {
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
 * @param	int $blogContentId
 * @param	int $year
 * @param	int $month
 * @return	boolean
 */
	public function existsEntry($blogContentId, $year, $month) {
		if ($this->find('first', [
				'fields' => ['BlogPost.id'],
				'conditions' => $this->_getEntryDatesConditions($blogContentId, $year, $month),
				'recursive' => -1,
				'cache' => false
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
 * @return string
 */
	protected function _getEntryDatesConditions($blogContentId, $year, $month) {

		$datasource = $this->getDataSource()->config['datasource'];

		switch ($datasource) {
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

		$conditions = am($conditions, ['BlogPost.blog_content_id' => $blogContentId], $this->getConditionAllowPublish());
		return $conditions;
	}

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param	array	$options
 * @return	array	コントロールソース
 * @access	public
 */
	public function getControlSource($field, $options = []) {
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
		
		switch ($field) {
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
 * @param array データリスト
 * @return boolean 公開状態
 */
	public function allowPublish($data) {
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
 * 公開済の conditions を取得
 * 
 * @return array
 * @access public 
 */
	public function getConditionAllowPublish() {
		$conditions[$this->alias . '.status'] = true;
		$conditions[] = ['or' => [[$this->alias . '.publish_begin <=' => date('Y-m-d H:i:s')],
				[$this->alias . '.publish_begin' => null],
				[$this->alias . '.publish_begin' => '0000-00-00 00:00:00']]];
		$conditions[] = ['or' => [[$this->alias . '.publish_end >=' => date('Y-m-d H:i:s')],
				[$this->alias . '.publish_end' => null],
				[$this->alias . '.publish_end' => '0000-00-00 00:00:00']]];
		return $conditions;
	}

/**
 * 公開状態の記事を取得する
 *
 * @param array $options
 * @return array
 */
	public function getPublishes($options) {
		if (!empty($options['conditions'])) {
			$options['conditions'] = array_merge($this->getConditionAllowPublish(), $options['conditions']);
		} else {
			$options['conditions'] = $this->getConditionAllowPublish();
		}
		// 毎秒抽出条件が違うのでキャッシュしない
		$options['cache'] = false;
		$datas = $this->find('all', $options);
		return $datas;
	}

/**
 * afterSave
 *
 * @param boolean $created
 * @return boolean
 */
	public function afterSave($created, $options = []) {
		// 検索用テーブルへの登録・削除
		if ($this->searchIndexSaving && !$this->data['BlogPost']['exclude_search']) {
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
 * @return array
 */
	public function createSearchIndex($data) {
		if (isset($data['BlogPost'])) {
			$data = $data['BlogPost'];
		}
		$content = $this->BlogContent->Content->findByType('Blog.BlogContent', $data['blog_content_id']);
		if(!$content) {
			return [];
		}
		$_data = [];
		$_data['SearchIndex']['type'] = 'ブログ';
		$_data['SearchIndex']['model_id'] = $this->id;
		$_data['SearchIndex']['content_filter_id'] = '';
		if (!empty($data['blog_category_id'])) {
			$_data['SearchIndex']['content_filter_id'] = $data['blog_category_id'];
		}
		$_data['SearchIndex']['content_id'] = $content['Content']['id'];
		$_data['SearchIndex']['site_id'] = $content['Content']['site_id'];
		$_data['SearchIndex']['title'] = $data['name'];
		$_data['SearchIndex']['detail'] = $data['content'] . ' ' . $data['detail'];
		$_data['SearchIndex']['url'] = $content['Content']['url'] . '/archives/' . $data['no'];
		$_data['SearchIndex']['status'] = $this->allowPublish($data);
		$_data['SearchIndex']['content_filter_id'] = $data['blog_category_id'];
		return $_data;
	}

/**
 * beforeDelete
 *
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		return $this->deleteSearchIndex($this->id);
	}

/**
 * コピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed page Or false
 */
	public function copy($id = null, $data = []) {
		if ($id) {
			$data = $this->find('first', ['conditions' => ['BlogPost.id' => $id], 'recursive' => 1]);
		}
		$sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
		if (!empty($_SESSION['Auth'][$sessionKey])) {
			$data['BlogPost']['user_id'] = $_SESSION['Auth'][$sessionKey]['id'];
		}

		$data['BlogPost']['name'] .= '_copy';
		$data['BlogPost']['no'] = $this->getMax('no', ['BlogPost.blog_content_id' => $data['BlogPost']['blog_content_id']]) + 1;
		$data['BlogPost']['status'] = '0'; // TODO intger の為 false では正常に保存できない（postgreSQLで確認）

		unset($data['BlogPost']['id']);
		unset($data['BlogPost']['created']);
		unset($data['BlogPost']['modified']);

		// 一旦退避(afterSaveでリネームされてしまうのを避ける為）
		$eyeCatch = $data['BlogPost']['eye_catch'];
		unset($data['BlogPost']['eye_catch']);

		if (!empty($data['BlogTag'])) {
			foreach ($data['BlogTag'] as $key => $tag) {
				$data['BlogTag'][$key] = $tag['id'];
			}
		}

		$this->create($data);
		$result = $this->save();

		if ($result) {
			if ($eyeCatch) {
				$data['BlogPost']['id'] = $this->getLastInsertID();
				$data['BlogPost']['eye_catch'] = $eyeCatch;
				$this->set($data);
				$this->set($this->renameToBasenameFields(true));	// 内部でリネームされたデータが再セットされる
				$result = $this->save();
			}
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
	public function createPreviewData($data) {
		$post['BlogPost'] = $data['BlogPost'];
		if(isset($post['BlogPost']['detail_tmp'])) {
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
		return $post;
	}

/**
 * Before Find
 *
 * @param array $options
 * @return array
 */
	public function beforeFind($options) {
		// ================================================================
		// 日付等全く同じ値のレコードが複数存在する場合の並び替え処理を安定する為、
		// IDが order に入っていない場合、IDを追加する
		// PostgreSQLの場合、max min count sum を利用している際に、order を
		// 指定するとエラーとなってしまうので、追加するのは最小限にする
		// ================================================================
		$idRequire = false;
		if(!empty($options['order']) && $options['order'][0] !== false) {
			$idRequire = true;
			if(is_array($options['order'])) {
				foreach($options['order'] as $key => $value) {
					if(strpos($value, ',') !== false) {
						$orders = explode(',', $value);
						foreach($orders as $order) {
							if(strpos($order, 'BlogPost.id') !== false) {
								$idRequire = false;
							}
						}
					} else {
						if(strpos($key, 'BlogPost.id') !== false) {
							$idRequire = false;
						}
					}
				}
			} else {
				if(strpos('BlogPost.id', $options['sort']) === false) {
					$idRequire = false;
				}
			}
		}
		if($idRequire) {
			$options['order']['BlogPost.id'] = 'DESC';
		}
		return $options;
	}

}
