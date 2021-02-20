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
 * ブログカテゴリモデル
 *
 * @package Blog.Model
 * @property BlogPost $BlogPost
 */
class BlogCategory extends BlogAppModel
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'BlogCategory';

	/**
	 * バリデーション設定
	 *
	 * @var array
	 */
	public $validationParams = [];

	/**
	 * actsAs
	 *
	 * @var array
	 */
	public $actsAs = ['Tree', 'BcCache'];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = ['BlogPost' =>
		['className' => 'Blog.BlogPost',
			'order' => 'id DESC',
			'limit' => 10,
			'foreignKey' => 'blog_category_id',
			'dependent' => false,
			'exclusive' => false,
			'finderQuery' => '']];

	/**
	 * BlogCategory constructor.
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
				['rule' => ['notBlank'], 'message' => __d('baser', 'カテゴリ名を入力してください。'), 'required' => true],
				['rule' => 'alphaNumericDashUnderscore', 'message' => __d('baser', 'カテゴリ名は半角のみで入力してください。')],
				['rule' => ['duplicateBlogCategory'], 'message' => __d('baser', '入力されたカテゴリ名は既に登録されています。')],
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'カテゴリ名は255文字以内で入力してください。')]],
			'title' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'カテゴリタイトルを入力してください。'), 'required' => true],
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'カテゴリタイトルは255文字以内で入力してください。')]]
		];
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param array $option オプション
	 * @return array コントロールソース
	 */
	public function getControlSource($field, $options = [])
	{
		switch($field) {
			case 'parent_id':
				if (!isset($options['blogContentId'])) {
					return false;
				}
				$conditions = [];
				if (isset($options['conditions'])) {
					$conditions = $options['conditions'];
				}
				$conditions['BlogCategory.blog_content_id'] = $options['blogContentId'];
				if (!empty($options['excludeParentId'])) {
					$children = $this->children($options['excludeParentId']);
					$excludeIds = [$options['excludeParentId']];
					foreach($children as $child) {
						$excludeIds[] = $child['BlogCategory']['id'];
					}
					$conditions['NOT']['BlogCategory.id'] = $excludeIds;
				}

				if (isset($options['ownerId'])) {
					$ownerIdConditions = [
						['BlogCategory.owner_id' => null],
						['BlogCategory.owner_id' => $options['ownerId']],
					];
					if (isset($conditions['OR'])) {
						$conditions['OR'] = am($conditions['OR'], $ownerIdConditions);
					} else {
						$conditions['OR'] = $ownerIdConditions;
					}
				}

				$parents = $this->generateTreeList($conditions);
				$controlSources['parent_id'] = [];
				foreach($parents as $key => $parent) {
					if (preg_match("/^([_]+)/i", $parent, $matches)) {
						$parent = preg_replace("/^[_]+/i", '', $parent);
						$prefix = str_replace('_', '　　　', $matches[1]);
						$parent = $prefix . '└' . $parent;
					}
					$controlSources['parent_id'][$key] = $parent;
				}
				break;
			case 'owner_id':
				$UserGroup = ClassRegistry::init('UserGroup');
				$controlSources['owner_id'] = $UserGroup->find('list', ['fields' => ['id', 'title'], 'recursive' => -1]);
				break;
		}

		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

	/**
	 * 同じニックネームのカテゴリがないかチェックする
	 * 同じブログコンテンツが条件
	 *
	 * @param array $check
	 * @return boolean
	 */
	public function duplicateBlogCategory($check)
	{
		$conditions = ['BlogCategory.' . key($check) => $check[key($check)],
			'BlogCategory.blog_content_id' => $this->validationParams['blogContentId']];
		if ($this->exists()) {
			$conditions['NOT'] = ['BlogCategory.id' => $this->id];
		}
		$ret = $this->find('first', ['conditions' => $conditions]);
		if ($ret) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 関連する記事データをカテゴリ無所属に変更し保存する
	 *
	 * @param boolean $cascade
	 * @return boolean
	 */
	public function beforeDelete($cascade = true)
	{
		parent::beforeDelete($cascade);
		$ret = true;
		if (!empty($this->data['BlogCategory']['id'])) {
			$id = $this->data['BlogCategory']['id'];
			$this->BlogPost->unBindModel(['belongsTo' => ['BlogCategory']]);
			$datas = $this->BlogPost->find('all', ['conditions' => ['BlogPost.blog_category_id' => $id]]);
			if ($datas) {
				foreach($datas as $data) {
					$data['BlogPost']['blog_category_id'] = '';
					$this->BlogPost->set($data);
					if (!$this->BlogPost->save()) {
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * カテゴリリストを取得する
	 *
	 * @param int $blogContentId
	 * @param array $options
	 * @return array
	 */
	public function getCategoryList($blogContentId = null, $options = [])
	{
		$options = array_merge([
			'siteId' => null,
			'depth' => 1,
			'type' => null,
			'limit' => false,
			'viewCount' => false,
			'parentId' => null,
			'fields' => ['BlogCategory.id', 'BlogCategory.name', 'BlogCategory.title', 'BlogCategory.lft', 'BlogCategory.rght'],
		], $options);
		$fields = $options['fields'];
		$depth = $options['depth'];
		$parentId = $options['parentId'];
		unset($options['fields']);
		unset($options['depth']);
		unset($options['parentId']);
		$datas = [];
		if (!$options['type']) {
			$datas = $this->_getCategoryList($blogContentId, $parentId, $options['viewCount'], $depth, 1, $fields, $options);
		} elseif ($options['type'] == 'year') {
			$options = [
				'category' => true,
				'limit' => $options['limit'],
				'viewCount' => $options['viewCount'],
				'type' => 'year'
			];
			$_datas = $this->BlogPost->getPostedDates($blogContentId, $options);
			$datas = [];
			foreach($_datas as $data) {
				if ($options['viewCount']) {
					$data['BlogCategory']['count'] = $data['count'];
				}
				$datas[$data['year']][] = ['BlogCategory' => $data['BlogCategory']];
			}
		}
		return $datas;
	}

	/**
	 * カテゴリリストを取得する（再帰処理）
	 *
	 * @param int $blogContentId
	 * @param int $parentId
	 * @param int $viewCount
	 * @param int $depth
	 * @param int $current
	 * @param array $fields
	 * @param array $options
	 * @return array
	 */
	protected function _getCategoryList($blogContentId = null, $parentId = null, $viewCount = false, $depth = 1, $current = 1, $fields = [], $options = [])
	{
		$options = array_merge([
			'id' => null,
			'siteId' => null,
			'order' => 'BlogCategory.lft asc',
			'conditions' => [],
			'threaded' => false
		], $options);
		$conditions = $options['conditions'];
		if (!empty($options['id'])) {
			$parentId = false;
		}
		// 親を指定する場合
		if ($parentId !== false) {
			$conditions['BlogCategory.parent_id'] = $parentId;
		}
		if (!empty($options['id'])) {
			$conditions['BlogCategory.id'] = $options['id'];
		}
		if ($options['siteId'] !== false && !is_null($options['siteId'])) {
			$conditions['Content.site_id'] = $options['siteId'];
		}
		if (!is_null($blogContentId)) {
			$conditions['BlogCategory.blog_content_id'] = $blogContentId;
		}
		$findType = 'all';
		if ($options['threaded']) {
			$findType = 'threaded';
			$options['order'] = 'BlogCategory.lft';
			unset($conditions['BlogCategory.parent_id']);
			$fields = [];
		} else {
			if ($fields) {
				if (is_array($fields)) {
					$fields[0] = 'DISTINCT ' . $fields[0];
				} else {
					$fields = 'DISTINCT ' . $fields;
				}
			}
		}
		$findOptions = [
			'conditions' => $conditions,
			'fields' => $fields,
			'order' => $options['order'],
			'recursive' => 0,
			'joins' => [[
				'type' => 'LEFT',
				'table' => 'blog_contents',
				'alias' => 'BlogContent',
				'conditions' => "BlogCategory.blog_content_id=BlogContent.id",
			],
				[
					'type' => 'LEFT',
					'table' => 'contents',
					'alias' => 'Content',
					'conditions' => "Content.entity_id=BlogContent.id AND Content.type='BlogContent'",
				]
			]
		];
		$datas = $this->find($findType, $findOptions);
		if ($datas && $findType == 'all') {
			foreach($datas as $key => $data) {
				if ($viewCount) {
					$childrenIds = $this->find('list', [
						'fields' => ['id'],
						'conditions' => [
							['BlogCategory.lft > ' => $data['BlogCategory']['lft']],
							['BlogCategory.rght < ' => $data['BlogCategory']['rght']],
						],
						'recursive' => -1
					]);
					$categoryId = [$data['BlogCategory']['id']];
					if ($childrenIds) {
						$categoryId = array_merge($categoryId, $childrenIds);
					}
					$datas[$key]['BlogCategory']['count'] = $this->BlogPost->find('count', [
						'conditions' =>
							array_merge(
								['BlogPost.blog_category_id' => $categoryId], $this->BlogPost->getConditionAllowPublish()
							),
						'cache' => false
					]);
				}
				if ($current < $depth) {
					$children = $this->_getCategoryList($blogContentId, $data['BlogCategory']['id'], $viewCount, $depth, $current + 1, $fields, $options);
					if ($children) {
						$datas[$key]['BlogCategory']['children'] = $children;
					}
				}
			}
		}
		return $datas;
	}

	/**
	 * アクセス制限としてカテゴリの新規追加ができるか確認する
	 *
	 * Ajaxを利用する箇所にて BcBaserHelper::link() が利用できない場合に利用
	 *
	 * @param int $userGroupId ユーザーグループID
	 * @param int $blogContentId ブログコンテンツID
	 */
	public function hasNewCategoryAddablePermission($userGroupId, $blogContentId)
	{
		if (ClassRegistry::isKeySet('Permission')) {
			$Permission = ClassRegistry::getObject('Permission');
		} else {
			$Permission = ClassRegistry::init('Permission');
		}
		$ajaxAddUrl = preg_replace('|^/index.php|', '', Router::url(['plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContentId]));
		return $Permission->check($ajaxAddUrl, $userGroupId);
	}

	/**
	 * 子カテゴリを持っているかどうか
	 *
	 * @param int $id
	 * @return bool
	 */
	public function hasChild($id)
	{
		return (bool)$this->childCount($id);
	}

	/**
	 * カテゴリ名よりカテゴリを取得
	 *
	 * @param int $blogContentId
	 * @param string $name
	 * @param array $options
	 * @return array|null
	 */
	public function getByName($blogContentId, $name, $options = [])
	{
		$options = array_merge([
			'conditions' => [
				'BlogCategory.blog_content_id' => $blogContentId,
				'BlogCategory.name' => urlencode($name),
			],
			'recursive' => -1
		], $options);
		$this->unbindModel(['hasMany' => ['BlogPost']]);
		return $this->find('first', $options);
	}

}
