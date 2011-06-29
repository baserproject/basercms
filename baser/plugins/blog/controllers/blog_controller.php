<?php
/* SVN FILE: $Id$ */
/**
 * ブログ記事コントローラー
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
 * @package			blog.controllers
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
 * ブログ記事コントローラー
 *
 * @package			baser.plugins.blog.controllers
 */
class BlogController extends BlogAppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Blog';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Blog.BlogCategory', 'Blog.BlogPost', 'Blog.BlogContent');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Html', 'TextEx', 'TimeEx', 'Freeze', 'Array', 'Paginator', 'Blog.Blog', 'Cache');
/**
 * コンポーネント
 * 
 * @var array
 * @access public
 */
	var $components = array('Auth', 'Cookie', 'AuthConfigure', 'RequestHandler', 'EmailEx', 'Security');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array();
/**
 * サブメニューエレメント
 *
 * @var		string
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ブログデータ
 * 
 * @var array
 * @access public
 */
	var $blogContent = array();
/**
 * プレビューフラグ
 * 
 * @var boolean
 * @access public
 */
	var $preview = false;
/**
 * beforeFilter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter() {
		
		parent::beforeFilter();

		/* 認証設定 */
		$this->Auth->allow(
			'index', 'mobile_index', 'archives', 'mobile_archives',
			'get_calendar', 'get_categories', 'get_blog_dates', 'get_recent_entries',
			'posts', 'mobile_posts'
		);
		
		$this->BlogContent->recursive = -1;
		if($this->contentId) {
			$this->blogContent = $this->BlogContent->read(null,$this->contentId);
		}else {
			$this->blogContent = $this->BlogContent->read(null,$this->params['pass'][0]);
		}

		$this->subMenuElements = array('default');
		$this->navis = array($this->blogContent['BlogContent']['title']=>'/'.$this->blogContent['BlogContent']['name'].'/index');

		// ページネーションのリンク対策
		// コンテンツ名を変更している際、以下の設定を行わないとプラグイン名がURLに付加されてしまう
		// Viewで $paginator->options = array('url' => $this->passedArgs) を行う事が前提
		if(!isset($this->params['admin'])) {
			$this->passedArgs['controller'] = $this->blogContent['BlogContent']['name'];
			$this->passedArgs['plugin'] = $this->blogContent['BlogContent']['name'];
			$this->passedArgs['action'] = $this->action;
		}

		// コメント送信用のトークンを出力する為にセキュリティコンポーネントを利用しているが、
		// 表示用のコントローラーなのでポストデータのチェックは必要ない
		$this->Security->enabled = true;
		$this->Security->validatePost = false;
		
	}
/**
 * beforeRender
 *
 * @return	void
 * @access 	public
 */
	function beforeRender() {

		parent::beforeRender();
		
		$this->set('blogContent',$this->blogContent);

		if($this->blogContent['BlogContent']['widget_area']){
			$this->set('widgetArea',$this->blogContent['BlogContent']['widget_area']);
		}

	}
