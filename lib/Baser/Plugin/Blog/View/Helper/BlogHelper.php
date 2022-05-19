<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログヘルパー
 * @package Blog.View.Helper
 * @property BcTimeHelper $BcTime BcTimeヘルパ
 * @property BcBaserHelper $BcBaser BcBaserヘルパ
 * @property BcUploadHelper $BcUpload BcUploadヘルパ
 * @property BcContentsHelper $BcContents BcContentsヘルパ
 */
class BlogHelper extends AppHelper
{

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['Html', 'BcTime', 'BcBaser', 'BcUpload', 'BcContents'];

	/**
	 * ブログカテゴリモデル
	 *
	 * @var BlogCategory
	 */
	public $BlogCategory = null;

	/**
	 * コンテンツ
	 *
	 * @var array
	 */
	public $content = null;

	/**
	 * コンストラクタ
	 *
	 * @param View $View Viewオブジェクト
	 * @param array $settings 設定
	 * @return void
	 */
	public function __construct(View $View, $settings = [])
	{
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
	public function setContent($blogContentId = null)
	{
		$blogContentUpdated = false;
		$setedBlogContentId = null;
		if (isset($this->blogContent['id'])) {
			$setedBlogContentId = $this->blogContent['id'];
		}
		if ($blogContentId && ($blogContentId != $setedBlogContentId)) {
			$BlogContent = ClassRegistry::init('Blog.BlogContent');
			$BlogContent->unbindModel(['hasMany' => ['BlogPost', 'BlogCategory']]);
			$blogContent = $BlogContent->find('first', ['conditions' => ['BlogContent.id' => $blogContentId], 'recursive' => -1]);
			$this->blogContent = Hash::extract($blogContent, 'BlogContent');
			$blogContentUpdated = true;
		}

		if (empty($this->blogContent)) {
			if (!empty($this->request->query['preview']) && $this->request->query['preview'] == 'default' && $this->request->data['BlogContent']) {
				$this->blogContent = $this->request->data['BlogContent'];
				$blogContentUpdated = true;

			} elseif (isset($this->_View->viewVars['blogContent']['BlogContent'])) {
				$this->blogContent = $this->_View->viewVars['blogContent']['BlogContent'];
				if ($this->request->params['Content']['type'] === 'BlogContent') {
					$this->content = $this->request->params['Content'];
				} else {
					$content = $this->BcContents->getContentByEntityId($this->blogContent['id'], 'BlogContent');
					if (!empty($content['Content'])) {
						$this->content = $content['Content'];
					}
				}
			}
		}


		if ($this->blogContent) {
			if ($blogContentUpdated) {
				$siteId = 0;
				if(isset($this->request->params['Site']['id'])) {
					$siteId = $this->request->params['Site']['id'];
				}
				$Content = ClassRegistry::init('Content');
				// 現在のサイトにエイリアスが存在するのであればそちらを優先する
				$content = [];
				if(!empty($this->request->params['Site']['id'])) {
					$content = $Content->find('first', ['conditions' => [
						'Content.entity_id' => $this->blogContent['id'],
						'Content.type' => 'BlogContent',
						'alias_id <>' => null,
						'site_id' => $siteId
					], 'recursive' => -1]);
				}
				if (!$content) {
					$content = $Content->find('first', ['conditions' => [
						'Content.entity_id' => $this->blogContent['id'],
						'Content.type' => 'BlogContent',
						'alias_id' => null,
					], 'recursive' => -1]);
				}
				$this->content = Hash::extract($content, 'Content');
			}
			$BlogPost = ClassRegistry::init('Blog.BlogPost');
			$BlogPost->setupUpload($this->blogContent['id']);
		} else {
			$this->content = null;
		}
	}

	/**
	 * ブログIDを出力する
	 *
	 * @return void
	 */
	public function currentBlogId()
	{
		echo $this->getCurrentBlogId();
	}

	/**
	 * ブログIDを取得する
	 *
	 * @return integer
	 */
	public function getCurrentBlogId()
	{
		return $this->blogContent['id'];
	}

	/**
	 * ブログのコンテンツ名を出力する
	 *
	 * @return void
	 */
	public function blogName()
	{
		echo $this->getBlogName();
	}

	/**
	 * ブログのコンテンツ名を取得する
	 *
	 * @return string
	 */
	public function getBlogName()
	{
		return $this->request->params['Content']['name'];
	}

	/**
	 * ブログタイトルを出力する
	 *
	 * @return void
	 */
	public function title()
	{
		echo $this->getTitle();
	}

	/**
	 * タイトルを取得する
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->request->params['Content']['title'];
	}

	/**
	 * ブログの説明文を取得する
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->blogContent['description'];
	}

	/**
	 * ブログの説明文を出力する
	 *
	 * @return void
	 */
	public function description()
	{
		echo $this->getDescription();
	}

	/**
	 * ブログの説明文が指定されているかどうかを判定する
	 *
	 * @return boolean
	 */
	public function descriptionExists()
	{
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
	public function postTitle($post, $link = true, $options = [])
	{
		echo $this->getPostTitle($post, $link, $options);
	}

	/**
	 * 記事タイトルを取得する
	 *
	 * @param array $post ブログ記事データ
	 * @param boolean $link 詳細ページへのリンクをつける場合には、true を指定する（初期値 : true）
	 * @param array $options オプション（初期値：arary()）
	 *    - `escape` : エスケープ処理を行うかどうか
	 *    ※ その他のオプションについては、HtmlHelper::link() を参照
	 * @return string 記事タイトル
	 */
	public function getPostTitle($post, $link = true, $options = [])
	{
		$options = array_merge([
			'escape' => true
		], $options);
		$title = $post['BlogPost']['name'];
		if ($link) {
			$title = $this->getPostLink($post, $title, $options);
		} else {
			if (!empty($options['escape'])) {
				$title = h($title);
			}
		}
		return $title;
	}

	/**
	 * 記事へのリンクを取得する
	 *
	 * @param array $post ブログ記事データ
	 * @param string $title タイトル
	 * @param array $options オプション（初期値 : array()）
	 *    ※ オプションについては、 HtmlHelper::link() を参照
	 * @return string 記事へのリンク
	 */
	public function getPostLink($post, $title, $options = [])
	{
		$options = array_merge([
			'escape' => true
		], $options);

		$url = $this->getPostLinkUrl($post, false);

		// EVENT beforeGetPostLink
		$event = $this->dispatchEvent('beforeGetPostLink', [
			'post' => $post,
			'title' => $title,
			'options' => $options,
			'url' => $url,
		], ['class' => 'Blog', 'plugin' => 'Blog']);
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true)? $event->data['options'] : $event->result;
			$post = $event->data['post'];
			$title = $event->data['title'];
			$url = $event->data['url'];
		}

		$out = $this->BcBaser->getLink($title, $url, $options);

		// EVENT afterGetPostLink
		$event = $this->dispatchEvent('afterGetPostLink', [
			'post' => $post,
			'title' => $title,
			'out' => $out,
			'url' => $url,
		], ['class' => 'Blog', 'plugin' => 'Blog']);
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}
		return $out;
	}

	/**
	 * ブログ記事のURLを取得する
	 *
	 * @param array $post ブログ記事データ
	 * @param bool $base ベースとなるURLを付与するかどうか
	 * @return string ブログ記事のURL
	 */
	public function getPostLinkUrl($post, $base = true)
	{
		$this->setContent($post['BlogPost']['blog_content_id']);
		if (empty($this->content['url'])) {
			return false;
		}
		$site = BcSite::findByUrl($this->content['url']);
		$contentUrl = $this->BcBaser->getContentsUrl($this->content['url'], !$this->isSameSiteBlogContent($post['BlogPost']['blog_content_id']), !empty($site->useSubDomain), false);
		$url = $contentUrl . 'archives/' . $post['BlogPost']['no'];
		if ($base) {
			return $this->url($url);
		} else {
			return $url;
		}
	}

	/**
	 * 記事へのリンクを出力する
	 *
	 * @param array $post ブログ記事データ
	 * @param string $title タイトル
	 * @param array $options オプション（初期値 : array()）
	 *    ※ オプションについては、 HtmlHelper::link() を参照
	 * @return void
	 */
	public function postLink($post, $title, $options = [])
	{
		echo $this->getPostLink($post, $title, $options);
	}

	/**
	 * 記事の本文を表示する
	 *
	 * @param array $post ブログ記事データ
	 * @param boolean $moreText 詳細データを表示するかどうか（初期値 : true）
	 * @param mixed $moreLink 詳細ページへのリンクを表示するかどうか。true に指定した場合、
	 *    「≫ 続きを読む」という文字列がリンクとして表示される。（初期値 : false）
	 * また、文字列を指定するとその文字列がリンクとなる
	 * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
	 * @param mixed $lastText 本文後に文字列を挿入するかを真偽値で指定。挿入する場合、テキストを入力（初期値 : false）
	 * @return void
	 */
	public function postContent($post, $moreText = true, $moreLink = false, $cut = false, $lastText = false)
	{
		echo $this->getPostContent($post, $moreText, $moreLink, $cut, $lastText);
	}

	/**
	 * 記事の本文を取得する
	 *
	 * @param array $post ブログ記事データ
	 * @param boolean $moreText 詳細データを表示するかどうか（初期値 : true）
	 * @param mixed $moreLink 詳細ページへのリンクを表示するかどうか。true に指定した場合、
	 *    「≫ 続きを読む」という文字列がリンクとして表示される。（初期値 : false）
	 * また、文字列を指定するとその文字列がリンクとなる
	 * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
	 * @param mixed $lastText 本文後に文字列を挿入するかを真偽値で指定。挿入する場合、テキストを入力（初期値 : false）
	 * @return string 記事本文
	 */
	public function getPostContent($post, $moreText = true, $moreLink = false, $cut = false, $lastText = false)
	{
		if ($moreLink === true) {
			$moreLink = __d('baser', '≫ 続きを読む');
		}
		$out = '';
		if ($this->blogContent['use_content']) {
			$out .= '<div class="post-body">' . $post['BlogPost']['content'] . '</div>';
		}
		if ($moreText && $post['BlogPost']['detail']) {
			$out .= '<div id="post-detail">' . $post['BlogPost']['detail'] . '</div>';
		}
		if ($cut) {
			$out = str_replace(["\r\n", "\r", "\n"], '', $out);
			$out = html_entity_decode($out, ENT_QUOTES, 'UTF-8');
			if ($lastText && mb_strlen(strip_tags($out)) > $cut) {
				$out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8') . strip_tags($lastText);
			} else {
				$out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
			}
		}
		if ($moreLink && trim($post['BlogPost']['detail']) && trim($post['BlogPost']['detail']) != "<br>") {
			if (!isset($this->Html)) {
				App::uses('HtmlHelper', 'View/Helper');
				$this->Html = new HtmlHelper($this->_View);
			}
			$out .= '<p class="more">' . $this->Html->link($moreLink, $this->getContentsUrl($post['BlogPost']['blog_content_id'], false) . 'archives/' . $post['BlogPost']['no'] . '#post-detail', null, null) . '</p>';
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
	public function postDetail($post, $options = [])
	{
		echo $this->getPostDetail($post, $options);
	}

	/**
	 * 記事の詳細を取得する
	 *
	 * @param array $post ブログ記事データ
	 * @param array $options オプション（初期値 : array()）
	 *    - `cut` : 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力（初期値 : false）
	 * @return string 記事本文
	 */
	public function getPostDetail($post, $options = [])
	{
		$options = array_merge([
			'cut' => false
		], $options);
		$cut = $options['cut'];
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
	 *    - `link` : リンクをつけるかどうか（初期値 : true）
	 *    ※ その他のオプションは、`link`オプションが`true`の場合に
	 *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
	 * @return void
	 */
	public function category($post, $options = [])
	{
		echo $this->getCategory($post, $options);
	}

	/**
	 * 記事が属するカテゴリ名を取得する
	 *
	 * @param array $post 記事データ
	 * @param array $options オプション（初期値 : array()）
	 *    - `link` : リンクをつけるかどうか（初期値 : true）
	 *    ※ その他のオプションは、`link`オプションが`true`の場合に
	 *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
	 * @return string カテゴリ名
	 */
	public function getCategory($post, $options = [])
	{
		if (!empty($post['BlogCategory']['name'])) {

			$options = am(['link' => true], $options);
			$link = false;

			if ($options['link']) {
				$link = true;
			}

			unset($options['link']);

			if ($link) {
				if (!isset($this->Html)) {
					App::uses('HtmlHelper', 'View/Helper');
					$this->Html = new HtmlHelper($this->_View);
				}
				$options['base'] = false;
				return $this->Html->link($post['BlogCategory']['title'], $this->getCategoryUrl($post['BlogCategory']['id'], $options), $options, null);
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
	public function tag($post, $separator = ' , ')
	{
		echo $this->getTag($post, $separator);
	}

	/**
	 * タグを取得する
	 *
	 * 複数所属する場合は複数取得する
	 *
	 * @param array $post 記事データ
	 * @param string $options
	 *    - `separator` : 区切り文字（初期値 :  , ）
	 *    - `tag` : リンク付きのタグで出力するかどうか（初期値 : true）
	 *        ※ link に統合予定
	 *    - `link` : リンク付きのタグで出力するかどうか（初期値 : true）
	 *    ※ 文字列で指定した場合は、separator として扱う
	 * @return mixed ''|string|array
	 */
	public function getTag($post, $options = [])
	{
		if ($options && is_string($options)) {
			$separator = $options;
			$options = [];
			$options['separator'] = $separator;
		}
		$options = array_merge([
			'separator' => ' , ',
			'tag' => true,
			'crossing' => false,
			'link' => true
		], $options);
		$tags = [];
		if ($options['crossing']) {
			$crossingId = null;
		} else {
			$crossingId = $this->blogContent['id'];
		}
		if ($options['tag'] === false) {
			$options['link'] = false;
		}
		if (!empty($post['BlogTag'])) {
			foreach($post['BlogTag'] as $tag) {
				if ($options['link']) {
					$tags[] = $this->BcBaser->getLink($tag['name'], $this->getTagLinkUrl($crossingId, $tag, false), ['escape' => true]);
				} else {
					$tags[] = [
						'name' => $tag['name'],
						'url' => $this->getTagLinkUrl($crossingId, $tag)
					];
				}
			}
		}
		if ($tags) {
			if ($options['link']) {
				return implode($options['separator'], $tags);
			} else {
				return $tags;
			}
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
	 *    `named` : URLの名前付きパラメーター
	 * @return string カテゴリ一覧へのURL
	 */
	public function getCategoryUrl($blogCategoryId, $options = [])
	{
		$options = array_merge([
			'named' => [],
			'base' => true
		], $options);
		if (!isset($this->BlogCategory)) {
			$this->BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		}
		$categoryPath = $this->BlogCategory->getPath($blogCategoryId);
		$blogContentId = $categoryPath[0]['BlogCategory']['blog_content_id'];
		$this->setContent($blogContentId);
		$site = BcSite::findByUrl($this->content['url']);
		$contentUrl = $this->BcBaser->getContentsUrl($this->content['url'], !$this->isSameSiteBlogContent($blogContentId), !empty($site->useSubDomain), false);
		$path = ['category'];
		if ($categoryPath) {
			foreach($categoryPath as $category) {
				$path[] = urldecode($category['BlogCategory']['name']);
			}
		}
		$url = $contentUrl . 'archives/' . implode('/', $path);
		if ($options['named']) {
			foreach($options['named'] as $key => $value) {
				$url .= sprintf('/%s:%s', $key, $value);
			}
		}
		if ($options['base']) {
			return $this->url($url);
		} else {
			return $url;
		}
	}

	/**
	 * 記事の登録日を出力する
	 *
	 * @param array $post ブログ記事
	 * @param string $format 日付フォーマット（初期値 : Y/m/d）
	 * @return void
	 */
	public function postDate($post, $format = 'Y/m/d')
	{
		echo $this->getPostDate($post, $format);
	}

	/**
	 * 登録日
	 *
	 * @param array $post ブログ記事
	 * @param string $format 日付フォーマット（初期値 : Y/m/d）
	 * @return string 登録日
	 */
	public function getPostDate($post, $format = 'Y/m/d')
	{
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
	public function author($post)
	{
		echo h($this->BcBaser->getUserName($post['User']));
	}

	/**
	 * カテゴリーの一覧をリストタグで取得する
	 *
	 * @param array $categories カテゴリ一覧データ
	 * @param int $depth 階層（初期値 : 3）
	 * @param boolean $count 件数を表示するかどうか（初期値 : false）
	 * @param array $options オプション（初期値 : array()）
	 *    - `link` : リンクをつけるかどうか（初期値 : true）
	 *    ※ その他のオプションは、`link`オプションが`true`の場合に
	 *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
	 * @return string HTMLのカテゴリ一覧
	 */
	public function getCategoryList($categories, $depth = 3, $count = false, $options = [])
	{
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
	 *    - `link` : リンクをつけるかどうか（初期値 : true）
	 *    ※ その他のオプションは、`link`オプションが`true`の場合に
	 *    生成されるa要素の属性設定となる。（HtmlHelper::link() を参照）
	 * @return string HTMLのカテゴリ一覧
	 */
	protected function _getCategoryList($categories, $depth = 3, $current = 1, $count = false, $options = [])
	{
		if ($depth < $current) {
			return '';
		}

		if ($categories) {
			$out = '<ul class="bc-blog-category-list depth-' . $current . '">';
			$current++;
			foreach($categories as $category) {
				if ($count && isset($category['BlogCategory']['count'])) {
					$category['BlogCategory']['title'] .= '(' . $category['BlogCategory']['count'] . ')';
				}
				$url = $this->getCategoryUrl($category['BlogCategory']['id'], ['base' => false]);
				$url = preg_replace('/^\//', '', $url);
				$class = ['bc-blog-category-list__item'];
				if ($this->_View->request->url == $url) {
					$class[] = 'current';
				} elseif (!empty($this->_View->params['named']['category']) && $this->_View->params['named']['category'] == $category['BlogCategory']['name']) {
					$class[] = 'selected';
				}
				$out .= '<li class="' . implode(' ', $class) . '">' . $this->getCategory($category, $options);
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
	 * 前の記事へのリンクを出力する
	 *
	 * @param array $post ブログ記事
	 * @param string $title タイトル
	 * @param array $htmlAttributes HTML属性
	 *    ※ HTML属性は、HtmlHelper::link() 参照
	 * @return void
	 */
	public function prevLink($post, $title = '', $htmlAttributes = [])
	{
		$prevPost = $this->getPrevPost($post);
		$_htmlAttributes = ['class' => 'prev-link', 'arrow' => '≪ '];
		$htmlAttributes = am($_htmlAttributes, $htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		if ($prevPost) {
			if (!$title) {
				$title = $arrow . $prevPost['BlogPost']['name'];
			}
			echo $this->getPostLink($prevPost, $title, $htmlAttributes);
		}
	}

	/**
	 * 前の記事へのリンクがあるかチェックする
	 *
	 * @param array $post ブログ記事
	 * @return bool
	 */
	public function hasPrevLink($post)
	{
		$prevPost = $this->getPrevPost($post);
		if ($prevPost) {
			return true;
		}
		return false;
	}

	/**
	 * 次の記事へのリンクを出力する
	 *
	 * @param array $post ブログ記事
	 * @param string $title タイトル
	 * @param array $htmlAttributes HTML属性
	 *    ※ HTML属性は、HtmlHelper::link() 参照
	 * @return void
	 */
	public function nextLink($post, $title = '', $htmlAttributes = [])
	{
		$nextPost = $this->getNextPost($post);
		$_htmlAttributes = ['class' => 'next-link', 'arrow' => ' ≫'];
		$htmlAttributes = am($_htmlAttributes, $htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		if ($nextPost) {
			if (!$title) {
				$title = $nextPost['BlogPost']['name'] . $arrow;
			}
			echo $this->getPostLink($nextPost, $title, $htmlAttributes);
		}
	}

	/**
	 * 次の記事へのリンクが存在するかチェックする
	 *
	 * @param array $post ブログ記事
	 * @return bool
	 */
	public function hasNextLink($post)
	{
		$nextPost = $this->getNextPost($post);
		if ($nextPost) {
			return true;
		}
		return false;
	}

	/**
	 * ブログテンプレートを取得
	 *
	 * コンボボックスのソースとして利用
	 *
	 * @return array ブログテンプレート一覧
	 * @todo 別のヘルパに移動
	 */
	public function getBlogTemplates($siteId = 0)
	{
		$site = BcSite::findById($siteId);
		$theme = $this->BcBaser->siteConfig['theme'];
		if ($site->theme) {
			$theme = $site->theme;
		}
		$templatesPathes = array_merge(App::path('View', 'Blog'), App::path('View'));
		if ($theme) {
			array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $theme . DS);
		}

		$_templates = [];
		foreach($templatesPathes as $templatePath) {
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
		$excludes = array_keys($excludes);

		$excludes[] = 'rss';
		$templates = [];
		foreach($_templates as $template) {
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
	public function allowPublish($data)
	{
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
	 *    - `num` : 何枚目の画像か順番を指定（初期値 : 1）
	 *    - `link` : 詳細ページへのリンクをつけるかどうか（初期値 : true）
	 *    - `alt` : ALT属性（初期値 : ブログ記事のタイトル）
	 * @return void
	 */
	public function postImg($post, $options = [])
	{
		echo $this->getPostImg($post, $options);
	}

	/**
	 * 記事中の画像を取得する
	 *
	 * @param array $post ブログ記事
	 * @param array $options オプション（初期値 : array()）
	 *    - `num` : 何枚目の画像か順番を指定（初期値 : 1）
	 *    - `link` : 詳細ページへのリンクをつけるかどうか（初期値 : true）
	 *    - `alt` : ALT属性（初期値 : ブログ記事のタイトル）
	 *    - `output` : 出力形式 tag, url のを指定できる（初期値 : ''）
	 * @return string
	 */
	public function getPostImg($post, $options = [])
	{
		$this->setContent($post['BlogPost']['blog_content_id']);
		$options = array_merge($_options = [
			'num' => 1,
			'link' => true,
			'alt' => $post['BlogPost']['name'],
			'output' => '', // 出力形式 tag or url
		], $options);
		$num = $options['num'];
		$link = $options['link'];
		$output = $options['output'];
		unset($options['num']);
		unset($options['link']);
		unset($options['output']);

		$contents = $post['BlogPost']['content'] . $post['BlogPost']['detail'];
		$pattern = '/<img.*?src="([^"]+)"[^>]*>/is';
		if (!preg_match_all($pattern, $contents, $matches)) {
			return '';
		}

		if (isset($matches[1][$num - 1])) {
			$url = $matches[1][$num - 1];
			$url = preg_replace('/^' . preg_quote($this->base, '/') . '/', '', $url);
			if ($output == 'url') {
				return $url; // 出力形式 が urlなら、URLを返す
			}
			$img = $this->BcBaser->getImg($url, $options);
			if ($link) {
				return $this->BcBaser->getLink($img, $this->request->params['Content']['url'] . 'archives/' . $post['BlogPost']['no']);
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
	public function getHtmlById($post, $id)
	{
		$content = $post['BlogPost']['content'] . $post['BlogPost']['detail'];
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
	public function getParentCategory($post)
	{
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
	 *    - `recursive` : 関連データを取得する場合の階層（初期値 : -1）
	 *    - `limit` : 件数（初期値 : 5）
	 *    - `order` : 並び順指定（初期値 : BlogPost.posts_date DESC）
	 * @return array
	 */
	public function getRelatedPosts($post, $options = [])
	{
		if (empty($post['BlogTag'])) {
			return [];
		}

		$options = array_merge([
			'recursive' => -1,
			'limit' => 5,
			'order' => 'BlogPost.posts_date DESC'
		], $options);

		$tagNames = [];
		foreach($post['BlogTag'] as $tag) {
			$tagNames[] = urldecode($tag['name']);
		}
		$BlogTag = ClassRegistry::init('Blog.BlogTag');
		$tags = $BlogTag->find('all', [
			'conditions' => ['BlogTag.name' => $tagNames],
			'recursive' => 1
		]);

		if (!isset($tags[0]['BlogPost'][0]['id'])) {
			return [];
		}

		$ids = array_unique(Hash::extract($tags, '{n}.BlogPost.{n}.id'));

		$BlogPost = ClassRegistry::init('Blog.BlogPost');

		$conditions = [
			['BlogPost.id' => $ids],
			['BlogPost.id <>' => $post['BlogPost']['id']],
			'BlogPost.blog_content_id' => $post['BlogPost']['blog_content_id']
		];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());

		$relatedPosts = $BlogPost->find('all', [
			'conditions' => $conditions,
			'recursive' => $options['recursive'],
			'order' => $options['order'],
			'limit' => $options['limit'],
		]);

		return $relatedPosts;
	}

	/**
	 * ブログのアーカイブタイプを取得する
	 *
	 * @return string ブログのアーカイブタイプ
	 */
	public function getBlogArchiveType()
	{
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
	public function isArchive()
	{
		return ($this->getBlogArchiveType());
	}

	/**
	 * カテゴリー別記事一覧ページ判定
	 *
	 * @return boolean 現在のページがカテゴリー別記事一覧ページの場合は true を返す
	 */
	public function isCategory()
	{
		return ($this->getBlogArchiveType() == 'category');
	}

	/**
	 * タグ別記事一覧ページ判定
	 *
	 * @return boolean 現在のページがタグ別記事一覧ページの場合は true を返す
	 */
	public function isTag()
	{
		return ($this->getBlogArchiveType() == 'tag');
	}

	/**
	 * 日別記事一覧ページ判定
	 *
	 * @return boolean 現在のページが日別記事一覧ページの場合は true を返す
	 */
	public function isDate()
	{
		return ($this->getBlogArchiveType() == 'daily');
	}

	/**
	 * 月別記事一覧ページ判定
	 *
	 * @return boolean 現在のページが月別記事一覧ページの場合は true を返す
	 */
	public function isMonth()
	{
		return ($this->getBlogArchiveType() == 'monthly');
	}

	/**
	 * 年別記事一覧ページ判定
	 *
	 * @return boolean 現在のページが年別記事一覧ページの場合は true を返す
	 */
	public function isYear()
	{
		return ($this->getBlogArchiveType() == 'yearly');
	}

	/**
	 * 個別ページ判定
	 *
	 * @return boolean 現在のページが個別ページの場合は true を返す
	 */
	public function isSingle()
	{
		if (empty($this->request->params['plugin'])) {
			return false;
		}
		return (
			$this->request->params['plugin'] == 'blog' &&
			$this->request->params['controller'] == 'blog' &&
			$this->request->params['action'] == 'archives' &&
			!$this->getBlogArchiveType()
		);
	}

	/**
	 * インデックスページ判定
	 *
	 * @return boolean 現在のページがインデックスページの場合は true を返す
	 */
	public function isHome()
	{
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
	 *    - `imgsize` : 画像サイズ[default|thumb|mobile_thumb]（初期値 : thumb）
	 *  - `link` : 大きいサイズの画像へのリンク有無（初期値 : true）
	 *  - `escape` : タイトルについてエスケープする場合に true を指定（初期値 : false）
	 *    - `mobile` : モバイルの画像を表示する場合に true を指定（初期値 : false）
	 *    - `alt` : alt属性（初期値 : ''）
	 *    - `width` : 横幅（初期値 : ''）
	 *    - `height` : 高さ（初期値 : ''）
	 *    - `noimage` : 画像が存在しない場合に表示する画像（初期値 : ''）
	 *    - `tmp` : 一時保存データの場合に true を指定（初期値 : false）
	 *    - `class` : タグの class を指定（初期値 : img-eye-catch）
	 *    - `force` : 画像が存在しない場合でも強制的に出力する場合に true を指定する（初期値 : false）
	 *  ※ その他のオプションについては、リンクをつける場合、HtmlHelper::link() を参照、つけない場合、Html::image() を参照
	 * @return void
	 */
	public function eyeCatch($post, $options = [])
	{
		echo $this->getEyeCatch($post, $options);
	}

	/**
	 * アイキャッチ画像を取得する
	 *
	 * @param array $post ブログ記事
	 * @param array $options オプション（初期値 : array()）
	 *    - `imgsize` : 画像サイズ[default|thumb|mobile_thumb]（初期値 : thumb）
	 *  - `link` : 大きいサイズの画像へのリンク有無（初期値 : true）
	 *  - `escape` : タイトルについてエスケープする場合に true を指定（初期値 : false）
	 *    - `mobile` : モバイルの画像を表示する場合に true を指定（初期値 : false）
	 *    - `alt` : alt属性（初期値 : ''）
	 *    - `width` : 横幅（初期値 : ''）
	 *    - `height` : 高さ（初期値 : ''）
	 *    - `noimage` : 画像が存在しない場合に表示する画像（初期値 : ''）
	 *    - `tmp` : 一時保存データの場合に true を指定（初期値 : false）
	 *    - `class` : タグの class を指定（初期値 : img-eye-catch）
	 *    - `force` : 画像が存在しない場合でも強制的に出力する場合に true を指定する（初期値 : false）
	 *    - `output` : 出力形式 tag, url のを指定できる（初期値 : ''）
	 *  ※ その他のオプションについては、リンクをつける場合、HtmlHelper::link() を参照、つけない場合、Html::image() を参照
	 * @return string アイキャッチ画像のHTML
	 */
	public function getEyeCatch($post, $options = [])
	{
		$this->setContent($post['BlogPost']['blog_content_id']);
		$options = array_merge([
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
		], $options);
		$eyecatch = null;
		if (!empty($post['BlogPost']['eye_catch'])) {
			$eyecatch = $post['BlogPost']['eye_catch'];
		}
		return $this->BcUpload->uploadImage('BlogPost.eye_catch', $eyecatch, $options);
	}

	/**
	 * メールフォームプラグインのフォームへのリンクを生成する
	 *
	 * @param string $title リンクのタイトル
	 * @param string $contentsName メールフォームのコンテンツ名
	 * @param array $datas メールフォームに引き継ぐデータ（初期値 : array()）
	 * @param array $options a タグの属性（初期値 : array()）
	 *    ※ オプションについては、HtmlHelper::link() を参照
	 * @return void
	 */
	public function mailFormLink($title, $contentsName, $datas = [], $options = [])
	{
		App::uses('MailHelper', 'Mail.View/Helper');
		$MailHelper = new MailHelper($this->_View);
		$MailHelper->link($title, $contentsName, $datas, $options);
	}

	/**
	 * 文字列から制御文字を取り除く
	 */
	public function removeCtrlChars($string)
	{
		# fixes #10683
		return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $string);
	}

	/**
	 * 次の記事を取得する
	 *
	 * @param array $post ブログ記事
	 * @return array
	 */
	public function getNextPost($post)
	{
		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		// 投稿日が年月日時分秒が同一のデータの対応の為、投稿日が同じでIDが小さいデータを検索
		$conditions = [];
		$conditions['BlogPost.id >'] = $post['BlogPost']['id'];
		$conditions['BlogPost.posts_date'] = $post['BlogPost']['posts_date'];
		$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		$order = 'BlogPost.posts_date, BlogPost.id';
		$nextPost = $BlogPost->find('first', [
			'conditions' => $conditions,
			'order' => $order,
			'recursive' => 0,
		]);

		if (empty($nextPost)) {
			// 投稿日が新しいデータを取得
			$conditions = [];
			$conditions['BlogPost.posts_date >'] = $post['BlogPost']['posts_date'];
			$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
			$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
			$nextPost = $BlogPost->find('first', [
				'conditions' => $conditions,
				'order' => $order,
				'recursive' => 0,
			]);
		}
		return $nextPost;
	}

	/**
	 * 前の記事を取得する
	 *
	 * @param array $post ブログ記事
	 * @return array
	 */
	public function getPrevPost($post)
	{
		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		// 投稿日が年月日時分秒が同一のデータの対応の為、投稿日が同じでIDが大きいデータを検索
		$conditions = [];
		$conditions['BlogPost.id <'] = $post['BlogPost']['id'];
		$conditions['BlogPost.posts_date'] = $post['BlogPost']['posts_date'];
		$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		$order = 'BlogPost.posts_date DESC, BlogPost.id DESC';
		$prevPost = $BlogPost->find('first', [
			'conditions' => $conditions,
			'order' => $order,
			'recursive' => 0,
		]);
		if (empty($prevPost)) {
			// 投稿日が古いデータを取得
			$conditions = [];
			$conditions['BlogPost.posts_date <'] = $post['BlogPost']['posts_date'];
			$conditions['BlogPost.blog_content_id'] = $post['BlogPost']['blog_content_id'];
			$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
			$prevPost = $BlogPost->find('first', [
				'conditions' => $conditions,
				'order' => $order,
				'recursive' => 0,
			]);
		}
		return $prevPost;
	}

	/**
	 * 記事が属するカテゴリ名を取得
	 *
	 * @param array $post
	 * @return string
	 */
	public function getCategoryName($post)
	{
		if (empty($post['BlogCategory']['name'])) {
			return '';
		} else {
			return $post['BlogCategory']['name'];
		}
	}

	/**
	 * 記事が属するカテゴリタイトルを取得
	 *
	 * @param array $post
	 * @return string
	 */
	public function getCategoryTitle($post)
	{
		if (empty($post['BlogCategory']['title'])) {
			return '';
		} else {
			return $post['BlogCategory']['title'];
		}
	}

	/**
	 * 記事のIDを取得
	 *
	 * @param array $post
	 * @return string
	 */
	public function getPostId($post)
	{
		if (empty($post['BlogPost']['id'])) {
			return '';
		} else {
			return $post['BlogPost']['id'];
		}
	}

	/**
	 * カテゴリを取得する
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function getCategories($options = [])
	{
		$options = array_merge([
			'blogContentId' => null
		], $options);
		$blogContentId = $options['blogContentId'];
		unset($options['blogContentId']);
		/* @var BlogCategory $BlogCategory */
		$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		return $BlogCategory->getCategoryList($blogContentId, $options);
	}

	/**
	 * 子カテゴリを持っているかどうか
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function hasChildCategory($id)
	{
		$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		return $BlogCategory->hasChild($id);
	}

	/**
	 * ブログタグリストを取得する
	 *
	 * @param mixed $name
	 * @param array $options
	 *    - `conditions` : CakePHP形式の検索条件
	 *  - `direction` : 並び順の方向
	 *  - `sort` : 並び順の対象フィールド
	 *  - `siteId` : サイトIDでフィルタリングする場合に指定する
	 *  - `postCount` : 記事件数を表示するかどうか
	 * @return array|null
	 */
	public function getTagList($name, $options = [])
	{
		$options = array_merge([
			'conditions' => [],
			'direction' => 'ASC',
			'sort' => 'name',
			'siteId' => null,
			'postCount' => false
		], $options);
		if ($name && !is_array($name)) {
			$name = [$name];
		}
		$options['contentId'] = $options['contentUrl'] = [];
		if ($name) {
			foreach($name as $value) {
				if (is_int($value)) {
					$options['contentId'][] = $value;
				} else {
					$options['contentUrl'][] = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $value) . '/';
				}
			}
		}
		/** @var \BlogTag $BlogTag */
		$BlogTag = ClassRegistry::init('Blog.BlogTag');
		$tags = $BlogTag->find('customParams', $options);
		// 公開記事数のカウントを追加
		if ($options['postCount']) {
			$tags = $this->_mergePostCountToTagsData($tags, $options);
		}
		return $tags;
	}

	/**
	 * タグリストを出力する
	 *
	 * @param mixed $name
	 * @param array $options
	 *    ※ オプションのパラーメーターは、BlogHelper::getTagList() に準ずる
	 */
	public function tagList($name, $options = [])
	{
		$options = array_merge([
			'postCount' => false
		], $options);
		$tags = $this->getTagList($name, $options);
		if ($name && !is_array($name)) {
			$name = [$name];
		}
		$blogContentId = null;
		if (!empty($name[0])) {
			if (is_int($name[0])) {
				$blogContentId = $name[0];
			} else {
				/** @var \Content $Content */
				$Content = ClassRegistry::init('Content');
				$url = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $name[0]) . '/';
				$blogContentId = $Content->field('entity_id', ['Content.url' => $url]);
			}
		}
		$this->BcBaser->element('Blog.blog_tag_list', [
			'tags' => $tags,
			'blogContentId' => $blogContentId,
			'postCount' => $options['postCount']
		]);
	}

	/**
	 * タグ一覧へのURLを取得する
	 *
	 * @param int $blogContentId
	 * @param array $tag
	 * @param bool $base
	 * @return string
	 */
	public function getTagLinkUrl($blogContentId, $tag, $base = true)
	{
		$url = null;
		if (isset($tag['BlogTag'])) {
			$tag = $tag['BlogTag'];
		}
		if ($blogContentId) {
			$this->setContent($blogContentId);
			if (!empty($this->content['url'])) {
				$site = BcSite::findByUrl($this->content['url']);
				$url = $this->BcBaser->getContentsUrl($this->content['url'], !$this->isSameSiteBlogContent($blogContentId), !empty($site->useSubDomain), false);
				$url = $url . 'archives/tag/' . $tag['name'];
			}
		}
		if (!$url) {
			$url = '/tags/' . $tag['name'];
			$site = BcSite::findCurrent(true);
			if ($site && $site->alias && !$site->useSubDomain) {
				$url = '/' . $site->alias . $url;
			}
		}
		if ($base) {
			return $this->url($url);
		} else {
			return $url;
		}
	}

	/**
	 * タグ一覧へのリンクタグを取得する
	 *
	 * @param int $blogContentId
	 * @param array $tag
	 * @param array $options
	 * @return string
	 */
	public function getTagLink($blogContentId, $tag, $options = [])
	{
		$url = $this->getTagLinkUrl($blogContentId, $tag, false);
		return $this->BcBaser->getLink($tag['BlogTag']['name'], $url, $options);
	}

	/**
	 * タグ一覧へのリンクタグを出力する
	 *
	 * @param int $blogContentId
	 * @param array $tag
	 * @param array $options
	 */
	public function tagLink($blogContentId, $tag, $options = [])
	{
		echo $this->getTagLink($blogContentId, $tag, $options);
	}

	/**
	 * ブログタグリストに公開記事数を追加する
	 *
	 * @param array $tags BlogTagの基本情報の配列
	 * @return array
	 */
	private function _mergePostCountToTagsData(array $tags, $options)
	{

		/** @var BlogPost $BlogPost */
		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		$blogTagIds = Hash::extract($tags, "{n}.BlogTag.id");
		$conditions = array_merge(
			['BlogTag.id' => $blogTagIds],
			$BlogPost->getConditionAllowPublish()
		);
		if (!empty($options['contentId'])) {
			$blogContentIds = $options['contentId'];
		}
		if (!empty($options['contentUrl'])) {
			/** @var BlogContent $BlogContent */
			$BlogContent = ClassRegistry::init('Blog.BlogContent');
			$blogContent = $BlogContent->find('all', [
				'fields' => ['BlogContent.id'],
				'conditions' => array_merge(
					$BlogContent->Content->getConditionAllowPublish(),
					['Content.url' => $options['contentUrl']]
				),
				'recursive' => 0,
			]);
			$blogContentIds = Hash::extract($blogContent, "{n}.BlogContent.id");
		}
		if (!empty($blogContentIds)) {
			$conditions[] = ['BlogPost.blog_content_id' => $blogContentIds];
		}

		$postCountsData = $BlogPost->find('all', [
			'fields' => [
				'BlogTag.id',
				'COUNT(BlogPost.id) as post_count',
			],
			'conditions' => $conditions,
			'group' => ['BlogTag.id'],
			'recursive' => -1,
			'joins' => [
				[
					'type' => 'INNER',
					'table' => 'blog_posts_blog_tags',
					'alias' => 'BlogPostsBlogTag',
					'conditions' => "BlogPostsBlogTag.blog_post_id=BlogPost.id"
				],
				[
					'type' => 'INNER',
					'table' => 'blog_tags',
					'alias' => 'BlogTag',
					'conditions' => "BlogPostsBlogTag.blog_tag_id=BlogTag.id"
				],
			]]);

		if (empty($postCountsData)) {
			foreach($tags as $tag) {
				$tag['BlogTag']['post_count'] = 0;
			}
			return $tags;
		}

		foreach($tags as $index => $tag) {
			$blogTagId = $tag['BlogTag']['id'];
			$countData = array_values(array_filter($postCountsData, function(array $data) use ($blogTagId) {
				return $data['BlogTag']['id'] == $blogTagId;
			}));
			if (empty($countData)) {
				$tags[$index]['BlogTag']['post_count'] = 0;
				continue;
			}
			$tags[$index]['BlogTag']['post_count'] = intval($countData[0][0]['post_count']);
		}
		return $tags;
	}

	/**
	 * ブログ記事一覧出力
	 *
	 * ページ編集画面等で利用する事ができる。
	 * ビュー: lib/Baser/Plugin/Blog/View/blog/{コンテンツテンプレート名}/posts.php
	 *
	 * 《利用例》
	 * $this->BcBaser->blogPosts('news', 3)
	 *
	 * 複数のコンテンツを指定する場合：配列にて複数のコンテンツ名を指定
	 *                                    コンテンツテンプレート名は配列の先頭を利用する
	 * $this->BcBaser->blogPosts(array('news', 'work'), 3)
	 *
	 * 全てのコンテンツを指定する場合：nullを指定
	 *                                    contentsTemplateオプションにて
	 *                                    コンテンツテンプレート名を指定する（必須）
	 * $this->BcBaser->blogPosts(null, 3, array('contentsTemplate' => 'news'))
	 *
	 * @param string | array $contentsName 管理システムで指定したコンテンツ名（初期値 : null）２階層目以降はURLで指定
	 * @param int $num 記事件数（初期値 : 5）
	 * @param array $options オプション（初期値 : array()）
	 *    - `conditions` : CakePHP形式の検索条件（初期値 : array()）
	 *    - `category` : カテゴリで絞り込む（初期値 : null）
	 *    - `tag` : タグで絞り込む（初期値 : null）
	 *    - `year` : 年で絞り込む（初期値 : null）
	 *    - `month` : 月で絞り込む（初期値 : null）
	 *    - `day` : 日で絞り込む（初期値 : null）
	 *    - `id` : 記事NO で絞り込む（初期値 : null）※ 後方互換の為 id を維持
	 *    - `no` : 記事NO で絞り込む（初期値 : null）
	 *    - `keyword` : キーワードで絞り込む場合にキーワードを指定（初期値 : null）
	 *  - `postId` : 記事ID で絞り込む（初期値 : null）
	 *  - `siteId` : サイトID で絞り込む（初期値 : null）
	 *  - `preview` : 非公開の記事も見る場合に指定（初期値 : false）
	 *    - `contentsTemplate` : コンテンツテンプレート名を指定（初期値 : null）
	 *    - `template` : 読み込むテンプレート名を指定する場合にテンプレート名を指定（初期値 : null）
	 *    - `direction` : 並び順の方向を指定 [昇順:ASC or 降順:DESC or ランダム:RANDOM]（初期値 : null）
	 *    - `page` : ページ数を指定（初期値 : null）
	 *    - `sort` : 並び替えの基準となるフィールドを指定（初期値 : null）
	 *    - `autoSetCurrentBlog` : $contentsName を指定していない場合、現在のコンテンツより自動でブログを指定する（初期値：true）
	 *    - `data` : エレメントに渡したい変数（初期値 : array）
	 * @return void
	 */
	public function posts($contentsName = [], $num = 5, $options = [])
	{
		/** @var BlogContent $BlogContent */
		$this->_View->loadHelper('Blog.Blog');
		$options = array_merge([
			'conditions' => [],
			'category' => null,
			'tag' => null,
			'year' => null,
			'month' => null,
			'day' => null,
			'id' => null,
			'no' => null,
			'keyword' => null,
			'author' => null,
			'postId' => null,
			'siteId' => null,
			'preview' => false,
			'contentsTemplate' => null,
			'template' => 'posts',
			'direction' => 'DESC',
			'page' => 1,
			'sort' => 'posts_date',
			'autoSetCurrentBlog' => true,
			'data' => [],
		], $options);

		if (!$contentsName && empty($options['contentsTemplate'])) {
			trigger_error(__d('baser', '$contentsName を省略時は、contentsTemplate オプションで、コンテンツテンプレート名を指定してください。'), E_USER_WARNING);
			return;
		}

		$contentsTemplate = $options['contentsTemplate'];
		$template = $options['template'];
		unset($options['contentsTemplate'], $options['template']);

		$blogPosts = $this->getPosts($contentsName, $num, $options);

		// テンプレートの決定
		$options = $this->parseContentName($contentsName, $options);
		if (!$contentsTemplate) {
			$BlogContent = ClassRegistry::init('Blog.BlogContent');
			$conditions['Content.url'] = $options['contentUrl'];
			$conditions = array_merge($conditions, $BlogContent->Content->getConditionAllowPublish());
			$blogContent = $BlogContent->find('first', [
				'fields' => ['BlogContent.template'],
				'conditions' => $conditions,
				'recursive' => 0,
			]);
			if ($blogContent) {
				$contentsTemplate = $blogContent['BlogContent']['template'];
			} else {
				$contentsTemplate = 'default';
			}
		}
		$template = 'Blog...' . DS . 'Blog' . DS . $contentsTemplate . DS . $template;
		$params = [];
		if (!empty($this->request->params['Site']['device'])) {
			$this->_View->subDir = $this->request->params['Site']['device'];
		}
		if (is_array($options['data'])) {
			$data = array_merge(['posts' => $blogPosts], $options['data']);
		} else {
			$data = ['posts' => $blogPosts];
		}
		$this->BcBaser->element($template, $data, $params);
	}

	/**
	 * ブログ記事を取得する
	 *
	 * @param array $contentsName
	 * @param int $num
	 * @param array $options
	 *    ※ パラメーターは、contentTemplate / template 以外、BlogBaserHelper::blogPosts() に準ずる
	 * @return mixed
	 */
	public function getPosts($contentsName = [], $num = 5, $options = [])
	{
		/** @var BlogContent $BlogContent */
		$this->_View->loadHelper('Blog.Blog');
		$options = array_merge([
			'conditions' => [],
			'category' => null,
			'tag' => null,
			'year' => null,
			'month' => null,
			'day' => null,
			'id' => null,
			'no' => null,
			'keyword' => null,
			'author' => null,
			'postId' => null,
			'siteId' => null,
			'preview' => false,
			'direction' => 'DESC',
			'page' => 1,
			'sort' => 'posts_date',
			'autoSetCurrentBlog' => true
		], $options);

		$options = $this->parseContentName($contentsName, $options);
		$options['num'] = $num;
		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		return $BlogPost->find('customParams', $options);
	}

	/**
	 * コンテンツ名を解析して検索条件を設定する
	 *
	 * @param mixed $contentsName
	 * @param array $options
	 * @return mixed
	 */
	public function parseContentName($contentsName, $options)
	{
		if ($contentsName && !is_array($contentsName)) {
			$contentsName = [$contentsName];
		}
		// 対象ブログを指定する条件を設定
		$options['contentUrl'] = $options['contentId'] = [];
		if ($contentsName) {
			foreach($contentsName as $value) {
				if (is_int($value)) {
					$options['contentId'][] = $value;
				} else {
					$options['contentUrl'][] = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $value) . '/';
				}
			}
		}
		if ($options['autoSetCurrentBlog'] && empty($options['contentUrl']) && empty($options['contentId'])) {
			if ($this->isBlog() && !empty($this->request->params['Content']['entity_id'])) {
				$options['contentId'] = $this->request->params['Content']['entity_id'];
			}
			if ($this->isBlog() && !empty($this->request->params['Content']['url'])) {
				$options['contentUrl'] = $this->request->params['Content']['url'];
			}
		}
		return $options;
	}

	/**
	 * Blogの基本情報を全て取得する
	 *
	 * @param string $name ブログのコンテンツ名を指定するとそのブログのみの基本情報を返す。空指定(default)で、全てのブログの基本情報。 ex) 'news' （初期値 : ''）
	 * @param array $options オプション（初期値 :array()）
	 *    - `sort` : データのソート順 取得出来るフィールドのどれかでソートができる ex) 'created DESC'（初期値 : 'id'）
	 *  - `siteId` : サブサイトIDで絞り込む場合に指定する（初期値：0）
	 *  - `postCount` : 公開記事数を取得するかどうか (初期値:false)
	 * @return mixed false|array Blogの基本情報
	 */
	public function getContents($name = '', $options = [])
	{
		$options = array_merge([
			'sort' => 'BlogContent.id',
			'siteId' => null,
			'postCount' => false,
		], $options);
		$conditions['Content.status'] = true;
		if (!empty($name)) {
			if (is_int($name)) {
				$conditions['BlogContent.id'] = $name;
			} else {
				$conditions['Content.name'] = $name;
			}
		}
		if ($options['siteId'] !== '' && !is_null($options['siteId']) && $options['siteId'] !== false) {
			$conditions['Content.site_id'] = $options['siteId'];
		}
		/** @var BlogContent $BlogContent */
		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$BlogContent->unbindModel(
			['hasMany' => ['BlogPost', 'BlogCategory']]
		);
		$datas = $BlogContent->find('all', [
				'conditions' => $conditions,
				'order' => $options['sort'],
				'recursive' => 0
			]
		);
		if (!$datas) {
			return false;
		}

		// 公開記事数のカウントを追加
		if ($options['postCount']) {
			$datas = $this->_mergePostCountToBlogsData($datas);
		}

		$contents = [];
		if (count($datas) === 1) {
			$datas = $BlogContent->constructEyeCatchSize($datas[0]);
			unset($datas['BlogContent']['eye_catch_size']);
			$contents[] = $datas;
		} else {
			foreach($datas as $val) {
				$val = $BlogContent->constructEyeCatchSize($val);
				unset($val['BlogContent']['eye_catch_size']);
				$contents[] = $val;
			}
		}
		if ($name && !is_array($name)) {
			$contents = $contents[0];
		}
		return $contents;
	}

	/**
	 * Blogの基本情報に公開記事数を追加する
	 *
	 * @param array $blogsData Blogの基本情報の配列
	 * @return array
	 */
	private function _mergePostCountToBlogsData(array $blogsData)
	{

		/** @var BlogPost $BlogPost */
		$BlogPost = ClassRegistry::init('Blog.BlogPost');

		$blogContentIds = Hash::extract($blogsData, "{n}.BlogContent.id");
		$conditions = array_merge(
			['BlogPost.blog_content_id' => $blogContentIds],
			$BlogPost->getConditionAllowPublish()
		);

		$postCountsData = $BlogPost->find('all', [
			'fields' => [
				'BlogPost.blog_content_id',
				'COUNT(BlogPost.id) as post_count',
			],
			'conditions' => $conditions,
			'group' => ['BlogPost.blog_content_id'],
			'recursive' => -1,
		]);

		if (empty($postCountsData)) {
			foreach($blogsData as $blogData) {
				$blogData['BlogContent']['post_count'] = 0;
			}
			return $blogsData;
		}

		foreach($blogsData as $index => $blogData) {

			$blogContentId = $blogData['BlogContent']['id'];
			$countData = array_values(array_filter($postCountsData, function(array $data) use ($blogContentId) {
				return $data['BlogPost']['blog_content_id'] == $blogContentId;
			}));

			if (empty($countData)) {
				$blogsData[$index]['BlogContent']['post_count'] = 0;
				continue;
			}

			$blogsData[$index]['BlogContent']['post_count'] = intval($countData[0][0]['post_count']);
		}

		return $blogsData;
	}

	/**
	 * 現在のページがブログプラグインかどうかを判定する
	 *
	 * @return bool
	 */
	public function isBlog()
	{
		return (!empty($this->request->params['Content']['plugin']) && $this->request->params['Content']['plugin'] == 'Blog');
	}

	/**
	 * ブログコンテンツのURLを取得する
	 *
	 * 別ドメインの場合はフルパスで取得する
	 *
	 * @param $blogContentId ブログコンテンツID
	 * @return string
	 */
	public function getContentsUrl($blogContentId, $base = true)
	{
		$this->setContent($blogContentId);
		$site = BcSite::findByUrl($this->content['url']);
		return $this->BcBaser->getContentsUrl($this->content['url'], !$this->isSameSiteBlogContent($blogContentId), !empty($site->useSubDomain), $base);
	}

	/**
	 * 指定したブログコンテンツIDが、現在のサイトと同じかどうか判定する
	 *
	 * @param $blogContentId ブログコンテンツID
	 * @return bool
	 */
	public function isSameSiteBlogContent($blogContentId)
	{
		$Content = ClassRegistry::init('Content');
		$siteId = $Content->field('site_id', [
			'Content.entity_id' => $blogContentId,
			'Content.type' => 'BlogContent'
		]);
		$currentSiteId = 0;
		if (!empty($this->request->params['Content']['alias_id'])) {
			$currentSiteId = $Content->field('site_id', [
				'Content.id' => $this->request->params['Content']['alias_id']
			]);
		} elseif (isset($this->request->params['Site']['id'])) {
			$currentSiteId = $this->request->params['Site']['id'];
		}
		return ($currentSiteId == $siteId);
	}

	/**
	 * プレビュー用のURLを取得する
	 *
	 * @param string $url 元となるURL
	 * @param bool $useSubDomain サブドメインを利用してるかどうか
	 * @return string
	 */
	public function getPreviewUrl($url, $useSubDomain = false)
	{
		if ($useSubDomain) {
			$targetSite = BcSite::findByUrl($url);
			return $this->BcBaser->getUrl($targetSite->getPureUrl($url)) . '?host=' . $targetSite->host;
		} else {
			return $this->BcBaser->getContentsUrl($url, false, false, true);
		}
	}

	/**
	 * ブログのカテゴリを取得する
	 * - 例: $this->Blog->getBlogArchiveCategoryData($this->Blog->getCurrentBlogId());
	 * 現在のページがカテゴリ一覧の場合、$categoryName は省略可
	 *
	 * @param int $blogContentId
	 * @param string $categoryName
	 * @param array $options
	 * @return array
	 */
	public function getCategoryByName($blogContentId, $categoryName = '', $options = [])
	{
		if (!$categoryName && $this->getBlogArchiveType() === 'category') {
			$pass = $this->request->params['pass'];
			$categoryName = $pass[count($pass) - 1];
		}
		return ClassRegistry::init('Blog.BlogCategory')->getByName($blogContentId, $categoryName, $options);
	}

	/**
	 * 記事件数を取得する
	 * 一覧でのみ利用可能
	 *
	 * @return false|mixed
	 */
	public function getPostCount()
	{
		$params = $this->_View->Paginator->params('BlogPost');
		if (isset($params['count'])) {
			return $params['count'];
		}
		return false;
	}

	/**
	 * 現在のブログタグアーカイブのブログタグ情報を取得する
	 *
	 * @return array
	 */
	public function getCurrentBlogTag()
	{
		$blogTag = [];
		if ($this->isTag()) {
			$pass = $this->request->params['pass'];
			$name = isset($pass[1])? $pass[1] : '';
			$BlogTagModel = ClassRegistry::init('Blog.BlogTag');
			$blogTag = $BlogTagModel->getByName(urldecode($name));
		}
		return $blogTag;
	}

}
