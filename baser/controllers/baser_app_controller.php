<?php
/* SVN FILE: $Id$ */
/**
 * Controller 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('View', 'AppView');
App::import('Component','BcAuthConfigure');
//App::import('Component', 'Emoji');
/**
 * Controller 拡張クラス
 *
 * @package			baser.controllers
 */
class BaserAppController extends Controller {
/**
 * view
 * 
 * @var string
 */
	var $view = 'App';
/**
 * ページタイトル
 *
 * @var		string
 * @access	public
 */
	var $pageTitle = '';
/**
 * ヘルパー
 *
 * @var		mixed
 * @access	public
 */
// TODO 見直し
	var $helpers = array(
		'Session', 'BcPluginHook', BC_HTML_HELPER, BC_HTML_HELPER, 'Form', BC_FORM_HELPER, 
		'Javascript', BC_BASER_HELPER, BC_XML_HELPER, BC_ARRAY_HELPER, BC_BASER_ADMIN_HELPER
	);
/**
 * レイアウト
 *
 * @var 		string
 * @access	public
 */
	var $layout = 'default';
/**
 * モデル
 *
 * @var mixed
 * @access protected
 * TODO メニュー管理を除外後、GlobalMenuを除外する
 */
	var $uses = array('GlobalMenu', 'Favorite');
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	var $components = array('BcPluginHook', 'RequestHandler', 'Security', 'Session', 'BcManager');
/**
 * サブディレクトリ
 *
 * @var		string
 * @access	public
 */
	var $subDir = null;
/**
 * サブメニューエレメント
 *
 * @var string
 * @access public
 */
	var $subMenuElements = '';
/**
 * パンくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array();
/**
 * 検索ボックス
 * 
 * @var string
 * @access public
 */
	var $search = '';
/**
 * ヘルプ
 * 
 * @var string
 * @access public
 */
	var $help = '';
/**
 * ページ説明文
 *
 * @var string
 * @access public
 */
	var $siteDescription = '';
/**
 * コンテンツタイトル
 *
 * @var string
 * @access public
 */
	var $contentsTitle = '';
/**
 * サイトコンフィグデータ
 * 
 * @var array
 * @access public
 */
	var $siteConfigs = array();
/**
 * プレビューフラグ
 * 
 * @var boolean
 * @access public
 */
	var $preview = false;
/**
 * 管理画面テーマ
 * 
 * @var string
 */
	var $adminTheme = null;
/**
 * コンストラクタ
 *
 * @return	void
 * @access	private
 */
	function __construct() {

		parent::__construct();
		
		// テンプレートの拡張子
		$this->ext = Configure::read('BcApp.templateExt');
		
		if(BC_INSTALLED) {
			
			// サイト基本設定の読み込み
			$SiteConfig = ClassRegistry::init('SiteConfig','Model');
			$this->siteConfigs = $SiteConfig->findExpanded();

			if(empty($this->siteConfigs['version'])) {
				$this->siteConfigs['version'] = $this->getBaserVersion();
				$SiteConfig->saveKeyValue($this->siteConfigs);
			}
			
		} elseif($this->name != 'Installations') {
			$this->redirect('/');
		}

		// TODO beforeFilterでも定義しているので整理する
		if($this->name == 'CakeError') {
			
			$this->uses = null;
			$params = Router::parse('/'.Configure::read('BcRequest.pureUrl'));
			
			$this->setTheme($params, true);
			
			// モバイルのエラー用
			if(Configure::read('BcRequest.agent')) {
				$this->layoutPath = Configure::read('BcRequest.agentPrefix');
				$agent = Configure::read('BcRequest.agent');
				if($agent == 'mobile') {
					$this->helpers[] = BC_MOBILE_HELPER;
				} elseif($agent == 'smartphone') {
					$this->helpers[] = BC_SMARTPHONE_HELPER;
				}
			}

		}

		if(Configure::read('BcRequest.agent') == 'mobile') {
			if(!Configure::read('BcApp.mobile')) {
				$this->notFound();
			}
		}
		if(Configure::read('BcRequest.agent') == 'smartphone') {
			if(!Configure::read('BcApp.smartphone')) {
				$this->notFound();
			}
		}
		
		/* 携帯用絵文字のモデルとコンポーネントを設定 */
		// TODO 携帯をコンポーネントなどで判別し、携帯からのアクセスのみ実行させるようにする
		// ※ コンストラクト時点で、$this->params['prefix']を利用できない為。

		// TODO 2008/10/08 egashira
		// beforeFilterに移動してみた。実際に携帯を使うサイトで使えるかどうか確認する
		//$this->uses[] = 'EmojiData';
		//$this->components[] = 'Emoji';

	}
/**
 * beforeFilter
 *
 * @return	void
 * @access	public
 */
	function beforeFilter() {

		parent::beforeFilter();
		
		if(!BC_INSTALLED || Configure::read('BcRequest.isUpdater')) {
			return;
		}
		
		// テーマを設定
		$this->setTheme($this->params);
		
		if($this->params['controller'] != 'installations') {
			// ===============================================================================
			// テーマ内プラグインのテンプレートをテーマに梱包できるようにプラグインパスにテーマのパスを追加
			// 実際には、プラグインの場合も下記パスがテンプレートの検索対象となっている為不要だが、
			// ビューが存在しない場合に、プラグインテンプレートの正規のパスがエラーメッセージに
			// 表示されてしまうので明示的に指定している。
			// （例）
			// [変更後] app/webroot/themed/demo/blog/news/index.php
			// [正　規] app/plugins/blog/views/themed/demo/blog/news/index.php
			// 但し、CakePHPの仕様としてはテーマ内にプラグインのテンプレートを梱包できる仕様となっていないので
			// 将来的には、blog / mail / feed をプラグインではなくコアへのパッケージングを検討する必要あり。
			// ※ AppView::_pathsも関連している
			// ===============================================================================
			$pluginThemePath = WWW_ROOT.'themed' . DS . $this->theme . DS;
			$pluginPaths = Configure::read('pluginPaths');
			if(!in_array($pluginThemePath, $pluginPaths)) {
				Configure::write('pluginPaths', am(array($pluginThemePath), $pluginPaths));
			}
		}

		// メンテナンス
		if(!empty($this->siteConfigs['maintenance']) &&
					($this->params['controller'] != 'maintenance' && @$this->params['url']['url'] != 'maintenance') &&
					(!isset($this->params['prefix']) || $this->params['prefix'] != 'admin') &&
					(Configure::read('debug') < 1 && empty($_SESSION['Auth']['User']))){
			if(!empty($this->params['return']) && !empty($this->params['requested'])){
				return;
			}else{
				$this->redirect('/' . Configure::read('BcRequest.agentAlias') . '/maintenance');
			}
		}

		/* 認証設定 */
		if ($this->name != 'Installations' && $this->name != 'Updaters' && isset($this->BcAuthConfigure)) {
			
			$configs = Configure::read('BcAuthPrefix');
			if (!empty($this->params['prefix']) && isset($configs[$this->params['prefix']])) {
				$config = $configs[$this->params['prefix']];
				if (count($configs) >= 2) {
					if(getDbDriver() != 'bc_csv') {
						$config['auth_prefix'] = $this->params['prefix'];
					} else {
						trigger_error('csv を利用している場合、プレフィックス認証は利用できません。config/baser.php の設定を見なおしてください。', E_USER_ERROR);
					}
				}
			} elseif (isset($configs['front'])) {
				$config = $configs['front'];
				if(count($configs) >= 2) {
					if(getDbDriver() != 'bc_csv') {
						$config['auth_prefix'] = 'front';
					} else {
						trigger_error('csv を利用している場合、プレフィックス認証は利用できません。config/baser.php の設定を見なおしてください。', E_USER_ERROR);
					}
				}
			} else {
				$config = array();
			}
		
			
			$this->BcAuthConfigure->setting($config);
			
			// ユーザーの存在チェック
			$user = $this->BcAuth->user();
			
			if($user) {
				$userModel = key($user);
				if($userModel) {
					if (!empty($this->{$userModel}) && !$this->{$userModel}->find('count', array(
						'conditions' => array($userModel . '.id' => $user[$userModel]['id'], $userModel . '.name' => $user[$userModel]['name']),
						'recursive'	 => -1))) {
						$this->Session->delete($this->BcAuth->sessionKey);
					}
				}
			}
		}
		
		// 送信データの文字コードを内部エンコーディングに変換
		$this->__convertEncodingHttpInput();
		
		// $this->params['url'] の調整
		// 環境によって？キーにamp;が付加されてしまうため
		if(isset($this->params['url']) && is_array($this->params['url'])) {
			foreach ($this->params['url']  as $key => $val ) {
				if ( strpos( $key, 'amp;' ) === 0 ) {
					$this->params['url'][substr( $key, 4 )] = $val;
					unset( $this->params['url'][$key] );
				}
			}
		}

		/* レイアウトとビュー用サブディレクトリの設定 */
		if(isset($this->params['prefix'])) {
			$this->layoutPath = str_replace('_', '/', $this->params['prefix']);
			$this->subDir = str_replace('_', '/', $this->params['prefix']);		
			$agent = Configure::read('BcRequest.agent');
			if($agent == 'mobile') {
				$this->helpers[] = BC_MOBILE_HELPER;
			} elseif($agent == 'smartphone') {
				$this->helpers[] = BC_SMARTPHONE_HELPER;
			}
		}

		// Ajax
		if(isset($this->RequestHandler) && $this->RequestHandler->isAjax() || !empty($this->params['url']['ajax'])) {
			// キャッシュ対策
			header("Cache-Control: no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
		}

		// 権限チェック
		if(isset($this->BcAuth) && isset($this->params['prefix']) && !Configure::read('BcRequest.agent') && isset($this->params['action']) && empty($this->params['requested'])) {
			if(!$this->BcAuth->allowedActions || !in_array($this->params['action'], $this->BcAuth->allowedActions)) {
				$user = $this->BcAuth->user();
				$Permission = ClassRegistry::init('Permission');
				if($user) {
					if(!$Permission->check($this->params['url']['url'], $user[key($user)]['user_group_id'])) {
						$this->setMessage('指定されたページへのアクセスは許可されていません。', true);
						$this->redirect($this->BcAuth->loginAction);
					}
				}
			}
		}

		// SSLリダイレクト設定
		if(Configure::read('BcApp.adminSsl') && !empty($this->params['admin'])) {
			$adminSslMethods = array_filter(get_class_methods(get_class($this)), array($this, '_adminSslMethods'));
			if($adminSslMethods) {
				$this->Security->blackHoleCallback = '_sslFail';
				$this->Security->requireSecure = $adminSslMethods;
			}
		} else {
			$this->Security->enabled = false;
		}

		// 管理画面は送信データチェックを行わない（全て対応させるのは大変なので暫定処置）
		if($this->Security->enabled && !empty($this->params['admin'])) {
			$this->Security->validatePost = false;
		}

	}
/**
 * テーマをセットする
 * 
 * @param array $params 
 * @return void
 * @access public
 */
	function setTheme($params, $isError = false) {
		
		// エラーの場合は強制的にフロントのテーマを設定する
		if($isError) {
			$this->theme = $this->siteConfigs['theme'];
			return;
		}
		
		if(BC_INSTALLED && $params['controller'] != 'installations') {
			
			if(empty($this->siteConfigs['admin_theme']) && Configure::read('BcApp.adminTheme')) {
				$this->siteConfigs['admin_theme'] = Configure::read('BcApp.adminTheme');
			}
			
			$this->theme = $this->siteConfigs['theme'];
			if(!empty($this->siteConfigs['admin_theme'])) {
				$this->adminTheme = $this->siteConfigs['admin_theme'];
			}

		}
		
	}
/**
 * 管理画面用のメソッドを取得（コールバックメソッド）
 *
 * @param	string	$var
 * @return	boolean
 * @access	public
 */
	function _adminSslMethods($var) {
		return preg_match('/^admin_/', $var);
	}
/**
 * beforeRender
 *
 * @return	void
 * @access	public
 */
	function beforeRender() {

		parent::beforeRender();

		// テーマのヘルパーをセット
		$this->setThemeHelpers();
		
		// テンプレートの拡張子
		// RSSの場合、RequestHandlerのstartupで強制的に拡張子を.ctpに切り替えられてしまう為、
		// beforeRenderでも再設定する仕様にした
		$this->ext = Configure::read('BcApp.templateExt');
		
		// モバイルでは、mobileHelper::afterLayout をフックしてSJISへの変換が必要だが、
		// エラーが発生した場合には、afterLayoutでは、エラー用のビューを持ったviewクラスを取得できない。
		// 原因は、エラーが発生する前のcontrollerがviewを登録してしまっている為。
		// エラー時のview登録にフックする場所はここしかないのでここでviewの登録を削除する
		if($this->name == 'CakeError') {
			ClassRegistry::removeObject('view');
		}

		$this->__updateFirstAccess();
		
		$favoriteBoxOpened = false;
		if(!empty($this->BcAuth) && !empty($this->params['url']['url']) && $this->params['url']['url'] != 'update') {
			$user = $this->BcAuth->user();
			if($user) {
				if($this->Session->check('Baser.favorite_box_opened')) {
					$favoriteBoxOpened = $this->Session->read('Baser.favorite_box_opened');
				} else {
					$favoriteBoxOpened = true;
				}
			}
		}

		$this->__loadDataToView();
		$this->set('favoriteBoxOpened', $favoriteBoxOpened);
		$this->set('isSSL', $this->RequestHandler->isSSL());
		$this->set('safeModeOn', ini_get('safe_mode'));
		$this->set('baserVersion',$this->getBaserVersion());
		$this->set('siteConfig',$this->siteConfigs);
		if(isset($this->siteConfigs['widget_area'])){
			$this->set('widgetArea',$this->siteConfigs['widget_area']);
		}

	}
/**
 * 初回アクセスメッセージ用のフラグを更新する
 */
	function __updateFirstAccess() {
		
		// 初回アクセスメッセージ表示設定
		if(!empty($this->params['admin']) && !empty($this->siteConfigs['first_access'])) {
			$data = array('SiteConfig' => array('first_access' => false));
			$SiteConfig = ClassRegistry::init('SiteConfig','Model');
			$SiteConfig->saveKeyValue($data);
		}
		
	}
/**
 * SSLエラー処理
 *
 * SSL通信が必要なURLの際にSSLでない場合、
 * SSLのURLにリダイレクトさせる
 *
 * @param	string	$err
 * @return	void
 * @access	protected
 */
	function _sslFail($err) {

		if ($err === 'secure') {
			// 共用SSLの場合、設置URLがサブディレクトリになる場合があるので、$this->here は利用せずURLを生成する
			$url = $this->params['url']['url'];
			if(Configure::read('App.baseUrl')) {
				$url = 'index.php/'.$url;
			}

			$url = Configure::read('BcEnv.sslUrl').$url;
			$this->redirect($url);
			exit();
		}

	}
/**
 * NOT FOUNDページを出力する
 *
 * @return	void
 * @access	public
 */
	function notFound() {

		return $this->cakeError('error404', array(array($this->here)));

	}
/**
 * 配列の文字コードを変換する
 *
 * @param 	array	変換前データ
 * @param 	string	変換後の文字コード
 * @return 	array	変換後データ
 * @access	protected
 */
	function _autoConvertEncodingByArray($data, $outenc) {

		foreach($data as $key=>$value) {

			if(is_array($value)) {
				$data[$key] = $this->_autoConvertEncodingByArray($value, $outenc);
			} else {

				if(isset($this->params['prefix']) && $this->params['prefix'] == 'mobile') {
					$inenc = 'SJIS';
				}else {
					$inenc = mb_detect_encoding($value);
				}

				if ($inenc != $outenc) {
					// 半角カナは一旦全角に変換する
					$value = mb_convert_kana($value, "KV",$inenc);
					$value = mb_convert_encoding($value, $outenc, $inenc);
					$data[$key] = $value;
				}

			}

		}

		return $data;

	}
/**
 * View用のデータを読み込む。
 * beforeRenderで呼び出される
 *
 * @return	void
 * @access	private
 */
	function __loadDataToView() {

		$this->set('subMenuElements',$this->subMenuElements);	// サブメニューエレメント
		$this->set('crumbs',$this->crumbs);                       // パンくずなび
		$this->set('search', $this->search);
		$this->set('help', $this->help);
		$this->set('preview', $this->preview);

		/* ログインユーザー */
		if (BC_INSTALLED && isset($_SESSION['Auth']['User']) && $this->name != 'Installations' && !Configure::read('BcRequest.isUpdater') && !Configure::read('BcRequest.isMaintenance') && $this->name != 'CakeError') {
			$this->set('user',$_SESSION['Auth']['User']);
			if(!empty($this->params['admin'])) {
				$this->set('favorites', $this->Favorite->find('all', array('conditions' => array('Favorite.user_id' => $_SESSION['Auth']['User']['id']), 'order' => 'Favorite.sort', 'recursive' => -1)));
			}
		}
		
		if(!empty($this->params['prefix'])) {
			$currentPrefix = $this->params['prefix'];
		} else {
			$currentPrefix = 'front';
		}
		$this->set('currentPrefix', $currentPrefix);
		$this->set('authPrefix', $this->Session->read('Auth.User.authPrefix'));
		
		/* 携帯用絵文字データの読込 */
		// TODO 実装するかどうか検討する
		/*if(isset($this->params['prefix']) && $this->params['prefix'] == 'mobile' && !empty($this->EmojiData)) {
			$emojiData = $this->EmojiData->find('all');
			$this->set('emoji',$this->Emoji->EmojiData($emojiData));
		}*/

	}
/**
 * baserCMSのバージョンを取得する
 *
 * @return string Baserバージョン
 * @access public
 */
	function getBaserVersion($plugin = '') {

		return getVersion($plugin);

	}
/**
 * テーマのバージョン番号を取得する
 *
 * @param	string	$theme
 * @return	string
 * @access	public
 */
	function getThemeVersion($theme) {

		$path = WWW_ROOT.'themed'.DS.$theme.DS.'VERSION.txt';
		if(!file_exists($path)) {
			return false;
		}
		App::import('File');
		$versionFile = new File($path);
		$versionData = $versionFile->read();
		$aryVersionData = split("\n",$versionData);
		if(!empty($aryVersionData[0])) {
			return $aryVersionData[0];
		}else {
			return false;
		}

	}
/**
 * DBのバージョンを取得する
 *
 * @return string
 * @access public
 */
	function getSiteVersion($plugin = '') {

		if(!$plugin) {
			if(isset($this->siteConfigs['version'])) {
				return preg_replace("/baserCMS ([0-9\.]+?[\sa-z]*)/is","$1",$this->siteConfigs['version']);
			} else {
				return '';
			}
		} else {
			$Plugin = ClassRegistry::init('Plugin');
			return $Plugin->field('version', array('name'=>$plugin));
		}
	}
/**
 * CakePHPのバージョンを取得する
 *
 * @return string Baserバージョン
 */
	function getCakeVersion() {
		App::import('File');
		$versionFile = new File(CAKE_CORE_INCLUDE_PATH.DS.CAKE.'VERSION.txt');
		$versionData = $versionFile->read();
		$lines = split("\n",$versionData);
		$version = null;
		foreach($lines as $line) {
			if(preg_match('/^([0-9\.]+)$/', $line, $matches)) {
				$version = $matches[1];
				break;
			}
		}
		if($version) {
			return $version;
		}else {
			return false;
		}
	}
/**
 * http経由で送信されたデータを変換する
 * とりあえず、UTF-8で固定
 *
 * @return	void
 * @access	private
 */
	function __convertEncodingHttpInput() {

		// TODO Cakeマニュアルに合わせた方がよいかも
		if(isset($this->params['form'])) {
			$this->params['form'] = $this->_autoConvertEncodingByArray($this->params['form'],'UTF-8');
		}

		if(isset($this->params['data'])) {
			$this->params['data'] = $this->_autoConvertEncodingByArray($this->params['data'],'UTF-8');
		}

	}
/**
 * メールを送信する
 *
 * @param	string	$to		送信先アドレス
 * @param	string	$title	タイトル
 * @param	mixed	$body	本文
 * @options	array
 * @return	boolean			送信結果
 * @access	public
 */
	function sendMail($to, $title = '', $body = '', $options = array()) {

		$formalName = $email = '';
		if(!empty($this->siteConfigs)) {
			$formalName = $this->siteConfigs['formal_name'];
			$email = $this->siteConfigs['email'];
			if(strpos($email, ',') !== false) {
				$email = explode(',', $email);
				$email = $email[0];
			}
		}
		if(!$formalName) {
			$formalName = Configure::read('BcApp.title');
		}

		$options = array_merge(array(
			'fromName'		=> $formalName,
			'reply'			=> $email,
			'cc'			=> '',
			'bcc'			=> '',
			'template'		=> 'default',
			'from'			=> $email,
			'agentTemplate'	=> true
		), $options);

		extract($options);

		if(!isset($this->BcEmail)) {
			return false;
		}

		if(strpos($to, ',') !== false) {
			$_to = explode(',', $to);
			$to = $_to[0];
			if(count($_to) > 1) {
				unset($_to[0]);
				if($bcc) {
					$bcc .= ',';
				}
				$bcc .= implode(',', $_to);
			}
		}
		
		// メール基本設定
		$this->_setMail();

		if(!empty($options['filePaths'])) {
			if(!is_array($options['filePaths'])) {
				$this->BcEmail->filePaths = array($options['filePaths']);
			} else {
				$this->BcEmail->filePaths = $options['filePaths'];
			}
		}
		if(!empty($options['attachments'])) {
			if(!is_array($options['attachments'])) {
				$this->BcEmail->attachments = array($options['attachments']);
			} else {
				$this->BcEmail->attachments = $options['attachments'];
			}
		}

		// テンプレート
		if($agentTemplate && Configure::read('BcRequest.agent')) {
			$this->BcEmail->layoutPath = Configure::read('BcRequest.agentPrefix');
			$this->BcEmail->subDir = Configure::read('BcRequest.agentPrefix');
		} else {
			$this->BcEmail->layoutPath = '';
			$this->BcEmail->subDir = '';
		}
		$this->BcEmail->template = $template;

		// データ
		if(is_array($body)) {
			$this->set($body);
		}else {
			$this->set('body', $body);
		}

		// 送信先アドレス
		$this->BcEmail->to = $to;

		// 件名
		$this->BcEmail->subject = $title;

		// 送信元・返信先
		if($from) {
			if(strpos($from, ',') !== false) {
				$_from = explode(',', $from);
				$from = $_from[0];
			}
			$this->BcEmail->from = $from;
			$this->BcEmail->additionalParams = '-f'.$from;
			$this->BcEmail->return = $from;
			$this->BcEmail->replyTo = $reply;
		} else {
			$this->BcEmail->from = $to;
			$this->BcEmail->additionalParams = '-f'.$to;
			$this->BcEmail->return = $to;
			$this->BcEmail->replyTo = $to;
		}

		// 送信元名
		if($from && $fromName) {
			$this->BcEmail->from = $fromName.' <'.$from.'>';
		}

		// CC
		if($cc) {
			if(strpos($cc, ',') !== false) {
				$cc = explode(',', $cc);
			}else{
				$cc = array($cc);
			}
			$this->BcEmail->cc = $cc;
		}
		
		// BCC
		if($bcc) {
			if(strpos($bcc, ',') !== false) {
				$bcc = explode(',', $bcc);
			}else{
				$bcc = array($bcc);
			}
			$this->BcEmail->bcc = $bcc;
		}
		
		return $this->BcEmail->send();

	}
/**
 * メールコンポーネントの初期設定
 *
 * @return	boolean 設定結果
 * @access	protected
 */
	function _setMail() {

		if(!isset($this->BcEmail)) {
			return false;
		}

		if(!empty($this->siteConfigs['mail_encode'])) {
			$encode = $this->siteConfigs['mail_encode'];
		} else {
			$encode = 'ISO-2022-JP';
		}
		$this->BcEmail->reset();
		$this->BcEmail->charset = $encode;
		$this->BcEmail->sendAs = 'text';		// text or html or both
		$this->BcEmail->lineLength=105;			// TODO ちゃんとした数字にならない大きめの数字で設定する必要がある。
		if(!empty($this->siteConfigs['smtp_host'])) {
			$this->BcEmail->delivery = 'smtp';	// mail or smtp or debug
			$this->BcEmail->smtpOptions = array(
					'host'		=> $this->siteConfigs['smtp_host'],
					'port'		=> ($this->siteConfigs['smtp_port'])? $this->siteConfigs['smtp_port'] : 25,
					'timeout'	=> 30,
					'username'	=> ($this->siteConfigs['smtp_user'])? $this->siteConfigs['smtp_user'] : null,
					'password'	=> ($this->siteConfigs['smtp_password'])? $this->siteConfigs['smtp_password'] : nul
			);
		} else {
			$this->BcEmail->delivery = "mail";
		}

		return true;

	}
/**
 * 画面の情報をセットする
 *
 * @param	array	$filterModels
 * @param	string	$options
 * @return	void
 * @access	public
 */
	function setViewConditions($filterModels = array(), $options = array()) {

		$_options = array('type' => 'post', 'session' => true);
		$options = am($_options, $options);
		extract($options);
		if($type == 'post' && $session == true) {
			$this->_saveViewConditions($filterModels, $options);
		} elseif ($type == 'get') {
			$options['session'] = false;
		}
		$this->_loadViewConditions($filterModels, $options);

	}
/**
 * 画面の情報をセッションに保存する
 *
 * @param	string		$options
 * @return	void
 * @access	protected
 */
	function _saveViewConditions($filterModels = array(), $options = array()) {

		$_options = array('action' => '', 'group' => '');
		$options = am($_options, $options);
		extract($options);

		if(!is_array($filterModels)){
			$filterModels = array($filterModels);
		}

		if(!$action) {
			$action = $this->action;
		}

		$contentsName = $this->name.Inflector::classify($action);
		if($group) {
			$contentsName .= ".".$group;
		}

		foreach($filterModels as $model) {
			if(isset($this->data[$model])) {
				$this->Session->write("{$contentsName}.filter.{$model}",$this->data[$model]);
			}
		}

		if(!empty($this->params['named'])) {
			$named = am($this->Session->read("{$contentsName}.named"), $this->params['named']);
			$this->Session->write("{$contentsName}.named", $named);
		}

	}

    /**
     * 画面の情報をセッションから読み込む
     *
     * @param array $filterModels
     * @param array|string $options
     * @return void
     * @access    protected
     */
	function _loadViewConditions($filterModels = array(), $options = array()) {

		$_options = array('default'=>array(), 'action' => '', 'group' => '', 'type' => 'post' , 'session' => true);
		$options = am($_options, $options);
		$named = array();
		$filter = array();
		extract($options);

		if(!is_array($filterModels)){
			$model = $filterModels;
			$filterModels = array($filterModels);
		} else {
			$model = $filterModels[0];
		}

		if(!$action) {
			$action = $this->action;
		}

		$contentsName = $this->name.Inflector::classify($action);
		if($group) {
			$contentsName .= ".".$group;
		}

		if($type == 'post' && $session) {
			foreach($filterModels as $model) {
				if($this->Session->check("{$contentsName}.filter.{$model}")) {
					$filter = $this->Session->read("{$contentsName}.filter.{$model}");
				} elseif(!empty($default[$model])) {
					$filter = $default[$model];
				} else {
					$filter = array();
				}
				$this->data[$model] = $filter;
			}
			$named = array();
			if(!empty($default['named'])) {
				$named = $default['named'];
			}
			if($this->Session->check("{$contentsName}.named")) {
				$named = am($named, $this->Session->read("{$contentsName}.named"));
			}
		} elseif($type == 'get') {
			if(!empty($this->params['url'])) {
				$url = $this->params['url'];
				unset($url['url']);
				unset($url['ext']);
				unset($url['x']);
				unset($url['y']);
			}
			if(!empty($url)) {
				$filter = $url;
			} elseif(!empty($default[$model])) {
				$filter = $default[$model];
			}
			$this->data[$model] = $filter;
			if(!empty($default['named'])) {
				$named = $default['named'];
			}
			$named['?'] = $filter;

		}

		$this->passedArgs += $named;

	}
/**
 * Select Text 用の条件を生成する
 *
 * @param	string	$fieldName
 * @param	mixed	$values
 * @param	array	$options
 * @return	string
 * @access	public
 */
	function convertSelectTextCondition($fieldName, $values, $options = array()) {

		$_options = array('type'=>'string', 'conditionType'=>'or');
		$options = am($_options, $options);
		$conditions = array();
		extract($options);

		if($type=='string' && !is_array($value)) {
			$values = explode(',',str_replace('\'', '', $values));
		}
        if(!empty($values) && is_array($values)){
            foreach($values as $value){
                $conditions[$conditionType][] = array($fieldName.' LIKE' => "%'".$value."'%");
            }
        }
		return $conditions;

	}
/**
 * BETWEEN 条件を生成
 *
 * @param	string	$fieldName
 * @param	mixed	$value
 * @return	array
 * @access	public
 */
	function convertBetweenCondition($fieldName, $value) {

		if(strpos($value, '-')===false) {
			return false;
		}
		list($start, $end) = explode('-', $value);
		if(!$start) {
			$conditions[$fieldName.' <='] = $end;
		}elseif(!$end) {
			$conditions[$fieldName.' >='] = $start;
		}else {
			$conditions[$fieldName.' BETWEEN ? AND ?'] = array($start, $end);
		}
		return $conditions;

	}
/**
 * ランダムなパスワード文字列を生成する
 *
 * @param	int		$len
 * @return	string	$password
 * @access	public
 */
	function generatePassword ($len = 8) {

		srand ( (double) microtime () * 1000000);
		$seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$password = "";
		while ($len--) {
			$pos = rand(0,61);
			$password .= $seed[$pos];
		}
		return $password;

	}
/**
 * 認証完了後処理
 *
 * @return	boolean
 */
	function isAuthorized() {

		$requestedPrefix = '';

		$authPrefix = $this->getAuthPreifx($this->BcAuth->user('name'));
		if(!$authPrefix || !$this->BcAuth->userScope) {
			// ユーザーモデルがユーザーグループと関連していない場合
			$user = $this->BcAuth->user();
			if($user) {
				$userModel = $this->Session->read('Auth.userModel');
				$authPrefixSettings = Configure::read('BcAuthPrefix');
				if(!empty($user['User']['authPrefix']) && !empty($authPrefixSettings[$user['User']['authPrefix']])) {
					$authPrefix = $user['User']['authPrefix'];
				} else {
					foreach($authPrefixSettings as $key => $authPrefixSetting) {
						if(!empty($authPrefixSetting['userModel'])) {
							$currentUserModel = $authPrefixSetting['userModel'];
						} else {
							$currentUserModel = 'User';
						}
						if($currentUserModel == $userModel) {
							$authPrefix = $key;
							break;
						}
					}
				}

			}
			if(!$authPrefix) {
				$this->setMessage('指定されたページへのアクセスは許可されていません。', true);
				$this->redirect($ref);
			}
		}

		if(!empty($this->params['prefix'])) {
			$requestedPrefix = $this->params['prefix'];
		}

		if($requestedPrefix && ($requestedPrefix != $authPrefix)) {
			// 許可されていないプレフィックスへのアクセスの場合、認証できなかったものとする
			$this->setMessage('指定されたページへのアクセスは許可されていません。', true);
			$this->redirect('/');
			return;
		}

		return true;

	}
/**
 * 対象ユーザーの認証コンテンツのプレフィックスを取得
 *
 * TODO 認証完了後は、セッションに保存しておいてもよいのでは？
 *
 * @param	string	$userName
 * @return	string
 */
	function getAuthPreifx($userName) {

		$user = $this->BcAuth->user();
		if(!$user) {
			return;
		}
		
		$userModel = $this->Session->read('Auth.userModel');
		if(!$userModel) {
			$this->Session->delete('Auth');
			return null;
		}
		if(isset($this->{$userModel})) {
			$UserClass = $this->{$userModel};
		} else {
			$UserClass = ClassRegistry::init($userModel);
		}

		return $UserClass->getAuthPrefix($userName);

	}
/**
 * Returns the referring URL for this request.
 *
 * @param string $default Default URL to use if HTTP_REFERER cannot be read from headers
 * @param boolean $local If true, restrict referring URLs to local server
 * @return string Referring URL
 * @access public
 * @link http://book.cakephp.org/view/430/referer
 */
	function referer($default = null, $local = false) {
		$ref = env('HTTP_REFERER');
		if (!empty($ref) && defined('FULL_BASE_URL')) {
			// >>> CUSTOMIZE MODIFY 2011/01/18 ryuring
			// スマートURLオフの際、$this->webrootがうまく動作しないので調整
			//$base = FULL_BASE_URL . $this->webroot;
			// ---
			$base = FULL_BASE_URL . $this->base;
			// <<<
			if (strpos($ref, $base) === 0) {
				$return =  substr($ref, strlen($base));
				if ($return[0] != '/') {
					$return = '/'.$return;
				}
				return $return;
			} elseif (!$local) {
				return $ref;
			}
		}

		if ($default != null) {
			return $default;
		}
		return '/';
	}
/**
 * フックメソッドを実行する
 * 
 * @param string $hook
 * @return mixed
 */
	function executeHook($hook) {

		$args = func_get_args();
		return call_user_func_array(array($this, 'dispatchPluginHook'), $args);

	}
/**
 * プラグインフックのイベントを発火させる
 * 
 * @param string $hook
 * @return mixed 
 */
	function dispatchPluginHook($hook) {
		
		$args = func_get_args();
		$args[0] =& $this;
		return call_user_func_array( array( $this->BcPluginHook, $hook ), $args );
		
	}
/**
 * プラグインフックのハンドラを実行する
 * 
 * @param string $hook
 * @param mixed $return
 * @return mixed 
 */
	function executePluginHook($hook, $controller) {
		
		$args = func_get_args();
		return call_user_func_array(array($this->BcPluginHook, 'executeHook'), $args);
		
	}
/**
 * 現在のユーザーのドキュメントルートの書き込み権限確認
 * 
 * @return boolean
 * @access public
 */
	function checkRootEditable() {
		
		if(!isset($this->BcAuth)) {
			return false;
		}
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		if(!$user || !$userModel) {
			return false;
		}
		if(@$this->siteConfigs['root_owner_id'] == $user[$userModel]['user_group_id'] ||
				!@$this->siteConfigs['root_owner_id'] || $user[$userModel]['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			return true;
		} else {
			return false;
		}
		
	}
/**
 * ユーザーモデルを取得する
 * 
 * @return mixed string Or false
 */
	function getUserModel() {
		
		if(!isset($this->BcAuth)) {
			return false;
		}
		return $this->BcAuth->userModel;
		
	}
/**
 * Redirects to given $url, after turning off $this->autoRender.
 * Script execution is halted after the redirect.
 *
 * @param mixed $url A string or array-based URL pointing to another location within the app, or an absolute URL
 * @param integer $status Optional HTTP status code (eg: 404)
 * @param boolean $exit If true, exit() will be called after the redirect
 * @return mixed void if $exit = false. Terminates script if $exit = true
 * @access public
 */
	function redirect($url, $status = null, $exit = true) {
		
		$url = addSessionId($url, true);
		// 管理システムでのURLの生成が CakePHP の標準仕様と違っていたので調整
		// ※ Routing.admin を変更した場合
		// >>> CUSTOMIZE MODIFY 2013/06/17 arata
		if(is_array($url)) {
			if (!isset($url['admin']) && !empty($this->params['admin'])) {
				$url['admin'] = true;
			} elseif (isset($url['admin']) && !$url['admin']) {
				unset($url['admin']);
			}
		}
		// <<<
		parent::redirect($url, $status, $exit);
		
	}
/**
 * Calls a controller's method from any location.
 *
 * @param mixed $url String or array-based url.
 * @param array $extra if array includes the key "return" it sets the AutoRender to true.
 * @return mixed Boolean true or false on success/failure, or contents
 *               of rendered action if 'return' is set in $extra.
 * @access public
 */
	function requestAction($url, $extra = array()) {
		
		// >>> CUSTOMIZE ADD 2011/12/16 ryuring
		// 管理システムやプラグインでのURLの生成が CakePHP の標準仕様と違っていたので調整
		// >>> CUSTOMIZE MODIFY 2012/1/28 ryuring
		// 配列でないURLの場合に、間違った値に書きなおされていたので配列チェックを追加
		if(is_array($url)) {
			if ((!isset($url['admin']) && !empty($this->params['admin'])) || !empty($url['admin'])) {
				$url['prefix'] = 'admin';
			}
			if (!isset($url['plugin']) && !empty($this->params['plugin'])) {
				$url['plugin'] = $this->params['plugin'];
			}
		}
		// <<<
		return parent::requestAction($url, $extra);
		
	}
/**
 * よく使う項目の表示状態を保存する
 * 
 * @param mixed $open 1 Or ''
 */
	function admin_ajax_save_favorite_box($open = '') {
		
		$this->Session->write('Baser.favorite_box_opened', $open);
		echo true;
		exit();
		
	}
/**
 * 一括処理
 * 
 * 一括処理としてコントローラーの次のメソッドを呼び出す
 * バッチ処理名は、バッチ処理指定用のコンボボックスで定義する
 * 
 * _batch{バッチ処理名} 
 * 
 * 処理結果として成功の場合は、バッチ処理名を出力する
 * 
 * @return void
 * @access public
 */
	function admin_ajax_batch () {
		
		$method = $this->data['ListTool']['batch'];
		
		if($this->data['ListTool']['batch_targets']) {
			foreach($this->data['ListTool']['batch_targets'] as $key => $batchTarget) {
				if(!$batchTarget) {
					unset($this->data['ListTool']['batch_targets'][$key]);
				}
			}
		}
		
		$action = '_batch_'.$method;
		
		if (method_exists($this, $action)) {
			if($this->{$action}($this->data['ListTool']['batch_targets'])) {
				echo $method;
			}
		}
		exit();
		
	}
/**
 * 検索ボックスの表示状態を保存する
 * 
 * @param mixed $open 1 Or ''
 */
	function admin_ajax_save_search_box($key, $open = '') {
		
		$this->Session->write('Baser.searchBoxOpened.'.$key, $open);
		echo true;
		exit();
		
	}
/**
 * Internally redirects one action to another. Examples:
 *
 * setAction('another_action');
 * setAction('action_with_parameters', $parameter1);
 *
 * @param string $action The new action to be redirected to
 * @param mixed  Any other parameters passed to this method will be passed as
 *               parameters to the new action.
 * @return mixed Returns the return value of the called action
 * @access public
 */
	function setAction($action) {
		
		// CUSTOMIZE ADD 2012/04/22 ryuring
		// >>>
		$_action = $this->action;
		// <<<
		
		$this->action = $action;
		$args = func_get_args();
		unset($args[0]);
		
		// CUSTOMIZE MODIFY 2012/04/22 ryuring
		// >>>
		//return call_user_func_array(array($this, $action), $args);
		// ---
		$return = call_user_func_array(array($this, $action), $args);
		$this->action = $_action;
		return $return;
		// <<<
		
	}
/**
 * テーマ用のヘルパーをセットする
 * 管理画面では読み込まない
 * 
 * @return void
 * @access public
 */
	function setThemeHelpers() {
		
		if(!empty($this->params['admin'])) {
			return;
		}
		
		$themeHelpersPath = WWW_ROOT.'themed'.DS.Configure::read('BcSite.theme').DS.'helpers';
		$Folder = new Folder($themeHelpersPath);
		$files = $Folder->read(true, true);
		if(!empty($files[1])) {
			foreach($files[1] as $file) {
				$file = str_replace('-', '_', $file);
				$this->helpers[] = Inflector::camelize(basename($file, '.php'));
			}
		}
		
	}
/**
 * Ajax用のエラーを出力する
 * 
 * @param int $errorNo
 * @param mixed $message 
 * @return void
 * @access public
 */
	function ajaxError($errorNo = 500, $message = '') {
		header('HTTP/1.1 '.$errorNo);
		if($message) {
			if(is_array($message)) {
				$message = implode('<br />', $message);
			}
			echo $message;
		}
		exit();
	}
/**
 * メッセージをビューにセットする
 * 
 * @param string $message
 * @param boolean $alert
 * @param boolean $saveDblog
 * @return void
 */
	function setMessage($message, $alert = false, $saveDblog = false) {
		
		if(!isset($this->Session)) {
			return;
		}
		
		$class = 'notice-message';
		if($alert) {
			$class = 'alert-message';
		}
		
		$this->Session->setFlash($message, 'default', array('class' => $class));
		
		if($saveDblog) {
			$AppModel = ClassRegistry::init('AppModel');
			$AppModel->saveDblog($message);
		}
		
	}

}
