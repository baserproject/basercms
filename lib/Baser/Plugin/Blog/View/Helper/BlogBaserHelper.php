<?php
/**
 * BlogBaserヘルパー
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
 * BlogBaserヘルパー
 * 
 * BcBaserHelper より透過的に呼び出される
 * 
 * 《利用例》
 * $this->BcBaser->blogPosts('news')
 *
 * @package Blog.View.Helper
 *
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
 * @param string | array $contentsName 管理システムで指定したコンテンツ名（初期値 : null）
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

		if (empty($contentsName)) {
			// コンテンツ名が空の場合
			$contentsName = array();
		} elseif (!is_array($contentsName)) {
			// コンテンツ名が配列でない場合
			$contentsName = array($contentsName);
		}

		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$blogContents = $BlogContent->find('all', array(
			'fields' => array('id', 'name', 'status'),
			'recursive' => -1,
		));
		if (empty($blogContents)) {
			return;
		} else {
			$blogContents = Hash::combine($blogContents, '{n}.BlogContent.name', '{n}.BlogContent');
		}
		if ($options['contentsTemplate']) {
			$contentsTemplate = $options['contentsTemplate'];
		} else {
			if ($contentsName) {
				if (is_array($contentsName)) {
					$contentsTemplate = current($contentsName);
				} else {
					$contentsTemplate = $contentsName;
				}
			} else {
				trigger_error('$contentsName を省略時は、contentsTemplate オプションで、コンテンツテンプレート名を指定していください。', E_USER_WARNING);
				return;
			}
		}

		if ($blogContents[$contentsTemplate]['id']) {
			$id = $blogContents[$contentsTemplate]['id'];
		}

		unset($options['contentsTemplate']);
		$blogContentId = array();

		if ($contentsName) {
			foreach ($blogContents as $key => $value) {
				if (array_search($key, $contentsName) !== false && $value['status']) {
					$blogContentId[] = $value['id'];
				}
			}
		} else {
			foreach ($blogContents as $key => $value) {
				if ($value['status']) {
					$blogContentId[] = $value['id'];
				}
			}
		}
		$options['contentId'] = $blogContentId;

		$url = array('admin' => false, 'plugin' => 'blog', 'controller' => 'blog', 'action' => 'posts');

		$settings = Configure::read('BcAgent');
		foreach ($settings as $key => $setting) {
			if (isset($options[$key])) {
				$agentOn = $options[$key];
				unset($options[$key]);
			} else {
				$agentOn = (Configure::read('BcRequest.agent') == $key);
			}
			if ($agentOn) {
				$url['prefix'] = $setting['prefix'];
				break;
			}
		}
		
		echo $this->requestAction($url, array('return', 'pass' => array($id, $num), 'named' => $options));
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

}
