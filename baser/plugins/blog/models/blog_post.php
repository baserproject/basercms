<?php
/* SVN FILE: $Id$ */
/**
 * 記事モデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * 記事モデル
 *
 * @package			baser.plugins.blog.models
 */
class BlogPost extends BlogAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'BlogPost';
/**
 * ビヘイビア
 *
 * @var array
 * @access public
 */
	var $actsAs = array('ContentsManager');
/**
 * belongsTo
 *
 * @var 	array
 * @access	public
 */
	var $belongsTo = array(
			'BlogCategory' =>   array(  'className'=>'Blog.BlogCategory',
							'foreignKey'=>'blog_category_id'),
			'User' =>           array(  'className'=>'User',
							'foreignKey'=>'user_id'),
			'BlogContent' =>    array(  'className'=>'Blog.BlogContent',
							'foreignKey'=>'blog_content_id')
	);
/**
 * hasMany
 *
 * @var		array
 * @access 	public
 */
	var $hasMany = array('BlogComment'=>
			array('className'=>'Blog.BlogComment',
							'order'=>'created',
							'foreignKey'=>'blog_post_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * HABTM
 * 
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array(
			'BlogTag' => array(
				'className'				=> 'Blog.BlogTag',
				'joinTable'				=> 'blog_posts_blog_tags',
				'foreignKey'			=> 'blog_post_id',
				'associationForeignKey'	=> 'blog_tag_id',
				'conditions'			=> '',
				'order'					=> '',
				'limit'					=> '',
				'unique'				=> true,
				'finderQuery'			=> '',
				'deleteQuery'			=> ''
		));
/**
 * validate
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'name' => array(
			array(  'rule'		=> array('notEmpty'),
					'message'	=> 'タイトルを入力してください。',
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'タイトルは50文字以内で入力してください。')
		),
		'posts_date' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '投稿日を入力してください。',
					'required'	=> true)
		),
		'user_id' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '投稿者を選択してください。')
		)
	);
/**
 * 初期値を取得する
 *
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue($authUser) {
		$data[$this->name]['user_id'] = $authUser['User']['id'];
		$data[$this->name]['posts_date'] = date('Y/m/d H:i:s');
		$data[$this->name]['status'] = 0;
		return $data;
	}
/**
 * ブログの月別一覧を取得する
 *
 * @return array    月別リストデータ
 * @access public
 */
	function getBlogDates($blogContentId, $count = false) {

		$conditions = array('BlogPost.blog_content_id'=>$blogContentId);
		$conditions = am($conditions, $this->getConditionAllowPublish());
		// TODO CSVDBではGROUP BYが実装されていない為、取り急ぎPHPで処理
		/*$dates = $this->find('all',array('fields'=>array('YEAR(posts_date) as year','MONTH(posts_date) as month','COUNT(id)' as count),
                                          $conditions,
                                          'group'=>array('YEAR(posts_date)','MONTH(posts_date)'))));*/
		// 毎秒抽出条件が違うのでキャッシュしない
		$posts = $this->find('all',array(
			'conditions'=> $conditions, 
			'order'		=> 'BlogPost.posts_date DESC', 
			'recursive' => -1,
			'cache'		=> false
		));

		$postsDates = Set::extract('/BlogPost/posts_date',$posts);

		$dates = array();
		foreach($postsDates as $postsDate) {
			$exists = false;
			$_date = array();
			foreach($dates as $key => $date) {
				if($date['year'] == date('Y',strtotime($postsDate)) &&
						$date['month'] == date('m',strtotime($postsDate))) {
					$exists = true;
					if($count) {
						$dates[$key]['count']++;
					}
				}
			}
			if(!$exists) {
				$_date['year'] = date('Y',strtotime($postsDate));
				$_date['month'] = date('m',strtotime($postsDate));
				if($count) {
					$_date['count'] = 1;
				}
				$dates[] = $_date;
			}
		}
		return $dates;

	}
/**
 * カレンダー用に指定した月で記事の投稿がある日付のリストを取得する
 */
	function getEntryDates($contentId,$year,$month) {

		$entryDates = $this->find('all', array(
			'fields'	=> array('BlogPost.posts_date'),
			'conditions'=> $this->_getEntryDatesConditions($contentId,$year,$month), 
			'recursive'	=> -1,
			'cache'		=> false
		));
		$entryDates = Set::extract('/BlogPost/posts_date',$entryDates);
		foreach($entryDates as $key => $entryDate) {
			$entryDates[$key] = date('Y-m-d',strtotime($entryDate));
		}
		return $entryDates;

	}
/**
 * 指定した月の記事が存在するかチェックする
 *
 * @param	int	$contentId
 * @param	int	$year
 * @param	int	$month
 * @return	boolean
 */
	function existsEntry($contentId,$year,$month) {
		
		if($this->find('first', array(
			'fields'	=> array('BlogPost.id'),
			'conditions'=> $this->_getEntryDatesConditions($contentId,$year,$month), 
			'recursive'	=> -1,
			'cache'		=> false
		))) {
			return true;
		} else {
			return false;
		}
		
	}
/**
 * 年月を指定した検索条件を生成
 *
 * データベースごとに構文が違う
 * 
 * @param	int	$contentId
 * @param	int	$year
 * @param	int	$month
 * @return	string
 */
	function _getEntryDatesConditions($contentId,$year,$month) {

		$dbConfig = new DATABASE_CONFIG();
		$driver = str_replace('_ex','',$dbConfig->plugin['driver']);

		switch($driver) {
			case 'mysql':
			case 'csv':
				if(!empty($year)) {
					$conditions["YEAR(`BlogPost`.`posts_date`)"] = $year;
				}else {
					$conditions["YEAR(`BlogPost`.`posts_date`)"] = date('Y');
				}
				if(!empty($month)) {
					$conditions["MONTH(`BlogPost`.`posts_date`)"] = $month;
				}else {
					$conditions["MONTH(`BlogPost`.`posts_date`)"] = date('m');
				}
				break;

			case 'postgres':
				if(!empty($year)) {
					$conditions["date_part('year', \"BlogPost\".\"posts_date\")"] = $year;
				}else {
					$conditions["date_part('year', \"BlogPost\".\"posts_date\")"] = date('Y');
				}
				if(!empty($month)) {
					$conditions["date_part('month', \"BlogPost\".\"posts_date\")"] = $month;
				}else {
					$conditions["date_part('month', \"BlogPost\".\"posts_date\")"] = date('m');
				}
				break;

			case 'sqlite':
			case 'sqlite3':
				if(!empty($year)) {
					$conditions["strftime('%Y',BlogPost.posts_date)"] = $year;
				}else {
					$conditions["strftime('%Y',BlogPost.posts_date)"] = date('Y');
				}
				if(!empty($month)) {
					$conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d',$month);
				}else {
					$conditions["strftime('%m',BlogPost.posts_date)"] = date('m');
				}
				break;

		}

		$conditions = am($conditions,  array('BlogPost.blog_content_id'=>$contentId), $this->getConditionAllowPublish());
		return $conditions;
		
	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field, $options = array()) {

		switch ($field) {
			case 'blog_category_id':
				$controlSources['blog_category_id'] = $this->BlogCategory->getControlSource('parent_id',$options);
				break;
			case 'user_id':
				$controlSources['user_id'] = $this->User->getUserList($options);
				break;
			case 'blog_tag_id':
				$controlSources['blog_tag_id'] = $this->BlogTag->find('list');
				break;
		}
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * 公開状態を取得する
 *
 * @param	array	データリスト
 * @return	boolean	公開状態
 * @access	public
 */
	function allowPublish($data){

		if(isset($data['BlogPost'])){
			$data = $data['BlogPost'];
		}

		$allowPublish = (int)$data['status'];

		if($data['publish_begin'] == '0000-00-00 00:00:00') {
			$data['publish_begin'] = NULL;
		}
		if($data['publish_end'] == '0000-00-00 00:00:00') {
			$data['publish_end'] = NULL;
		}

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if(($data['publish_begin'] && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
				($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))){
			$allowPublish = false;
		}

		return $allowPublish;

	}
/**
 * 公開済の conditions を取得
 * 
 * @return	array
 */
	function getConditionAllowPublish() {
		
		$conditions[$this->alias.'.status'] = true;
		$conditions[] = array('or'=> array(array($this->alias.'.publish_begin <=' => date('Y-m-d H:i:s')),
										array($this->alias.'.publish_begin' => NULL),
										array($this->alias.'.publish_begin' => '0000-00-00 00:00:00')));
		$conditions[] = array('or'=> array(array($this->alias.'.publish_end >=' => date('Y-m-d H:i:s')),
										array($this->alias.'.publish_end' => NULL),
										array($this->alias.'.publish_end' => '0000-00-00 00:00:00')));
		return $conditions;
		
	}
/**
 * 公開状態の記事を取得する
 *
 * @param array $options
 * @return array
 */
	function getPublishes ($options) {

		if(!empty($options['conditions'])) {
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
 * @return boolean
 * @access public
 */
	function afterSave($created) {

		// 検索用テーブルに登録
		$this->saveContent($this->createContent($this->data));

	}
/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 * @access public
 */
	function createContent($data) {

		if(isset($data['BlogPost'])) {
			$data = $data['BlogPost'];
		}

		$_data = array();
		$_data['Content']['model_id'] = $this->id;
		$_data['Content']['category'] = '';
		if(!empty($data['blog_category_id'])) {
			$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
			$categoryPath = $BlogCategory->getPath($data['blog_category_id'], array('title'));
			if($categoryPath) {
				$_data['Content']['category'] = $categoryPath[0]['BlogCategory']['title'];
			}
		}
		$_data['Content']['title'] = $data['name'];
		$_data['Content']['detail'] = $data['content'].' '.$data['detail'];
		$PluginContent = ClassRegistry::init('PluginContent');
		$_data['Content']['url'] = '/'.$PluginContent->field('name', array('PluginContent.content_id' => $data['blog_content_id'], 'plugin' => 'blog')).'/archives/'.$data['no'];
		$_data['Content']['status'] = $this->allowPublish($data);

		return $_data;

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
}
?>