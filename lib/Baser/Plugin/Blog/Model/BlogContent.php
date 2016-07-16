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
 * ブログコンテンツモデル
 *
 * @package Blog.Model
 */
class BlogContent extends BlogAppModel {

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
	public $actsAs = array('BcSearchIndexManager', 'BcPluginContent', 'BcCache');

/**
 * hasMany
 *
 * @var array
 */
	public $hasMany = array('BlogPost' =>
		array('className' => 'Blog.BlogPost',
			'order' => 'id DESC',
			'limit' => 10,
			'foreignKey' => 'blog_content_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => ''),
		'BlogCategory' =>
		array('className' => 'Blog.BlogCategory',
			'order' => 'id',
			'limit' => 10,
			'foreignKey' => 'blog_content_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => ''));

/**
 * validate
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('halfText'),
				'message' => 'ブログアカウント名は半角のみ入力してください。',
				'allowEmpty' => false),
			array('rule' => array('notInList', array('blog')),
				'message' => 'ブログアカウント名に「blog」は利用できません。'),
			array('rule' => array('isUnique'),
				'message' => '入力されたブログアカウント名は既に使用されています。'),
			array('rule' => array('maxLength', 100),
				'message' => 'ブログアカウント名は100文字以内で入力してください。')
		),
		'title' => array(
			array('rule' => array('notEmpty'),
				'message' => 'ブログタイトルを入力してください。'),
			array('rule' => array('maxLength', 255),
				'message' => 'ブログタイトルは255文字以内で入力してください。')
		),
		'layout' => array(
			array('rule' => 'halfText',
				'message' => 'レイアウトテンプレート名は半角で入力してください。',
				'allowEmpty' => false),
			array('rule' => array('maxLength', 20),
				'message' => 'レイアウトテンプレート名は20文字以内で入力してください。')
		),
		'template' => array(
			array('rule' => 'halfText',
				'message' => 'コンテンツテンプレート名は半角で入力してください。',
				'allowEmpty' => false),
			array('rule' => array('maxLength', 20),
				'message' => 'レイアウトテンプレート名は20文字以内で入力してください。')
		),
		'list_count' => array(array('rule' => 'halfText',
				'message' => "一覧表示件数は半角で入力してください。",
				'allowEmpty' => false)
		),
		'list_direction' => array(array('rule' => array('notEmpty'),
				'message' => "一覧に表示する順番を指定してください。")
		),
		'eye_catch_size' => array(array(
				'rule' => array('checkEyeCatchSize'),
				'message' => 'アイキャッチ画像のサイズが不正です。'
			))
	);

/**
 * アイキャッチ画像サイズバリデーション
 * 
 * @return boolean 
 */
	public function checkEyeCatchSize() {
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
	public function alphaNumeric($check) {
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
	public function getControlSource($field = null, $options = array()) {
		$controlSources['id'] = $this->find('list');

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
	public function afterSave($created, $options = array()) {
		if (empty($this->data['BlogContent']['id'])) {
			$this->data['BlogContent']['id'] = $this->getInsertID();
		}

		// 検索用テーブルへの登録・削除
		if (!$this->data['BlogContent']['exclude_search'] && $this->data['BlogContent']['status']) {
			$this->saveSearchIndex($this->createSearchIndex($this->data));
			clearDataCache();
			$datas = $this->BlogPost->find('all', array(
				'conditions' => array('BlogPost.blog_content_id' => $this->data['BlogContent']['id']),
				'recursive' => -1
			));
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
 * @return	boolean
 * @access	public
 */
	public function beforeDelete($cascade = true) {
		return $this->deleteSearchIndex($this->id);
	}

/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 */
	public function createSearchIndex($data) {
		if (isset($data['BlogContent'])) {
			$data = $data['BlogContent'];
		}

		$_data = array();
		$_data['SearchIndex']['type'] = 'ブログ';
		// $this->idに値が入ってない場合もあるので
		if (!empty($data['id'])) {
			$_data['SearchIndex']['model_id'] = $data['id'];
		} else {
			$_data['SearchIndex']['model_id'] = $this->id;
		}
		$_data['SearchIndex']['category'] = '';
		$_data['SearchIndex']['title'] = $data['title'];
		$_data['SearchIndex']['detail'] = $data['description'];
		$_data['SearchIndex']['url'] = '/' . $data['name'] . '/index';
		$_data['SearchIndex']['status'] = true;
		return $_data;
	}

/**
 * ユーザーグループデータをコピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed BlogContent Or false
 */
	public function copy($id, $data = null) {
		if ($id) {
			$data = $this->find('first', array('conditions' => array('BlogContent.id' => $id), 'recursive' => -1));
		}
		$data['BlogContent']['name'] .= '_copy';
		$data['BlogContent']['title'] .= '_copy';
		$data['BlogContent']['status'] = false;
		unset($data['BlogContent']['id']);
		$this->create($data);
		$result = $this->save();
		if ($result) {
			$result['BlogContent']['id'] = $this->getInsertID();
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
 * フォームの初期値を取得する
 *
 * @return void
 */
	public function getDefaultValue() {
		$data['BlogContent']['comment_use'] = true;
		$data['BlogContent']['comment_approve'] = false;
		$data['BlogContent']['layout'] = 'default';
		$data['BlogContent']['template'] = 'default';
		$data['BlogContent']['list_count'] = 10;
		$data['BlogContent']['feed_count'] = 10;
		$data['BlogContent']['auth_captcha'] = 1;
		$data['BlogContent']['tag_use'] = false;
		$data['BlogContent']['status'] = false;
		$data['BlogContent']['eye_catch_size_thumb_width'] = 600;
		$data['BlogContent']['eye_catch_size_thumb_height'] = 600;
		$data['BlogContent']['eye_catch_size_mobile_thumb_width'] = 150;
		$data['BlogContent']['eye_catch_size_mobile_thumb_height'] = 150;
		$data['BlogContent']['use_content'] = true;

		return $data;
	}

/**
 * アイキャッチサイズフィールドの値をDB用に変換する
 * 
 * @param array $data
 * @return array 
 */
	public function deconstructEyeCatchSize($data) {
		$data['BlogContent']['eye_catch_size'] = BcUtil::serialize(array(
			'thumb_width' => $data['BlogContent']['eye_catch_size_thumb_width'],
			'thumb_height' => $data['BlogContent']['eye_catch_size_thumb_height'],
			'mobile_thumb_width' => $data['BlogContent']['eye_catch_size_mobile_thumb_width'],
			'mobile_thumb_height' => $data['BlogContent']['eye_catch_size_mobile_thumb_height'],
		));
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
	public function constructEyeCatchSize($data) {
		$eyeCatchSize = BcUtil::unserialize($data['BlogContent']['eye_catch_size']);
		$data['BlogContent']['eye_catch_size_thumb_width'] = $eyeCatchSize['thumb_width'];
		$data['BlogContent']['eye_catch_size_thumb_height'] = $eyeCatchSize['thumb_height'];
		$data['BlogContent']['eye_catch_size_mobile_thumb_width'] = $eyeCatchSize['mobile_thumb_width'];
		$data['BlogContent']['eye_catch_size_mobile_thumb_height'] = $eyeCatchSize['mobile_thumb_height'];
		return $data;
	}

}
