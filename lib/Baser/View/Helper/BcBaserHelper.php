<?php
/**
 * BcBaserHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('AppHelper', 'View/Helper');

/**
 * BcBaserHelper
 * 
 * テーマより利用される事を前提としたヘルパーで、テーマで必要となる機能をひと通り提供する。
 *
 * @package Baser.View.Helper
 */
class BcBaserHelper extends AppHelper {

/**
 * サイト基本設定データ
 *
 * @var array
 */
	public $siteConfig = array();

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcHtml', 'Js', 'Session', 'BcXml', 'BcArray');

/**
 * ページモデル
 * 
 * 一度初期化した後に再利用し、処理速度を向上する為にコンストラクタでセットする。
 *
 * @var Page
 */
	protected $_Page = null;
	
/**
 * アクセス制限設定モデル
 * 
 * 一度初期化した後に再利用し、処理速度を向上する為にコンストラクタでセットする。
 *
 * @var Permission
 */
	protected $_Permission = null;
	
/**
 * カテゴリタイトル設定
 * 
 * パンくず用の配列を取得する際、カテゴリのタイトルを取得するかどうかの判定を保持
 *
 * @var mixed boolean or null
 */
	protected $_categoryTitleOn = true;

/**
 * カテゴリタイトル
 *
 * @var mixed boolean or string
 */
	protected $_categoryTitle = true;

/**
 * BcBaserHelper を拡張するプラグインのヘルパ
 * 
 * BcBaserHelper::_initPluginBasers() で自動的に初期化される。
 *
 * @var array
 */
	protected $_pluginBasers = array();

/**
 * コンストラクタ
 *
 * @param View $View ビュークラス
 * @param array $settings ヘルパ設定値（BcBaserHelper では利用していない）
 */
	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		// モデルクラスをセット
		// 一度初期化した後に再利用し、処理速度を向上する為にコンストラクタでセットしておく
		if ($this->_View && BC_INSTALLED && !Configure::read('BcRequest.isUpdater') && !Configure::read('BcRequest.isMaintenance')) {
			// DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
			try {
				$this->_Permission = ClassRegistry::init('Permission');
				$this->_Page = ClassRegistry::init('Page');
			} catch (Exception $ex) {}
		}

		// サイト基本設定データをセット
		if (BC_INSTALLED || isConsole()) {
			if (isset($this->_View->viewVars['siteConfig'])) {
				$this->siteConfig = $this->_View->viewVars['siteConfig'];
			}
		}

		// プラグインのBaserヘルパを初期化
		if (BC_INSTALLED && !Configure::read('BcRequest.isUpdater') && !Configure::read('BcRequest.isMaintenance')) {
			$this->_initPluginBasers();
		}

	}

/**
 * メニューのデータを取得する
 * 
 * 配列で全件取得する
 *
 * @return array メニューデータ Or false
 */
	public function getMenus() {
		$Menu = ClassRegistry::init('Menu');
		if($Menu) {
			return $Menu->find('all', array('order' => 'sort'));
		} else {
			return false;
		}
	}
	
/**
 * タイトルを設定する
 *
 * @param string $title タイトル
 * @return void
 */
	public function setTitle($title, $categoryTitleOn = null) {
		if (!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}
		$this->_View->pageTitle = $title;
	}

/**
 * meta タグのキーワードを設定する
 *
 * @param string $keywords キーワード（複数の場合はカンマで区切る）
 * @return void
 */
	public function setKeywords($keywords) {
		$this->_View->set('keywords', $keywords);
	}

/**
 * meta タグの説明文を設定する
 *
 * @param string $description 説明文
 * @return void
 */
	public function setDescription($description) {
		$this->_View->set('description', $description);
	}

/**
 * レイアウトで利用する為の変数を設定する
 * 
 * View::set() のラッパー
 *
 * @param string $key 変数名
 * @param mixed $value 値
 * @return void
 */
	public function set($key, $value) {
		$this->_View->set($key, $value);
	}

/**
 * タイトルへのカテゴリタイトルの出力有無を設定する
 * 
 * コンテンツごとに個別設定をする為に利用する。
 * パンくずにも影響する。
 *
 * @param boolean|string|array $on true を指定した場合は、コントローラーで指定した crumbs を参照し、
 *		文字列を指定した場合には、その文字列をカテゴリとして利用する。
 *		パンくずにリンクをつける場合には、配列で指定する。 
 *		（例） array('name' => '会社案内', 'url' => '/company/index')
 * @return void
 */
	public function setCategoryTitle($on = true) {
		$this->_categoryTitle = $on;
	}

/**
 * meta タグ用のキーワードを取得する
 *
 * @return string meta タグ用のキーワード
 */
	public function getKeywords() {

		$keywords = '';
		if (!empty($this->_View->viewVars['keywords'])) {
			$keywords = $this->_View->viewVars['keywords'];
		} elseif (!empty($this->siteConfig['keyword'])) {
			$keywords = $this->siteConfig['keyword'];
		}
		return $keywords;
		
	}

/**
 * meta タグ用のページ説明文を取得する
 *
 * @return string meta タグ用の説明文
 */
	public function getDescription() {

		$description = '';
		if (!empty($this->_View->viewVars['description'])) {
			$description = $this->_View->viewVars['description'];
		} elseif (!empty($this->siteConfig['description'])) {
			$description = $this->siteConfig['description'];
		}
		return $description;
		
	}

/**
 * タイトルタグを取得する
 * 
 * ページタイトルと直属のカテゴリ名が同じ場合は、ページ名を省略する
 *
 * @param string $separator 区切り文字
 * @param string $categoryTitleOn カテゴリタイトルを表示するかどうか boolean で指定
 * @return string メタタグ用のタイトルを返す
 */
	public function getTitle($separator = '｜', $categoryTitleOn = null) {

		$title = array();
		$crumbs = $this->getCrumbs($categoryTitleOn);
		if ($crumbs) {
			$crumbs = array_reverse($crumbs);
			foreach ($crumbs as $key => $crumb) {
				if ($this->BcArray->first($crumbs, $key) && isset($crumbs[$key + 1])) {
					if ($crumbs[$key + 1]['name'] == $crumb['name']) {
						continue;
					}
				}
				$title[] = $crumb['name'];
			}
		}

		// サイトタイトルを追加
		if (!empty($this->siteConfig['name'])) {
			$title[] = $this->siteConfig['name'];
		}

		return implode($separator, $title);
		
	}

