<?php
/* SVN FILE: $Id$ */
/**
 * Baserヘルパー
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
 * @package			cake
 * @subpackage		baser.app.view.helpers
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
 * Baserヘルパー
 *
 * @package			cake
 * @subpackage		baser.app.views.helpers
 */
class BaserHelper extends AppHelper {
	var $_view = null;
	var $siteConfig = array();
	var $helpers = array('Html','Javascript','Session','XmlEx');
	var $_content = null;			// コンテンツ
	var $_categoryTitleOn = true;
	var $_categoryTitle = null;
	var $Page = null;
	var $Permission = null;
/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	function __construct() {
		$this->_view =& ClassRegistry::getObject('view');
		// エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
		$cn = ConnectionManager::getInstance();
		if(file_exists(CONFIGS.'database.php') && !empty($cn->config->baser['driver'])) {
			$db =& ConnectionManager::getDataSource('baser');
			if ($db->isInterfaceSupported('listSources')) {
				$sources = $db->listSources();

				if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'permissions'), array_map('strtolower', $sources))) {
					if (ClassRegistry::isKeySet('Permission')) {
						$this->Permission = ClassRegistry::getObject('Permission');
					}else {
						$this->Permission = ClassRegistry::init('Permission');
					}
				}

				if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'pages'), array_map('strtolower', $sources))) {
					if (ClassRegistry::isKeySet('Page')) {
						$this->Page = ClassRegistry::getObject('Page');
					}else {
						$this->Page = ClassRegistry::init('Page');
					}
				}

				if(isset($this->_view->viewVars['siteConfig'])){
					$this->siteConfig = $this->_view->viewVars['siteConfig'];
				}
				
			}
		}
	}
/**
 * afterRender
 */
	function afterRender() {
		parent::afterRender();
		// コンテンツをフックする
		$this->_content = ob_get_contents();
	}
/**
 * グローバルメニューを取得する
 * @param string $menuType
 * @return array $globalMenus
 * @access public
 */
	function getGlobalMenus ($menuType = null) {

		if(!$menuType) {
			$menuType = 'default';
		}
		if (ClassRegistry::isKeySet('GlobalMenu')) {
			if(!file_exists(CONFIGS.'database.php')) {
				return '';
			}
			$dbConfig = new DATABASE_CONFIG();
			if(!$dbConfig->baser) {
				return '';
			}
			$GlobalMenu = ClassRegistry::getObject('GlobalMenu');
			// エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
			$db =& ConnectionManager::getDataSource('baser');
			if ($db->isInterfaceSupported('listSources')) {
				$sources = $db->listSources();
				if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'global_menus'), array_map('strtolower', $sources))) {
					if (empty($this->params['prefix'])) {
						$prefix = 'publish';
					} else {
						$prefix = $this->params['prefix'];
					}
					return $GlobalMenu->find('all',array('conditions'=>array('menu_type'=>$menuType),'order'=>'sort'));
				}
			}
		}
		return '';
	}
/**
 * タイトルをセットする
 * @param string $title
 * @access public
 */
	function setTitle($title,$categoryTitleOn = null) {
		if(!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}
		$this->_view->set('title',$title);
	}
/**
 * キーワードをセットする
 * @param string $title
 * @access public
 */
	function setKeywords($keywords) {
		$this->_view->set('keywords',$keywords);
	}
/**
 * 説明文をセットする
 * @param string $title
 * @access public
 */
	function setDescription($description) {
		$this->_view->set('description',$description);
	}
/**
 * レイアウト用の変数をセットする
 * $view->set のラッパー
 * @param string $title
 * @access public
 */
	function set($key,$value) {
		$this->_view->set($key,$value);
	}
/**
 * タイトルへのカテゴリタイトル表示を設定
 * コンテンツごとの個別設定
 * @param mixed $on	boolean / 文字列（カテゴリ名として出力した文字を指定する）
 */
	function setCategoryTitle($on = true) {
		$this->_categoryTitle = $on;
	}
