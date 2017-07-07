<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * BlogBaserヘルパー
 * 
 * BcBaserHelper より透過的に呼び出される
 * 
 * 《利用例》
 * $this->BcBaser->blogPosts('news')
 *
 * @package Blog.View.Helper
 * @property BlogHelper $Blog
 */
class BlogBaserHelper extends AppHelper {

/**
 * ヘルパー
 * @var array
 */
	public $helpers = array('Blog.Blog');

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
 *									コンテンツテンプレート名は配列の先頭を利用する
 * $this->BcBaser->blogPosts(array('news', 'work'), 3)
 * 
 * 全てのコンテンツを指定する場合：nullを指定
 *									contentsTemplateオプションにて
 *									コンテンツテンプレート名を指定する（必須）
 * $this->BcBaser->blogPosts(null, 3, array('contentsTemplate' => 'news'))
 * 
 * @param string | array $contentsName 管理システムで指定したコンテンツ名（初期値 : null）２階層目以降はURLで指定
 * @param int $num 記事件数（初期値 : 5）
 * @param array $options オプション（初期値 : array()）
 *	- `category` : カテゴリで絞り込む場合にアルファベットのカテゴリ名指定（初期値 : null）
 *	- `tag` : タグで絞り込む場合にタグ名を指定（初期値 : null）
 *	- `year` : 年で絞り込む場合に年を指定（初期値 : null）
 *	- `month` : 月で絞り込む場合に月を指定（初期値 : null）
 *	- `day` : 日で絞り込む場合に日を指定（初期値 : null）
 *	- `id` : id で絞り込む場合に id を指定（初期値 : null）
 *	- `keyword` : キーワードで絞り込む場合にキーワードを指定（初期値 : null）
 *	- `contentsTemplate` : コンテンツテンプレート名を指定（初期値 : null）
 *	- `template` : 読み込むテンプレート名を指定する場合にテンプレート名を指定（初期値 : null）
 *	- `direction` : 並び順の方向を指定 [昇順:ASC or 降順:DESC or ランダム:RANDOM]（初期値 : null）
 *	- `sort` : 並び替えの基準となるフィールドを指定（初期値 : null）
 *	- `page` : ページ数を指定（初期値 : null）
 * @return void
 */
	public function blogPosts($contentsName = null, $num = 5, $options = array()) {
		$options = array_merge(array(
			'category' => null,
			'tag' => null,
			'year' => null,
			'month' => null,
			'day' => null,
			'id' => null,
			'keyword' => null,
			'contentsTemplate' => null,
			'template' => null,
			'direction' => null,
			'page' => null,
			'sort' => null
		), $options);

			if(!$contentsName && empty($options['contentsTemplate'])) {
			trigger_error('$contentsName を省略時は、contentsTemplate オプションで、コンテンツテンプレート名を指定していください。', E_USER_WARNING);
			return;
		}
		
		// コンテンツ名を配列に
		if (empty($contentsName)) {
			$contentsName = [];
		} elseif (!is_array($contentsName)) {
			$contentsName = [$contentsName];
		}
		
		// URL形式に変換
		foreach($contentsName as $key => $value) {
			$contentsName[$key] = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $value) . '/';
		}
		
		// ブログコンテンツの条件生成
		$Content = ClassRegistry::init('Content');
		$conditions = [];
		if($contentsName) {
			$conditions['Content.url'] = $contentsName;
		}
		$conditions = array_merge($conditions, $Content->getConditionAllowPublish());
		$conditions['Content.type'] = "BlogContent";
		
		// 有効ブログを取得
		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$blogContents = $BlogContent->find('all', [
			'fields' => ['BlogContent.id', 'BlogContent.template', 'Content.name', 'Content.status'],
			'conditions' => $conditions,
			'recursive' => 0,
			'cache' => false
		]);
		
		if (empty($blogContents)) {
			trigger_error('指定されたコンテンツが見つかりません。（' . implode(', ', $contentsName) . '）', E_USER_NOTICE);
			return;
		}

		$options['contentId'] = Hash::extract($blogContents, "{n}.BlogContent.id");
		
		// 指定したコンテンツテンプレートに紐づくブログIDを特定
		// 指定したコンテンツネームに紐づくブログIDを取得
		$blogContentId = null;
		foreach($blogContents as $key => $blogContent) {
			if(!empty($options['contentsTemplate']) && $options['contentsTemplate'] == $blogContent['BlogContent']['template']) {
				$blogContentId = $blogContent['BlogContent']['id'];	
				break;
			}
		}
		
		// コンテンツテンプレートに紐づくブログIDを特定できない場合は
		// 対象ブログの先頭のブログIDとする
		if (!$blogContentId) {
			$blogContentId = current($options['contentId']);
		}
		