/**
 * パンくず用の配列を取得する
 * 
 * 基本的には、コントローラーの crumbs プロパティで設定した値を取得する仕様だが
 * 事前に setCategoryTitle メソッドで出力内容をカスタマイズする事ができる
 *
 * @param mixid $categoryTitleOn 親カテゴリの階層を表示するかどうか
 * @return array パンくず用の配列
 * @todo 
 * HTMLレンダリングも含めた状態で取得できる、HtmlHelper::getCrumbs() とメソッド名が
 * 同じで、 処理内容がわかりにくいので変数名のリファクタリング要。
 * ただし、BcBaserHelper::getCrumbs() は、テーマで利用されている可能性が高いので、
 * 後方互換を考慮する必要がある。
 */
	public function getCrumbs($categoryTitleOn = null) {

		// ページカテゴリを追加
		if (!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}

		// 親となるパンくずを取得
		$crumbs = array();
		if ($this->_categoryTitleOn) {
			// true の場合は、コントローラーで設定された crumbs を取得
			if($this->_categoryTitle === true) {
				if( $this->_View->getVar('crumbs')) {
					$crumbs = $this->_View->getVar('crumbs');
				}
			} else {
				if (is_array($this->_categoryTitle)) {
					$crumbs[] = $this->_categoryTitle;
				} elseif($this->_categoryTitle) {
					$crumbs[] = array('name' => $this->_categoryTitle, 'url' => '');
				}
			}
		}

		// カレントのページを追加
		$contentsTitle = $this->getContentsTitle();
		if ($contentsTitle) {
			$crumbs[] = array('name' => $contentsTitle, 'url' => '');
		}

		return $crumbs;
		
	}

/**
 * コンテンツタイトルを取得する
 * 
 * @return string コンテンツタイトル
 */
	public function getContentsTitle() {

		$contentsTitle = '';
		if ($this->_View->pageTitle) {
			$contentsTitle = $this->_View->pageTitle;
		}
		if ($this->_View->name != 'CakeError') {
			return $contentsTitle;
		} else {
			return '';
		}
		
	}

/**
 * コンテンツのタイトルを出力する
 *
 * @return void
 */
	public function contentsTitle() {
		echo $this->getContentsTitle();
	}

/**
 * タイトルタグを出力する
 *
 * @param string $separator 区切り文字
 * @param string $categoryTitleOn カテゴリを表示するかどうか boolean で指定
 * @return void
 */
	public function title($separator = '｜', $categoryTitleOn = null) {
		echo '<title>' . strip_tags($this->getTitle($separator, $categoryTitleOn)) . "</title>\n";
	}

/**
 * キーワード用のメタタグを出力する
 *
 * @return void
 */
	public function metaKeywords() {
		echo $this->BcHtml->meta('keywords', $this->getkeywords()) . "\n";
	}

/**
 * ページ説明文用のメタタグを出力する
 *
 * @return void
 */
	public function metaDescription() {
		echo $this->BcHtml->meta('description', strip_tags($this->getDescription())) . "\n";
	}

/**
 * RSSフィードのリンクタグを出力する
 *
 * @param string $title RSSのタイトル
 * @param string $link RSSのURL
 * @return void
 */
	public function rss($title, $link) {
		echo $this->BcHtml->meta($title, $link, array('type' => 'rss')) . "\n";
	}
//------------------------------------------------------------------------------ テストケース作成　ここまで完了 ryuring
/**
 * トップページかどうか判断する
 *
 * @return boolean
 * @deprecated isHomeに統合する
 */
	public function isTop() {

		return $this->isHome();
	}

/**
 * 現在のページがトップページかどうかを判定する
 *
 * @return boolean
 */
	public function isHome() {

		// TODO 2013/07/29 ryuring
		// CakeRequestの仕様として、トップページの場合は、url には、false が設定される。
		// here に変更したいところだが、スマートURLオフの場合、index.php も含まれていたような気がする。
		// スマートURLオフでの動作が確認できるまで見送り。
		return ($this->request->url == false ||
			$this->request->url == 'index' ||
			$this->request->url == Configure::read('BcRequest.agentAlias') . '/' ||
			$this->request->url == Configure::read('BcRequest.agentAlias') . '/index');
	}

/**
 * baserCMSが設置されているパスを出力する
 *
 * @return void
 */
	public function root() {

		echo $this->getRoot();
	}

/**
 * baserCMSが設置されているパスを取得する
 *
 * @return string
 */
	public function getRoot() {

		return $this->request->base . '/';
	}

/**
 * ベースを考慮したURLを出力
 *
 * @param string $url オプションのパラメータ、初期値は null
 * @param boolean $full オプションのパラメータ、初期値は false
 * @param boolean $sessionId オプションのパラメータ、初期値は true
 * @return void
 */
	public function url($url = null, $full = false, $sessionId = true) {

		echo $this->getUrl($url, $full, $sessionId);
	}

/**
 * 相対パスから実際のパスを取得する
 *
 * @param string $url 
 * @param boolean $full オプションのパラメータ、初期値は false
 * @param boolean $sessionId オプションのパラメータ、初期値は true
 * @manual
 */
	public function getUrl($url, $full = false, $sessionId = true) {

		return parent::url($url, $full, $sessionId);
	}

/**
 * エレメント（部品）テンプレートを取得する
 * View::elementを取得するだけのラッパー
 *
 * @param string $name
 * @param array $data オプションのパラメータ、初期値は arrau()
 * @param array $options オプションのパラメータ、初期値は arrau()
 * @return string
 */
	public function getElement($name, $data = array(), $options = array()) {

		$options = array_merge(array(
			'subDir' => true
			), $options);

		if (isset($options['plugin']) && !$options['plugin']) {
			unset($options['plugin']);
		}

		/*** beforeElement ***/
		$event = $this->dispatchEvent('beforeElement', array(
			'name' => $name,
			'data' => $data,
			'options' => $options
			), array('layer' => 'View', 'class' => '', 'plugin' => ''));
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true) ? $event->data['options'] : $event->result;
		}

		/*** Controller.beforeElement ***/
		$event = $this->dispatchEvent('beforeElement', array(
			'name' => $name,
			'data' => $data,
			'options' => $options
			), array('layer' => 'View', 'class' => $this->_View->name));
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true) ? $event->data['options'] : $event->result;
		}

		extract($options);


		if ($subDir === false) {
			if (!$this->_subDir && $this->_View->subDir) {
				$this->_subDir = $this->_View->subDir;
			}
			$this->_View->subDir = null;
		} else {
			if ($this->_subDir) {
				$this->_View->subDir = $this->_subDir;
			}
		}

		$out = $this->_View->element($name, $data, $options);

		/*** afterElement ***/
		$event = $this->dispatchEvent('afterElement', array(
			'name' => $name,
			'out' => $out
			), array('layer' => 'View', 'class' => '', 'plugin' => ''));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		/*** Controller.afterElement ***/
		$event = $this->dispatchEvent('afterElement', array(
			'name' => $name,
			'out' => $out
			), array('layer' => 'View', 'class' => $this->_View->name));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		return $out;
	}

/**
 * エレメント（部品）テンプレートを出力する
 * View::elementを出力するだけのラッパー
 *
 * @param string $name
 * @param array $data オプションのパラメータ、初期値は array()
 * @param boolean $options オプションのパラメータ、初期値は array()
 * @return void
 */
	public function element($name, $data = array(), $options = array()) {
		if(!$data) {
			$data = array();
		}
		$options = array_merge(array(
			'subDir' => true
			), $options);

		echo $this->getElement($name, $data, $options);
	}