/**
 * キーワードを取得する
 * @return string $keyword
 * @access public
 */
	function getKeywords() {
		$keywords = '';
		if(!empty($this->_view->viewVars['keywords'])) {
			$keywords = $this->_view->viewVars['keywords'];
		}elseif(!empty($this->siteConfig['keyword'])) {
			$keywords = $this->siteConfig['keyword'];
		}
		return $keywords;
	}
/**
 * 説明文を取得する
 * @return string $description
 * @access public
 */
	function getDescription() {
		$description = '';
		if(!empty($this->_view->viewVars['description'])) {
			$description = $this->_view->viewVars['description'];
		}elseif(!empty($this->siteConfig['description'])) {
			$description = $this->siteConfig['description'];
		}
		return $description;
	}
/**
 * タイトルを取得する
 * @return string $description
 * @access public
 */
	function getTitle($separator='｜',$categoryTitleOn = null) {

		// ページコントローラーでタイトルが指定されてない場合はページタイトルを出力しない
		if(strpos($this->_view->pageTitle,'.html') !== false) {
			$title = '';
		}else {
			$title = $this->_view->pageTitle;
		}

		// ページカテゴリを追加
		if(!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}
		if(!empty($this->_view->viewVars['subpage']) && $this->_categoryTitleOn && $this->_categoryTitle) {
			if($this->_categoryTitle === true) {
				$PageCategory =& ClassRegistry::getObject('PageCategory','Model');
				$categoryName = $PageCategory->field('title',array('name'=>$this->_view->viewVars['page']));
				$title .= $separator.$categoryName;
			}else {
				$title .= $separator.$this->_categoryTitle;
			}
		}

		// サイトタイトルを追加
		if ($title && !empty($this->siteConfig['name'])) {
			$title .= $separator;
		}
		if(!empty($this->siteConfig['name'])) {
			$title .= $this->siteConfig['name'];
		}

		return $title;

	}
/**
 * コンテンツタイトルを取得する
 * @return string $description
 * @access public
 */
	function getContentsTitle() {

		$contentsTitle = '';
		// トップページの場合は、タイトルをサイト名だけにする
		if (!empty($this->_view->viewVars['contentsTitle'])) {
			$contentsTitle = $this->_view->viewVars['contentsTitle'];
		}elseif($this->params['url']['url'] == '/' || $this->params['url']['url'] == '/pages/index.html') {
			if(!empty($this->siteConfig['name'])) {
				$contentsTitle = $this->siteConfig['name'];
			}
		}elseif($this->_view->pageTitle) {
			$contentsTitle = $this->_view->pageTitle;
		}

		if ($this->_view->name != 'CakeError' && !empty($contentsTitle)) {
			return $contentsTitle;
		}

	}
/**
 * コンテンツタイトルを出力する
 * @access public
 */
	function contentsTitle() {
		echo $this->getContentsTitle();
	}
/**
 * タイトルを出力する
 * @access public
 */
	function title($separator='｜',$categoryTitleOn = null) {
		echo '<title>'.$this->getTitle($separator,$categoryTitleOn).'</title>';
	}
/**
 * メタキーワードタグを出力する
 * @access public
 */
	function metaKeywords() {
		echo $this->Html->meta('keywords',$this->getkeywords());
	}
/**
 * メタディスクリプションを出力する
 * @access public
 */
	function metaDescription() {
		echo $this->Html->meta('description',$this->getDescription());
	}
/**
 * トップページかどうか判断する
 * @return boolean
 */
	function isTop() {
		return ($this->params['url']['url'] == '/' ||
						$this->params['url']['url'] == 'index.html' ||
						$this->params['url']['url'] == Configure::read('Mobile.prefix').'/' ||
						$this->params['url']['url'] == Configure::read('Mobile.prefix').'/pages/index.html');
	}
/**
 * webrootを出力する為だけのラッパー
 * @return void
 */
	function root() {
		echo $this->getRoot();
	}
/**
 * webrootを取得する為だけのラッパー
 * @return string
 */
	function getRoot() {
		return $this->base.'/';
	}
