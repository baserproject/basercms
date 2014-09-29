<?php
/**
 * ブログヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログヘルパー
 * @package Blog.View.Helper
 */
class BlogHelper extends AppHelper {

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'BcTime', 'BcBaser', 'BcUpload');

/**
 * ブログカテゴリモデル
 *
 * @var BlogCategory
 * @access public
 */
	public $BlogCategory = null;

/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->setContent();
	}

/**
 * ブログコンテンツデータをセットする
 *
 * @param int $blogContentId
 * @return void
 * @access protected
 */
	public function setContent($blogContentId = null) {
		if (isset($this->blogContent) && !$blogContentId) {
			return;
		}
		if ($blogContentId) {
			$BlogContent = ClassRegistry::getObject('BlogContent');
			$BlogContent->expects(array());
			$this->blogContent = Hash::extract($BlogContent->read(null, $blogContentId), 'BlogContent');
		} elseif (isset($this->_View->viewVars['blogContent']['BlogContent'])) {
			$this->blogContent = $this->_View->viewVars['blogContent']['BlogContent'];
		}
		if ($this->blogContent) {
			$BlogPost = ClassRegistry::init('Blog.BlogPost');
			$BlogPost->setupUpload($this->blogContent['id']);
		}
	}

/**
 * ブログタイトルを出力する
 *
 * @return void
 * @access public
 */
	public function title() {
		echo $this->getTitle();
	}

/**
 * タイトルを取得する
 *
 * @return string
 * @access public
 */
	public function getTitle() {
		return $this->blogContent['title'];
	}

/**
 * ブログの説明文を取得する
 *
 * @return string
 * @access public
 */
	public function getDescription() {
		return $this->blogContent['description'];
	}

/**
 * ブログの説明文を出力する
 *
 * @return void
 * @access public
 */
	public function description() {
		echo $this->getDescription();
	}

