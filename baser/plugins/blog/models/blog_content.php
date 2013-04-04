<?php
/* SVN FILE: $Id$ */
/**
 * ブログコンテンツモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ブログコンテンツモデル
 *
 * @package baser.plugins.blog.models
 */
class BlogContent extends BlogAppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogContent';
/**
 * behaviors
 *
 * @var array
 * @access public
 */
	var $actsAs = array('BcContentsManager', 'BcPluginContent', 'BcCache');
/**
 * hasMany
 *
 * @var array
 * @access public
 */
	var $hasMany = array('BlogPost'=>
			array('className'=>'Blog.BlogPost',
							'order'=>'id DESC',
							'limit'=>10,
							'foreignKey'=>'blog_content_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''),
			'BlogCategory'=>
			array('className'=>'Blog.BlogCategory',
							'order'=>'id',
							'limit'=>10,
							'foreignKey'=>'blog_content_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
				array(	'rule'		=> array('halfText'),
						'message'	=> 'ブログアカウント名は半角のみ入力してください。',
						'allowEmpty'=> false),
				array(	'rule'		=> array('notInList', array('blog')),
						'message'	=> 'ブログアカウント名に「blog」は利用できません。'),
				array(	'rule'		=> array('isUnique'),
						'message'	=> '入力されたブログアカウント名は既に使用されています。'),
				array(	'rule'		=> array('maxLength', 100),
						'message'	=> 'ブログアカウント名は100文字以内で入力してください。')
		),
		'title' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'ブログタイトルを入力してください。'),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'ブログタイトルは255文字以内で入力してください。')
		),
		'layout' => array(
			array(	'rule'		=> 'halfText',
					'message'	=> 'レイアウトテンプレート名は半角で入力してください。',
					'allowEmpty'=>false),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'レイアウトテンプレート名は20文字以内で入力してください。')
		),
		'template' => array(
			array(	'rule'		=> 'halfText',
					'message'	=> 'コンテンツテンプレート名は半角で入力してください。',
					'allowEmpty'=>false),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'レイアウトテンプレート名は20文字以内で入力してください。')
		),
		'list_count' => array(array(	'rule' => 'halfText',
						'message' => "一覧表示件数は半角で入力してください。",
						'allowEmpty'=>false)
		),
		'list_direction' => array(array(	'rule' => array('notEmpty'),
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
	function checkEyeCatchSize() {
		
		$data = $this->constructEyeCatchSize($this->data);
		if(empty($data['BlogContent']['eye_catch_size_thumb_width']) ||
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
 * @access public
 */
	function alphaNumeric($check) {

		if(preg_match("/^[a-z0-9]+$/",$check[key($check)])) {
			return true;
		}else {
			return false;
		}

	}
/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	function getControlSource($field = null,$options = array()) {

		$controlSources['id'] = $this->find('list');

		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * afterSave
 *
 * @return boolean
 * @access public
 */
	function afterSave($created) {

		if(empty($this->data['BlogContent']['id'])) {
			$this->data['BlogContent']['id'] = $this->getInsertID();
		}
		
		// 検索用テーブルへの登録・削除
		if(!$this->data['BlogContent']['exclude_search'] && $this->data['BlogContent']['status'] ) {
			$this->saveContent($this->createContent($this->data));
		} else {
			
			$this->deleteContent($this->data['BlogContent']['id']);
		}
		
	}
/**
 * beforeDelete
 *
 * @return	boolean
 * @access	public
 */
	function beforeDelete() {

		return $this->deleteContent($this->id);

	}
/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 * @access public
 */
	function createContent($data) {

		if(isset($data['BlogContent'])) {
			$data = $data['BlogContent'];
		}

		$_data = array();
		$_data['Content']['type'] = 'ブログ';
		$_data['Content']['model_id'] = $this->id;
		$_data['Content']['category'] = '';
		$_data['Content']['title'] = $data['title'];
		$_data['Content']['detail'] = $data['description'];
		$_data['Content']['url'] = '/'.$data['name'].'/index';
		$_data['Content']['status'] = true;

		return $_data;

	}
/**
 * ユーザーグループデータをコピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed BlogContent Or false
 */
	function copy($id, $data = null) {
		
		if($id) {
			$data = $this->find('first', array('conditions' => array('BlogContent.id' => $id), 'recursive' => -1));
		}
		$data['BlogContent']['name'] .= '_copy';
		$data['BlogContent']['title'] .= '_copy';
		$data['BlogContent']['status'] = false;
		unset($data['BlogContent']['id']);
		$this->create($data);
		$result = $this->save();
		if($result) {
			$result['BlogContent']['id'] = $this->getInsertID();
			return $result;
		} else {
			if(isset($this->validationErrors['name'])) {
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
 * @access protected
 */
	function getDefaultValue() {

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
	function deconstructEyeCatchSize($data) {
		
		$data['BlogContent']['eye_catch_size'] = serialize(array(
			'thumb_width'			=> $data['BlogContent']['eye_catch_size_thumb_width'],
			'thumb_height'			=> $data['BlogContent']['eye_catch_size_thumb_height'],
			'mobile_thumb_width'	=> $data['BlogContent']['eye_catch_size_mobile_thumb_width'],
			'mobile_thumb_height'	=> $data['BlogContent']['eye_catch_size_mobile_thumb_height'],
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
	function constructEyeCatchSize($data) {
		
		$eyeCatchSize = unserialize($data['BlogContent']['eye_catch_size']);
		$data['BlogContent']['eye_catch_size_thumb_width'] = $eyeCatchSize['thumb_width'];
		$data['BlogContent']['eye_catch_size_thumb_height'] = $eyeCatchSize['thumb_height'];
		$data['BlogContent']['eye_catch_size_mobile_thumb_width'] = $eyeCatchSize['mobile_thumb_width'];
		$data['BlogContent']['eye_catch_size_mobile_thumb_height'] = $eyeCatchSize['mobile_thumb_height'];
		return $data;
		
	}
	
}