/**
 * ベースを考慮したURLを出力
 * @param string $url
 * @param boolean $full
 */
	function url($url,$full = false) {
		echo $this->getUrl($url,$full);
	}
/**
 * ベースを考慮したURLを取得
 * @param string $url
 * @param boolean $full
 */
	function getUrl($url,$full = false) {
		return parent::url($url,$full);
	}
/**
 * エレメントを取得する
 * View::elementを取得するだけのラッパー
 * @param string $name
 * @param array $params
 * @param boolean $loadHelpers
 * @return string
 */
	function getElement($name, $params = array(), $loadHelpers = false, $subDir = true) {

		if(!empty($this->_view->subDir) && $subDir) {
			$name = $this->_view->subDir.DS.$name;
		}
		return $this->_view->element($name, $params, $loadHelpers);
	}
/**
 * エレメントを出力する
 * View::elementを出力するだけのラッパー
 * @param string $name
 * @param array $params
 * @param boolean $loadHelpers
 * @return void
 */
	function element($name, $params = array(), $loadHelpers = false, $subDir = true) {
		echo $this->getElement($name, $params, $loadHelpers, $subDir);
	}
/**
 * ページネーションを出力する
 * @param string $name
 * @param array $params
 * @param boolean $loadHelpers
 * @return <type>
 */
	function pagination($name = 'default', $params = array(), $loadHelpers = false, $subDir = true) {
		if(!$name) {
			$name = 'default';
		}
		$file = 'paginations'.DS.$name;
		echo $this->getElement($file,$params,$loadHelpers, $subDir);
	}
/**
 * コンテンツを出力する
 * $content_for_layout を出力するだけのラッパー
 * @return void
 */
	function content() {
		echo $this->_content;
	}
/**
 * セッションメッセージをフラッシュするだけのラッパー
 * @return void
 */
	function flash() {
		if ($this->Session->check('Message.flash')) {
			$this->Session->flash();
		}
	}
/**
 * スクリプトを出力する
 * $scripts_for_layout を出力するだけのラッパー
 * @return void
 */
	function scripts() {
		echo join("\n\t", $this->_view->__scripts);
	}
/**
 * サブメニューをセットする
 * @param array $submenus
 * @access public
 */
	function setSubMenus($submenus) {
		$this->_view->set('subMenuElements',$submenus);
	}
/**
 * XMLヘッダを出力する
 */
	function xmlHeader() {
		echo $this->XmlEx->header();
	}
/**
 * アイコンタグを出力するだけのラッパー
 */
	function icon() {
		echo  $this->Html->meta('icon');
	}
/**
 * DOC TYPE を出力するだけのラッパー
 */
	function docType($type = 'xhtml-trans') {
		echo $this->Html->docType($type);
	}
/**
 * CSSタグを出力するだけのラッパー
 */
	function css($path, $rel = null, $htmlAttributes = array(), $inline = true) {
		echo $this->Html->css($path, $rel, $htmlAttributes, $inline);
	}
/**
 * Javascriptのlinkタグを出力するだけのラッパー
 */
	function js($url, $inline = true) {
		echo $this->Javascript->link($url, $inline);
	}
/**
 * imageタグを出力するだけのラッパー
 */
	function img($path, $options = array()) {
		echo $this->getImg($path, $options);
	}
/**
 * imageタグを取得するだけのラッパー
 */
	function getImg($path, $options = array()) {
		return $this->Html->image($path, $options);
	}
/**
 * aタグを表示するだけのラッパー関数
 */
	function link($title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = false) {
		echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
	}
