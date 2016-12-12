<?php
/**
 * ブログヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログヘルパー
 * @package Blog.View.Helper
 * @property BcTimeHelper $BcTime BcTimeヘルパ
 * @property BcBaserHelper $BcBaser BcBaserヘルパ
 * @property BcUploadHelper $BcUpload BcUploadヘルパ
 */
class BlogHelper extends AppHelper {

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('Html', 'BcTime', 'BcBaser', 'BcUpload');

/**
 * ブログカテゴリモデル
 *
 * @var BlogCategory
 */
	public $BlogCategory = null;

/**
 * コンストラクタ
 *
 * @param View $View Viewオブジェクト
 * @param array $settings 設定
 * @return void
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->setContent();
	}

/**
 * ブログコンテンツデータをセットする
 *
 * アイキャッチを利用する場合に必ず設定が必要
 *
 * @param int $blogContentId ブログコンテンツID
 * @return void
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
 * ブログIDを出力する
 *
 * @return void
 */
	public function currentBlogId() {
		echo $this->getCurrentBlogId();
	}

/**
 * ブログIDを取得する
 *
 * @return integer
 */
	public function getCurrentBlogId() {
		return $this->blogContent['id'];
	}

/**
 * ブログアカウント名を出力する
 *
 * @return void
 */
	public function blogName() {
		echo $this->getBlogName();
	}

/**
 * ブログアカウント名を取得する
 *
 * @return string
 */
	public function getBlogName() {
		return $this->blogContent['name'];
	}

/**
 * ブログタイトルを出力する
 *
 * @return void
 */
	public function title() {
		echo $this->getTitle();
	}

/**
 * タイトルを取得する
 *
 * @return string
 */
	public function getTitle() {
		return $this->blogContent['title'];
	}

/**
 * ブログの説明文を取得する
 *
 * @return string
 */
	public function getDescription() {
		return $this->blogContent['description'];
	}

/**
 * ブログの説明文を出力する
 *
 * @return void
 */
	public function description() {
		echo $this->getDescription();
	}

/**
 * ブログの説明文が指定されているかどうかを判定する
 *
 * @return boolean
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
 * @param array $post ブログ記事データ
 * @param boolean $link 詳細ページへのリンクをつける場合には、true を指定する（初期値 : true）
 * @return void
 */
	public function postTitle($post, $link = true) {
		echo $this->getPostTitle($post, $link);
	}

/**
 * 記事タイトルを取得する
 *
 * @param array $post ブログ記事データ
 * @param boolean $link 詳細ページへのリンクをつける場合には、true を指定する（初期値 : true）
 * @return string 記事タイトル
 */
	public function getPostTitle($post, $link = true) {
		if ($link) {
			return $this->getPostLink($post, $post['BlogPost']['name']);
		} else {
			return $post['BlogPost']['name'];
		}
	}

/**
 * 記事へのリンクを取得する
 *
 * @param array $post ブログ記事データ
 * @param string $title タイトル
 * @param array $options オプション（初期値 : array()）
 *	※ オプションについては、 HtmlHelper::link() を参照
 * @return string 記事へのリンク
 */
	public function getPostLink($post, $title, $options = array()) {
		$this->setContent($post['BlogPost']['blog_content_id']);
		$url = array('admin' => false, 'plugin' => '', 'controller' => $this->blogContent['name'], 'action' => 'archives', $post['BlogPost']['no']);
		return $this->BcBaser->getLink($title, $url, $options);
	}

/**
 * ブログ記事のURLを取得する
 *
 * @param array $post ブログ記事データ
 * @return string ブログ記事のURL
 */
	public function getPostLinkUrl($post) {
		$this->setContent($post['BlogPost']['blog_content_id']);
		return $this->url(array('admin' => false, 'plugin' => '', 'controller' => $this->blogContent['name'], 'action' => 'archives', $post['BlogPost']['no']));
	}

/**
 * 記事へのリンクを出力する
 *
 * @param array $post ブログ記事データ
 * @param string $title タイトル
 * @param array $options オプション（初期値 : array()）
 *	※ オプションについては、 HtmlHelper::link() を参照
 * @return void
 */
	public function postLink($post, $title, $options = array()) {
		echo $this->getPostLink($post, $title, $options);
	}

/**
 * 記事の本文を表示する
 *
 * @param array $post ブログ記事データ
 * @param boolean $moreText 詳細データを表示するかどうか（初期値 : true）
 * @param mixied $moreLink 詳細ページへのリンクを表示するかどうか。true に指定した場合、
 *	「≫ 続きを読む」という文字列がリンクとして表示される。（初期値 : false）
 * また、文字列を指定するとその文字列がリンクとなる
 * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
 * @return void
 */
	public function postContent($post, $moreText = true, $moreLink = false, $cut = false) {
		echo $this->getPostContent($post, $moreText, $moreLink, $cut);
	}

/**
 * 記事の本文を取得する
 *
 * @param array $post ブログ記事データ
 * @param boolean $moreText 詳細データを表示するかどうか（初期値 : true）
 * @param mixied $moreLink 詳細ページへのリンクを表示するかどうか。true に指定した場合、
 *	「≫ 続きを読む」という文字列がリンクとして表示される。（初期値 : false）
 * また、文字列を指定するとその文字列がリンクとなる
 * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
 * @return string 記事本文
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
			$out = str_replace(array("\r\n", "\r", "\n"), '', $out);
			$out = html_entity_decode($out, ENT_QUOTES, 'UTF-8');
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
 * 記事の詳細を表示する
 *
 * @param array $post ブログ記事データ
 * @param array $options オプション（初期値 : array()）getPostDetailを参照
 * @return void
 */
	public function postDetail($post, $options = array()) {
		echo $this->getPostDetail($post, $options);
	}

/**
 * 記事の詳細を取得する
 *
 * @param array $post ブログ記事データ
 * @param array $options オプション（初期値 : array()）
 *	- `cut` : 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
 * @return string 記事本文
 */
	public function getPostDetail($post, $options = array()) {

		$options = array_merge(array(
			'cut' => false
		), $options);
		extract($options);

		unset($options['cut']);

		$out = $post['BlogPost']['detail'];

		if ($cut) {
			$out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
		}

		return $out;
	}

/**
 * 記事が属するカテゴリ名を出力する
 *
 * @param array $post 記事データ
 * @param array $options オプション（初期値 : array()）
 *	- `link` : リンクをつけるかどうか（初期値 : true）
 *	※ その他のオプションは、`link`オプションが`true`の場合に
 *	生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
 * @return void
 */
	public function category($post, $options = array()) {
		echo $this->getCategory($post, $options);
	}

/**
 * 記事が属するカテゴリ名を取得する
 *
 * @param array $post 記事データ
 * @param array $options オプション（初期値 : array()）
 *	- `link` : リンクをつけるかどうか（初期値 : true）
 *	※ その他のオプションは、`link`オプションが`true`の場合に
 *	生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
 * @return string カテゴリ名
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
 * 複数所属する場合は複数出力する
 *
 * @param array $post 記事データ
 * @param string $separator 区切り文字（初期値 :  , ）
 * @return void
 */
	public function tag($post, $separator = ' , ') {
		echo $this->getTag($post, $separator);
	}

/**
 * タグを取得する
 *
 * 複数所属する場合は複数取得する
 *
 * @param array $post 記事データ
 * @param string $separator 区切り文字（初期値 :  , ）
 * @return void
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
 *
 * [注意] リンク関数でラップする前提の為、ベースURLは考慮されない
 *
 * @param string $blogCategoyId ブログカテゴリID
 * @param array $options オプション（初期値 : array()）
 *	`named` : URLの名前付きパラメーター
 * @return string カテゴリ一覧へのURL
 */
	public function getCategoryUrl($blogCategoryId, $options = array()) {
		$options = array_merge(array(
			'named' => array()
		), $options);
		extract($options);

		if (!isset($this->BlogCategory)) {
			$this->BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		}
		$categoryPath = $this->BlogCategory->getPath($blogCategoryId);
		$blogContentId = $categoryPath[0]['BlogCategory']['blog_content_id'];
		$this->setContent($blogContentId);
		$blogContentName = $this->blogContent['name'];

		$path = array('category');
		if ($categoryPath) {
			foreach ($categoryPath as $category) {
				$path[] = urldecode($category['BlogCategory']['name']);
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
 * @param array $post ブログ記事
 * @param string $format 日付フォーマット（初期値 : Y/m/d）
 * @return void
 */
	public function postDate($post, $format = 'Y/m/d') {
		echo $this->getPostDate($post, $format);
	}

/**
 * 登録日
 *
 * @param array $post ブログ記事
 * @param string $format 日付フォーマット（初期値 : Y/m/d）
 * @return string 登録日
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
 * @param array $post ブログ記事
 * @return void
 */
	public function author($post) {
		echo $this->BcBaser->getUserName($post['User']);
	}

/**
 * カテゴリーの一覧をリストタグで取得する
 *
 * @param array $categories カテゴリ一覧データ
 * @param int $depth 階層（初期値 : 3）
 * @param boolean $count 件数を表示するかどうか（初期値 : false）
 * @param array $options オプション（初期値 : array()）
 *	- `link` : リンクをつけるかどうか（初期値 : true）
 *	※ その他のオプションは、`link`オプションが`true`の場合に
 *	生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
 * @return string HTMLのカテゴリ一覧
 */
	public function getCategoryList($categories, $depth = 3, $count = false, $options = array()) {
		return $this->_getCategoryList($categories, $depth, 1, $count, $options);
	}

/**
 * カテゴリーリストを取得する
 *
 * @param array $categories カテゴリ一覧データ
 * @param int $depth 階層（初期値 : 3）
 * @param int $current 現在の階層（初期値 : 1）
 * @param boolean $count 件数を表示するかどうか（初期値 : false）
 * @param array $options オプション（初期値 : array()）
 *	- `link` : リンクをつけるかどうか（初期値 : true）
 *	※ その他のオプションは、`link`オプションが`true`の場合に
 *	生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
 * @return string HTMLのカテゴリ一覧
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
 * ブログ編集ページへのリンクを出力
 *
 * @param int $blogContentId ブログコンテンツID
 * @param int $blogPostId ブログ記事ID
 * @return void
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
 * @param array $post ブログ記事
 * @param string $title タイトル
 * @param array $htmlAttributes HTML属性
 *	※ HTML属性は、HtmlHelper::link() 参照
 * @return void
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
		// 投稿日が年月日時分秒が同一のデータの対応の為、投稿日が同じでIDが大きいデータを検索
		$conditions = array();
		$conditions['BlogPost.id <'] = $post['BlogPost']['id'];
		$conditions['BlogPost.posts_date'] = $post['BlogPost']['posts_date'];
		$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		$order = 'BlogPost.posts_date DESC, BlogPost.id DESC';
		// 毎秒抽出条件が違うのでキャッシュしない
		$prevPost = $BlogPost->find('first', array(
			'conditions' => $conditions,
			'fields' => array('no', 'name'),
			'order' => $order,
			'recursive' => -1,
			'cache' => false
		));
		if (empty($prevPost)) {
			// 投稿日が古いデータを取得
			$conditions = array();
			$conditions['BlogPost.posts_date <'] = $post['BlogPost']['posts_date'];
			$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
			$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
			// 毎秒抽出条件が違うのでキャッシュしない
			$prevPost = $BlogPost->find('first', array(
				'conditions' => $conditions,
				'fields' => array('no', 'name'),
				'order' => $order,
				'recursive' => -1,
				'cache' => false
			));
		}
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
 * @param array $post ブログ記事
 * @param string $title タイトル
 * @param array $htmlAttributes HTML属性
 *	※ HTML属性は、HtmlHelper::link() 参照
 * @return void
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

		// 投稿日が年月日時分秒が同一のデータの対応の為、投稿日が同じでIDが小さいデータを検索
		$conditions = array();
		$conditions['BlogPost.id >'] = $post['BlogPost']['id'];
		$conditions['BlogPost.posts_date'] = $post['BlogPost']['posts_date'];
		$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		$order = 'BlogPost.posts_date, BlogPost.id';
		// 毎秒抽出条件が違うのでキャッシュしない
		$nextPost = $BlogPost->find('first', array(
			'conditions' => $conditions,
			'fields' => array('no', 'name'),
			'order' => $order,
			'recursive' => -1,
			'cache' => false
		));

		if (empty($nextPost)) {
			// 投稿日が新しいデータを取得
			$conditions = array();
			$conditions['BlogPost.posts_date >'] = $post['BlogPost']['posts_date'];
			$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
			$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
			// 毎秒抽出条件が違うのでキャッシュしない
			$nextPost = $BlogPost->find('first', array(
				'conditions' => $conditions,
				'fields' => array('no', 'name'),
				'order' => $order,
				'recursive' => -1,
				'cache' => false
			));
		}
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
 *
 * コンボボックスのソースとして利用
 *
 * @return array レイアウトテンプレート一覧
 * @todo 別のヘルパに移動
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
 *
 * コンボボックスのソースとして利用
 *
 * @return array ブログテンプレート一覧
 * @todo 別のヘルパに移動
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
 * @param array $data ブログ記事
 * @return boolean 公開状態
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
 * @param array $post ブログ記事
 * @param array $options オプション（初期値 : array()）
 *	- `num` : 何枚目の画像か順番を指定（初期値 : 1）
 *	- `link` : 詳細ページへのリンクをつけるかどうか（初期値 : true）
 *	- `alt` : ALT属性（初期値 : ブログ記事のタイトル）
 * @return void
 */
	public function postImg($post, $options = array()) {
		echo $this->getPostImg($post, $options);
	}

/**
 * 記事中の画像を取得する
 *
 * @param array $post ブログ記事
 * @param array $options オプション（初期値 : array()）
 *	- `num` : 何枚目の画像か順番を指定（初期値 : 1）
 *	- `link` : 詳細ページへのリンクをつけるかどうか（初期値 : true）
 *	- `alt` : ALT属性（初期値 : ブログ記事のタイトル）
 * @return void
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
 * @param array $post ブログ記事
 * @param string $id 取得したいデータが属しているタグのID属性
 * @return string 指定したIDの内容
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
 * @param array $post ブログ記事
 * @return array $parentCategory 親カテゴリ
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
 * @param array $post ブログ記事
 * @param array $options オプション（初期値 : array()）
 *	- `recursive` : 関連データを取得する場合の階層（初期値 : -1）
 *	- `limit` : 件数（初期値 : 5）
 *	- `order` : 並び順指定（初期値 : BlogPost.posts_date DESC）
 * @return array
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

		$ids = array_unique(Hash::extract($tags, '{n}.BlogPost.{n}.id'));
		
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
			'recursive' => $options['recursive'],
			'order' => $options['order'],
			'limit' => $options['limit'],
			'cache' => false
		));

		return $relatedPosts;
	}

/**
 * ブログのアーカイブタイプを取得する
 *
 * @return string ブログのアーカイブタイプ
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
 *
 * @return boolean 現在のページがアーカイブページの場合は true を返す
 */
	public function isArchive() {
		return ($this->getBlogArchiveType());
	}

/**
 * カテゴリー別記事一覧ページ判定
 *
 * @return boolean 現在のページがカテゴリー別記事一覧ページの場合は true を返す
 */
	public function isCategory() {
		return ($this->getBlogArchiveType() == 'category');
	}

/**
 * タグ別記事一覧ページ判定
 *
 * @return boolean 現在のページがタグ別記事一覧ページの場合は true を返す
 */
	public function isTag() {
		return ($this->getBlogArchiveType() == 'tag');
	}

/**
 * 日別記事一覧ページ判定
 *
 * @return boolean 現在のページが日別記事一覧ページの場合は true を返す
 */
	public function isDate() {
		return ($this->getBlogArchiveType() == 'daily');
	}

/**
 * 月別記事一覧ページ判定
 *
 * @return boolean 現在のページが月別記事一覧ページの場合は true を返す
 */
	public function isMonth() {
		return ($this->getBlogArchiveType() == 'monthly');
	}

/**
 * 年別記事一覧ページ判定
 *
 * @return boolean 現在のページが年別記事一覧ページの場合は true を返す
 */
	public function isYear() {
		return ($this->getBlogArchiveType() == 'yearly');
	}

/**
 * 個別ページ判定
 *
 * @return boolean 現在のページが個別ページの場合は true を返す
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
 *
 * @return boolean 現在のページがインデックスページの場合は true を返す
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
 * @param array $post ブログ記事
 * @param array $options オプション（初期値 : array()）
 *	- `imgsize` : 画像サイズ[thumb|small|medium|large]（初期値 : thumb）
 *  - `link` : 大きいサイズの画像へのリンク有無（初期値 : true）
 *  - `escape` : タイトルについてエスケープする場合に true を指定（初期値 : false）
 *	- `mobile` : モバイルの画像を表示する場合に true を指定（初期値 : false）
 *	- `alt` : alt属性（初期値 : ''）
 *	- `width` : 横幅（初期値 : ''）
 *	- `height` : 高さ（初期値 : ''）
 *	- `noimage` : 画像が存在しない場合に表示する画像（初期値 : ''）
 *	- `tmp` : 一時保存データの場合に true を指定（初期値 : false）
 *	- `class` : タグの class を指定（初期値 : img-eye-catch）
 *	- `force` : 画像が存在しない場合でも強制的に出力する場合に true を指定する（初期値 : false）
 *  ※ その他のオプションについては、リンクをつける場合、HtmlHelper::link() を参照、つけない場合、Html::image() を参照
 * @return void
 */
	public function eyeCatch($post, $options = array()) {
		echo $this->getEyeCatch($post, $options);
	}

/**
 * アイキャッチ画像を取得する
 *
 * @param array $post ブログ記事
 * @param array $options オプション（初期値 : array()）
 *	- `imgsize` : 画像サイズ[thumb|small|medium|large]（初期値 : thumb）
 *  - `link` : 大きいサイズの画像へのリンク有無（初期値 : true）
 *  - `escape` : タイトルについてエスケープする場合に true を指定（初期値 : false）
 *	- `mobile` : モバイルの画像を表示する場合に true を指定（初期値 : false）
 *	- `alt` : alt属性（初期値 : ''）
 *	- `width` : 横幅（初期値 : ''）
 *	- `height` : 高さ（初期値 : ''）
 *	- `noimage` : 画像が存在しない場合に表示する画像（初期値 : ''）
 *	- `tmp` : 一時保存データの場合に true を指定（初期値 : false）
 *	- `class` : タグの class を指定（初期値 : img-eye-catch）
 *	- `force` : 画像が存在しない場合でも強制的に出力する場合に true を指定する（初期値 : false）
 *	- `output` : 出力形式 tag, url のを指定できる（初期値 : ''）
 *  ※ その他のオプションについては、リンクをつける場合、HtmlHelper::link() を参照、つけない場合、Html::image() を参照
 * @return string アイキャッチ画像のHTML
 */
	public function getEyeCatch($post, $options = array()) {
		$this->setContent($post['BlogPost']['blog_content_id']);
		$options = array_merge(array(
			'imgsize' => 'thumb',
			'link' => true, // 大きいサイズの画像へのリンク有無
			'escape' => false, // エスケープ
			'mobile' => false, // モバイル
			'alt' => '', // alt属性
			'width' => '', // 横幅
			'height' => '', // 高さ
			'noimage' => '', // 画像がなかった場合に表示する画像
			'tmp' => false,
			'class' => 'img-eye-catch',
			'output' => '', // 出力形式 tag or url
		), $options);

		return $this->BcUpload->uploadImage('BlogPost.eye_catch', $post['BlogPost']['eye_catch'], $options);
	}

/**
 * メールフォームプラグインのフォームへのリンクを生成する
 *
 * @param string $title リンクのタイトル
 * @param string $contentsName メールフォームのコンテンツ名
 * @param array $datas メールフォームに引き継ぐデータ（初期値 : array()）
 * @param array $options a タグの属性（初期値 : array()）
 *	※ オプションについては、HtmlHelper::link() を参照
 * @return void
 */
	public function mailFormLink($title, $contentsName, $datas = array(), $options = array()) {
		App::uses('MailHelper', 'Mail.View/Helper');
		$MailHelper = new MailHelper($this->_View);
		$MailHelper->link($title, $contentsName, $datas, $options);
	}

/**
 * 文字列から制御文字を取り除く
 */
	public function removeCtrlChars($string) {
		# fixes #10683
		return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $string);
	}
}