/**
 * ヘッダーテンプレートを出力する
 *
 * @param array $data オプションのパラメータ、初期値は array()
 * @param array $options オプションのパラメータ、初期値は array()
 * @return void
 */
	public function header($data = array(), $options = array()) {

		$options = array_merge(array(
			'subDir' => true
			), $options);

		$out = $this->getElement('header', $data, $options);

		/*** header ***/
		$event = $this->dispatchEvent('header', array(
			'out' => $out
			), array('layer' => 'View', 'class' => '', 'plugin' => ''));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		/*** Controller.header ***/
		$event = $this->dispatchEvent('header', array(
			'out' => $out
			), array('layer' => 'View', 'class' => $this->_View->name));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}
		echo $out;
	}

/**
 * フッターテンプレートを出力する
 *
 * @param array $data オプションのパラメータ、初期値は array()
 * @param array $options オプションのパラメータ、初期値は array()
 * @return void
 */
	public function footer($data = array(), $options = array()) {

		$options = array_merge(array(
			'subDir' => true
			), $options);

		$out = $this->getElement('footer', $data, $options);

		/*** footer ***/
		$event = $this->dispatchEvent('footer', array(
			'out' => $out
			), array('layer' => 'View', 'class' => '', 'plugin' => ''));
		if ($event) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		/*** Controller.footer ***/
		$event = $this->dispatchEvent('footer', array(
			'out' => $out
			), array('layer' => 'View', 'class' => $this->_View->name));
		if ($event) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}
		echo $out;
	}

/**
 * ページネーションを出力する
 * [非推奨]
 * @param string $name
 * @param array $params オプションのパラメータ、初期値は array()
 * @param array $options オプションのパラメータ、初期値は array()
 * @return void
 * @deprecated
 */
	public function pagination($name = 'default', $data = array(), $options = array()) {

		$options = array_merge(array(
			'subDir' => true
			), $options);

		if (!$name) {
			$name = 'default';
		}

		$file = 'paginations' . DS . $name;

		echo $this->getElement($file, $data, $options);
	}

/**
 * コンテンツを出力する
 * $content_for_layout を出力するだけのラッパー
 *
 * @return void
 */
	public function content() {
		
		/*** beforeContent ***/
		$this->dispatchEvent('contentHeader', null, array('layer' => 'View', 'class' => '', 'plugin' => ''));

		/*** Controller.beforeContent ***/
		$this->dispatchEvent('contentHeader', null, array('layer' => 'View', 'class' => $this->_View->name));
		
		echo $this->_View->fetch('content');
		
		/*** afterContent ***/
		$event = $this->dispatchEvent('contentFooter', null, array('layer' => 'View', 'class' => '', 'plugin' => ''));

		/*** Controller.afterContent ***/
		$event = $this->dispatchEvent('contentFooter', null, array('layer' => 'View', 'class' => $this->_View->name));
		
	}

/**
 * セッションメッセージを出力する
 *
 * @param string $key 出力するメッセージのキー
 * @return void
 * @manual
 */
	public function flash($key = 'flash') {

		if ($this->Session->check('Message.' . $key)) {
			echo '<div id="MessageBox">';
			echo $this->Session->flash($key);
			echo '</div>';
		}
	}

/**
 * コンテンツ内で設定したCSSやjavascriptをレイアウトテンプレートに出力
 * $scripts_for_layout を出力する
 *
 * @return void
 */
	public function scripts() {

		$currentPrefix = $this->Session->read('Auth.User.authPrefix');
		$authPrefixes = Configure::read('BcAuthPrefix.' . $currentPrefix);
		$toolbar = true;

		if (isset($authPrefixes['toolbar'])) {
			$toolbar = $authPrefixes['toolbar'];
		}

		echo $this->_View->viewVars['scripts_for_layout'];

		// ツールバー設定
		if (empty($this->_View->viewVars['preview']) && $toolbar && !Configure::read('BcRequest.agent')) {
			if (!isset($this->request->query['toolbar']) || ($this->request->query['toolbar'] !== false && $this->request->query['toolbar'] !== 'false')) {
				if (empty($this->request->params['admin']) && !empty($this->_View->viewVars['user'])) {
					$this->css('admin/toolbar');
				}
			}
		}
		if (!BcUtil::isAdminSystem() && $this->params['controller'] != 'installations' && file_exists(WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css')) {
			$this->css('/files/theme_configs/config');
		}
	}

/**
 * ツールバーやCakeのデバッグ出力を表示
 *
 * @return void
 */
	public function func() {

		$currentPrefix = $this->Session->read('Auth.User.authPrefix');
		$authPrefixes = Configure::read('BcAuthPrefix.' . $currentPrefix);
		$toolbar = true;
		if (isset($authPrefixes['toolbar'])) {
			$toolbar = $authPrefixes['toolbar'];
		}

		// ツールバー表示
		if (empty($this->_View->viewVars['preview']) && $toolbar && !Configure::read('BcRequest.agent')) {
			if (!isset($this->request->query['toolbar']) || ($this->request->query['toolbar'] !== false && $this->request->query['toolbar'] !== 'false')) {
				if (empty($this->request->params['admin']) && !empty($this->_View->viewVars['user'])) {
					$this->element('admin/toolbar', array(), array('subDir' => false));
				}
			}
		}

		// デバッグ
		if (Configure::read('debug') >= 2) {
			$this->element('sql_dump', array(), array('subDir' => false));
		}
	}

/**
 * サブメニューを設定する
 *
 * @param array $submenus
 * @return void
 */
	public function setSubMenus($submenus) {

		$this->_View->set('subMenuElements', $submenus);
	}

/**
 * XMLヘッダタグを出力する
 *
 * @param array $attrib
 * @return void
 */
	public function xmlHeader($attrib = array()) {

		if (empty($attrib['encoding']) && Configure::read('BcRequest.agent') == 'mobile') {
			$attrib['encoding'] = 'Shift-JIS';
		}
		echo $this->BcXml->header($attrib) . "\n";
	}

/**
 * アイコン（favicon）タグを出力する
 *
 * @return void
 */
	public function icon() {

		echo $this->BcHtml->meta('icon') . "\n";
	}

/**
 * ドキュメントタイプを指定するタグを出力する
 *
 * @param string $type 出力ドキュメントタイプの文字列 オプションのパラメータ、初期値は 'xhtml-trans'
 * @return void
 */
	public function docType($type = 'xhtml-trans') {

		echo $this->BcHtml->docType($type) . "\n";
	}

/**
 *
 * CSSの読み込みタグを出力する
 *
 * @param string $path
 * @param array $options オプションのパラメータ、初期値は array()
 * @param boolean $inline
 * @return void
 */
	public function css($path, $options = array()) {

		$options = array_merge(array(
			'rel' => 'stylesheet',
			'inline' => true
			), $options);

		$rel = $options['rel'];
		unset($options['rel']);

		$ret = $this->BcHtml->css($path, $rel, $options);
		if ($options['inline']) {
			echo $ret;
		}
	}

/**
 * javascriptの読み込みタグを出力する
 *
 * @param string|array $url String or array of javascript files to include
 * @param boolean $inline
 * @return void
 */
	public function js($url, $inline = true) {

		$ret = $this->BcHtml->script($url, array('inline' => $inline));
		if ($inline) {
			echo $ret;
		}
	}

/**
 * 画像読み込みタグを出力する
 *
 * @param array $path
 * @param array $options
 * @return void
 */
	public function img($path, $options = array()) {

		echo $this->getImg($path, $options);
	}

/**
 * 画像タグを取得する
 *
 * @param string $path Path to the image file, relative to the app/webroot/img/ directory.
 * @param array $options Array of HTML attributes. See above for special options.
 * @return string completed img tag
 */
	public function getImg($path, $options = array()) {

		return $this->BcHtml->image($path, $options);
	}

/**
 * アンカータグを出力する
 *
 * @param string $title
 * @param string $url オプションのパラメータ、初期値は null
 * @param array $htmlAttributes オプションのパラメータ、初期値は array()
 * @param boolean $confirmMessage オプションのパラメータ、初期値は false
 * @return void
 */
	public function link($title, $url = null, $htmlAttributes = array(), $confirmMessage = false) {

		echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage);
	}