/**
 * [PUBLIC] ブログを一覧表示する
 *
 * @retur void
 * @access public
 */
	function index() {

		if($this->contentId) {
			$contentId = $this->contentId;
		}else {
			// TODO ブログの数を確認し、一つであればそのIDを格納し、記事が複数の場合はnotFoundとする？
			// もしくは、デフォルト設定させるか、idが一番小さいものをデフォルトとするか。
			$contentId = 1;
		}

		if ($this->RequestHandler->isRss()) {
			Configure::write('debug', 0);
			$this->set('channel', array('title' => h($this->blogContent['BlogContent']['title'].'｜'.$this->siteConfigs['name']),
					'description' => h($this->blogContent['BlogContent']['description'])));
			$this->layout = 'default';
			$limit = $this->blogContent['BlogContent']['feed_count'];
			$template = 'index';
		}else {
			$this->layout = $this->blogContent['BlogContent']['layout'];
			$limit = $this->blogContent['BlogContent']['list_count'];
			$template = $this->blogContent['BlogContent']['template'].DS.'index';
		}

		/* ブログ記事一覧を取得 */
		$conditions["BlogPost.blog_content_id"] = $contentId;
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());

		$this->BlogPost->expects(array('BlogCategory', 'User', 'BlogTag', 'BlogContent'), false);
		// 毎秒抽出条件が違うのでキャッシュしない
		$this->paginate = array(
				'conditions'=> $conditions,
				'order'		=> 'BlogPost.posts_date '.$this->blogContent['BlogContent']['list_direction'],
				'limit'		=> $limit,
				'recursive'	=> 1,
				'cache'		=> false
		);
		$this->set('posts', $this->paginate('BlogPost'));

		/* 表示設定 */
		$this->subMenuElements = array_merge($this->subMenuElements,array('blog_calendar', 'blog_recent_entries', 'blog_category_archives', 'blog_monthly_archives'));
		$this->set('single',false);
		$this->pageTitle = $this->blogContent['BlogContent']['title'];
		$this->navis = array();
		$this->render($template);

	}
/**
 * [MOBILE] ブログ記事を一覧表示する
 *
 * @return void
 * @access public
 */
	function mobile_index() {

		$this->setAction('index');

	}
/**
 * [PUBLIC] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 * @access public
 */
	function archives() {

		// パラメーター処理
		$pass = $this->params['pass'];
		$type = $year = $month = $day = $id = '';
		$navis = $posts = array();
		$single = false;

		if($pass[0] == 'category') {
			$type = 'category';
		}elseif($pass[0] == 'tag') {
			$type = 'tag';
		}elseif($pass[0] == 'date') {
			$type='date';
		}

		switch($type) {
			
			/* カテゴリ一覧 */
			case 'category':
			
				$category = $pass[count($pass)-1];
				if(empty($category)) {
					$this->notFound();
				}

				// 記事を取得
				$categoryId = $this->BlogCategory->field('id', array(
					'BlogCategory.blog_content_id'	=> $this->contentId,
					'BlogCategory.name'				=> $category
				));
				$posts = $this->_getBlogPosts(array('categoryId' => $categoryId));

				// ナビゲーションを設定
				$blogCategories = $this->BlogCategory->getpath($categoryId,array('name','title'));
				if(count($blogCategories) > 1){
					foreach($blogCategories as $key => $blogCategory) {
						if($key < count($blogCategories) -1 ) {
							$navis[$blogCategory['BlogCategory']['title']] = '/'.$this->blogContent['BlogContent']['name'].'/archives/category/'.$blogCategory['BlogCategory']['name'];
						}
					}
				}
				$this->pageTitle = $blogCategories[count($blogCategories)-1]['BlogCategory']['title'];
				$template = $this->blogContent['BlogContent']['template'].DS.'archives';
				break;
			
			/* タグ別記事一覧 */
			case 'tag':

				$tag = urldecode($pass[count($pass)-1]);
				if(empty($this->blogContent['BlogContent']['tag_use']) || empty($tag)) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(array('tag' => $tag));
				$this->pageTitle = $tag;
				$template = $this->blogContent['BlogContent']['template'].DS.'archives';
				break;
				
			/* 月別アーカイブ一覧 */
			case 'date':

				$year = $pass[1];
				$month = @$pass[2];
				$day = @$pass[3];
				if(!$year && !$month && !$day) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(array('year' => $year, 'month' => $month, 'day' => $day));
				$this->pageTitle = $year.'年';
				if($month) $this->pageTitle .= $month.'月';
				if($day) $this->pageTitle .= $day.'日';
				$template = $this->blogContent['BlogContent']['template'].DS.'archives';
				break;
			
			/* 単ページ */
			default:

				// プレビュー
				if($this->preview) {
					
					$this->contentId = $pass[0];
					if(!empty($pass[1])) {
						$id = $pass[1];
					} elseif(empty($this->data['BlogPost'])) {
						$this->notFound();
					}
					
					$post['BlogPost'] = $this->data['BlogPost'];
					if(isset($this->data['BlogTag'])) {
						$tags = $this->BlogPost->BlogTag->find('all', array('conditions' => $this->data['BlogTag']['BlogTag']));
						if($tags) {
							$tags = Set::extract('/BlogTag/.', $tags);
							$post['BlogTag'] = $tags;
						}
					}
					
				} else {
					
					if(!empty($pass[0])) {
						$id = $pass[0];
					} else {
						$this->notFound();
					}
					// コメント送信
					if(isset($this->data['BlogComment'])) {
						$this->add_comment($id);
					}
					
					$posts = $this->_getBlogPosts(array('id' => $id));
					if(!empty($posts[0])) {
						$post = $posts[0];
						$this->set('post', $post);
					} else {
						$this->notFound();
					}
					
				}

				// ナビゲーションを設定
				if(!empty($post['BlogPost']['blog_category_id'])) {
					$blogCategories = $this->BlogCategory->getpath($post['BlogPost']['blog_category_id'],array('name','title'));
					if($blogCategories) {
						foreach($blogCategories as $blogCategory) {
							$this->navis[$blogCategory['BlogCategory']['title']] = '/'.$this->blogContent['BlogContent']['name'].'/archives/category/'.$blogCategory['BlogCategory']['name'];
						}
					}
				}
				$this->pageTitle = $post['BlogPost']['name'];
				$single = true;
				$template = $this->blogContent['BlogContent']['template'].DS.'single';
				if($this->preview) {
					$this->blogContent['BlogContent']['comment_use'] = false;
				}

		}

		// 表示設定
		$this->navis += $navis;
		$this->set('single',$single);
		$this->set('posts', $posts);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->contentsTitle = $this->pageTitle;
		$this->subMenuElements = array_merge($this->subMenuElements,array('blog_calendar', 'blog_recent_entries', 'blog_category_archives', 'blog_monthly_archives'));
		$this->layout = $this->blogContent['BlogContent']['layout'];
		$this->render($template);

	}
