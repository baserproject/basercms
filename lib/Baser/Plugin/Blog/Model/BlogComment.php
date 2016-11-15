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
 * ブログコメントモデル
 *
 * @package Blog.Model
 */
class BlogComment extends BlogAppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'BlogComment';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = ['BcCache'];

/**
 * belongsTo
 *
 * @var array
 */
	public $belongsTo = ['BlogPost' => ['className' => 'Blog.BlogPost',
			'foreignKey' => 'blog_post_id']];

/**
 * validate
 *
 * @var array
 */
	public $validate = [
		'name' => [
			['rule' => ['notBlank'],
				'message' => 'お名前を入力してください。'],
			['rule' => ['maxLength', 50],
				'message' => 'お名前は50文字以内で入力してください。']
		],
		'email' => [
			'email' => [
				'rule' => ['email'],
				'message' => 'Eメールの形式が不正です。',
				'allowEmpty' => true],
			'maxLength' => [
				'rule' => ['maxLength', 255],
				'message' => 'Eメールは255文字以内で入力してください。']
		],
		'url' => [
			'url' => [
				'rule' => ['url'],
				'message' => 'URLの形式が不正です。',
				'allowEmpty' => true],
			'maxLength' => [
				'rule' => ['maxLength', 255],
				'message' => 'URLは255文字以内で入力してください。']
		],
		'message' => [
			['rule' => ['notBlank'],
				'message' => "コメントを入力してください。"]
		]
	];

/**
 * 初期値を取得する
 *
 * @return array 初期値データ
 */
	public function getDefaultValue() {
		$data[$this->name]['name'] = 'NO NAME';
		return $data;
	}

/**
 * コメントを追加する
 * @param array $data
 * @param string $contentId
 * @param string $postId
 * @param string $commentApprove
 * @return boolean
 */
	public function add($data, $contentId, $postId, $commentApprove) {
		if (isset($data['BlogComment'])) {
			$data = $data['BlogComment'];
		}

		// サニタイズ
		foreach ($data as $key => $value) {
			$data[$key] = Sanitize::html($value);
		}

		// Modelのバリデートに引っかからない為の対処
		$data['url'] = str_replace('&#45;', '-', $data['url']);
		$data['email'] = str_replace('&#45;', '-', $data['email']);

		$data['blog_post_id'] = $postId;
		$data['blog_content_id'] = $contentId;

		if ($commentApprove) {
			$data['status'] = false;
		} else {
			$data['status'] = true;
		}

		$data['no'] = $this->getMax('no', ['blog_content_id' => $contentId]) + 1;
		$this->create($data);

		return $this->save();
	}

}