/**
 * アンカータグを取得する
 *
 * @param string $title
 * @param string $url オプションのパラメータ、初期値は null
 * @param array $htmlAttributes オプションのパラメータ、初期値は array()
 * @param boolean $confirmMessage オプションのパラメータ、初期値は false
 * @return string
 */
	public function getLink($title, $url = null, $options = array(), $confirmMessage = false) {

		if (!is_array($options)) {
			$options = array($options);
		}

		$options = array_merge(array(
			'escape' => false,
			'prefix' => false,
			'forceTitle' => false,
			'ssl' => false
			), $options);

		/*** beforeGetLink ***/
		$event = $this->dispatchEvent('beforeGetLink', array(
			'title' => $title,
			'url' => $url,
			'options' => $options,
			'confirmMessage' => $confirmMessage
			), array('class' => 'Html', 'plugin' => ''));
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true) ? $event->data['options'] : $event->result;
		}

		if ($options['prefix']) {
			if (!empty($this->request->params['prefix']) && is_array($url)) {
				$url[$this->request->params['prefix']] = true;
			}
		}
		$forceTitle = $options['forceTitle'];
		$ssl = $options['ssl'];

		unset($options['prefix']);
		unset($options['forceTitle']);
		unset($options['ssl']);

		// 管理システムメニュー対策
		// プレフィックスが変更された場合も正常動作させる為
		// TODO メニューが廃止になったら削除
		if (!is_array($url)) {
			$prefixes = Configure::read('Routing.prefixes');
			$url = preg_replace('/^\/admin\//', '/' . $prefixes[0] . '/', $url);
		}

		$_url = $this->getUrl($url);
		$_url = preg_replace('/^' . preg_quote($this->request->base, '/') . '\//', '/', $_url);
		$enabled = true;

		if ($options == false) {
			$enabled = false;
		}

		// 認証チェック
		if (isset($this->_Permission) && !empty($this->_View->viewVars['user']['user_group_id'])) {
			$userGroupId = $this->_View->viewVars['user']['user_group_id'];
			if (!$this->_Permission->check($_url, $userGroupId)) {
				$enabled = false;
			}
		}

		// ページ公開チェック
		if (isset($this->_Page) && empty($this->request->params['admin'])) {
			$adminPrefix = Configure::read('Routing.prefixes.0');
			if (isset($this->_Page) && !preg_match('/^\/' . $adminPrefix . '/', $_url)) {
				if ($this->_Page->isPageUrl($_url) && !$this->_Page->checkPublish($_url)) {
					$enabled = false;
				}
			}
		}

		if (!$enabled) {
			if ($forceTitle) {
				return "<span>$title</span>";
			} else {
				return '';
			}
		}

		// 現在SSLのURLの場合、フルパスで取得（javascript:とhttpから始まるものは除外）
		if (($this->isSSL() || $ssl) && !preg_match('/^javascript:/', $_url) && !preg_match('/^http/', $_url)) {

			$_url = preg_replace("/^\//", "", $_url);
			if (preg_match('/^admin\//', $_url)) {
				$admin = true;
			} else {
				$admin = false;
			}
			if (Configure::read('App.baseUrl')) {
				$_url = 'index.php/' . $_url;
			}
			if (!$ssl && !$admin) {
				$url = Configure::read('BcEnv.siteUrl') . $_url;
			} else {
				$url = Configure::read('BcEnv.sslUrl') . $_url;
			}
		}

		if (!$options) {
			$options = array();
		}

		$out = $this->BcHtml->link($title, $url, $options, $confirmMessage);

		/*** afterGetLink ***/
		$event = $this->dispatchEvent('afterGetLink', array(
			'url' => $url,
			'out' => $out
			), array('class' => 'Html', 'plugin' => ''));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		return $out;
	}

/**
 * SSL通信かどうか確認する
 *
 * @return boolean
 */
	public function isSSL() {

		if (!empty($this->_View->viewVars['isSSL'])) {
			return true;
		} else {
			return false;
		}
	}

/**
 * charsetメタタグを出力する
 *
 * @param string $charset オプションのパラメータ、初期値は null
 * @return void
 */
	public function charset($charset = null) {

		if (!$charset && Configure::read('BcRequest.agent') == 'mobile') {
			$charset = 'Shift-JIS';
		}
		echo $this->BcHtml->charset($charset);
	}

/**
 * コピーライト用の年を出力する
 *
 * @param integer $begin 開始年
 * @return void
 */
	public function copyYear($begin) {

		$year = date('Y');
		if ($begin == $year) {
			echo $year;
		} else {
			echo $begin . ' - ' . $year;
		}
	}

/**
 * ページ編集へのリンクを出力する
 *
 * @param string $id
 * @return void
 */
	public function setPageEditLink($id) {

		if (empty($this->request->params['admin']) && !empty($this->_View->viewVars['user']) && !Configure::read('BcRequest.agent')) {
			$this->_View->viewVars['editLink'] = array('admin' => true, 'controller' => 'pages', 'action' => 'edit', $id);
		}
	}

/**
 * 編集リンクを出力する
 *
 * @return void
 */
	public function editLink() {

		if ($this->existsEditLink()) {
			$this->link('編集する', $this->_View->viewVars['editLink'], array('class' => 'tool-menu'));
		}
	}

/**
 * 編集リンクが存在するかチェックする
 *
 * @return boolean
 */
	public function existsEditLink() {

		return ($this->_View->viewVars['authPrefix'] == 'admin' && !empty($this->_View->viewVars['editLink']));
	}

/**
 * 公開ページへのリンクを出力する
 *
 * @return void
 */
	public function publishLink() {

		if ($this->existsPublishLink()) {
			$this->link('公開ページ', $this->_View->viewVars['publishLink'], array('class' => 'tool-menu'));
		}
	}

