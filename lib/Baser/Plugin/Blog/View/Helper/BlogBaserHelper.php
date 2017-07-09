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
	public $helpers = array('Blog.Blog', 'BcBaser');

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
 * 	- `conditions` : CakePHP形式の検索条件（初期値 : array()）
 *	- `category` : カテゴリで絞り込む（初期値 : null）
 *	- `tag` : タグで絞り込む（初期値 : null）
 *	- `year` : 年で絞り込む（初期値 : null）
 *	- `month` : 月で絞り込む（初期値 : null）
 *	- `day` : 日で絞り込む（初期値 : null）
 *	- `id` : 記事NO で絞り込む（初期値 : null）※ 後方互換の為 id を維持
 * 	- `no` : 記事NO で絞り込む（初期値 : null）
 *	- `keyword` : キーワードで絞り込む場合にキーワードを指定（初期値 : null）
 *  - `postId` : 記事ID で絞り込む（初期値 : null）
 *  - `siteId` : サイトID で絞り込む（初期値 : null）
 *  - `preview` : 非公開の記事も見る場合に指定（初期値 : false）
 *	- `contentsTemplate` : コンテンツテンプレート名を指定（初期値 : null）
 *	- `template` : 読み込むテンプレート名を指定する場合にテンプレート名を指定（初期値 : null）
 *	- `direction` : 並び順の方向を指定 [昇順:ASC or 降順:DESC or ランダム:RANDOM]（初期値 : null）
 *	- `page` : ページ数を指定（初期値 : null）
 *	- `sort` : 並び替えの基準となるフィールドを指定（初期値 : null）
 *	- `autoSetCurrentBlog` : $contentsName を指定していない場合、現在のコンテンツより自動でブログを指定する（初期値：true）
 * @return void
 */
	public function blogPosts($contentsName = [], $num = 5, $options = []) {
		/**
		 * @var BlogContent $BlogContent
		 */
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
			'autoSetCurrentBlog' => true
		], $options);
		
		if(!$contentsName && empty($options['contentsTemplate'])) {
			trigger_error('$contentsName を省略時は、contentsTemplate オプションで、コンテンツテンプレート名を指定してください。', E_USER_WARNING);
			return;
		}

		$contentsTemplate = $options['contentsTemplate'];
		$template = $options['template'];
		unset($options['contentsTemplate'], $options['template']);

		$blogPosts = $this->getBlogPosts($contentsName, $num, $options);
		
		// テンプレートの決定
		$options = $this->parseContentName($contentsName, $options);
		if(!$contentsTemplate) {
			$BlogContent = ClassRegistry::init('Blog.BlogContent');
			$conditions['Content.url'] = $options['contentUrl'];
			$conditions = array_merge($conditions, $BlogContent->Content->getConditionAllowPublish());
			$blogContent = $BlogContent->find('first', [
				'fields' => ['BlogContent.template'],
				'conditions' => $conditions,
				'recursive' => 0,
				'cache' => false
			]);
			if($blogContent) {
				$contentsTemplate = $blogContent['BlogContent']['template'];
			} else {
				$contentsTemplate = 'default';
			}
		}
		$template = 'Blog...' . DS . 'Blog' . DS . $contentsTemplate . DS . $template;
		$params = [];
		if(!empty($this->request->params['Site']['device'])) {
			$this->_View->subDir = $this->request->params['Site']['device'];
		}
		$this->BcBaser->element($template, ['posts' => $blogPosts], $params);
	}

/**
 * ブログ記事を取得する
 * 
 * @param array $contentsName
 * @param int $num
 * @param array $options
 * 	※ パラメーターは、contentTemplate / template 以外、BlogBaserHelper::blogPosts() に準ずる
 * @return mixed
 */
	public function getBlogPosts($contentsName = [], $num = 5, $options = array()) {
		/**
		 * @var BlogContent $BlogContent
		 */
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
	public function parseContentName($contentsName, $options) {
		if ($contentsName && !is_array($contentsName)) {
			$contentsName = [$contentsName];
		}
		// 対象ブログを指定する条件を設定
		$options['contentUrl'] = $options['contentId'] = [];
		if($contentsName) {
			foreach($contentsName as $key => $value) {
				if(is_int($value)) {
					$options['contentId'] = $value;
				} else {
					$options['contentUrl'][$key] = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $value) . '/';
				}
			}
		}
		if($options['autoSetCurrentBlog'] && !$options['contentUrl'] && !empty($this->request->params['Content']['url'])) {
			$options['contentUrl'] = $this->request->params['Content']['url'];
			$options['contentId'] = $this->request->params['Content']['entity_id'];
		}
		return $options;
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
 *  - `postCount` : 公開記事数を取得するかどうか (初期値:false)
 * @return mixed false|array Blogの基本情報
 */
	public function getBlogs($name = '', $options = array()) {
		$options = array_merge(array(
			'sort' => 'BlogContent.id',
			'siteId' => null,
			'postCount' => false,
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
		/** @var BlogContent $BlogContent */
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

		// 公開記事数のカウントを追加
		if ($options['postCount']) {
			$datas = $this->_mergePostCountToBlogsData($datas);
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
 * Blogの基本情報に公開記事数を追加する
 *
 * @param array $blogsData Blogの基本情報の配列
 * @return array
 */
	private function _mergePostCountToBlogsData(array $blogsData) {

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

		if(empty($postCountsData)) {
			foreach ($blogsData as $blogData) {
				$blogData['BlogContent']['post_count'] = 0;
			}
			return $blogsData;
		}

		foreach($blogsData as $index => $blogData) {

			$blogContentId = $blogData['BlogContent']['id'];
			$countData = array_values(array_filter($postCountsData, function(array $data) use ($blogContentId) {
				return $data['BlogPost']['blog_content_id'] == $blogContentId;
			}));

			if(empty($countData)) {
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
