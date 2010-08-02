<?php
/* SVN FILE: $Id$ */
/**
 * ブログヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ブログヘルパー
 * @package			baser.plugins.blog.views.helpers
 */
class BlogHelper extends AppHelper {
/**
 * view
 * @var		View
 * @access	protected
 */
	var $_view = null;
/**
 * ヘルパー
 * @var		array
 * @access	public
 */
	var $helpers = array('Html','TimeEx','Baser');
/**
 * ブログカテゴリモデル
 * @var		BlogCategory
 * @access	public
 */
	var $BlogCategory = null;
/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	function __construct() {
		$this->_view =& ClassRegistry::getObject('view');
		if(isset($this->_view->viewVars['blogContent'])) {
			$this->blogContent = $this->_view->viewVars['blogContent']['BlogContent'];
		}
	}
/**
 * タイトルを表示する
 * @return void
 */
	function title() {
		echo $this->getTitle();
	}
/**
 * タイトルを取得する
 * @return string
 */
	function getTitle() {
		return $this->blogContent['title'];
	}
/**
 * ブログの説明文を取得する
 * @return string
 */
	function getDescription() {
		return $this->blogContent['description'];
	}
/**
 * ブログの説明文を表示する
 * @return void
 */
	function description() {
		echo $this->getDescription();
	}
/**
 * ブログの説明文が指定されているかどうか
 * @return boolean
 */
	function descriptionExists() {
		if(!empty($this->blogContent['description'])) {
			return true;
		}else {
			return false;
		}
	}
/**
 * 記事のタイトルを表示する
 * @param array $post
 * @return void
 */
	function postTitle($post) {
		$url = array('admin'=>false,'plugin'=>'','controller'=>$this->blogContent['name'],'action'=>'archives', $post['BlogPost']['no']);
		$this->Baser->link($post['BlogPost']['name'], $url,array('prefix'=>true));
	}
/**
 * コンテンツを表示する
 * @param array $post
 * @param mixied boolean / string $moreLink
 * @return void
 */
	function postContent($post,$moreText = true, $moreLink = false, $cut = false) {
		echo $this->getPostContent($post, $moreText, $moreLink, $cut);
	}
/**
 * コンテンツデータを取得する
 * @param array $post
 * @param mixied boolean / string $moreLink
 * @return string
 */
	function getPostContent($post,$moreText = true, $moreLink = false, $cut = false) {

		if($moreLink === true) {
			$moreText = '&gt;&gt; 続きを読む';
		}elseif($moreLink !== false) {
			$moreText = $moreLink;
		}

		$out =	'<div class="post-body">'.$post['BlogPost']['content'].'</div>';
		if($moreLink && trim($post['BlogPost']['detail']) && trim($post['BlogPost']['detail']) != "<br>") {
			$out .= '<p class="more">'.$this->Html->link($moreText, array('admin'=>false,'plugin'=>'', 'controller'=>$this->blogContent['name'],'action'=>'archives', $post['BlogPost']['no'],'#'=>'post-detail'), null,null,false).'</p>';
		}elseif($moreText && $post['BlogPost']['detail']) {
			$out .=	'<div id="post-detail">'.$post['BlogPost']['detail'].'</div>';
		}
		if($cut) {
			$out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
		}
		return $out;

	}
/**
 * カテゴリを出力する
 * @param array $post
 * @return void
 */
	function category($post) {
		echo $this->getCategory($post);
	}
/**
 * カテゴリを取得する
 * @param array $post
 * @return string
 */
	function getCategory($post) {
		if(!empty($post['BlogCategory']['name'])) {
			if(!isset($this->Html)){
				$this->Html = new HtmlHelper();
			}
			return $this->Html->link($post['BlogCategory']['title'],$this->getCategoryUrl($post['BlogCategory']['id']),null,null,false);
		}else {
			return '';
		}
	}
/**
 * カテゴリのURLを取得する
 * @param string $blogCategoyId
 * @return void
 */
	function getCategoryUrl($blogCategoryId) {

		$view =& ClassRegistry::getObject('view');
		$blogContentName = $view->viewVars['blogContent']['BlogContent']['name'];
		if (!isset($this->BlogCategory)) {
			if(ClassRegistry::isKeySet('BlogCategory')) {
				$this->BlogCategory = ClassRegistry::getObject('BlogCategory');
			}else {
				$this->BlogCategory =& ClassRegistry::init('BlogCategory','Model');
			}
		}
		$categoryPath = $this->BlogCategory->getPath($blogCategoryId);
		$path = array('category');
		if($categoryPath) {
			foreach($categoryPath as $category) {
				$path[] = $category['BlogCategory']['name'];
			}
		}
		$_url = array('admin'=>false,'blog'=>false,'plugin'=>'','controller'=>$blogContentName,'action'=>'archives',implode(DS,$path));
		if(!empty($this->params['prefix']) && $this->params['prefix'] != 'admin') {
			$_url[$this->params['prefix']] = true;
		}
		$url = Router::url($_url);
		return str_replace($this->base,'',$url);

	}
/**
 * 登録日
 * @param array $post
 * @param string $format
 * @return void
 */
	function postDate($post,$format = 'Y/m/d') {
		echo $this->TimeEx->format($format,$post['BlogPost']['posts_date']);
	}
/**
 * 投稿者を出力
 * @param array $post
 * @return void
 */
	function author($post) {
		$author = '';
		if(!empty($post['User']['real_name_1'])) {
			$author .= $post['User']['real_name_1'];
		}
		if(!empty($post['User']['real_name_2'])) {
			$author .= " ".$post['User']['real_name_2'];
		}
		echo $author;
	}
/**
 * カテゴリーリストを取得する
 * @param $categories
 * @param $depth
 * @return string
 */
	function getCategoryList($categories,$depth=3) {
		return $this->_getCategoryList($categories,$depth);
	}
/**
 * カテゴリーリストを取得する
 * @param $categories
 * @param $depth
 * @return string
 */
	function _getCategoryList($categories,$depth=3,$current=1) {
		if($depth < $current) {
			return '';
		}
		if($categories) {
			$out = '<ul class="depth-'.$current.'">';
			$current++;
			foreach($categories as $category) {
				$out .= '<li>'.$this->getCategory($category);
				if(!empty($category['children'])) {
					$out.= $this->_getCategoryList($category['children'],$depth,$current);
				}
				$out.='</li>';
			}
			$out .= '</ul>';
			return $out;
		}else {
			return '';
		}
	}
/**
 * ブログ編集ページへのリンクを出力
 * @param string $id
 */
	function editPost($blogContentId,$blogPostId) {
		if(empty($this->params['admin']) && !empty($this->_view->viewVars['user']) && !Configure::read('Mobile.on')) {
			echo '<div class="edit-link">'.$this->Baser->getLink('≫ 編集する',array('admin'=>true,'prefix'=>'blog','controller'=>'blog_posts','action'=>'edit',$blogContentId,$blogPostId),array('target'=>'_blank')).'</div>';
		}
	}
/**
 * 前の記事へのリンクを取得する
 * @param array $post
 */
	function prevLink($post,$title='',$htmlAttributes = array()) {

		$_htmlAttributes = array('class'=>'prev-link','arrow'=>'≪ ');
		$htmlAttributes = am($_htmlAttributes,$htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		$htmlAttributes['prefix'] = true;
		$BlogPost =& ClassRegistry::getObject('BlogPost');
		$conditions = array();
		$conditions['BlogPost.posts_date <'] = $post['BlogPost']['posts_date'];
		$conditions["BlogPost.blog_content_id"] = $post['BlogPost']['blog_content_id'];
		$conditions['BlogPost.status'] = true;
		$prevPost = $BlogPost->find($conditions,array('no','name'),'posts_date DESC',-1);
		if($prevPost) {
			$no = $prevPost['BlogPost']['no'];
			if(!$title) {
				$title = $arrow.$prevPost['BlogPost']['name'];
			}
			$this->Baser->link($title, array('admin'=>false,'plugin'=>'','controller'=>$this->blogContent['name'],'action'=>'archives', $no),$htmlAttributes);
		}

	}
/**
 * 次の記事へのリンクを取得する
 * @param array $post
 */
	function nextLink($post,$title='',$htmlAttributes = array()) {

		$_htmlAttributes = array('class'=>'next-link','arrow'=>' ≫');
		$htmlAttributes = am($_htmlAttributes,$htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		$htmlAttributes['prefix'] = true;
		$BlogPost =& ClassRegistry::getObject('BlogPost');
		$conditions = array();
		$conditions['BlogPost.posts_date >'] = $post['BlogPost']['posts_date'];
		$conditions["BlogPost.blog_content_id"] = $post['BlogPost']['blog_content_id'];
		$conditions['BlogPost.status'] = true;
		$nextPost = $BlogPost->find($conditions,array('no','name'),'posts_date',-1);
		if($nextPost) {
			$no = $nextPost['BlogPost']['no'];
			if(!$title) {
				$title = $nextPost['BlogPost']['name'].$arrow;
			}
			$this->Baser->link($title, array('admin'=>false,'plugin'=>'','controller'=>$this->blogContent['name'],'action'=>'archives', $no),$htmlAttributes);
		}

	}
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * @return	array
 * @access	public
 */
	function getLayoutTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'layouts'.DS;
		}
		$templatesPathes = am($templatesPathes,array(BASER_PLUGINS.'blog'.DS.'views'.DS.'layouts'.DS,
														BASER_VIEWS.'layouts'.DS));
		
		$_templates = array();
		foreach($templatesPathes as $templatesPath){
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[1]){
				if($_templates){
					$_templates = am($_templates,$files[1]);
				}else{
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'installations.ctp'){
				$template = basename($template, '.ctp');
				$templates[$template] = $template;
			}
		}
		return $templates;
	}
/**
 * ブログテンプレートを取得
 * コンボボックスのソースとして利用
 * @return	array
 * @access	public
 */
	function getBlogTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'blog'.DS;
		}
		$templatesPathes[] = BASER_PLUGINS.'blog'.DS.'views'.DS.'blog'.DS;
		
		$_templates = array();
		foreach($templatesPathes as $templatePath){
			$folder = new Folder($templatePath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[0]){
				if($_templates){
					$_templates = am($_templates,$files[0]);
				}else{
					$_templates = $files[0];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'rss' && $template != 'mobile'){
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

}
?>