/**
 * 公開ページへのリンクが存在するかチェックする
 *
 * @return boolean
 */
	public function existsPublishLink() {

		return ($this->_View->viewVars['authPrefix'] == Configure::read('Routing.prefixes.0') && !empty($this->_View->viewVars['publishLink']));
	}

/**
 * アップデート処理が必要かチェックする
 * 
 * @return boolean
 * @todo 別のヘルパに移動する
 */
	public function checkUpdate() {

		$baserVerpoint = verpoint($this->_View->viewVars['baserVersion']);
		if (isset($this->siteConfig['version'])) {
			$siteVerpoint = verpoint($this->siteConfig['version']);
		} else {
			$siteVerpoint = 0;
		}

		if (!$baserVerpoint === false || $siteVerpoint === false) {
			return false;
		} else {
			return ($baserVerpoint > $siteVerpoint);
		}
	}

/**
 * アップデート用のメッセージを出力する
 * 
 * @return void
 * @todo 別のヘルパに移動する
 */
	public function updateMessage() {
		$adminPrefix = Configure::read('Routing.prefixes.0');
		if ($this->checkUpdate() && $this->request->params['controller'] != 'updaters') {
			$updateLink = $this->BcHtml->link('ここ', "/{$adminPrefix}/updaters");
			echo '<div id="UpdateMessage">WEBサイトのアップデートが完了していません。' . $updateLink . ' からアップデートを完了させてください。</div>';
		}
	}

/**
 * コンテンツを特定するIDを出力する
 *
 * @param boolean $detail オプションのパラメータ、初期値は false 
 * @param array $options オプションのパラメータ、初期値は array()
 * @return void
 */
	public function contentsName($detail = false, $options = array()) {

		echo $this->getContentsName($detail, $options);
	}

/**
 * コンテンツを特定するIDを取得する
 * ・キャメルケースで取得
 * ・URLのコントローラー名までを取得
 * ・ページの場合は、カテゴリ名（カテゴリがない場合は Default）
 * ・トップページは、Home
 *
 * @param boolean $detail オプションのパラメータ、初期値は false
 * @param array $options オプションのパラメータ、初期値は array()
 * @return string 
 */
	public function getContentsName($detail = false, $options = array()) {

		$options = array_merge(array(
			'home' => 'Home',
			'default' => 'Default',
			'error' => 'Error',
			'underscore' => false), $options);

		extract($options);

		$prefix = '';
		$plugin = '';
		$controller = '';
		$action = '';
		$pass = '';
		$url0 = '';
		$url1 = '';
		$url2 = '';
		$aryUrl = array();

		if (!empty($this->request->params['prefix']) && Configure::read('BcRequest.agentPrefix') != $this->request->params['prefix']) {
			$prefix = h($this->request->params['prefix']);
		}
		if (!empty($this->request->params['plugin'])) {
			$plugin = h($this->request->params['plugin']);
		}
		$controller = h($this->request->params['controller']);
		if ($prefix) {
			$action = str_replace($prefix . '_', '', h($this->request->params['action']));
		} else {
			$action = h($this->request->params['action']);
		}
		if (!empty($this->request->params['pass'])) {
			foreach ($this->request->params['pass'] as $key => $value) {
				$pass[$key] = h($value);
			}
		}

		$url = explode('/', h($this->request->url));

		if (Configure::read('BcRequest.agent')) {
			array_shift($url);
		}

		if (isset($url[0])) {
			$url0 = $url[0];
		}
		if (isset($url[1])) {
			$url1 = $url[1];
		}
		if (isset($url[2])) {
			$url2 = $url[2];
		}

		// 固定ページの場合
		if ($controller == 'pages' && ($action == 'display' || $action == 'mobile_display' || $action == 'smartphone_display')) {

			if (strpos($pass[0], 'pages/') !== false) {
				$pageUrl = str_replace('pages/', '', $pass[0]);
			} else {
				if (empty($pass)) {
					$pageUrl = h($this->request->url);
				} else {
					$pageUrl = implode('/', $pass);
				}
			}
			if (preg_match('/\/$/', $pageUrl)) {
				$pageUrl .= 'index';
			}
			$pageUrl = preg_replace('/\.html$/', '', $pageUrl);
			$pageUrl = preg_replace('/^\//', '', $pageUrl);
			$aryUrl = explode('/', $pageUrl);
		} else {

			// プラグインルーティングの場合
			if ((($url1 == '' && $action == 'index') || ($url1 == $action)) && $url2 != $action && $plugin) {
				$plugin = '';
				$controller = $url0;
			}

			if ($plugin) {
				$controller = $plugin . '_' . $controller;
			}
			if ($prefix) {
				$controller = $prefix . '_' . $controller;
			}
			if ($controller) {
				$aryUrl[] = $controller;
			}
			if ($action) {
				$aryUrl[] = $action;
			}
			if ($pass) {
				$aryUrl = $aryUrl + $pass;
			}
		}

		if ($this->_View->name == 'CakeError') {

			$contentsName = $error;
		} elseif (count($aryUrl) >= 2) {

			if (!$detail) {
				$contentsName = $aryUrl[0];
			} else {
				$contentsName = implode('_', $aryUrl);
			}
		} elseif (count($aryUrl) == 1 && $aryUrl[0] == 'index') {

			$contentsName = $home;
		} else {
			if (!$detail) {
				$contentsName = $default;
			} else {
				$contentsName = $aryUrl[0];
			}
		}

		if ($underscore) {
			$contentsName = Inflector::underscore($contentsName);
		} else {
			$contentsName = Inflector::camelize($contentsName);
		}

		return $contentsName;
	}

/**
 * パンくずリストを出力する
 * 
 * 事前に BcBaserHelper::addCrumb() にて、パンくず情報を追加しておく必要がある。
 * また、アクセス制限がかかっているリンクはテキストのみ表示する
 *
 * @param string $separator パンくずの区切り文字
 * @param string $startText トップページを先頭に追加する場合にはトップページのテキストを指定する
 */
	public function crumbs($separator = '&raquo;', $startText = false) {

		$crumbs = $this->BcHtml->getStripCrumbs();
		if (!empty($crumbs)) {
			$out = array();
			if ($startText) {
				$out[] = $this->getLink($startText, '/');
			}
			foreach ($crumbs as $crumb) {
				if (!empty($crumb[1])) {
					$out[] = $this->getLink($crumb[0], $crumb[1], $crumb[2]);
				} else {
					$out[] = $crumb[0];
				}
			}
			echo $this->output(implode($separator, $out));
		}
	}

/**
 * パンくずリストの要素を追加する
 * 
 * デフォルトでアクセス制限がかかっているリンクの場合でもタイトルを表示する
 * $options の forceTitle キー に false を指定する事で表示しない設定も可能
 *
 * @param string $name パンくず用のテキスト
 * @param string $link パンくず用のリンク（指定しない場合はリンクは設定しない）
 * @param mixed $options リンクタグ用の属性
 * @return void
 */
	public function addCrumb($name, $link = null, $options = array()) {

		$options = array_merge(array(
			'forceTitle' => true
		), $options);
		$this->BcHtml->addCrumb($name, $link, $options);
		
	}