/**
 * aタグを取得するだけのラッパー
 */
	function getLink($title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = false) {
		if(!empty($htmlAttributes['prefix'])) {
			if(!empty($this->params['prefix'])) {
				$url[$this->params['prefix']] = true;
			}
			unset($htmlAttributes['prefix']);
		}
		if(isset($htmlAttributes['forceTitle'])) {
			$forceTitle = $htmlAttributes['forceTitle'];
			unset($htmlAttributes['forceTitle']);
		}else {
			$forceTitle = false;
		}
		
		$_url = str_replace($this->base, '', $this->getUrl($url));
		$enabled = true;

		// 認証チェック
		if(isset($this->Permission) && !empty($this->_view->viewVars['user']['user_group_id'])) {
			$userGroupId = $this->_view->viewVars['user']['user_group_id'];
			if(!$this->Permission->check($_url,$userGroupId)) {
				$enabled = false;
			}
		}

		// ページ公開チェック
		if(isset($this->Page)){
			if(!$this->Page->checkPublish($_url)){
				$enabled = false;
			}
		}
		
		if(!$enabled){
			if($forceTitle) {
				return "<span>$title</span>";
			}else {
				return '';
			}
		}
		
		return $this->Html->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);

	}
/**
 * charsetを出力するだけのラッパー
 */
	function charset($charset = null) {
		echo $this->Html->charset($charset);
	}
/**
 * コピーライト用の年を出力する
 * @param string 開始年
 */
	function copyYear($begin) {
		$year = date('Y');
		if($begin == $year) {
			echo $year;
		}else {
			echo $begin.' - '.$year;
		}
	}
/**
 * ページ編集へのリンクを出力する
 * @param string $id
 * @return void
 */
	function editPage($id) {
		if(!empty($this->_view->viewVars['user']) && !Configure::read('Mobile.on')) {
			echo '<div class="edit-link">'.$this->getLink('≫ 編集する',array('admin'=>true,'controller'=>'pages','action'=>'edit',$id),array('target'=>'_blank')).'</div>';
		}
	}
/**
 * アップデート処理が必要かチェックする
 * @return boolean
 */
	function checkUpdate() {
		$baserRev = revision($this->_view->viewVars['baserVersion']);
		if(isset($this->siteConfig['version'])) {
			$siteRev = revision($this->siteConfig['version']);
		}else {
			$siteRev = 0;
		}
		return ($baserRev > $siteRev);
	}
/**
 * アップデート用のメッセージを出力する
 * @return void
 */
	function updateMessage() {
		if($this->checkUpdate()) {
			$updateLink = $this->Html->link('ここ','/installations/update');
			echo '<div id="UpdateMessage">WEBサイトのアップデートが完了していません。'.$updateLink.' からアップデートを完了させてください。</div>';
		}
	}
/**
 * コンテンツ名を出力する
 * @return void
 */
	function contentsName($detail = false) {
		echo $this->getContentsName($detail);
	}
