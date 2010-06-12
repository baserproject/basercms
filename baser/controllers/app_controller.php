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
	var $helpers = array('Html', 'HtmlEx', 'Form', 'Javascript', 'Baser', 'XmlEx', 'PluginHook');
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
	var $components = array('PluginHook');
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
					$data['SiteConfig']['version'] = $this->getBaserVersion();
					$SiteConfig->saveKeyValue($data);
				}

				// テーマの設定
				if($base) {
					$reg = '/^'.str_replace('/','\/',$base).'(installations)/i';
				}else {
					$reg = '/^\/(installations)/i';
				}
				if(!preg_match($reg,$_SERVER['REQUEST_URI'])) {
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
		if(isset($this->Auth)) {
			$params = Router::parse($this->params['url']['url']);
			if(!empty($params['prefix'])) {
				$user = $this->Auth->user();
				$Permission = ClassRegistry::init('Permission');
				if(!$Permission->check($this->params['url']['url'],$user['User']['user_group_id'])) {
					$this->redirect('/'.$params['prefix']);
				}
			}
		}
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
		$this->set('contentsTitle',$this->contentsTitle);
		$this->set('baserVersion',$this->getBaserVersion());
		$this->set('siteConfig',$this->siteConfigs);

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

		$this->set('declareXml',Configure::read('declareXml'));	// XML宣言
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
 * Baserのバージョンを取得する
 *
 * @return string Baserバージョン
 */
	function getBaserVersion() {
		App::import('File');
		$versionFile = new File(BASER.'VERSION.txt');
		$versionData = $versionFile->read();
		$aryVersionData = split("\n",$versionData);
		if(!empty($aryVersionData[0])) {
			return $aryVersionData[0];
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
 * @param	mixed	mailform
 * @return	void
 * @access	protected
 */
	function sendmail($to,$from,$fromName,$title,$template,$data = null) {

		if(!isset($this->EmailEx)) {
			return false;
		}

		$this->EmailEx->reset();
		$this->EmailEx->to = $to;
		$this->EmailEx->subject = $title;
		if($from && $fromName) {
			$this->EmailEx->return = $from;
			$this->EmailEx->replyTo = $from;
			$this->EmailEx->from = $fromName . '<'.$from.'>';
		}elseif($from) {
			$this->EmailEx->return = $from;
			$this->EmailEx->replyTo = $from;
			$this->EmailEx->from = $from;
		}else {
			$this->EmailEx->return = $to;
			$this->EmailEx->replyTo = $to;
			$this->EmailEx->from = $to;
		}

		if(Configure::read('Mobile.on')) {
			$this->EmailEx->template = 'mobile'.DS.$template;
		}else {
			$this->EmailEx->template = $template;
		}

		$this->EmailEx->sendAs = 'text';		// text or html or both
		$this->EmailEx->lineLength=105;			// TODO ちゃんとした数字にならない大きめの数字で設定する必要がある。
		// TODO SMTPの設定は、サイト基本設定でできるようにする
		$this->EmailEx->charset='ISO-2022-JP';
		/*if($mailConfig['smtp_host']){
			$this->EmailEx->delivery = 'smtp';	// mail or smtp or debug
			$this->EmailEx->smtpOptions = array('host'	=>$mailConfig['smtp_host'],
													  'port'	=>25,
													  'timeout'	=>30,
													  'username'=>$mailConfig['smtp_username'],
													  'password'=>$mailConfig['smtp_password']);
		}else{*/
		$this->EmailEx->delivery = "mail";
		//}
		if(Configure::read('Mobile.on')) {
			$this->EmailEx->template = 'mobile'.DS.$template;
		}else {
			$this->EmailEx->template = $template;
		}
		if($data) {
			$this->set($data);
		}

		$this->EmailEx->send();

	}
}
?>