/**
 * ページ機能で作成したページの一覧データを取得する
 *
 * @param string $categoryId オプションのパラメータ、初期値は null
 * @return mixed boolean / array
 */
	public function getPageList($categoryId = null) {

		if ($this->_Page) {
			$conditions = array('Page.status' => 1);
			if ($categoryId) {
				$conditions['Page.page_category_id'] = $categoryId;
			}
			$this->_Page->unbindModel(array('belongsTo' => array('PageCategory')));
			$pages = $this->_Page->find('all', array('conditions' => $conditions,
				'fields' => array('title', 'url'),
				'order' => 'Page.sort'));
			return Hash::extract($pages, '{n}.Page');
		} else {
			return false;
		}
	}

/**
 *
 */

/**
 * ブラウザにキャッシュさせる為のヘッダーを出力する
 *
 * @param numeric $expire キャッシュの有効時間
 * @param string $type どのタイプ(拡張子)に対してのキャッシュか オプションのパラメータ、初期値は 'html'
 * @return void
 */
	public function cacheHeader($expire = null, $type = 'html') {

		$contentType = array(
			'html' => 'text/html',
			'js' => 'text/javascript', 'css' => 'text/css',
			'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png'
		);
		$fileModified = filemtime(WWW_ROOT . 'index.php');

		if (!$expire) {
			$expire = Configure::read('BcCache.duration');
		}
		if (!is_numeric($expire)) {
			$expire = strtotime($expire);
		}

		header("Date: " . date("D, j M Y G:i:s ", $fileModified) . 'GMT');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $fileModified) . " GMT");
		header('Content-type: ' . $contentType[$type]);
		header("Expires: " . gmdate("D, j M Y H:i:s", time() + $expire) . " GMT");
		header('Cache-Control: max-age=' . $expire);
		// Firefoxの場合は不要↓
		//header("Cache-Control: cache");
		header("Pragma: cache");
	}

/**
 * httpから始まるURLを取得する
 *
 * @param mixed $url
 * @param boolean $sessionId オプションのパラメータ、初期値は true 
 * @return string
 */
	public function getUri($url, $sessionId = true) {
		if (is_string($url) && preg_match('/^http/is', $url)) {
			return $url;
		} else {
			if (empty($_SERVER['HTTPS'])) {
				$protocol = 'http';
			} else {
				$protocol = 'https';
			}
			return $protocol . '://' . $_SERVER['HTTP_HOST'] . $this->getUrl($url, false, $sessionId);
		}
	}

/**
 * プラグインのBaserヘルパを初期化する
 * 
 * BaserHelperに定義されていないメソッドをプラグイン内のヘルパに定義する事で
 * BaserHelperから呼び出せるようになる仕組みを提供する。
 * コアからプラグインのヘルパメソッドをBaserHelper経由で直接呼び出せる為、
 * コア側のコントローラーでいちいちヘルパの定義をしなくてよくなり、
 * プラグインを導入しただけでテンプレート上でプラグインのメソッドが呼び出せるようになる。
 * 例えばページ機能のWISIWIG内でプラグインのメソッドを書き込む事ができる。
 *
 * プラグインのBaserヘルパの命名規則：{プラグイン名}BaserHelper
 * （呼びだし方）$this->BcBaser->feed(1);
 *
 * @return void
 */
	protected function _initPluginBasers() {

		$view = $this->_View;
		$plugins = Configure::read('BcStatus.enablePlugins');

		if (!$plugins) {
			return;
		}

		$pluginBasers = array();
		foreach ($plugins as $plugin) {
			$pluginName = Inflector::camelize($plugin);
			if (App::import('Helper', $pluginName . '.' . $pluginName . 'Baser')) {
				$pluginBasers[] = $pluginName . 'BaserHelper';
			}
		}
		$vars = array(
			'base', 'webroot', 'here', 'params', 'action', 'data', 'themeWeb', 'plugin'
		);
		$c = count($vars);
		foreach ($pluginBasers as $key => $pluginBaser) {
			$this->_pluginBasers[$key] = new $pluginBaser($view);
			for ($j = 0; $j < $c; $j++) {
				if (isset($view->{$vars[$j]})) {
					$this->_pluginBasers[$key]->{$vars[$j]} = $view->{$vars[$j]};
				}
			}
		}
	}

/**
 * プラグインBaserヘルパ用マジックメソッド
 * Baserヘルパに存在しないメソッドが呼ばれた際プラグインのBaserヘルパを呼び出す
 * call__ から __call へメソット名を変更、Helper.php の __call をオーバーライト
 *
 * @param string $method
 * @param array $params
 * @return mixed
 */
	public function __call($method, $params) {

		foreach ($this->_pluginBasers as $pluginBaser) {
			if (method_exists($pluginBaser, $method)) {
				return call_user_func_array(array($pluginBaser, $method), $params);
			}
		}
	}

/**
 * 文字列を検索しマークとしてタグをつける
 *
 * @param string $search 検索文字列
 * @param string $text 検索対象文字列
 * @param string $name マーク用タグ
 * @param array $attributes タグの属性
 * @param boolean $escape エスケープ有無
 * @return string $text 変換後文字列
 */
	public function mark($search, $text, $name = 'strong', $attributes = array(), $escape = false) {

		if (!is_array($search)) {
			$search = array($search);
		}
		foreach ($search as $value) {
			$text = str_replace($value, $this->BcHtml->tag($name, $value, $attributes, $escape), $text);
		}
		return $text;
	}

/**
 * サイトマップを出力する
 *
 * @param mixid $pageCategoryId / '' / 0
 * @param string $recursive
 * @return void
 */
	public function sitemap($pageCategoryId = null, $recursive = null) {

		$pageList = $this->requestAction('/contents/get_page_list_recursive', array('pass' => array($pageCategoryId, $recursive)));
		$params = array('pageList' => $pageList);
		if (empty($_SESSION['Auth']['User'])) {
			$params = am($params, array(
				'cache' => array(
					'time' => Configure::read('BcCache.duration'),
					'key' => $pageCategoryId))
			);
		}

		$this->element('sitemap', $params);
	}

/**
 * Flashを表示する
 *
 * @param string $path
 * @param string $id
 * @param int $width
 * @param int $height
 * @param array $options オプションのパラメータ、初期値は array()
 * @return string
 */
	public function swf($path, $id, $width, $height, $options = array()) {

		$options = array_merge(array(
			'version' => 7,
			'script' => 'admin/swfobject-2.2',
			'noflash' => '&nbsp;'
			), $options);
		extract($options);

		if (!preg_match('/\.swf$/', $path)) {
			$path .= '.swf';
		}

		if (is_array($path)) {
			$path = $this->getUrl($path);
		} elseif (strpos($path, '://') === false) {
			if ($path[0] !== '/') {
				$path = Configure::read('App.imageBaseUrl') . $path;
			}
			$path = $this->webroot($path);
		}
		$out = $this->js($script, true) . "\n";
		$out = <<< END_FLASH
<div id="{$id}">{$noflash}</div>
<script type="text/javascript">
	swfobject.embedSWF("{$path}", "{$id}", "{$width}", "{$height}", "{$version}");
</script>
END_FLASH;

		echo $out;
	}