/**
 * コメントを送信する
 * 
 * @param int $id
 * @return void
 * @access public 
 */
	function add_comment($id) {
		
		// blog_post_idを取得
		$conditions = array(
			'BlogPost.no'				=> $id,
			'BlogPost.blog_content_id'	=> $this->contentId
		);
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		
		// 毎秒抽出条件が違うのでキャッシュしない
		$data = $this->BlogPost->find('first', array(
			'conditions'=> $conditions,
			'fields'	=> array('BlogPost.id'),
			'cache'		=> false,
			'recursive'	=> -1
		));
		
		if(empty($data['BlogPost']['id'])) {
			$this->notFound();
		} else {
			$postId = $data['BlogPost']['id'];
		}

		if($this->BlogPost->BlogComment->add($this->data, $this->contentId, $postId, $this->blogContent['BlogContent']['comment_approve'])) {
			
			$this->_sendComment();
			if($this->blogContent['BlogContent']['comment_approve']) {
				$commentMessage = '送信が完了しました。送信された内容は確認後公開させて頂きます。';
			}else {
				$commentMessage = 'コメントの送信が完了しました。';
			}
			$this->data = null;
			
		}else {
			
			$commentMessage = 'コメントの送信に失敗しました。';
			
		}
		
		$this->set('commentMessage',$commentMessage);
		
	}
