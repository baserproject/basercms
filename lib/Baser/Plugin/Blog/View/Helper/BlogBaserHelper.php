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
 * BlogBaserヘルパー
 *
 * BcBaserHelper より透過的に呼び出される
 *
 * 《利用例》
 * $this->BcBaser->blogPosts('news')
 *
 * BcBaserHeleper へのインターフェイスを提供する役割だけとし、
 * 実装をできるだけこのクラスで持たないようにし、BlogHelper 等で実装する
 *
 * @package Blog.View.Helper
 * @property BlogHelper $Blog
 */
class BlogBaserHelper extends AppHelper
{

	/**
	 * ヘルパー
	 * @var array
	 */
	public $helpers = ['Blog.Blog', 'BcBaser'];

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
	public function blogPosts($contentsName = [], $num = 5, $options = [])
	{
		$this->Blog->posts($contentsName, $num, $options);
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
	public function getBlogPosts($contentsName = [], $num = 5, $options = [])
	{
		return $this->Blog->getPosts($contentsName, $num, $options);
	}

	/**
	 * カテゴリー別記事一覧ページ判定
	 *
	 * @return boolean 現在のページがカテゴリー別記事一覧ページであれば true を返す
	 */
	public function isBlogCategory()
	{
		return $this->Blog->isCategory();
	}

	/**
	 * タグ別記事一覧ページ判定
	 *
	 * @return boolean 現在のページがタグ別記事一覧ページであれば true を返す
	 */
	public function isBlogTag()
	{
		return $this->Blog->isTag();
	}

	/**
	 * 日別記事一覧ページ判定
	 *
	 * @return boolean 現在のページが日別記事一覧ページであれば true を返す
	 */
	public function isBlogDate()
	{
		return $this->Blog->isDate();
	}

	/**
	 * 月別記事一覧ページ判定
	 *
	 * @return boolean 現在のページが月別記事一覧ページであれば true を返す
	 */
	public function isBlogMonth()
	{
		return $this->Blog->isMonth();
	}

	/**
	 * 年別記事一覧ページ判定
	 *
	 * @return boolean 現在のページが年別記事一覧ページであれば true を返す
	 */
	public function isBlogYear()
	{
		return $this->Blog->isYear();
	}

	/**
	 * 個別ページ判定
	 *
	 * @return boolean 現在のページが個別ページであれば true を返す
	 */
	public function isBlogSingle()
	{
		return $this->Blog->isSingle();
	}

	/**
	 * インデックスページ判定
	 *
	 * @return boolean 現在のページがインデックスページであれば true を返す
	 */
	public function isBlogHome()
	{
		return $this->Blog->isHome();
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
	public function getBlogs($name = '', $options = [])
	{
		return $this->Blog->getContents($name, $options);
	}

	/**
	 * 現在のページがブログプラグインかどうかを判定する
	 *
	 * @return bool
	 */
	public function isBlog()
	{
		return $this->Blog->isBlog();
	}

	/**
	 * ブログカテゴリを取得する
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function getBlogCategories($options = [])
	{
		return $this->Blog->getCategories($options);
	}

	/**
	 * 子カテゴリを持っているかどうか
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function hasChildBlogCategory($id)
	{
		return $this->Blog->hasChildCategory($id);
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
	 * @return array|null
	 */
	public function getBlogTagList($name, $options = [])
	{
		return $this->Blog->getTagList($name, $options);
	}

	/**
	 * ブログタグリストを出力する
	 *
	 * @param mixed $name
	 * @param array $options
	 *    オプションは、BlogBaserHelper::getBlogTagList() と同じ
	 */
	public function blogTagList($name, $options = [])
	{
		$this->Blog->tagList($name, $options);
	}

	/**
	 * ブログコンテンツのURLを取得する
	 *
	 * 別ドメインに対応
	 *
	 * @param int $blogContentId ブログコンテンツID
	 * @param bool $base ベースURLを付与するかどうか
	 * @return string
	 */
	public function getBlogContentsUrl($blogContentId, $base = true)
	{
		return $this->Blog->getContentsUrl($blogContentId, $base);
	}

	/**
	 * 記事件数を取得する
	 * 一覧でのみ利用可能
	 *
	 * @return false|mixed
	 */
	public function getBlogPostCount()
	{
		return $this->Blog->getPostCount();
	}

}