/**
 * URLをリンクとして利用可能なURLに変換する
 * ページの確認用URL取得に利用する
 * /smartphone/about → /s/about
 *
 * @param string $url
 * @param string $type mobile / smartphone
 * @return string URL
 */
	public function changePrefixToAlias($url, $type) {
		$alias = Configure::read("BcAgent.{$type}.alias");
		$prefix = Configure::read("BcAgent.{$type}.prefix");
		return preg_replace('/^\/' . $prefix . '\//is', '/' . $alias . '/', $url);
	}

/**
 * 現在のログインユーザーが管理者グループかどうかチェックする
 *
 * @return boolean
 */
	public function isAdminUser($id = null) {
		if (!$id && !empty($this->_View->viewVars['user']['user_group_id'])) {
			$id = $this->_View->viewVars['user']['user_group_id'];
		}
		if ($id == 1) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 現在のページが固定ページかどうかを判定する
 *
 * @return boolean
 */
	public function isPage() {
		return $this->_Page->isPageUrl($this->getHere());
	}

/**
 * 現在のページの純粋なURLを取得する
 * スマートURLかどうか、サブフォルダかどうかに依存しないスラッシュから始まるURL
 *
 * @return string
 */
	public function getHere() {
		return '/' . preg_replace('/^\//', '', $this->request->url);
	}

/**
 * 現在のページがページカテゴリのトップかどうかを判定する
 *
 * @return boolean
 * @manual
 */
	public function isCategoryTop() {

		$url = $this->getHere();
		$url = preg_replace('/^\//', '', $url);
		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		if (preg_match('/\/index$/', $url)) {
			$param = explode('/', $url);
			if (count($param) >= 2) {
				return true;
			}
		}
		return false;
	}

/**
 * ページをエレメントとして読み込む
 *
 * ※ レイアウトは読み込まない
 * @param string $url
 * @param array $params オプションのパラメータ、初期値は array()
 * @param array $options オプションのパラメータ、初期値は array()
 * @return void
 */
	public function page($url, $params = array(), $options = array()) {

		if (isset($this->_View->viewVars['pageRecursive']) && !$this->_View->viewVars['pageRecursive']) {
			return;
		}

		$options = array_merge(array(
			'loadHelpers' => false,
			'subDir' => true,
			'recursive' => true
			), $options);

		extract($options);

		$this->_View->viewVars['pageRecursive'] = $recursive;

		// 現在のページの情報を退避
		$editLink = null;
		$description = $this->getDescription();
		$title = $this->getContentsTitle();
		if (!empty($this->_View->viewVars['editLink'])) {
			$editLink = $this->_View->viewVars['editLink'];
		}

		// urlを取得
		if (empty($this->_View->subDir)) {
			$url = '/../Pages' . $url;
		} else {
			$dirArr = explode('/', $this->_View->subDir);
			$url = str_repeat('/..', count($dirArr)) . '/../Pages' . $url;
		}

		$this->element($url, $params, array('subDir' => $subDir));

		// 現在のページの情報に戻す
		$this->setDescription($description);
		$this->setTitle($title);
		if ($editLink) {
			$this->_View->viewVars['editLink'] = $editLink;
		}
	}

/**
 * ウィジェットエリアを出力する
 * 
 * @param int $no
 * @param array $options
 */
	public function widgetArea($no = null, $options = array()) {

		$options = array_merge(array(
			'loadHelpers' => false,
			'subDir' => true,
			), $options);

		extract($options);

		if (!$no && isset($this->_View->viewVars['widgetArea'])) {
			$no = $this->_View->viewVars['widgetArea'];
		}
		if ($no) {
			$this->element('widget_area', array('no' => $no, 'subDir' => $subDir), array('subDir' => $subDir));
		}
	}

/**
 * 指定したURLが現在のURLかどうか判定する
 * 
 * @param string $url
 * @return boolean 
 */
	public function isCurrentUrl($url) {
		$pattern = '/\/$|\/index$/';
		$shortenedUrl = preg_replace($pattern, '', $this->getUrl($url));
		$shortenedHere = preg_replace($pattern, '', $this->request->here);
		return ($shortenedUrl === $shortenedHere);
	}

/**
 * ユーザー名を整形して表示する
 * 
 * @param array $user
 * @return string $userName
 */
	public function getUserName($user) {
		if (isset($user['User'])) {
			$user = $user['User'];
		}

		if (!empty($user['nickname'])) {
			$userName = $user['nickname'];
		} else {
			$userName = array();
			if (!empty($user['real_name_1'])) {
				$userName[] = $user['real_name_1'];
			}
			if (!empty($user['real_name_2'])) {
				$userName[] = $user['real_name_2'];
			}
			$userName = implode(' ', $userName);
		}
		return $userName;
	}

/**
 * コアテンプレートを読み込む
 * 
 * @param string $name
 * @param array $data オプションのパラメータ、初期値は array()
 * @param array $options オプションのパラメータ、初期値は array()
 */
	public function includeCore($name, $data = array(), $options = array()) {

		$plugin = '';
		if (strpos($name, '.') !== false) {
			list($plugin, $name) = explode('.', $name);
			$plugin = Inflector::camelize($plugin);
			$name = '../../../lib/Baser/Plugin/' . $plugin . '/View/' . $name;
		} else {
			$name = '../../../lib/Baser/View/' . $name;
		}

		$options = array_merge($options, array('subDir' => false));
		$this->element($name, $data, $options);
	}

/**
 * ロゴを出力する
 * 
 * 《options》
 * _getThemeImage() を参照
 * 
 * @param array $options オプションのパラメータ、初期値は array()
 * @return void
 */
	public function logo($options = array()) {
		echo $this->_getThemeImage('logo', $options);
	}

/**
 * メインイメージを出力する
 * 
 * 《options》
 * - `all`: 全ての画像を出力する。
 * - `num`: 指定した番号の画像を出力する。all を true とした場合は、出力する枚数となる。
 * - `id` : all を true とした場合、UL タグの id 属性を指定できる。
 * 
 * @param array $options
 * @return void
 */
	public function mainImage($options = array()) {

		$options = array_merge(array(
			'num' => 1,
			'all' => false,
			'id' => 'MainImage'
			), $options);
		if ($options['all']) {
			$id = $options['id'];
			$num = $options['num'];
			unset($options['all']);
			unset($options['id']);
			$tag = '';
			for ($i = 1; $i <= $num; $i++) {
				$options['num'] = $i;
				$themeImage = $this->_getThemeImage('main_image', $options);
				if($themeImage) {
					$tag .= '<li>' . $themeImage . '</li>' . "\n";
				}
			}
			echo '<ul id="' . $id . '">' . "\n" . $tag . "\n" . '</ul>';
		} else {
			echo $this->_getThemeImage('main_image', $options);
		}
	}

/**
 * テーマ画像を取得する
 * 
 * 《options》
 * - `thumb`: サムネイルを取得する
 * - `class`: 画像に設定する class 属性
 * - `popup`: ポップアップリンクを指定
 * - `alt`	: 画像に設定する alt 属性。リンクの title 属性にも設定される。
 * - `link`	: リンク先URL。popup を true とした場合、オリジナルの画像へのリンクとなる。
 * - `maxWidth : 最大横幅
 * - `maxHeight: 最大高さ
 * 
 * @param string $name
 * @param array $options オプションのパラメータ、初期値は array()
 * @return string $tag
 */
	public function _getThemeImage($name, $options = array()) {

		$ThemeConfig = ClassRegistry::init('ThemeConfig');
		$data = $ThemeConfig->findExpanded();

		$url = $imgPath = $uploadUrl = $uploadThumbUrl = $originUrl = '';
		$thumbSuffix = '_thumb';
		$dir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
		$themeDir = $path = getViewPath() . 'img' . DS;
		$num = '';
		if (!empty($options['num'])) {
			$num = '_' . $options['num'];
		}
		$options = array_merge(array(
			'thumb' => false,
			'class' => '',
			'popup' => false,
			'alt' => $data[$name . '_alt' . $num],
			'link' => $data[$name . '_link' . $num],
			'maxWidth' => '',
			'maxHeight' => '',
			'width' => '',
			'height' => ''
			), $options);
		$name = $name . $num;

		if ($data[$name]) {
			$pathinfo = pathinfo($data[$name]);
			$uploadPath = $dir . $data[$name];
			$uploadThumbPath = $dir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension'];
			$uploadUrl = '/files/theme_configs/' . $data[$name];
			$uploadThumbUrl = '/files/theme_configs/' . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension'];
		}

		if ($data[$name]) {
			if (!$options['thumb']) {
				if (file_exists($uploadPath)) {
					$imgPath = $uploadPath;
					$url = $uploadUrl;
				}
			} else {
				if (file_exists($uploadThumbPath)) {
					$imgPath = $uploadThumbPath;
					$url = $uploadThumbUrl;
				}
			}
			$originUrl = $uploadUrl;
		}

		if (!$url) {
			$exts = array('png', 'jpg', 'gif');
			foreach ($exts as $ext) {
				if (file_exists($themeDir . $name . '.' . $ext)) {
					$url = '/theme/' . $this->siteConfig['theme'] . '/img/' . $name . '.' . $ext;
					$imgPath = $themeDir . $name . '.' . $ext;
					$originUrl = $url;
				}
			}
		}

		if (!$url) {
			return;
		}

		$imgOptions = array();
		if ($options['class']) {
			$imgOptions['class'] = $options['class'];
		}
		if ($options['alt']) {
			$imgOptions['alt'] = $options['alt'];
		}
		if ($options['maxWidth'] || $options['maxHeight']) {
			$imginfo = getimagesize($imgPath);
			$widthRate = $heightRate = 0;
			if ($options['maxWidth']) {
				$widthRate = $imginfo[0] / $options['maxWidth'];
			}
			if ($options['maxHeight']) {
				$heightRate = $imginfo[1] / $options['maxHeight'];
			}
			if ($widthRate > $heightRate) {
				if ($options['maxWidth'] && $imginfo[0] > $options['maxWidth']) {
					$imgOptions['width'] = $options['maxWidth'];
				}
			} else {
				if ($options['maxHeight'] && ($imginfo[1] > $options['maxHeight'])) {
					$imgOptions['height'] = $options['maxHeight'];
				}
			}
		}
		if ($options['width']) {
			$imgOptions['width'] = $options['width'];
		}
		if ($options['height']) {
			$imgOptions['height'] = $options['height'];
		}

		$tag = $this->getImg($url, $imgOptions);
		if ($options['link'] || $options['popup']) {
			$linkOptions = array();
			if ($options['popup']) {
				$linkOptions['rel'] = 'colorbox';
				$link = $originUrl;
			} elseif ($options['link']) {
				$link = $options['link'];
			}
			if ($options['alt']) {
				$linkOptions['title'] = $options['alt'];
			}
			$tag = $this->getLink($tag, $link, $linkOptions);
		}
		return $tag;
	}
	
/**
 * テーマのURLを取得する
 * 
 * @return string
 */
	public function getThemeUrl() {
		return $this->webroot . 'theme' . '/' . $this->siteConfig['theme'] . '/';
	}
	
/**
 * テーマのURLを出力する
 * 
 * @return void
 */
	public function themeUrl() {
		echo $this->getThemeUrl();
	}

/**
 * ベースとなるURLを取得する
 * サブフォルダやスマートURLについて考慮されている事が前提
 * 
 * @return string
 */
	public function getBaseUrl() {
		return $this->base . '/';
	}
	
/**
 * ベースとなるURLを出力する
 * サブフォルダやスマートURLについて考慮されている事が前提
 * 
 * @return void
 */
	public function baseUrl() {
		echo $this->getBaseUrl();
	}
	
/**
 * サブメニューを出力する
 * 
 * @return void
 */
	public function subMenu($data = array(), $options = array()) {
		if (!$this->_View->getVar('subMenuElements')) {
			return;
		}
		$this->element('sub_menu', $data, $options);
	}
	
/**
 * コンテンツナビを出力する
 * 
 * @return void
 */
	public function contentsNavi($data = array(), $options = array()) {
		if (!isset($this->_View->BcPage) || !$this->_View->BcPage->contentsNaviAvailable()) {
			return;
		}
		$this->element('contents_navi', $data, $options);
	}
	
/**
 * パンくずリストを出力する
 * 
 * @return void
 */
	public function crumbsList($data = array(), $options = array()) {
		$this->element('crumbs', $data, $options);
	}

/**
 * グローバルメニューを出力する
 * 
 * @return void
 */
	public function globalMenu($data = array(), $options = array()) {
		$this->element('global_menu', $data, $options);
	}
	
/**
 * Google Analytics のトラッキングコードを出力する
 * 
 * @return void
 */
	public function googleAnalytics($data = array(), $options = array()) {
		$this->element('google_analytics', $data, $options);
	}
	
/**
 * Google Maps を出力する
 * 
 * @return void
 */
	public function googleMaps($data = array(), $options = array()) {
		$this->element('google_maps', $data, $options);
	}
	
/**
 * 表示件数設定機能を出力する
 * 
 * @return void
 */
	public function listNum($data = array(), $options = array()) {
		$this->element('list_num', $data, $options);
	}
	
/**
 * サイト内検索フォームを出力
 * 
 * @return void
 */
	public function siteSearchForm($data = array(), $options = array()) {
		$this->element('site_search_form', $data, $options);
	}
}