/**
 * ブログ記事を取得する
 * 
 * @param array $options
 * @return array
 * @access protected
 */
	function _getBlogPosts($options = array()) {
		
		$_options = array(
			'listDirection'	=> $this->blogContent['BlogContent']['list_direction'],
			'listCount'		=> $this->blogContent['BlogContent']['list_count'],
			'category'	=> null,
			'tag'			=> null,
			'year'			=> null,
			'month'			=> null,
			'day'			=> null,
			'id'			=> null
		);
			
		$options = am($_options, $options);
		if(!empty($this->params['named'])) {
			$options = am($options, $this->params['named']);
		}
		extract($options);
		$expects = array('BlogCategory', 'User', 'BlogTag');
		$conditions = array('BlogPost.blog_content_id'	=> $this->contentId);
		
		// カテゴリ条件
		if($category) {
			
			$categoryId = $this->BlogCategory->field('id', array(
				'BlogCategory.blog_content_id'	=> $this->contentId,
				'BlogCategory.name'				=> $category
			));
			$categoryIds = array(0 => $categoryId);

			// 指定したカテゴリ名にぶら下がる子カテゴリを取得
			$catChildren = $this->BlogCategory->children($categoryId);
			if($catChildren) {
				$catChildren = Set::extract('/BlogCategory/id',$catChildren);
				$categoryIds = am($categoryIds, $catChildren);
			}
			$conditions['BlogPost.blog_category_id'] = $categoryIds;
			
		}
		
		// タグ条件
		if($tag) {
			
			$tags = $this->BlogPost->BlogTag->find('all', array(
				'conditions'=> array('BlogTag.name' => $tag), 
				'recursive'	=> 1
			));
			if(isset($tags[0]['BlogPost'][0]['id'])) {
				$ids = Set::extract('/BlogPost/id',$tags);
				$conditions['BlogPost.id'] = $ids;
			} else {
				return array();
			}			
			
		}
		
		// 年月日条件
		if($year || $month || $day) {
			
			$db=& ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
			switch (str_replace('_ex','',$db->config['driver'])) {
				case 'mysql':
				case 'csv':
					if($year) $conditions["YEAR(BlogPost.posts_date)"] = $year;
					if($month) $conditions["MONTH(BlogPost.posts_date)"] = $month;
					if($day) $conditions["DAY(BlogPost.posts_date)"] = $day;
					break;
				case 'postres':
					if($year) $conditions["date_part('year'(BlogPost.posts_date)"] = $year;
					if($month) $conditions["date_part('month'(BlogPost.posts_date)"] = $month;
					if($day) $conditions["date_part('day'(BlogPost.posts_date)"] = $day;
					break;
				case 'sqlite':
				case 'sqlite3':
					if($year) $conditions["strftime('%Y',BlogPost.posts_date)"] = $year;
					if($month) $conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d',$month);
					if($day) $conditions["strftime('%d',BlogPost.posts_date)"] = sprintf('%02d',$day);
					break;
			}

		}
		
		if($id) {
			$conditions["BlogPost.no"] = $id;
			$expects[] = 'BlogComment';
			$this->BlogPost->hasMany['BlogComment']['conditions'] = array('BlogComment.status'=>true);
			$listCount = 1;
		}

		// プレビューの場合は公開ステータスを条件にしない
		if(!$this->preview) {
			$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		}

		$this->BlogPost->expects($expects, false);

		if(!empty($direction)) {
			$listDirection = $direction;
		}
		$order = "BlogPost.posts_date {$listDirection}";
		if(!empty($sort)) {
			$order = "BlogPost.{$sort} {$listDirection}";
			if($sort != 'id') {
				$order .= ", BlogPost.id ASC";
			}
		}
		
		// 毎秒抽出条件が違うのでキャッシュしない
		$this->paginate = array(
				'conditions'=> $conditions,
				'fields'	=> array(),
				'order'		=> $order,
				'limit'		=> $listCount,
				'recursive'	=> 1,
				'cache'		=> false
		);
		
		return $this->paginate('BlogPost');

	}
/**
 * [MOBILE] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 * @access public
 */
	function mobile_archives() {

		$this->setAction('archives');

	}
/**
 * [ADMIN] プレビューを表示する
 * 
 * @param int $blogContentsId
 * @param int $id
 * @param string $mode
 * @return void
 * @access public
 */
	function admin_preview($blogContentsId, $id, $mode) {
		
		if($mode == 'create') {
			$this->_createPreview($blogContentsId, $id);
		} elseif($mode == 'view') {
			$this->_viewPreview($blogContentsId, $id);
		}
		
	}
