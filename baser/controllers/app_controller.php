<?php
/* SVN FILE: $Id$ */
/**
 * AppController 拡張クラス
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('View', 'AppView');
App::import('Component','AuthConfigure');
// TODO パスをそのまま指定しているので、App内にapp_errorを定義しても利用できない
App::import('Core', 'AppError', array('file'=>'../baser/app_error.php'));
//App::import('Component', 'Emoji');
/**
 * AppController 拡張クラス
 *
 * @package			baser.controllers
 */
class AppController extends Controller {
	var $view = 'App';
/**
 * ヘルパー
 *
 * @var		mixed
 * @access	public
 */
// TODO 見直し
	var $helpers = array('PluginHook', 'Html', 'HtmlEx', 'Form', 'Javascript', 'Baser', 'XmlEx');
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
 */
	var $uses = array('GlobalMenu');
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	var $components = array('PluginHook', 'RequestHandler', 'Security');
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
 * @var		array
 * @access	public
 */
	var $subMenuElements = '';
/**
 * コントローラータイトル
 *
 * @var		string
 * @access	public
 */
	var $navis = array();
/**
 * ページ説明文
 *
 * @var		string
 * @access	public
 */
	var $siteDescription = '';
/**
 * コンテンツタイトル
 *
 * @var     string
 * @access  public
 */
	var $contentsTitle = '';
/**
 * 有効プラグイン
 * @var     array
 * @access  public
 */
	var $enablePlugins = array();
/**
 * サイトコンフィグデータ
 * @var array
 */
	var $siteConfigs = array();
/**
 * コンストラクタ
 *
 * @return	void
 * @access	private
 */
	function __construct() {

		parent::__construct();

		$base = baseUrl();

		// サイト基本設定の読み込み
		if(file_exists(CONFIGS.'database.php')) {
			$dbConfig = new DATABASE_CONFIG();
			if($dbConfig->baser['driver']) {
				$SiteConfig = ClassRegistry::init('SiteConfig','Model');
				$this->siteConfigs = $SiteConfig->findExpanded();

				if(empty($this->siteConfigs['version'])) {
					$this->siteConfigs['version'] = $this->getBaserVersion();
					$SiteConfig->saveKeyValue($this->siteConfigs);
				}

				// テーマの設定
				if($base) {
					$reg = '/^'.str_replace('/','\/',$base).'(installations)/i';
				}else {
					$reg = '/^\/(installations)/i';
				}
				if(!preg_match($reg,$_SERVER['REQUEST_URI']) || isInstalled()) {
					$this->theme = $this->siteConfigs['theme'];
				}

			}

		}

		// TODO beforeFilterでも定義しているので整理する
		if($this->name == 'CakeError') {
			// モバイルのエラー用
			if(Configure::read('Mobile.on')) {
				$this->layoutPath = 'mobile';
				$this->helpers[] = 'Mobile';
			}

			if($base) {
				$reg = '/^'.str_replace('/','\/',$base).'admin/i';
			}else {
				$reg = '/^\/admin/i';
			}
			if(preg_match($reg,$_SERVER['REQUEST_URI'])) {
				$this->layoutPath = 'admin';
				$this->subDir = 'admin';
			}

		}

		if(Configure::read('Mobile.on')) {
			if(isset($this->siteConfigs['mobile_on']) && !$this->siteConfigs['mobile_on']) {
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

		// 初回アクセスメッセージ表示設定
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin' && Configure::read('Baser.firstAccess')) {
			$this->writeInstallSetting('Baser.firstAccess', 'false');
		}

		// メンテナンス
		if(!empty($this->siteConfigs['maintenance']) && 
					($this->params['controller'] != 'maintenance' && $this->params['url']['url'] != 'maintenance') &&
					(!isset($this->params['prefix']) || $this->params['prefix'] != 'admin') &&
					(Configure::read('debug') < 1 && empty($_SESSION['Auth']['User']))){
			if(!empty($this->params['return']) && !empty($this->params['requested'])){
				return;
			}else{
				$this->redirect('/maintenance');
			}
		}

		/* 認証設定 */
		if(isset($this->AuthConfigure) && !empty($this->params['prefix'])) {
			$this->AuthConfigure->setting($this->params['prefix']);
		}

		// 送信データの文字コードを内部エンコーディングに変換
		$this->__convertEncodingHttpInput();

		/* レイアウトとビュー用サブディレクトリの設定 */
		if(isset($this->params['prefix'])) {
			$this->layoutPath = $this->params['prefix'];
			$this->subDir = $this->params['prefix'];
			if($this->params['prefix'] == 'mobile') {
				$this->helpers[] = 'Mobile';
			}
		}

		// Ajax
		if(isset($this->RequestHandler) && $this->RequestHandler->isAjax()) {
			// キャッシュ対策
			header("Cache-Control: no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			// デバックを出力しない。
			Configure::write('debug', 0);
			$this->layout = "ajax";
		}

		// 権限チェック
		if(isset($this->Auth) && isset($this->params['url']['url'])) {
			$params = Router::parse($this->params['url']['url']);
			if(!empty($params['prefix'])) {
				$user = $this->Auth->user();
				$Permission = ClassRegistry::init('Permission');
				if(!$Permission->check($this->params['url']['url'],$user['User']['user_group_id'])) {
					$this->redirect('/'.$params['prefix']);
				}
			}
		}

		// SSLリダイレクト設定
		if(Configure::read('Baser.adminSslOn') && !empty($this->params['admin'])) {
			$adminSslMethods = array_filter(get_class_methods(get_class($this)), array($this, '_adminSslMethods'));
			if($adminSslMethods) {
				$this->Security->blackHoleCallback = '_sslFail';
				$this->Security->requireSecure = $adminSslMethods;
			}
		}
		
		// 管理画面は送信データチェックを行わない（全て対応させるのは大変なので暫定処置）
		if(!empty($this->params['admin'])) {
			$this->Security->validatePost = false;
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

		// モバイルでは、mobileHelper::afterLayout をフックしてSJISへの変換が必要だが、
		// エラーが発生した場合には、afterLayoutでは、エラー用のビューを持ったviewクラスを取得できない。
		// 原因は、エラーが発生する前のcontrollerがviewを登録してしまっている為。
		// エラー時のview登録にフックする場所はここしかないのでここでviewの登録を削除する
		if($this->name == 'CakeError') {
			ClassRegistry::removeObject('view');
		}

		$this->__loadDataToView();
		$this->set('isSSL', $this->RequestHandler->isSSL());
		$this->set('safeModeOn', ini_get('safe_mode'));
		$this->set('contentsTitle',$this->contentsTitle);
		$this->set('baserVersion',$this->getBaserVersion());
		$this->set('siteConfig',$this->siteConfigs);
		if(isset($this->siteConfigs['widget_area'])){
			$this->set('widgetArea',$this->siteConfigs['widget_area']);
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
			
			$url = Configure::read('Baser.sslUrl').$url;
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

		$this->cakeError('error404', array(array($this->here)));

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
					//var_dump($value);
					$value = mb_convert_encoding($value, $outenc, $inenc);
					//var_dump(mb_convert_encoding($value,'SJIS','UTF-8'));
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
		$this->set('navis',$this->navis);                       // パンくずなび

		/* ログインユーザー */
		if (isset ($_SESSION['Auth']['User'])) {
			$this->set('user',$_SESSION['Auth']['User']);
		}

		/* 携帯用絵文字データの読込 */
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'mobile' && !empty($this->EmojiData)) {
			//$emojiData = $this->EmojiData->findAll();
			//$this->set('emoji',$this->Emoji->EmojiData($emojiData));
		}

	}
/**
 * BaserCMSのバージョンを取得する
 *
 * @return string Baserバージョン
 */
	function getBaserVersion($plugin = '') {

		$corePlugins = array('blog', 'feed', 'mail');
		if(!$plugin || in_array($plugin, $corePlugins)) {
			$path = BASER.'VERSION.txt';
		} else {
			$appPath = APP.'plugins'.DS.$plugin.DS.'VERSION.txt';
			$baserPath = BASER_PLUGINS.$plugin.DS.'VERSION.txt';
			if(file_exists($appPath)) {
				$path = $appPath;
			}elseif(file_exists($baserPath)) {
				$path = $baserPath;
			} else {
				return false;
			}
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
 * @return	string
 */
	function getSiteVersion($plugin = '') {

		if(!$plugin) {
			if(isset($this->siteConfigs['version'])) {
				return preg_replace("/BaserCMS ([0-9\.]+?[\sa-z]*)/is","$1",$this->siteConfigs['version']);
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
		$aryVersionData = split("\n",$versionData);
		if(!empty($aryVersionData[0])) {
			return 'CakePHP '.$aryVersionData[0];
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
 * /app/core.php のデバッグモードを書き換える
 * @param int $mode
 */
	function writeDebug($mode) {
		$file = new File(CONFIGS.'core.php');
		$core = $file->read(false,'w');
		if($core) {
			$core = preg_replace('/Configure::write\(\'debug\',[\s\-0-9]+?\)\;/is',"Configure::write('debug', ".$mode.");",$core);
			$file->write($core);
			$file->close();
			return true;
		}else {
			$file->close();
			return false;
		}
	}
/**
 * /app/core.phpのデバッグモードを取得する
 * @return string $mode
 */
	function readDebug() {
		$mode = '';
		$file = new File(CONFIGS.'core.php');
		$core = $file->read(false,'r');
		if(preg_match('/Configure::write\(\'debug\',([\s\-0-9]+?)\)\;/is',$core,$matches)) {
			$mode = trim($matches[1]);
		}
		return $mode;
	}
/**
 * メールを送信する
 * 
 * @param	string	$to		送信先アドレス
 * @param	string	$from	送信元アドレス
 * @param	string	$title	タイトル
 * @param	mixed	$body	本文
 * @return	boolean			送信結果
 * @access	public
 */
	function sendMail($to, $title = '', $body = '', $options = array()) {

		$_options = array('fromName' => $this->siteConfigs['formal_name'],
							'reply' => $this->siteConfigs['email'],
							'cc' => '',
							'template' => 'default',
							'from' => $this->siteConfigs['email']
		);

		$options = am($_options, $options);

		extract($options);

		if(!isset($this->EmailEx)) {
			return false;
		}
		
		// メール基本設定
		$this->_setMail();

		// テンプレート
		if(Configure::read('Mobile.on')) {
			$this->EmailEx->template = 'mobile'.DS.$template;
		}else {
			$this->EmailEx->template = $template;
		}

		// データ
		if(is_array($body)) {
			$this->set($body);
		}else {
			$this->set('body', $body);
		}
		
		// 送信先アドレス
		$this->EmailEx->to = $to;

		// 件名
		$this->EmailEx->subject = $title;

		// 送信元・返信先
		if($from) {
			$this->EmailEx->from = $from;
			$this->EmailEx->return = $from;
			$this->EmailEx->replyTo = $reply;
		} else {
			$this->EmailEx->from = $to;
			$this->EmailEx->return = $to;
			$this->EmailEx->replyTo = $to;
		}

		// 送信元名
		if($from && $fromName) {
			$this->EmailEx->from = $fromName . ' <'.$from.'>';
		}

		// CC
		if($cc) {
			if(strpos(',',$cc !== false)) {
				$cc = split(',', $cc);
			}else{
				$cc = array($cc);
			}
			$this->EmailEx->cc = $cc;
		}

		$this->EmailEx->send();

	}
/**
 * メールコンポーネントの初期設定
 *
 * @return	boolean 設定結果
 * @access	protected
 */
	function _setMail() {

		if(!isset($this->EmailEx)) {
			return false;
		}

		$this->EmailEx->reset();
		$this->EmailEx->charset = $this->siteConfigs['mail_encode'];
		$this->EmailEx->sendAs = 'text';		// text or html or both
		$this->EmailEx->lineLength=105;			// TODO ちゃんとした数字にならない大きめの数字で設定する必要がある。
		if($this->siteConfigs['smtp_host']) {
			$this->EmailEx->delivery = 'smtp';	// mail or smtp or debug
			$this->EmailEx->smtpOptions = array('host'	=>$this->siteConfigs['smtp_host'],
					'port'	=>25,
					'timeout'	=>30,
					'username'=>($this->siteConfigs['smtp_user'])?$this->siteConfigs['smtp_user']:null,
					'password'=>($this->siteConfigs['smtp_password'])?$this->siteConfigs['smtp_password']:null);
		} else {
			$this->EmailEx->delivery = "mail";
		}

		return true;

	}
/**
 * インストール設定を書き換える
 *
 * @param	string	$key
 * @param	string	$value
 * @return	boolean
 * @access	public
 */
	function writeInstallSetting($key, $value) {

		/* install.php の編集 */
		$setting = "Configure::write('".$key."', ".$value.");\n";
		$key = str_replace('.', '\.', $key);
		$pattern = '/Configure\:\:write[\s]*\([\s]*\''.$key.'\'[\s]*,[\s]*([^\s]*)[\s]*\);\n/is';
		$file = new File(CONFIGS.'install.php');
		$data = $file->read();
		if(preg_match($pattern, $data)) {
			$data = preg_replace($pattern, $setting, $data);
		} else {
			$data = preg_replace("/\n\?>/is", "\n".$setting.'?>', $data);
		}
		$return = $file->write($data);
		$file->close();
		return $return;
		
	}
/**
 * スマートURLの設定を取得
 *
 * @return	boolean
 * @access	public
 */
	function readSmartUrl(){
		if (Configure::read('App.baseUrl')) {
			return false;
		} else {
			return true;
		}
	}
/**
 * スマートURLの設定を行う
 *
 * @param	boolean	$smartUrl
 * @return	boolean
 * @access	public
 */
	function writeSmartUrl($smartUrl) {

		/* install.php の編集 */
		if($smartUrl) {
			if(!$this->writeInstallSetting('App.baseUrl', "''")){
				return false;
			}
		} else {
			if(!$this->writeInstallSetting('App.baseUrl', "env('SCRIPT_NAME')")){
				return false;
			}
		}

		/* /.htaccess の編集 */
		$rewritePatterns = array(	"/\n[^\n#]*RewriteEngine.+/i",
									"/\n[^\n#]*RewriteBase.+/i",
									"/\n[^\n#]*RewriteCond.+/i",
									"/\n[^\n#]*RewriteRule.+/i");
		$rewriteSettings = array(	'RewriteEngine on',
									'RewriteBase '.$this->getRewriteBase('/'),
									'RewriteRule ^$ app/webroot/ [L]',
									'RewriteRule (.*) app/webroot/$1 [L]');
		$path = ROOT.DS.'.htaccess';
		$file = new File($path);
		$data = $file->read();
		foreach ($rewritePatterns as $rewritePattern) {
			$data = preg_replace($rewritePattern, '', $data);
		}
		if($smartUrl) {
			$data .= "\n".implode("\n", $rewriteSettings);
		}
		if(!$file->write($data)){
			$file->close();
			return false;
		}
		$file->close();

		/* /app/webroot/.htaccess の編集 */
		$rewriteSettings = array(	'RewriteEngine on',
									'RewriteBase '.$this->getRewriteBase('/app/webroot'),
									'RewriteCond %{REQUEST_FILENAME} !-d',
									'RewriteCond %{REQUEST_FILENAME} !-f',
									'RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]');
		$path = WWW_ROOT.'.htaccess';
		$file = new File($path);
		$data = $file->read();
		foreach ($rewritePatterns as $rewritePattern) {
			$data = preg_replace($rewritePattern, '', $data);
		}
		if($smartUrl) {
			$data .= "\n".implode("\n", $rewriteSettings);
		}
		if(!$file->write($data)){
			$file->close();
			return false;
		}
		$file->close();
		return true;

	}
/**
 * RewriteBase の設定を取得する
 *
 * @param	string	$base
 * @return	string
 */
	function getRewriteBase($url){

		$baseUrl = baseUrl();
		if(preg_match("/index\.php/", $baseUrl)){
			$baseUrl = str_replace('index.php/', '', baseUrl());
		}
		$baseUrl = preg_replace("/\/$/",'',$baseUrl);
		if($url != '/' || !$baseUrl) {
			$url = $baseUrl.$url;
		}else{
			$url = $baseUrl;
		}

		return $url;

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
		$this->_saveViewConditions($filterModels, $options);
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
 * @param	string		$options
 * @access	protected
 */
	function _loadViewConditions($filterModels = array(), $options = array()) {

		$_options = array('default'=>array(), 'action' => '', 'group' => '');
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
			if($this->Session->check("{$contentsName}.filter.{$model}")) {
				$filter = $this->Session->read("{$contentsName}.filter.{$model}");
			} elseif(!empty($default[$model])) {
				$filter = $default[$model];
			} else {
				$filter = array();
			}
			$this->data[$model] = $filter;
		}

		if($this->Session->check("{$contentsName}.named")) {
			$named = $this->Session->read("{$contentsName}.named");
		} elseif(!empty($default['named'])) {
			$named = $default['named'];
		} else {
			$named = array();
		}
		$this->passedArgs = $named;

	}
}
?>