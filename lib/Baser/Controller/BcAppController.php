<?php
/**
 * Controller 拡張クラス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('ConnectionManager', 'Model');
App::uses('AppView', 'View');
App::uses('BcAuthConfigureComponent', 'Controller/Component');
App::uses('File', 'Core.Utility');
App::uses('ErrorHandler', 'Core.Error');
App::uses('CakeEmail', 'Network/Email');

/**
 * Controller 拡張クラス
 *
 * @package			Baser.Controller
 */
class BcAppController extends Controller {

/**
 * view
 * 
 * @var string
 */
	public $viewClass = 'App';

/**
 * ページタイトル
 *
 * @var		string
 * @access	public
 */
	public $pageTitle = '';

/**
 * ヘルパー
 *
 * @var		mixed
 * @access	public
 */
	// TODO 見直し
	public $helpers = array(
		'Session', 'BcHtml', 'Form', 'BcForm',
		'Js' => array('Jquery'), 'BcBaser', 'BcXml', 'BcArray', 'BcAdmin'
	);

/**
 * レイアウト
 *
 * @var 		string
 * @access	public
 */
	public $layout = 'default';

/**
 * モデル
 *
 * @var mixed
 * @access protected
 * TODO メニュー管理を除外後、Menuを除外する
 */
	public $uses = array('User', 'Menu', 'Favorite');

/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	public $components = array('RequestHandler', 'Security', 'Session', 'BcManager', 'Email');

/**
 * サブディレクトリ
 *
 * @var		string
 * @access	public
 */
	public $subDir = null;

/**
 * サブメニューエレメント
 *
 * @var string
 * @access public
 */
	public $subMenuElements = '';

/**
 * パンくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array();

/**
 * 検索ボックス
 * 
 * @var string
 * @access public
 */
	public $search = '';

/**
 * ヘルプ
 * 
 * @var string
 * @access public
 */
	public $help = '';

/**
 * ページ説明文
 *
 * @var string
 * @access public
 */
	public $siteDescription = '';

/**
 * コンテンツタイトル
 *
 * @var string
 * @access public
 */
	public $contentsTitle = '';

/**
 * サイトコンフィグデータ
 * 
 * @var array
 * @access public
 */
	public $siteConfigs = array();

/**
 * プレビューフラグ
 * 
 * @var boolean
 * @access public
 */
	public $preview = false;

/**
 * 管理画面テーマ
 * 
 * @var string
 */
	public $adminTheme = null;

/**
 * コンストラクタ
 *
 * @return	void
 * @access	private
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);

		if (isConsole()) {
			unset($this->components['Session']);
		}
		// テンプレートの拡張子
		$this->ext = Configure::read('BcApp.templateExt');

		// コンソールベースのインストールの際のページテンプレート生成において、
		// BC_INSTALLEDが true でない為、コンソールの場合も実行する
		if (BC_INSTALLED || isConsole()) {

			// サイト基本設定の読み込み
			$SiteConfig = ClassRegistry::init('SiteConfig');
			$this->siteConfigs = $SiteConfig->findExpanded();

			if (empty($this->siteConfigs['version'])) {
				$this->siteConfigs['version'] = $this->getBaserVersion();
				$SiteConfig->saveKeyValue($this->siteConfigs);
			}
		} else {
			if ($this->name != 'Installations') {
				if ($this->name == 'CakeError' && $request->params['controller'] != 'installations') {
					$this->redirect('/');
				}
			}
		}

		// TODO beforeFilterでも定義しているので整理する
		if ($this->name == 'CakeError') {

			$this->uses = null;

			// モバイルのエラー用
			if (Configure::read('BcRequest.agent')) {
				$this->layoutPath = Configure::read('BcRequest.agentPrefix');
				$agent = Configure::read('BcRequest.agent');
				if ($agent == 'mobile') {
					$this->helpers[] = 'BcMobile';
				} elseif ($agent == 'smartphone') {
					$this->helpers[] = 'BcSmartphone';
				}
			}
		}

		if (Configure::read('BcRequest.agent') == 'mobile') {
			if (!Configure::read('BcApp.mobile')) {
				$this->notFound();
			}
		}
		if (Configure::read('BcRequest.agent') == 'smartphone') {
			if (!Configure::read('BcApp.smartphone')) {
				$this->notFound();
			}
		}
		
		// $this->request->here の調整
		// index 省略時の場合は、indexを追加
		// .html がある場合は削除
		// $this->request->here は、ビューキャッシュの命名規則に影響する為、
		// 同一ページによる複数キャッシュの生成を防ぐ
		if(!BcUtil::isAdminSystem() && $this->name == 'Pages') {
			if($this->request->params['pass'][count($this->request->params['pass'])-1] == 'index' && !preg_match('/\/index$/', $this->request->here)) {
				$this->request->here .= 'index';
			}
			$this->request->here = preg_replace('/\.html$/', '', $this->request->here);
		} else {
			if($this->request->action == 'index') {
				list($here,) = explode('?', $this->request->here);
				if(!empty($this->request->params['pass'])) {
					foreach ($this->request->params['pass'] as $pass) {
						$here = preg_replace('/\/' . $pass . '$/', '', $here);
					}
				}
				if(!preg_match('/\/index$/', $here)) {
					$this->request->here .= 'index';
				}
			}
		}

		/* 携帯用絵文字のモデルとコンポーネントを設定 */
		// TODO 携帯をコンポーネントなどで判別し、携帯からのアクセスのみ実行させるようにする
		// ※ コンストラクト時点で、$this->request->params['prefix']を利用できない為。
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
	public function beforeFilter() {
		parent::beforeFilter();

		// テーマを設定
		$this->setTheme();

		// TODO 管理画面は送信データチェックを行わない（全て対応させるのは大変なので暫定処置）
		if (!empty($this->request->params['admin'])) {
			$this->Security->validatePost = false;
			$corePlugins = Configure::read('BcApp.corePlugins');
			if(BC_INSTALLED && (!$this->plugin || in_array($this->plugin, $corePlugins))) {
				$this->Security->csrfCheck = true;
			} else {
				$this->Security->csrfCheck = false;
			}
		}

		if (!BC_INSTALLED || Configure::read('BcRequest.isUpdater')) {
			$this->Security->validatePost = false;
			return;
		}

		if ($this->request->params['controller'] != 'installations') {
			// ===============================================================================
			// テーマ内プラグインのテンプレートをテーマに梱包できるようにプラグインパスにテーマのパスを追加
			// 実際には、プラグインの場合も下記パスがテンプレートの検索対象となっている為不要だが、
			// ビューが存在しない場合に、プラグインテンプレートの正規のパスがエラーメッセージに
			// 表示されてしまうので明示的に指定している。
			// （例）
			// [変更後] app/webroot/theme/demo/blog/news/index.php
			// [正　規] app/plugins/blog/views/theme/demo/blog/news/index.php
			// 但し、CakePHPの仕様としてはテーマ内にプラグインのテンプレートを梱包できる仕様となっていないので
			// 将来的には、blog / mail / feed をプラグインではなくコアへのパッケージングを検討する必要あり。
			// ※ AppView::_pathsも関連している
			// ===============================================================================
			$pluginThemePath = WWW_ROOT . 'theme' . DS . $this->theme . DS;
			$pluginPaths = Configure::read('pluginPaths');
			if ($pluginPaths && !in_array($pluginThemePath, $pluginPaths)) {
				Configure::write('pluginPaths', am(array($pluginThemePath), $pluginPaths));
			}
		}

		// メンテナンス
		if (!empty($this->siteConfigs['maintenance']) &&
			($this->request->params['controller'] != 'maintenance' && $this->request->url != 'maintenance') &&
			(!isset($this->request->params['prefix']) || $this->request->params['prefix'] != 'admin') &&
			(Configure::read('debug') < 1 && empty($_SESSION['Auth']['User']))) {
			if (!empty($this->request->params['return']) && !empty($this->request->params['requested'])) {
				return;
			} else {
				$redirectUrl = '/maintenance';
				if(Configure::read('BcRequest.agentAlias')) {
					$redirectUrl = '/' . Configure::read('BcRequest.agentAlias') . $redirectUrl;
				}
				$this->redirect($redirectUrl);
			}
		}

		/* 認証設定 */
		if ($this->name != 'Installations' && $this->name != 'Updaters' && isset($this->BcAuthConfigure)) {

			$configs = Configure::read('BcAuthPrefix');
			if (!empty($this->request->params['prefix']) && isset($configs[$this->request->params['prefix']])) {
				$config = $configs[$this->request->params['prefix']];
				if (count($configs) >= 2) {
					$config['auth_prefix'] = $this->request->params['prefix'];
				}
			} elseif (isset($configs['front'])) {
				$config = $configs['front'];
				if (count($configs) >= 2) {
					$config['auth_prefix'] = 'front';
				}
			} else {
				$config = array();
			}

			// 認証設定
			$this->BcAuthConfigure->setting($config);
			
			// =================================================================
			// ユーザーの存在チェック
			// ログイン中のユーザーを管理側で削除した場合、ログイン状態を削除する必要がある為
			// =================================================================
			$user = $this->BcAuth->user();
			if ($user) {
				$userModel = $this->Session->read('Auth.User.userModel');
				if(strpos($userModel, '.') !== false) {
					list($plugin, $userModel) = explode('.', $userModel);
				}
				if ($userModel && !empty($this->{$userModel})) {
					$authPrefix = $this->Session->read('Auth.User.authPrefix');
					$UserGroup = ClassRegistry::init('UserGroup');
					$userGroups = $UserGroup->find('all', array('conditions' => array('UserGroup.auth_prefix' => $authPrefix), 'recursive' => -1));
					$userGroupIds = Hash::extract($userGroups, '{n}.UserGroup.id');
					$conditions = array(
						$userModel . '.id'				=> $user['id'], 
						$userModel . '.name'			=> $user['name'],
						$userModel . '.user_group_id'	=> $userGroupIds
					);
					if (!$this->{$userModel}->find('count', array(
							'conditions' => $conditions,
							'recursive' => -1))) {
						$this->Session->delete(BcAuthComponent::$sessionKey);
					}
				}
			}
		}

		// 送信データの文字コードを内部エンコーディングに変換
		$this->__convertEncodingHttpInput();

		// $this->request->query['url'] の調整
		// 環境によって？キーにamp;が付加されてしまうため
		if (isset($this->request->query) && is_array($this->request->query)) {
			foreach ($this->request->query as $key => $val) {
				if (strpos($key, 'amp;') === 0) {
					$this->request->query[substr($key, 4)] = $val;
					unset($this->request->query[$key]);
				}
			}
		}

		/* レイアウトとビュー用サブディレクトリの設定 */
		if (isset($this->request->params['prefix'])) {
			if ($this->name != 'CakeError') {
				$this->layoutPath = str_replace('_', '/', $this->request->params['prefix']);
				$this->subDir = str_replace('_', '/', $this->request->params['prefix']);
			}
			$agent = Configure::read('BcRequest.agent');
			if ($agent == 'mobile') {
				$this->helpers[] = 'BcMobile';
			} elseif ($agent == 'smartphone') {
				$this->helpers[] = 'BcSmartphone';
			}
		}

		// Ajax
		if (isset($this->RequestHandler) && $this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			// キャッシュ対策
			header("Cache-Control: no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
		}

		// 権限チェック
		if (isset($this->BcAuth) && isset($this->request->params['prefix']) && !Configure::read('BcRequest.agent') && isset($this->request->params['action']) && empty($this->request->params['requested'])) {
			if (!$this->BcAuth->allowedActions || !in_array($this->request->params['action'], $this->BcAuth->allowedActions)) {
				$user = $this->BcAuth->user();
				$Permission = ClassRegistry::init('Permission');
				if ($user) {
					if (!$Permission->check($this->request->url, $user['user_group_id'])) {
						$this->setMessage('指定されたページへのアクセスは許可されていません。', true);
						$this->redirect($this->BcAuth->loginAction);
					}
				}
			}
		}

		// SSLリダイレクト設定
		if (Configure::read('BcApp.adminSsl') && !empty($this->request->params['admin'])) {
			$adminSslMethods = array_filter(get_class_methods(get_class($this)), array($this, '_adminSslMethods'));
			if ($adminSslMethods) {
				$this->Security->blackHoleCallback = '_sslFail';
				$this->Security->requireSecure = $adminSslMethods;
			}
		}

		$this->_isRequireCheckSubmitToken();

	}

/**
 * テーマをセットする
 * 
 * @return void
 * @access public
 */
	public function setTheme() {
		$theme = '';
		if (!empty($this->siteConfigs['theme'])) {
			$theme = $this->siteConfigs['theme'];
		} else {
			$theme = Configure::read('BcApp.adminTheme');
		}
		if (!empty($this->siteConfigs['admin_theme'])) {
			$adminTheme = $this->siteConfigs['admin_theme'];
		} else {
			$adminTheme = Configure::read('BcApp.adminTheme');
			$this->siteConfigs['admin_theme'] = $adminTheme;
		}
		$this->theme = $theme;
		$this->adminTheme = $adminTheme;
	}

/**
 * 管理画面用のメソッドを取得（コールバックメソッド）
 *
 * @param	string	$var
 * @return	boolean
 * @access	public
 */
	protected function _adminSslMethods($var) {
		return preg_match('/^admin_/', $var);
	}

/**
 * beforeRender
 *
 * @return	void
 * @access	public
 */
	public function beforeRender() {
		parent::beforeRender();

		// テーマのヘルパーをセット
		if (BC_INSTALLED) {
			$this->setThemeHelpers();
		}

		// テンプレートの拡張子
		// RSSの場合、RequestHandlerのstartupで強制的に拡張子を.ctpに切り替えられてしまう為、
		// beforeRenderでも再設定する仕様にした
		$this->ext = Configure::read('BcApp.templateExt');

		// モバイルでは、mobileHelper::afterLayout をフックしてSJISへの変換が必要だが、
		// エラーが発生した場合には、afterLayoutでは、エラー用のビューを持ったviewクラスを取得できない。
		// 原因は、エラーが発生する前のcontrollerがviewを登録してしまっている為。
		// エラー時のview登録にフックする場所はここしかないのでここでviewの登録を削除する
		if ($this->name == 'CakeError') {
			ClassRegistry::removeObject('view');
			$this->response->disableCache();
		}

		$this->__updateFirstAccess();

		$favoriteBoxOpened = false;
		if (!empty($this->BcAuth) && !empty($this->request->url) && $this->request->url != 'update') {
			$user = $this->BcAuth->user();
			if ($user) {
				if ($this->Session->check('Baser.favorite_box_opened')) {
					$favoriteBoxOpened = $this->Session->read('Baser.favorite_box_opened');
				} else {
					$favoriteBoxOpened = true;
				}
			}
		}

		$this->set('favoriteBoxOpened', $favoriteBoxOpened);
		$this->__loadDataToView();
		$this->set('isSSL', $this->RequestHandler->isSSL());
		$this->set('safeModeOn', ini_get('safe_mode'));
		$this->set('baserVersion', $this->getBaserVersion());
		$this->set('siteConfig', $this->siteConfigs);
		if (isset($this->siteConfigs['widget_area'])) {
			$this->set('widgetArea', $this->siteConfigs['widget_area']);
		}
	}

/**
 * 初回アクセスメッセージ用のフラグを更新する
 */
	private function __updateFirstAccess() {
		// 初回アクセスメッセージ表示設定
		if (!empty($this->request->params['admin']) && !empty($this->siteConfigs['first_access'])) {
			$data = array('SiteConfig' => array('first_access' => false));
			$SiteConfig = ClassRegistry::init('SiteConfig', 'Model');
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
	protected function _sslFail($err) {
		if ($err === 'secure') {
			// 共用SSLの場合、設置URLがサブディレクトリになる場合があるので、$this->request->here は利用せずURLを生成する
			$url = $this->request->url;
			if (Configure::read('App.baseUrl')) {
				$url = 'index.php/' . $url;
			}

			$url = Configure::read('BcEnv.sslUrl') . $url;
			$this->redirect($url);
			exit();
		}
	}

/**
 * NOT FOUNDページを出力する
 *
 * @return	void
 * @access	public
 * @throws	NotFoundException
 */
	public function notFound() {
		throw new NotFoundException('見つかりませんでした。');
	}

/**
 * 配列の文字コードを変換する
 *
 * @param 	array	変換前データ
 * @param 	string	変換後の文字コード
 * @return 	array	変換後データ
 * @access	protected
 */
	protected function _autoConvertEncodingByArray($data, $outenc) {
		foreach ($data as $key => $value) {

			if (is_array($value)) {
				$data[$key] = $this->_autoConvertEncodingByArray($value, $outenc);
			} else {

				if (isset($this->request->params['prefix']) && $this->request->params['prefix'] == 'mobile') {
					$inenc = 'SJIS';
				} else {
					$inenc = mb_detect_encoding($value);
				}

				if ($inenc != $outenc) {
					// 半角カナは一旦全角に変換する
					$value = mb_convert_kana($value, "KV", $inenc);
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
	private function __loadDataToView() {
		$this->set('subMenuElements', $this->subMenuElements);	// サブメニューエレメント
		$this->set('crumbs', $this->crumbs);					// パンくずなび
		$this->set('search', $this->search);
		$this->set('help', $this->help);
		$this->set('preview', $this->preview);

		/* ログインユーザー */
		if (BC_INSTALLED && isset($_SESSION['Auth']['User']) && $this->name != 'Installations' && !Configure::read('BcRequest.isUpdater') && !Configure::read('BcRequest.isMaintenance') && $this->name != 'CakeError') {
			$this->set('user', $_SESSION['Auth']['User']);
			if (!empty($this->request->params['admin'])) {
				$this->set('favorites', $this->Favorite->find('all', array('conditions' => array('Favorite.user_id' => $_SESSION['Auth']['User']['id']), 'order' => 'Favorite.sort', 'recursive' => -1)));
			}
		}

		if (!empty($this->request->params['prefix'])) {
			$currentPrefix = $this->request->params['prefix'];
		} else {
			$currentPrefix = 'front';
		}
		$this->set('currentPrefix', $currentPrefix);
		$this->set('authPrefix', $this->Session->read('Auth.User.authPrefix'));

		/* 携帯用絵文字データの読込 */
		// TODO 実装するかどうか検討する
		/* if(isset($this->request->params['prefix']) && $this->request->params['prefix'] == 'mobile' && !empty($this->EmojiData)) {
		  $emojiData = $this->EmojiData->find('all');
		  $this->set('emoji',$this->Emoji->EmojiData($emojiData));
		  } */
	}

/**
 * baserCMSのバージョンを取得する
 *
 * @return string Baserバージョン
 * @access public
 */
	public function getBaserVersion($plugin = '') {
		return getVersion($plugin);
	}

/**
 * テーマのバージョン番号を取得する
 *
 * @param	string	$theme
 * @return	string
 * @access	public
 */
	public function getThemeVersion($theme) {
		$path = WWW_ROOT . 'theme' . DS . $theme . DS . 'VERSION.txt';
		if (!file_exists($path)) {
			return false;
		}
		$versionFile = new File($path);
		$versionData = $versionFile->read();
		$aryVersionData = explode("\n", $versionData);
		if (!empty($aryVersionData[0])) {
			return $aryVersionData[0];
		} else {
			return false;
		}
	}

/**
 * DBのバージョンを取得する
 *
 * @return string
 * @access public
 */
	public function getSiteVersion($plugin = '') {
		if (!$plugin) {
			if (isset($this->siteConfigs['version'])) {
				return preg_replace("/baserCMS ([0-9\.]+?[\sa-z]*)/is", "$1", $this->siteConfigs['version']);
			} else {
				return '';
			}
		} else {
			$Plugin = ClassRegistry::init('Plugin');
			return $Plugin->field('version', array('name' => $plugin));
		}
	}

/**
 * CakePHPのバージョンを取得する
 *
 * @return string Baserバージョン
 */
	public function getCakeVersion() {
		$versionFile = new File(CAKE_CORE_INCLUDE_PATH . DS . CAKE . 'VERSION.txt');
		$versionData = $versionFile->read();
		$lines = explode("\n", $versionData);
		$version = null;
		foreach ($lines as $line) {
			if (preg_match('/^([0-9\.]+)$/', $line, $matches)) {
				$version = $matches[1];
				break;
			}
		}
		if ($version) {
			return $version;
		} else {
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
	private function __convertEncodingHttpInput() {
		// TODO Cakeマニュアルに合わせた方がよいかも
		if (isset($this->request->params['form'])) {
			$this->request->params['form'] = $this->_autoConvertEncodingByArray($this->request->params['form'], 'UTF-8');
		}

		if (isset($this->request->params['data'])) {
			$this->request->params['data'] = $this->_autoConvertEncodingByArray($this->request->params['data'], 'UTF-8');
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
	public function sendMail($to, $title = '', $body = '', $options = array()) {
		$options = array_merge(array(
			'agentTemplate' => true,
			'template' => 'default'
			), $options);

		if (!empty($this->siteConfigs['smtp_host'])) {
			$transport = 'Smtp';
			$host = $this->siteConfigs['smtp_host'];
			$port = ($this->siteConfigs['smtp_port']) ? $this->siteConfigs['smtp_port'] : 25;
			$username = ($this->siteConfigs['smtp_user']) ? $this->siteConfigs['smtp_user'] : null;
			$password = ($this->siteConfigs['smtp_password']) ? $this->siteConfigs['smtp_password'] : null;
			$tls = $this->siteConfigs['smtp_tls'] && ($this->siteConfigs['smtp_tls'] == 1);
		} else {
			$transport = 'Mail';
			$host = 'localhost';
			$port = 25;
			$username = null;
			$password = null;
			$tls = null;
		}

		$config = array(
			'transport' => $transport,
			'host' => $host,
			'port' => $port,
			'username' => $username,
			'password' => $password,
			'tls' => $tls
		);

		$cakeEmail = new CakeEmail($config);

		// charset
		if (!empty($this->siteConfigs['mail_encode'])) {
			$encode = $this->siteConfigs['mail_encode'];
		} else {
			$encode = 'ISO-2022-JP';
		}

		//CakeEmailの内部処理のencodeを統一したいので先に値を渡しておく
		$cakeEmail->headerCharset($encode);
		$cakeEmail->charset($encode);

		//$format
		if (!empty($options['format'])) {
			$cakeEmail->emailFormat($options['format']);
		} else {
			$cakeEmail->emailFormat('text');
		}

		//bcc 'mail@example.com,mail2@example.com'
		if (!empty($options['bcc'])) {
			// 文字列の場合
			$bcc = array();
			if (is_string($options['bcc'])) {
				if (strpos($options['bcc'], ',') !== false) {
					$bcc = explode(',', $options['bcc']);
				} else {
					$bcc[] = $options['bcc'];
				}
				// 配列の場合
			} elseif (is_array($options['bcc'])) {
				$bcc = $options['bcc'];
			}
			foreach ($bcc as $val) {
				if (Validation::email(trim($val))) {
					$cakeEmail->addBcc($val);
				}
			}
			unset($bcc);
		}

		//cc 'mail@example.com,mail2@example.com'
		if (!empty($options['cc'])) {
			// 文字列の場合
			$cc = array();
			if (is_string($options['cc'])) {
				if (strpos($options['cc'], ',') !== false) {
					$cc = explode(',', $options['cc']);
				} else {
					$cc[] = $options['cc'];
				}
				// 配列の場合
			} elseif (is_array($options['cc'])) {
				$cc = $options['cc'];
			}
			foreach ($cc as $val) {
				if (Validation::email(trim($val))) {
					$cakeEmail->addCc($val);
				}
			}
			unset($cc);
		}

		// to 送信先アドレス (最初の1人がTOで残りがBCC)
		if (strpos($to, ',') !== false) {
			$_to = explode(',', $to);
			$i = 0;
			if (count($_to) >= 1) {
				foreach ($_to as $val) {
					if ($i == 0) {
						$cakeEmail->addTo($val);
						$toAddress = $val;
					} else {
						$cakeEmail->addBcc($val);
					}
					++$i;
				}
			}
		} else {
			$cakeEmail->addTo($to);
		}

		// 件名
		$cakeEmail->subject($title);

		//From
		$fromName = $from = '';
		if (!empty($options['from'])) {
			$from = $options['from'];
		} else {
			if (!empty($this->siteConfigs['email'])) {
				$from = $this->siteConfigs['email'];
				if (strpos($from, ',') !== false) {
					$from = explode(',', $from);
				}
			} else {
				$from = $toAddress;
			}
		}

		if (!empty($options['fromName'])) {
			$fromName = $options['fromName'];
		} else {
			if (!empty($this->siteConfigs['formal_name'])) {
				$fromName = $this->siteConfigs['formal_name'];
			} else {
				$formalName = Configure::read('BcApp.title');
			}
		}

		$cakeEmail->from($from, $fromName);
		$cakeEmail->replyTo($from);
		$cakeEmail->returnPath($from);

		//$sender
		if (!empty($options['sender'])) {
			$cakeEmail->sender($options['sender']);
		}

		//$theme
		if ($this->theme) {
			$cakeEmail->theme($this->theme);
		}
		if (!empty($options['theme'])) {
			$cakeEmail->theme($options['theme']);
		}

		//viewRender (利用するviewクラスを設定する)
		$cakeEmail->viewRender('BcApp');

		//template
		if (!empty($options['template'])) {

			$layoutPath = $subDir = $plugin = '';
			if ($options['agentTemplate'] && Configure::read('BcRequest.agent')) {
				$layoutPath = Configure::read('BcRequest.agentPrefix');
				$subDir = Configure::read('BcRequest.agentPrefix');
			}

			list($plugin, $template) = pluginSplit($options['template']);

			if ($subDir) {
				$template = "{$subDir}/{$template}";
			}

			if (!empty($plugin)) {
				$template = "{$plugin}.{$template}";
			}

			if (!empty($options['layout'])) {
				$cakeEmail->template($template, $options['layout']);
			} else {
				$cakeEmail->template($template);
			}
			$content = '';
			if (is_array($body)) {
				$cakeEmail->viewVars($body);
			} else {
				$cakeEmail->viewVars(array('body' => $body));
			}
		} else {
			$content = $body;
		}

		// TODO $attachments tmp file path
		// filePaths と attachments のどっちが本当の分か確認すること
		$attachments = null;
		if (!empty($options['filePaths'])) {
			if (!is_array($options['filePaths'])) {
				$attachments = array($options['filePaths']);
			} else {
				$attachments = $options['filePaths'];
			}
		}
		if (!empty($options['attachments'])) {
			if (!is_array($options['attachments'])) {
				$attachments = array($options['attachments']);
			} else {
				$attachments = $options['attachments'];
			}
		}
		$cakeEmail->attachments($attachments);

		try {
			$cakeEmail->send($content);
			return true;
		} catch (Exception $e) {
			$this->log($e->getMessage());
			return false;
		}
	}

/**
 * 画面の情報をセットする
 *
 * @param	array	$filterModels
 * @param	string	$options
 * @return	void
 * @access	public
 */
	public function setViewConditions($filterModels = array(), $options = array()) {
		$_options = array('type' => 'post', 'session' => true);
		$options = am($_options, $options);
		extract($options);
		if ($type == 'post' && $session == true) {
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
	protected function _saveViewConditions($filterModels = array(), $options = array()) {
		$_options = array('action' => '', 'group' => '');
		$options = am($_options, $options);
		extract($options);

		if (!is_array($filterModels)) {
			$filterModels = array($filterModels);
		}

		if (!$action) {
			$action = $this->request->action;
		}

		$contentsName = $this->name . Inflector::classify($action);
		if ($group) {
			$contentsName .= "." . $group;
		}

		foreach ($filterModels as $model) {
			if (isset($this->request->data[$model])) {
				$this->Session->write("{$contentsName}.filter.{$model}", $this->request->data[$model]);
			}
		}

		if (!empty($this->request->params['named'])) {
			$named = am($this->Session->read("{$contentsName}.named"), $this->request->params['named']);
			$this->Session->write("{$contentsName}.named", $named);
		}
	}

/**
 * 画面の情報をセッションから読み込む
 *
 * @param array $filterModels
 * @param array|string $options
 * @return void
 * @access	protected
 */
	protected function _loadViewConditions($filterModels = array(), $options = array()) {
		$_options = array('default' => array(), 'action' => '', 'group' => '', 'type' => 'post', 'session' => true);
		$options = am($_options, $options);
		$named = array();
		$filter = array();
		extract($options);

		if (!is_array($filterModels)) {
			$model = $filterModels;
			$filterModels = array($filterModels);
		} else {
			$model = $filterModels[0];
		}

		if (!$action) {
			$action = $this->request->action;
		}

		$contentsName = $this->name . Inflector::classify($action);
		if ($group) {
			$contentsName .= "." . $group;
		}

		if ($type == 'post' && $session) {
			foreach ($filterModels as $model) {
				if ($this->Session->check("{$contentsName}.filter.{$model}")) {
					$filter = $this->Session->read("{$contentsName}.filter.{$model}");
				} elseif (!empty($default[$model])) {
					$filter = $default[$model];
				} else {
					$filter = array();
				}
				$this->request->data[$model] = $filter;
			}
			$named = array();
			if (!empty($default['named'])) {
				$named = $default['named'];
			}
			if ($this->Session->check("{$contentsName}.named")) {
				$named = am($named, $this->Session->read("{$contentsName}.named"));
			}
		} elseif ($type == 'get') {
			if (!empty($this->request->query)) {
				$url = $this->request->query;
				unset($url['url']);
				unset($url['ext']);
				unset($url['x']);
				unset($url['y']);
			}
			if (!empty($url)) {
				$filter = $url;
			} elseif (!empty($default[$model])) {
				$filter = $default[$model];
			}
			$this->request->data[$model] = $filter;
			if (!empty($default['named'])) {
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
	public function convertSelectTextCondition($fieldName, $values, $options = array()) {
		$_options = array('type' => 'string', 'conditionType' => 'or');
		$options = am($_options, $options);
		$conditions = array();
		extract($options);

		if ($type == 'string' && !is_array($value)) {
			$values = explode(',', str_replace('\'', '', $values));
		}
		if (!empty($values) && is_array($values)) {
			foreach ($values as $value) {
				$conditions[$conditionType][] = array($fieldName . ' LIKE' => "%'" . $value . "'%");
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
	public function convertBetweenCondition($fieldName, $value) {
		if (strpos($value, '-') === false) {
			return false;
		}
		list($start, $end) = explode('-', $value);
		if (!$start) {
			$conditions[$fieldName . ' <='] = $end;
		} elseif (!$end) {
			$conditions[$fieldName . ' >='] = $start;
		} else {
			$conditions[$fieldName . ' BETWEEN ? AND ?'] = array($start, $end);
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
	public function generatePassword($len = 8) {
		srand((double)microtime() * 1000000);
		$seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$password = "";
		while ($len--) {
			$pos = rand(0, 61);
			$password .= $seed[$pos];
		}
		return $password;
	}

/**
 * 認証完了後処理
 *
 * @return	boolean
 */
	public function isAuthorized() {
		$requestedPrefix = '';

		$userModel = $this->Session->read(BcAuthComponent::$sessionKey . '.userModel');
		list(, $userModel) = pluginSplit($userModel);
		if (isset($this->{$userModel})) {
			$UserClass = $this->{$userModel};
		} else {
			$UserClass = ClassRegistry::init('User');
		}

		$authPrefix = $UserClass->getAuthPrefix($this->BcAuth->user('name'));
		if (!$authPrefix || !$this->BcAuth->userScope) {
			// ユーザーモデルがユーザーグループと関連していない場合
			$user = $this->BcAuth->user();
			if ($user) {
				$userModel = $this->Session->read('Auth.userModel');
				$authPrefixSettings = Configure::read('BcAuthPrefix');
				if (!empty($user['authPrefix']) && !empty($authPrefixSettings[$user['authPrefix']])) {
					$authPrefix = $user['authPrefix'];
				} else {
					foreach ($authPrefixSettings as $key => $authPrefixSetting) {
						if (!empty($authPrefixSetting['userModel'])) {
							$currentUserModel = $authPrefixSetting['userModel'];
						} else {
							$currentUserModel = 'User';
						}
						if ($currentUserModel == $userModel) {
							$authPrefix = $key;
							break;
						}
					}
				}
			}
			if (!$authPrefix) {
				$ref = $this->referer();
				$this->setMessage('指定されたページへのアクセスは許可されていません。', true);
				$this->redirect($ref);
			}
		}

		if (!empty($this->request->params['prefix'])) {
			$requestedPrefix = $this->request->params['prefix'];
		}

		if ($requestedPrefix && ($requestedPrefix != $authPrefix)) {
			$this->setMessage('指定されたページへのアクセスは許可されていません。', true);
			$this->redirect('/');
			return;
		}

		return true;
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
	public function referer($default = null, $local = false) {
		$ref = env('HTTP_REFERER');
		if (!empty($ref) && defined('FULL_BASE_URL')) {
			// >>> CUSTOMIZE MODIFY 2011/01/18 ryuring
			// スマートURLオフの際、$this->request->webrootがうまく動作しないので調整
			//$base = FULL_BASE_URL . $this->request->webroot;
			// ---
			$base = FULL_BASE_URL . $this->request->base;
			// <<<
			if (strpos($ref, $base) === 0) {
				$return = substr($ref, strlen($base));
				if ($return[0] != '/') {
					$return = '/' . $return;
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
 * 現在のユーザーのドキュメントルートの書き込み権限確認
 * 
 * @return boolean
 * @access public
 */
	public function checkRootEditable() {
		if (!isset($this->BcAuth)) {
			return false;
		}
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		if (!$user || !$userModel) {
			return false;
		}
		if (@$this->siteConfigs['root_owner_id'] == $user['user_group_id'] ||
			!@$this->siteConfigs['root_owner_id'] || $user[$userModel]['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			return true;
		} else {
			return false;
		}
	}

/**
 * リクエストされた画面に対しての認証用ユーザーモデルを取得する
 * 
 * @return mixed string Or false
 */
	public function getUserModel() {
		if (!isset($this->BcAuth)) {
			return false;
		}
		return $this->BcAuth->authenticate['Form']['userModel'];
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
	public function redirect($url, $status = null, $exit = true) {
		$url = addSessionId($url, true);
		// 管理システムでのURLの生成が CakePHP の標準仕様と違っていたので調整
		// ※ Routing.admin を変更した場合
		if (is_array($url)) {
			if (!isset($url['admin']) && !empty($this->request->params['admin'])) {
				$url['admin'] = true;
			} elseif (isset($url['admin']) && !$url['admin']) {
				unset($url['admin']);
			}
		}
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
	public function requestAction($url, $extra = array()) {
		// >>> CUSTOMIZE ADD 2011/12/16 ryuring
		// 管理システムやプラグインでのURLの生成が CakePHP の標準仕様と違っていたので調整
		// >>> CUSTOMIZE MODIFY 2012/1/28 ryuring
		// 配列でないURLの場合に、間違った値に書きなおされていたので配列チェックを追加
		if (is_array($url)) {
			if ((!isset($url['admin']) && !empty($this->request->params['admin'])) || !empty($url['admin'])) {
				$url['prefix'] = 'admin';
			}
			if (!isset($url['plugin']) && !empty($this->request->params['plugin'])) {
				$url['plugin'] = $this->request->params['plugin'];
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
	public function admin_ajax_save_favorite_box($open = '') {
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
	public function admin_ajax_batch() {
		$method = $this->request->data['ListTool']['batch'];

		if ($this->request->data['ListTool']['batch_targets']) {
			foreach ($this->request->data['ListTool']['batch_targets'] as $key => $batchTarget) {
				if (!$batchTarget) {
					unset($this->request->data['ListTool']['batch_targets'][$key]);
				}
			}
		}

		$action = '_batch_' . $method;

		if (method_exists($this, $action)) {
			if ($this->{$action}($this->request->data['ListTool']['batch_targets'])) {
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
	public function admin_ajax_save_search_box($key, $open = '') {
		$this->Session->write('Baser.searchBoxOpened.' . $key, $open);
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
	public function setAction($action) {
		// CUSTOMIZE ADD 2012/04/22 ryuring
		// >>>
		$_action = $this->request->action;
		// <<<

		$this->request->action = $action;
		$args = func_get_args();
		unset($args[0]);

		// CUSTOMIZE MODIFY 2012/04/22 ryuring
		// >>>
		//return call_user_func_array(array($this, $action), $args);
		// ---
		$return = call_user_func_array(array($this, $action), $args);
		$this->request->action = $_action;
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
	public function setThemeHelpers() {
		if (!empty($this->request->params['admin'])) {
			return;
		}

		$themeHelpersPath = WWW_ROOT . 'theme' . DS . Configure::read('BcSite.theme') . DS . 'Helper';
		$Folder = new Folder($themeHelpersPath);
		$files = $Folder->read(true, true);
		if (!empty($files[1])) {
			foreach ($files[1] as $file) {
				$file = str_replace('-', '_', $file);
				$this->helpers[] = Inflector::camelize(basename($file, 'Helper.php'));
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
	public function ajaxError($errorNo = 500, $message = '') {
		header('HTTP/1.1 ' . $errorNo);
		if ($message) {
			if (is_array($message)) {
				$aryMessage = array();
				foreach ($message as $value) {
					if (is_array($value)) {
						$aryMessage[] = implode('<br />', $value);
					} else {
						$aryMessage[] = $value;
					}
				}
				$message = implode('<br />', $aryMessage);
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
	public function setMessage($message, $alert = false, $saveDblog = false) {
		if (!isset($this->Session)) {
			return;
		}

		$class = 'notice-message';
		if ($alert) {
			$class = 'alert-message';
		}

		$this->Session->setFlash($message, 'default', array('class' => $class));

		if ($saveDblog) {
			$AppModel = ClassRegistry::init('AppModel');
			$AppModel->saveDblog($message);
		}
	}

/**
 * イベントを発火
 * 
 * @param string $name
 * @param array $params
 * @return mixed
 */
	public function dispatchEvent($name, $params = array(), $options = array()) {
		$options = array_merge(array(
			'modParams'	=> 0,
			'plugin'	=> $this->plugin,
			'layer'		=> 'Controller',
			'class'		=> $this->name
			), $options);
		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch($name, $this, $params, $options);
	}

/**
 * Token の key を取得
 *
 * @return string
 */
	public function admin_ajax_get_token() {
		$this->autoRender = false;
		return $this->request->params['_Token']['key'];
	}
	
/**
 * リクエストメソッドとトークンをチェックする
 *
 * - GETでのアクセスの場合 not found
 * - トークンが送信されていない場合 not found
 */
	protected function _checkSubmitToken() {
		if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' || empty($_POST['_Token']['key']) && empty($_POST['data']['_Token']['key'])) {
			$this->notFound();
		}
	}

	protected function _isRequireCheckSubmitToken() {
		if($this->name == 'CakeError') {
			return;
		}
		$controller = $this->request->params['controller'];
		$action = $this->request->params['action'];
		$requires = [
			'dashboard' => ['admin_del'],
			'editor_templates' => ['admin_delete', 'admin_ajax_delete'],
			'pages' => ['admin_delete', 'admin_ajax_copy', 'admin_ajax_publish', 'admin_ajax_unpublish', 'admin_ajax_update_sort', 'admin_ajax_delete', 'admin_entry_page_files', 'admin_write_page_files'],
			'page_categories' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_copy', 'admin_ajax_down', 'admin_ajax_up'],
			'permissions' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_copy', 'admin_ajax_unpublish', 'admin_ajax_publish'],
			'plugins' => ['admin_ajax_delete_file', 'admin_ajax_delete'],
			'search_indices' => ['admin_ajax_delete'],
			'site_configs' => ['admin_del_cache'],
			'theme_files' => ['admin_del', 'admin_ajax_del', 'admin_copy_to_theme', 'admin_copy_folder_to_theme'],
			'themes' => ['admin_reset_data', 'admin_ajax_copy', 'admin_ajax_delete', 'admin_del', 'admin_apply'],
			'user_groups' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_copy'],
			'users' => ['admin_ajax_delete', 'admin_delete'],
			'widget_areas' => ['admin_ajax_delete', 'admin_delete', 'admin_del_widget'],
			'blog_categories' => ['admin_ajax_delete', 'admin_delete'],
			'blog_comments' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_unpublish', 'admin_ajax_publish'],
			'blog_contents' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_copy'],
			'blog_posts' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_unpublish', 'admin_ajax_publish', 'admin_ajax_copy'],
			'blog_tags' => ['admin_delete', 'admin_ajax_delete'],
			'feed_configs' => ['admin_ajax_delete', 'admin_delete', 'admin_delete_cache'],
			'feed_details' => ['admin_ajax_delete', 'admin_delete'],
			'mail_contents' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_copy'],
			'mail_fields' => ['admin_ajax_delete', 'admin_delete', 'admin_ajax_copy', 'admin_ajax_unpublish', 'admin_ajax_publish'],
			'mail_messages' => ['admin_ajax_delete', 'admin_delete'],
			'uploader_categories' => ['admin_delete', 'admin_ajax_delete', 'admin_ajax_copy'],
			'uploader_files' => ['admin_delete'],
			'menus' => ['admin_delete', 'admin_ajax_delete'],
		];
		if($controller == 'tools' && $action == 'admin_log' && $this->request->params['pass'][0] == 'delete') {
			$this->_checkSubmitToken();
		} elseif($action == 'admin_ajax_batch') {
			$this->_checkSubmitToken();
		}

		foreach($requires as $checkController => $checkActions) {
			foreach($checkActions as $checkAction) {
				if($controller == $checkController && $action == $checkAction) {
					$this->_checkSubmitToken();
					break;
				}
			}
		}
	}

}