/**
 * コンテンツ名を取得する
 * ・キャメルケースで取得
 * ・URLのコントローラー名までを取得
 * ・ページの場合は、カテゴリ名（カテゴリがない場合はdefault）
 * @return string
 */
	function getContentsName($detail = false) {

		$prefix = '';
		$plugin = '';
		$controller = '';
		$action = '';
		$pass = '';
		$url0 = '';
		$url1 = '';
		$url2 = '';

		if(!empty($this->params['prefix'])) {
			$prefix = $this->params['prefix'];
		}
		if(!empty($this->params['plugin'])) {
			$plugin = $this->params['plugin'];
		}
		$controller = $this->params['controller'];
		if($prefix) {
			$action = str_replace($prefix.'_','',$this->params['action']);
		}else {
			$action = $this->params['action'];
		}
		if(!empty($this->params['pass'][0])) {
			$pass = $this->params['pass'];
		}
		$url = split('/',$this->params['url']['url']);
		if(isset($url[0])) {
			$url0 = $url[0];
		}
		if(isset($url[1])) {
			$url1 = $url[1];
		}
		if(isset($url[2])) {
			$url2 = $url[2];
		}

		// ページ機能の場合
		if($controller=='pages' && $action=='display') {
			$pageUrl = str_replace('pages/','',$this->params['pass'][0]);
			$pos = strpos($pageUrl,'.html');
			if($pos !== false){
				$pageUrl = substr($pageUrl, 0, $pos);
			}
			if(!$detail){
				$aryPageUrl = split('/',$pageUrl);
				$controller = $aryPageUrl[0];
			} else {
				return Inflector::camelize(str_replace('/', '_', $pageUrl));
			}
		}

		// プラグインルーティングの場合
		if((($url1==''&&$action=='index')||($url1==$action)) && $url2!=$action && $plugin) {
			$plugin = '';
			$controller = $url0;
		}

		if($prefix)	$prefix .= '_';
		if($plugin) $plugin .= '_';
		if($controller) $controller .= '_';
		if($action) $action .= '_';

		$contentsName = $prefix.$plugin.$controller;

		if($detail){
			$contentsName .= $action;
			if($pass){
				$contentsName .= '_'.implode('_', $pass);
			}
		}
		
		$contentsName = Inflector::camelize($contentsName);

		return $contentsName;

	}
	function getContentsName2($detail = false){

		$url = 'archives/index.html?test=1&test2=2';
		$url = split('\?',$url);
		$url = split('\.',$url[0]);
		if($detail){
			return Inflector::camelize(str_replace('/','_',$url[0]));
		}

		$aryUrl = split('/',$url[0]);
		$prefix = '';
		$plugin = '';
		$controller = '';
		$action = '';
		$pass = '';

		if(!empty($this->params['prefix'])) {
			$prefix = $this->params['prefix'];
		}
		if(!empty($this->params['plugin'])) {
			$plugin = $this->params['plugin'];
		}
		$controller = $this->params['controller'];
		if($prefix) {
			$action = str_replace($prefix.'_','',$this->params['action']);
		}else {
			$action = $this->params['action'];
		}
		if(!empty($this->params['pass'][0])) {
			$pass = $this->params['pass'][0];
		}

		if($prefix){
			$contentsName = $prefix.$aryUrl[1];
		}

		return $contentsName;
		
	}
/**
 * パンくずリストを出力する
 * アクセス制限がかかっているリンクはテキストのみ表示する
 *
 * @param  string  $separator Text to separate crumbs.
 * @param  string  $startText This will be the first crumb, if false it defaults to first crumb in array
 * @return string
 */
	function crumbs($separator = '&raquo;', $startText = false) {
		if (!empty($this->Html->_crumbs)) {
			$out = array();
			if ($startText) {
				$out[] = $this->getLink($startText, '/');
			}

			foreach ($this->Html->_crumbs as $crumb) {
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
 * パンくずリストに要素を追加する
 * アクセス制限がかかっているリンクの場合でもタイトルを表示できるオプションを付加
 * $options に forceTitle を指定する事で表示しない設定も可能
 *
 * @param string $name Text for link
 * @param string $link URL for link (if empty it won't be a link)
 * @param mixed $options Link attributes e.g. array('id'=>'selected')
 */
	function addCrumb($name, $link = null, $options = null) {
		$_options = array('forceTitle'=>true);
		$options = am($_options,$options);
		$this->Html->_crumbs[] = array($name, $link, $options);
	}
/**
 * ページリストを取得する
 * @param string $categoryId
 * @return mixed boolean / array
 */
	function getPageList($categoryId=null){
		if ($this->Page) {
			$conditions = array('Page.status'=>1);
			if(!is_null($categoryId)){
				$conditions['Page.page_category_id'] = $categoryId;
			}
			$this->Page->unbindModel(array('belongsTo'=>array('PageCategory')));
			$pages = $this->Page->find('all',array('conditions'=>$conditions,
											'fields'=>array('title','url'),
											'order'=>'Page.sort'));
			return Set::extract('/Page/.',$pages);
		}else{
			return false;
		}
	}
/**
 * ブラウザにキャッシュさせる為のヘッダーを出力する
 */
	function cacheHeader(){
		header('Cache-Control: max-age=' . 86400);
		header( "Last-Modified: " . gmdate( "D, d M Y H:i:s", filemtime(WWW_ROOT.'index.php') ) . " GMT" );
		if (session_id()) { //session_startしている場合
			header('Expires: ' . gmdate( "D, d M Y H:i:s", filemtime(WWW_ROOT.'index.php')+86400 ) . " GMT");
			header('Pragma: ');
		}
	}
}
?>