/**
 * ブログ記事をプレビュー
 *
 * @param mixed blog_post_id / type
 * @param mixed blog_post_id / ""
 * @return void
 * @access protected
 */
	function _createPreview($blogContentsId, $id) {

		Cache::write('blog_posts_preview_'.$id, $this->data);
		echo true;
		exit();
		
	}
/**
 * プレビューを表示する
 *
 * @param int $blogContentId
 * @param int $id
 * @return void
 * @access protected
 */
	function _viewPreview($blogContentsId, $id){

		$data = Cache::read('blog_posts_preview_'.$id);
		Cache::delete('blog_posts_preview_'.$id);
		$this->data = $this->params['data'] = $data;
		$this->preview = true;
		$this->layoutPath = '';
		$this->subDir = '';
		$this->params['prefix'] = '';
		$this->theme = $this->siteConfigs['theme'];
		$this->setAction('archives');

	}
/**
 * ブログカレンダー用のデータを取得する
 * 
 * @param int $id
 * @param int $year
 * @param int $month
 * @return array
 * @access public
 */
	function get_calendar($id,$year='',$month=''){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$data['entryDates'] = $this->BlogPost->getEntryDates($id,$year,$month);

		if(!$year) {
			$year = date('Y');
		}
		if(!$month) {
			$month = date('m');
		}

		if($month==12) {
			$data['next'] = $this->BlogPost->existsEntry($id, $year+1, 1);
		} else {
			$data['next'] = $this->BlogPost->existsEntry($id, $year, $month+1);
		}
		if($month==1) {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year-1, 12);
		} else {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year, $month-1);
		}
		
		return $data;
		
	}
/**
 * カテゴリー一覧用のデータを取得する
 * 
 * @param int $id
 * @param mixed $count
 * @return array
 * @access public
 */
	function get_categories($id, $count = false){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$data['categories'] = $this->BlogCategory->getCategories($id, $count);
		return $data;
		
	}
/**
 * 月別アーカイブ一覧用のデータを取得する
 * 
 * @param int $id
 * @return mixed $count
 * @access public
 */
	function get_blog_dates($id, $count = false){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$data['blogDates'] = $this->BlogPost->getBlogDates($id, $count);
		return $data;
		
	}
/**
 * 最近の投稿用のデータを取得する
 * 
 * @param int $id
 * @param mixed $count
 * @return array
 * @access public
 */
	function get_recent_entries($id, $count = 5){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$conditions = array('BlogPost.blog_content_id'=>$id);
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		// 毎秒抽出条件が違うのでキャッシュしない
		$data['recentEntries'] = $this->BlogPost->find('all', array(
				'fields'	=> array('no','name'),
				'conditions'=> $conditions,
				'limit'		=> $count,
				'order'		=> 'posts_date DESC',
				'recursive'	=> -1,
				'cache'		=> false
		));
		return $data;
		
	}
/**
 * 記事リストを出力
 *
 * requestAction用
 * 
 * @param int $blogContentId
 * @param mixed $num
 * @access public
 */
	function posts($blogContentId, $num = 5) {
		
		$this->layout = null;
		$conditions = array('BlogPost.blog_content_id' => $blogContentId);
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		$this->BlogPost->unbindModel(array('belongsTo' => array('BlogContent', 'User')));
		// 毎秒抽出条件が違うのでキャッシュしない
		$posts = $this->BlogPost->find('all', array(
				'conditions'=> $conditions,
				'limit'		=> $num,
				'order'		=> 'posts_date DESC',
				'recursive'	=> 1,
				'cache'		=> false
		));
		$this->set('posts', $posts);
		$this->render($this->blogContent['BlogContent']['template'].DS.'posts');
		
	}
/**
 * [MOBILE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 * @access public
 */
	function mobile_posts($blogContentId, $num = 5) {
		
		$this->setAction('posts', $blogContentId, $num);
		
	}
	
}
?>