/**
 * ブログの説明文が指定されているかどうかを判定する
 *
 * @return boolean
 * @access public
 */
	public function descriptionExists() {
		if (!empty($this->blogContent['description'])) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 記事のタイトルを出力する
 *
 * @param array $post
 * @return void
 */
	public function postTitle($post, $link = true) {
		echo $this->getPostTitle($post, $link);
	}

/**
 * 記事タイトルを取得する
 *
 * @param array $post
 * @param boolean $link
 * @return string
 * @access public
 */
	public function getPostTitle($post, $link = true) {
		if ($link) {
			return $this->getPostLinkUrl($post, $post['BlogPost']['name']);
		} else {
			return $post['BlogPost']['name'];
		}
	}

/**
 * 記事へのリンクを出力する
 *
 * @param array $post
 * @param string $title
 * @return void
 * @access public
 */
	public function postLink($post, $title, $options = array()) {
		echo $this->getPostLinkUrl($post, $title, $options);
	}

/**
 * 記事の本文を表示する
 *
 * @param array $post
 * @param mixied boolean / string $moreLink
 * @return void
 * @access public
 */
	public function postContent($post, $moreText = true, $moreLink = false, $cut = false) {
		echo $this->getPostContent($post, $moreText, $moreLink, $cut);
	}

/**
 * 記事の本文を取得する
 *
 * @param array $post
 * @param mixied boolean / string $moreLink
 * @return string
 * @access public
 */
	public function getPostContent($post, $moreText = true, $moreLink = false, $cut = false) {
		if ($moreLink === true) {
			$moreLink = '≫ 続きを読む';
		}
		$out = '<div class="post-body">' . $post['BlogPost']['content'] . '</div>';
		if ($moreText && $post['BlogPost']['detail']) {
			$out .= '<div id="post-detail">' . $post['BlogPost']['detail'] . '</div>';
		}
		if ($cut) {
			$out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
		}
		if ($moreLink && trim($post['BlogPost']['detail']) && trim($post['BlogPost']['detail']) != "<br>") {
			if (!isset($this->Html)) {
				$this->Html = new HtmlHelper($this->_View);
			}
			$out .= '<p class="more">' . $this->Html->link($moreLink, array('admin' => false, 'plugin' => '', 'controller' => $this->blogContent['name'], 'action' => 'archives', $post['BlogPost']['no'], '#' => 'post-detail'), null, null, false) . '</p>';
		}
		return $out;
	}

/**
 * 記事が属するカテゴリ名を出力する
 * 
 * @param array $post
 * @return void
 * @access public
 */
	public function category($post, $options = array()) {
		echo $this->getCategory($post, $options);
	}

/**
 * 記事が属するカテゴリ名の一覧を取得する
 *
 * @param array $post
 * @return string
 */
	public function getCategory($post, $options = array()) {
		if (!empty($post['BlogCategory']['name'])) {

			$options = am(array('link' => true), $options);
			$link = false;

			if ($options['link']) {
				$link = true;
			}

			unset($options['link']);

			if ($link) {
				if (!isset($this->Html)) {
					$this->Html = new HtmlHelper($this->_View);
				}
				return $this->Html->link($post['BlogCategory']['title'], $this->getCategoryUrl($post['BlogCategory']['id'], $options), $options, null, false);
			} else {
				return $post['BlogCategory']['title'];
			}
		} else {
			return '';
		}
	}

/**
 * タグを出力する
 *
 * @param array $post
 * @param string $separator
 * @return void
 * @access public
 */
	public function tag($post, $separator = ' , ') {
		echo $this->getTag($post, $separator);
	}

/**
 * タグを取得する
 *
 * @param array $post
 * @param string $separator
 * @return void
 * @access public
 */
	public function getTag($post, $separator = ' , ') {
		$tagLinks = array();
		if (!empty($post['BlogTag'])) {
			foreach ($post['BlogTag'] as $tag) {
				$url = array(
					'admin' => false,
					'plugin' => '',
					'controller' => $post['BlogContent']['name'],
					'action' => 'archives', 'tag', $tag['name']
				);
				$tagLinks[] = $this->BcBaser->getLink($tag['name'], $url);
			}
		}
		if ($tagLinks) {
			return implode($separator, $tagLinks);
		} else {
			return '';
		}
	}

/**
 * カテゴリ一覧へのURLを取得する
 * [注意] リンク関数でラップする前提の為、ベースURLは考慮されない
 *
 * @param string $blogCategoyId
 * @return void
 */
	public function getCategoryUrl($blogCategoryId, $options = array()) {
		$options = array_merge(array(
			'named' => array()
			), $options);
		extract($options);

		if (!isset($this->BlogCategory)) {
			$this->BlogCategory = ClassRegistry::init('BlogCategory', 'Model');
		}
		$categoryPath = $this->BlogCategory->getPath($blogCategoryId);
		$blogContentId = $categoryPath[0]['BlogCategory']['blog_content_id'];
		$this->setContent($blogContentId);
		$blogContentName = $this->blogContent['name'];

		$path = array('category');
		if ($categoryPath) {
			foreach ($categoryPath as $category) {
				$path[] = $category['BlogCategory']['name'];
			}
		}

		if ($named) {
			$path = array_merge($path, $named);
		}

		$url = Router::url(am(array('admin' => false, 'plugin' => '', 'controller' => $blogContentName, 'action' => 'archives'), $path));
		$baseUrl = preg_replace('/\/$/', '', BC_BASE_URL);
		return preg_replace('/^' . preg_quote($baseUrl, '/') . '/', '', $url);
	}

/**
 * 記事の登録日を出力する
 *
 * @param array $post
 * @param string $format
 * @return void
 * @access public
 */
	public function postDate($post, $format = 'Y/m/d') {
		echo $this->getPostDate($post, $format);
	}

/**
 * 登録日
 *
 * @param array $post
 * @param string $format
 * @return void
 * @access public
 */
	public function getPostDate($post, $format = 'Y/m/d') {
		if (!isset($this->BcTime)) {
			$this->BcTime = new BcTimeHelper($this->_View);
		}
		return $this->BcTime->format($format, $post['BlogPost']['posts_date']);
	}

/**
 * 記事の投稿者を出力する
 *
 * @param array $post
 * @return void
 * @access public
 */
	public function author($post) {
		echo $this->BcBaser->getUserName($post['User']);
	}

/**
 * カテゴリーの一覧をリストタグで取得する
 *
 * @param $categories
 * @param $depth
 * @return string
 * @access public
 */
	public function getCategoryList($categories, $depth = 3, $count = false, $options = array()) {
		return $this->_getCategoryList($categories, $depth, 1, $count, $options);
	}

/**
 * カテゴリーリストを取得する
 *
 * @param $categories
 * @param $depth
 * @return string
 * @access public
 */
	protected function _getCategoryList($categories, $depth = 3, $current = 1, $count = false, $options = array()) {
		if ($depth < $current) {
			return '';
		}

		if ($categories) {
			$out = '<ul class="depth-' . $current . '">';
			$current++;
			foreach ($categories as $category) {
				if ($count && isset($category['BlogCategory']['count'])) {
					$category['BlogCategory']['title'] .= '(' . $category['BlogCategory']['count'] . ')';
				}
				$url = $this->getCategoryUrl($category['BlogCategory']['id']);
				$url = preg_replace('/^\//', '', $url);

				if ($this->_View->request->url == $url) {
					$class = ' class="current"';
				} elseif (!empty($this->_View->params['named']['category']) && $this->_View->params['named']['category'] == $category['BlogCategory']['name']) {
					$class = ' class="selected"';
				} else {
					$class = '';
				}
				$out .= '<li' . $class . '>' . $this->getCategory($category, $options);
				if (!empty($category['BlogCategory']['children'])) {
					$out .= $this->_getCategoryList($category['BlogCategory']['children'], $depth, $current, $count, $options);
				}
				$out .= '</li>';
			}
			$out .= '</ul>';
			return $out;
		} else {
			return '';
		}
	}

/**
 * ブログ編集ページへのリンクを出力【非推奨】
 *
 * @param string $id
 * @return void
 * @access public
 * @deprecated ツールバーに移行
 */
	public function editPost($blogContentId, $blogPostId) {
		if (empty($this->request->params['admin']) && !empty($this->_View->viewVars['user']) && !Configure::read('BcRequest.agent')) {
			echo '<div class="edit-link">' . $this->BcBaser->getLink('≫ 編集する', array('admin' => true, 'prefix' => 'blog', 'controller' => 'blog_posts', 'action' => 'edit', $blogContentId, $blogPostId), array('target' => '_blank')) . '</div>';
		}
	}

/**
 * 前の記事へのリンクを出力する
 *
 * @param array $post
 * @param string $title
 * @param array $htmlAttributes
 * @return void
 * @access pulic
 */
	public function prevLink($post, $title = '', $htmlAttributes = array()) {
		if (ClassRegistry::isKeySet('BlogPost')) {
			$BlogPost = ClassRegistry::getObject('BlogPost');
		} else {
			$BlogPost = ClassRegistry::init('BlogPost');
		}
		$_htmlAttributes = array('class' => 'prev-link', 'arrow' => '≪ ');
		$htmlAttributes = am($_htmlAttributes, $htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		$BlogPost = ClassRegistry::getObject('BlogPost');
		$conditions = array();
		$conditions['BlogPost.posts_date <'] = $post['BlogPost']['posts_date'];
		$conditions["BlogPost.blog_content_id"] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		// 毎秒抽出条件が違うのでキャッシュしない
		$prevPost = $BlogPost->find('first', array(
			'conditions' => $conditions,
			'fields' => array('no', 'name'),
			'order' => 'posts_date DESC',
			'recursive' => 0,
			'cache' => false
		));
		if ($prevPost) {
			$no = $prevPost['BlogPost']['no'];
			if (!$title) {
				$title = $arrow . $prevPost['BlogPost']['name'];
			}
			$this->BcBaser->link($title, array('admin' => false, 'plugin' => '', 'controller' => $this->blogContent['name'], 'action' => 'archives', $no), $htmlAttributes);
		}
	}

/**
 * 次の記事へのリンクを出力する
 *
 * @param array $post
 * @return void
 * @access public
 */
	public function nextLink($post, $title = '', $htmlAttributes = array()) {
		if (ClassRegistry::isKeySet('BlogPost')) {
			$BlogPost = ClassRegistry::getObject('BlogPost');
		} else {
			$BlogPost = ClassRegistry::init('BlogPost');
		}
		$_htmlAttributes = array('class' => 'next-link', 'arrow' => ' ≫');
		$htmlAttributes = am($_htmlAttributes, $htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		$BlogPost = ClassRegistry::getObject('BlogPost');
		$conditions = array();
		$conditions['BlogPost.posts_date >'] = $post['BlogPost']['posts_date'];
		$conditions["BlogPost.blog_content_id"] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		// 毎秒抽出条件が違うのでキャッシュしない
		$nextPost = $BlogPost->find('first', array(
			'conditions' => $conditions,
			'fields' => array('no', 'name'),
			'order' => 'posts_date',
			'recursive' => 0,
			'cache' => false
		));
		if ($nextPost) {
			$no = $nextPost['BlogPost']['no'];
			if (!$title) {
				$title = $nextPost['BlogPost']['name'] . $arrow;
			}
			$this->BcBaser->link($title, array('admin' => false, 'plugin' => '', 'mobile' => false, 'controller' => $this->blogContent['name'], 'action' => 'archives', $no), $htmlAttributes);
		}
	}

/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 別のヘルパに移動
 * @return array
 * @access public
 */
	public function getLayoutTemplates() {
		$templatesPathes = array_merge(App::path('View', 'Blog'), App::path('View'));
		
		if ($this->BcBaser->siteConfig['theme']) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $this->BcBaser->siteConfig['theme'] . DS);
		}

		$_templates = array();
		foreach ($templatesPathes as $templatesPath) {
			$templatesPath .= 'Layouts' . DS;
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if ($files[1]) {
				if ($_templates) {
					$_templates = am($_templates, $files[1]);
				} else {
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach ($_templates as $template) {
			$ext = Configure::read('BcApp.templateExt');
			if ($template != 'installations' . $ext) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

/**
 * ブログテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 別のヘルパに移動
 * @return array
 * @access public
 */
	public function getBlogTemplates() {
		$templatesPathes = array_merge(App::path('View', 'Blog'), App::path('View'));
		if ($this->BcBaser->siteConfig['theme']) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $this->BcBaser->siteConfig['theme'] . DS);
		}

		$_templates = array();
		foreach ($templatesPathes as $templatePath) {
			$templatePath .= 'Blog' . DS;
			$folder = new Folder($templatePath);
			$files = $folder->read(true, true);
			$foler = null;
			if ($files[0]) {
				if ($_templates) {
					$_templates = am($_templates, $files[0]);
				} else {
					$_templates = $files[0];
				}
			}
		}

		$excludes = Configure::read('BcAgent');
		$excludes = Hash::extract($excludes, '{s}.prefix');

	$excludes[] = 'rss';
		$templates = array();
		foreach ($_templates as $template) {
			if (!in_array($template, $excludes)) {
				$templates[$template] = $template;
			}
		}

		return $templates;
	}

/**
 * 公開状態を取得する
 *
 * @param array $data データリスト
 * @return boolean 公開状態
 * @access	public
 */
	public function allowPublish($data) {
		if (ClassRegistry::isKeySet('BlogPost')) {
			$BlogPost = ClassRegistry::getObject('BlogPost');
		} else {
			$BlogPost = ClassRegistry::init('BlogPost');
		}
		return $BlogPost->allowPublish($data);
	}

/**
 * 記事中の画像を出力する
 *
 * @param array $post
 * @param array $options
 * @return void
 * @access public
 */
	public function postImg($post, $options = array()) {
		echo $this->getPostImg($post, $options);
	}

/**
 * 記事中の画像を取得する
 *
 * @param array $post
 * @param array $options
 * @return void
 * @access public
 */
	public function getPostImg($post, $options = array()) {
		$this->setContent($post['BlogPost']['blog_content_id']);
		$options = array_merge($_options = array(
			'num' => 1,
			'link' => true,
			'alt' => $post['BlogPost']['name']
			), $options);

		extract($options);

		unset($options['num']);
		unset($options['link']);

		$contents = $post['BlogPost']['content'] . $post['BlogPost']['detail'];
		$pattern = '/<img.*?src="([^"]+)"[^>]*>/is';
		if (!preg_match_all($pattern, $contents, $matches)) {
			return '';
		}

		if (isset($matches[1][$num - 1])) {
			$url = $matches[1][$num - 1];
			$url = preg_replace('/^' . preg_quote($this->base, '/') . '/', '', $url);
			$img = $this->BcBaser->getImg($url, $options);
			if ($link) {
				return $this->BcBaser->getLink($img, $url = array('admin' => false, 'plugin' => '', 'controller' => $this->blogContent['name'], 'action' => 'archives', $post['BlogPost']['no']));
			} else {
				return $img;
			}
		} else {
			return '';
		}
	}

/**
 * 記事中のタグで指定したIDの内容を取得する
 *
 * @param array $post
 * @param string $id
 * @return string
 * @access public
 */
	public function getHtmlById($post, $id) {
		$content = $post['BlogPost']['content'] . $post['BlogPost']['detail'];

		$values = array();
		$pattern = '/<([^\s]+)\s[^>]*?id="' . $id . '"[^>]*>(.*?)<\/\1>/is';
		if (preg_match($pattern, $content, $matches)) {
			return $matches[2];
		} else {
			return '';
		}
	}

/**
 * 親カテゴリを取得する
 *
 * @param array $post
 * @return array $parentCategory
 * @access public
 */
	public function getParentCategory($post) {
		if (empty($post['BlogCategory']['id'])) {
			return null;
		}

		$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		return $BlogCategory->getParentNode($post['BlogCategory']['id']);
	}

/**
 * 同じタグの関連投稿を取得する
 *
 * @param array $post
 * @return array
 * @access public
 */
	public function getRelatedPosts($post, $options = array()) {
		if (empty($post['BlogTag'])) {
			return array();
		}

		$options = array_merge(array(
			'recursive' => -1,
			'limit' => 5,
			'order' => 'BlogPost.posts_date DESC'
			), $options);

		extract($options);

		$tagNames = array();
		foreach ($post['BlogTag'] as $tag) {
			$tagNames[] = urldecode($tag['name']);
		}
		$BlogTag = ClassRegistry::init('Blog.BlogTag');
		$tags = $BlogTag->find('all', array(
			'conditions' => array('BlogTag.name' => $tagNames),
			'recursive' => 1
		));

		if (!isset($tags[0]['BlogPost'][0]['id'])) {
			return array();
		}

		$ids = Hash::extract($tags, '{n}.BlogPost.id');

		$BlogPost = ClassRegistry::init('Blog.BlogPost');

		$conditions = array(
			array('BlogPost.id' => $ids),
			array('BlogPost.id <>' => $post['BlogPost']['id']),
			'BlogPost.blog_content_id' => $post['BlogPost']['blog_content_id']
		);
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());

		// 毎秒抽出条件が違うのでキャッシュしない
		$relatedPosts = $BlogPost->find('all', array(
			'conditions' => $conditions,
			'recursive' => $recursive,
			'order' => $order,
			'limit' => $limit,
			'cache' => false
		));

		return $relatedPosts;
	}

/**
 * ブログのアーカイブタイプを取得する
 *
 * @return string
 * @access public
 */
	public function getBlogArchiveType() {
		if (!empty($this->_View->viewVars['blogArchiveType'])) {
			return $this->_View->viewVars['blogArchiveType'];
		} else {
			return '';
		}
	}

/**
 * アーカイブページ判定
 * @return boolean 
 */
	public function isArchive() {
		return ($this->getBlogArchiveType());
	}

/**
 * カテゴリー別記事一覧ページ判定
 * @return boolean
 */
	public function isCategory() {
		return ($this->getBlogArchiveType() == 'category');
	}

/**
 * タグ別記事一覧ページ判定
 * @return boolean
 */
	public function isTag() {
		return ($this->getBlogArchiveType() == 'tag');
	}

/**
 * 日別記事一覧ページ判定
 * @return boolean
 */
	public function isDate() {
		return ($this->getBlogArchiveType() == 'daily');
	}

/**
 * 月別記事一覧ページ判定
 * @return boolean 
 */
	public function isMonth() {
		return ($this->getBlogArchiveType() == 'monthly');
	}

/**
 * 年別記事一覧ページ判定
 * @return boolean
 */
	public function isYear() {
		return ($this->getBlogArchiveType() == 'yearly');
	}

/**
 * 個別ページ判定
 * @return boolean
 */
	public function isSingle() {
		if (empty($this->request->params['plugin'])) {
			return false;
		}
		$agentPrefix = Configure::read('BcRequest.agentPrefix');
		if($agentPrefix) {
			$agentPrefix .= '_';
		}
		return (
			$this->request->params['plugin'] == 'blog' && 
			$this->request->params['controller'] == 'blog' && 
			$this->request->params['action'] == $agentPrefix . 'archives' && 
			!$this->getBlogArchiveType()
		);
	}

/**
 * インデックスページ判定
 * @return boolean
 */
	public function isHome() {
		if (empty($this->request->params['plugin'])) {
			return false;
		}
		return ($this->request->params['plugin'] == 'blog' && $this->request->params['controller'] == 'blog' && $this->request->params['action'] == 'index');
	}

/**
 * アイキャッチ画像を出力する
 * 
 * @param array $post
 * @param array $options 
 */
	public function eyeCatch($post, $options = array()) {
		echo $this->getEyeCatch($post, $options);
	}

/**
 * アイキャッチ画像を取得する
 * 
 * @param array $post
 * @param array $options
 * @return string 
 */
	public function getEyeCatch($post, $options = array()) {
		$options = array_merge(array(
			'imgsize' => 'thumb', // 画像サイズ
			'link' => true, // 大きいサイズの画像へのリンク有無
			'escape' => false, // エスケープ
			'mobile' => false, // モバイル
			'alt' => '', // alt属性
			'width' => '', // 横幅
			'height' => '', // 高さ
			'noimage' => '', // 画像がなかった場合に表示する画像
			'tmp' => false,
			'class' => 'img-eye-catch'
		), $options);

		return $this->BcUpload->uploadImage('BlogPost.eye_catch', $post['BlogPost']['eye_catch'], $options);
	}
	
/**
 * メールフォームプラグインのフォームへのリンクを生成する
 * 
 * @param string $title リンクのタイトル
 * @param string $contentsName メールフォームのコンテンツ名
 * @param array $datas メールフォームに引き継ぐデータ
 * @param array $options a タグのオプション設定
 */
	public function mailFormLink($title, $contentsName, $datas, $options) {
		App::uses('MailHelper', 'Mail.View/Helper');
		$MailHelper = new MailHelper($this->_View);
		$MailHelper->link($title, $contentsName, $datas, $options);
	}
	
/**
 * ブログ記事のURLを生成して返す
 * 
 * @param type $post
 * @param type $options
 * @return string
 */
    public function getPostLinkUrl($post, $title = '', $options = array()) {
		
		$blogContent = $this->blogContent;
		$url = array('admin'=>false, 'plugin'=>'', 'controller'=>$blogContent['name'], 'action'=>'archives', $post['BlogPost']['no']);
		if(!empty($title)){
			return $this->BcBaser->getLink($title, $url, $options);
		}else{
			return $this->BcBaser->url($url);
		}
		
        return;
    
	}
	
}