		unset($options['contentsTemplate']);

		$url = array('admin' => false, 'plugin' => 'blog', 'controller' => 'blog', 'action' => 'posts');
		if(!empty($this->request->params['Site']['device'])) {
			$url['prefix'] = $this->request->params['Site']['device'];
		}
		echo $this->requestAction($url, array('return', 'pass' => array($blogContentId, $num), 'entityId' => $blogContentId, 'named' => $options));
	}

/**
 * カテゴリー別記事一覧ページ判定
 *
 * @return boolean 現在のページがカテゴリー別記事一覧ページであれば true を返す
 */
	public function isBlogCategory() {
		return $this->Blog->isCategory();
	}

/**
 * タグ別記事一覧ページ判定
 * 
 * @return boolean 現在のページがタグ別記事一覧ページであれば true を返す
 */
	public function isBlogTag() {
		return $this->Blog->isTag();
	}

/**
 * 日別記事一覧ページ判定
 * 
 * @return boolean 現在のページが日別記事一覧ページであれば true を返す
 */
	public function isBlogDate() {
		return $this->Blog->isDate();
	}

/**
 * 月別記事一覧ページ判定
 * 
 * @return boolean 現在のページが月別記事一覧ページであれば true を返す
 */
	public function isBlogMonth() {
		return $this->Blog->isMonth();
	}

/**
 * 年別記事一覧ページ判定
 * 
 * @return boolean 現在のページが年別記事一覧ページであれば true を返す
 */
	public function isBlogYear() {
		return $this->Blog->isYear();
	}

/**
 * 個別ページ判定
 * 
 * @return boolean 現在のページが個別ページであれば true を返す
 */
	public function isBlogSingle() {
		return $this->Blog->isSingle();
	}

/**
 * インデックスページ判定
 * 
 * @return boolean 現在のページがインデックスページであれば true を返す
 */
	public function isBlogHome() {
		return $this->Blog->isHome();
	}

/**
 * Blogの基本情報を全て取得する
 *
 * @param string $name ブログアカウント名を指定するとそのブログのみの基本情報を返す。空指定(default)で、全てのブログの基本情報。 ex) 'news' （初期値 : ''）
 * @param array $options オプション（初期値 :array()）
 *	- `sort` : データのソート順 取得出来るフィールドのどれかでソートができる ex) 'created DESC'（初期値 : 'id'）
 *  - `siteId` : サブサイトIDで絞り込む場合に指定する（初期値：0）
 * @return mixed false|array Blogの基本情報
 */
	public function getBlogs($name = '', $options = array()) {
		$options = array_merge(array(
			'sort' => 'BlogContent.id',
			'siteId' => null
		), $options);
		$conditions['Content.status'] = true;
		if(!empty($name)){
			if(is_int($name)) {
				$conditions['BlogContent.id'] = $name;
			} else {
				$conditions['Content.name'] = $name;
			}
		}
		if($options['siteId'] !== '' && !is_null($options['siteId']) && $options['siteId'] !== false) {
			$conditions['Content.site_id'] = $options['siteId'];
		}
		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$BlogContent->unbindModel(
			['hasMany' => ['BlogPost', 'BlogCategory']]
		);
		$datas = $BlogContent->find('all', array(
				'conditions' => $conditions,
				'order' => $options['sort'],
				'cache' => false,
				'recursive' => 0
			)
		);
		if(!$datas) {
			return false;
		}
		$contents = array();
		if( count($datas) === 1 ){
			$datas = $BlogContent->constructEyeCatchSize($datas[0]);
			unset($datas['BlogContent']['eye_catch_size']);
			$contents[] = $datas;
		} else {
			foreach($datas as $val){
				$val = $BlogContent->constructEyeCatchSize($val);
				unset($val['BlogContent']['eye_catch_size']);
				$contents[] = $val;
			}
		}
		if($name && !is_array($name)) {
			$contents = $contents[0];
		}
		return $contents;
	}


/**
 * 現在のページがブログプラグインかどうかを判定する
 *
 * @return bool
 */
	public function isBlog() {
		return (!empty($this->request->params['Content']['plugin']) && $this->request->params['Content']['plugin'] == 'Blog');
	}

/**
 * ブログカテゴリを取得する
 * 
 * @param array $options
 * @return mixed
 */
	public function getBlogCategories($options = []) {
		return $this->Blog->getCategories($options);
	}

/**
 * 子カテゴリを持っているかどうか
 * 
 * @param int $id
 * @return mixed
 */
	public function hasChildBlogCategory($id) {
		return $this->Blog->hasChildCategory($id);
	}
	
}
