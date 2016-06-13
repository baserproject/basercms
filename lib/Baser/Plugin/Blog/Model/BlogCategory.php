<?php

/**
 * ブログカテゴリモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('BlogAppModel', 'Blog.Model');

/**
 * ブログカテゴリモデル
 *
 * @package Blog.Model
 */
class BlogCategory extends BlogAppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'BlogCategory';

/**
 * バリデーション設定
 * 
 * @var array
 * @access public
 */
	public $validationParams = array();

/**
 * actsAs
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('Tree', 'BcCache');

/**
 * hasMany
 *
 * @var array
 * @access public
 */
	public $hasMany = array('BlogPost' =>
		array('className' => 'Blog.BlogPost',
			'order' => 'id DESC',
			'limit' => 10,
			'foreignKey' => 'blog_category_id',
			'dependent' => false,
			'exclusive' => false,
			'finderQuery' => ''));

/**
 * validate
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => "ブログカテゴリ名を入力してください。",
				'required' => true),
			array('rule' => 'halfText',
				'message' => 'ブログカテゴリ名は半角のみで入力してください。'),
			array('rule' => array('duplicateBlogCategory'),
				'message' => '入力されたブログカテゴリは既に登録されています。'),
			array('rule' => array('maxLength', 255),
				'message' => 'ブログカテゴリ名は255文字以内で入力してください。')
		),
		'title' => array(
			array('rule' => array('notEmpty'),
				'message' => "ブログカテゴリタイトルを入力してください。",
				'required' => true),
			array('rule' => array('maxLength', 255),
				'message' => 'ブログカテゴリ名は255文字以内で入力してください。')
		)
	);

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $option オプション
 * @return array コントロールソース
 * @access public
 */
	public function getControlSource($field, $options = array()) {
		switch ($field) {
			case 'parent_id':
				if (!isset($options['blogContentId'])) {
					return false;
				}
				$conditions = array();
				if (isset($options['conditions'])) {
					$conditions = $options['conditions'];
				}
				$conditions['BlogCategory.blog_content_id'] = $options['blogContentId'];
				if (!empty($options['excludeParentId'])) {
					$children = $this->children($options['excludeParentId']);
					$excludeIds = array($options['excludeParentId']);
					foreach ($children as $child) {
						$excludeIds[] = $child['BlogCategory']['id'];
					}
					$conditions['NOT']['BlogCategory.id'] = $excludeIds;
				}

				if (isset($options['ownerId'])) {
					$ownerIdConditions = array(
						array('BlogCategory.owner_id' => null),
						array('BlogCategory.owner_id' => $options['ownerId']),
					);
					if (isset($conditions['OR'])) {
						$conditions['OR'] = am($conditions['OR'], $ownerIdConditions);
					} else {
						$conditions['OR'] = $ownerIdConditions;
					}
				}

				$parents = $this->generateTreeList($conditions);
				$controlSources['parent_id'] = array();
				foreach ($parents as $key => $parent) {
					if (preg_match("/^([_]+)/i", $parent, $matches)) {
						$parent = preg_replace("/^[_]+/i", '', $parent);
						$prefix = str_replace('_', '&nbsp&nbsp&nbsp', $matches[1]);
						$parent = $prefix . '└' . $parent;
					}
					$controlSources['parent_id'][$key] = $parent;
				}
				break;
			case 'owner_id':
				$UserGroup = ClassRegistry::init('UserGroup');
				$controlSources['owner_id'] = $UserGroup->find('list', array('fields' => array('id', 'title'), 'recursive' => -1));
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
 * @access public
 */
	public function duplicateBlogCategory($check) {
		$conditions = array('BlogCategory.' . key($check) => $check[key($check)],
			'BlogCategory.blog_content_id' => $this->validationParams['blogContentId']);
		if ($this->exists()) {
			$conditions['NOT'] = array('BlogCategory.id' => $this->id);
		}
		$ret = $this->find('first', array('conditions' => $conditions));
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
 * @access public
 */
	public function beforeDelete($cascade = true) {
		parent::beforeDelete($cascade);
		$ret = true;
		if (!empty($this->data['BlogCategory']['id'])) {
			$id = $this->data['BlogCategory']['id'];
			$this->BlogPost->unBindModel(array('belongsTo' => array('BlogCategory')));
			$datas = $this->BlogPost->find('all', array('conditions' => array('BlogPost.blog_category_id' => $id)));
			if ($datas) {
				foreach ($datas as $data) {
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
 * @access public
 */
	public function getCategoryList($blogContentId, $options) {
		$options = array_merge(array(
			'depth' => 1,
			'type' => null,
			'order' => 'BlogCategory.id',
			'limit' => false,
			'viewCount' => false,
			'fields' => array('id', 'name', 'title')
			), $options);
		$datas = array();

		extract($options);
		if (!$type) {
			$datas = $this->_getCategoryList($blogContentId, null, $viewCount, $depth);
		} elseif ($type == 'year') {
			$options = array(
				'category' => true,
				'limit' => $limit,
				'viewCount' => $viewCount,
				'type' => 'year'
			);
			$_datas = $this->BlogPost->getPostedDates($blogContentId, $options);
			$datas = array();
			foreach ($_datas as $data) {
				if ($viewCount) {
					$data['BlogCategory']['count'] = $data['count'];
				}
				$datas[$data['year']][] = array('BlogCategory' => $data['BlogCategory']);
			}
		}

		return $datas;
	}

/**
 * カテゴリリストを取得する（再帰処理）
 * 
 * @param int $blogContentId
 * @param int $id
 * @param int $viewCount
 * @param int $depth
 * @param int $current
 * @param array $fields
 * @return array
 */
	protected function _getCategoryList($blogContentId, $id = null, $viewCount = false, $depth = 1, $current = 1, $fields = array()) {
		$datas = $this->find('all', array(
			'conditions' => array('BlogCategory.blog_content_id' => $blogContentId, 'BlogCategory.parent_id' => $id),
			'fields' => $fields,
			'recursive' => -1));
		if ($datas) {
			foreach ($datas as $key => $data) {
				if ($viewCount) {
					$datas[$key]['BlogCategory']['count'] = $this->BlogPost->find('count', array(
						'conditions' =>
						am(
							array('BlogPost.blog_category_id' => $data['BlogCategory']['id']), $this->BlogPost->getConditionAllowPublish()
						),
						'cache' => false
					));
				}

				if ($current < $depth) {
					$children = $this->_getCategoryList($blogContentId, $data['BlogCategory']['id'], $viewCount, $depth, $current + 1);
					if ($children) {
						$datas[$key]['BlogCategory']['children'] = $children;
					}
				}
			}
		}
		return $datas;
	}

/**
 * カテゴリオーナーの基準において新しいカテゴリが追加できる状態かチェックする
 * 
 * @param int $userGroupId ユーザーグループID
 * @param bool $rootEditable ドキュメントルートの書き込み権限の有無
 * @return bool
 */
	public function checkNewCategoryAddable($userGroupId, $rootEditable) {
		
		$newCatAddable = false;
		$ownerCats = $this->find('count', array(
			'conditions' => array(
				'OR' => array(
					array('BlogCategory.owner_id' => null),
					array('BlogCategory.owner_id' => $userGroupId)
				)
		)));

		if ($ownerCats || $rootEditable) {
			$newCatAddable = true;
		}

		return $newCatAddable;

	}
	
/**
 * アクセス制限としてカテゴリの新規追加ができるか確認する
 * 
 * Ajaxを利用する箇所にて BcBaserHelper::link() が利用できない場合に利用
 * 
 * @param int $userGroupId ユーザーグループID
 * @param int $blogContentId ブログコンテンツID
 */
	public function hasNewCategoryAddablePermission($userGroupId, $blogContentId) {
		
		if (ClassRegistry::isKeySet('Permission')) {
			$Permission = ClassRegistry::getObject('Permission');
		} else {
			$Permission = ClassRegistry::init('Permission');
		}

		$ajaxAddUrl = preg_replace('|^/index.php|', '', Router::url(array('plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContentId)));

		return $Permission->check($ajaxAddUrl, $userGroupId);
		
	}

}
