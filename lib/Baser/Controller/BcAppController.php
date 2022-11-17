<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('ConnectionManager', 'Model');
App::uses('AppView', 'View');
App::uses('BcAuthConfigureComponent', 'Controller/Component');
App::uses('File', 'Core.Utility');
App::uses('ErrorHandler', 'Core.Error');
App::uses('CakeEmail', 'Network/Email');
App::uses('Controller', 'Controller');

/**
 * Class BcAppController
 *
 * Controller 拡張クラス
 *
 * @package Baser.Controller
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcAuthComponent $BcAuth
 * @property BcMessageComponent $BcMessage
 */
class BcAppController extends Controller
{

	/**
	 * view
	 *
	 * @var string
	 */
	public $viewClass = 'App';

	/**
	 * ページタイトル
	 *
	 * @var        string
	 * @access    public
	 */
	public $pageTitle = '';

	/**
	 * ヘルパー
	 *
	 * @var        mixed
	 * @access    public
	 */
	// TODO 見直し
	public $helpers = [
		'Session', 'BcHtml', 'Form', 'BcForm', 'BcWidgetArea',
		'Js' => ['Jquery'], 'BcBaser', 'BcXml', 'BcArray', 'BcAdmin',
		'BcListTable', 'BcSearchBox', 'BcFormTable', 'BcLayout'
	];

	/**
	 * レイアウト
	 *
	 * @var        string
	 * @access    public
	 */
	public $layout = 'default';

	/**
	 * モデル
	 *
	 * @var mixed
	 */
	public $uses = ['User', 'Favorite'];

	/**
	 * コンポーネント
	 *
	 * @var        array
	 * @access    public
	 */
	public $components = ['RequestHandler', 'Security', 'Session', 'BcManager', 'Email', 'Flash', 'BcEmail', 'BcMessage'];

	/**
	 * サブディレクトリ
	 *
	 * @var        string
	 * @access    public
	 */
	public $subDir = null;

	/**
	 * サブメニューエレメント
	 *
	 * @var string
	 */
	public $subMenuElements = '';

	/**
	 * パンくずナビ
	 *
	 * @var array
	 */
	public $crumbs = [];

	/**
	 * 検索ボックス
	 *
	 * @var string
	 */
	public $search = '';

	/**
	 * ヘルプ
	 *
	 * @var string
	 */
	public $help = '';

	/**
	 * ページ説明文
	 *
	 * @var string
	 */
	public $siteDescription = '';

	/**
	 * コンテンツタイトル
	 *
	 * @var string
	 */
	public $contentsTitle = '';

	/**
	 * サイトコンフィグデータ
	 *
	 * @var array
	 */
	public $siteConfigs = [];

	/**
	 * プレビューフラグ
	 *
	 * @var bool
	 */
	public $preview = false;

	/**
	 * 管理画面テーマ
	 *
	 * @var string
	 */
	public $adminTheme = null;

	/**
	 * サイトデータ
	 *
	 * @var array
	 */
	public $site = [];

	/**
	 * コンテンツデータ
	 *
	 * @var array
	 */
	public $content = [];

	/**
	 * コンストラクタ
	 *
	 * @param CakeRequest $request リクエストオブジェクト
	 * @param CakeResponse $response レスポンスオブジェクト
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		if (isConsole()) {
			unset($this->components['Session']);
		}

		// テンプレートの拡張子
		$this->ext = Configure::read('BcApp.templateExt');
		$isRequestView = $request->is('requestview');
		$isInstall = $request->is('install');

		// インストールされていない場合、トップページにリダイレクトする
		// コンソールベースのインストールの際のページテンプレート生成において、
		// BC_INSTALLED、$isInstall ともに true でない為、コンソールの場合は無視する
		if (!(BC_INSTALLED || isConsole()) && !$isInstall) {
			$this->redirect('/');
		}

		// コンソールベースのインストールの際のページテンプレート生成において、
		// BC_INSTALLEDが true でない為、コンソールの場合も実行する
		if ((BC_INSTALLED || isConsole()) && $isRequestView) {

			// サイト基本設定の読み込み
			// DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
			try {
				$SiteConfig = ClassRegistry::init('SiteConfig');
				$this->siteConfigs = Configure::read('BcSite');

				// asset ファイルの読み込みの際、bootstrap で、loadSiteConfig() を実行しない仕様となっているが、
				// 存在しない asset ファイルを読み込んだ際に、上記理由により、Not Found ページで、テーマが適用されない為、
				// 再度、loadSiteConfig() を実行
				if (!$this->siteConfigs) {
					loadSiteConfig();
					$this->siteConfigs = Configure::read('BcSite');
				}

				if (empty($this->siteConfigs['version'])) {
					$this->siteConfigs['version'] = $this->getBaserVersion();
					$SiteConfig->saveKeyValue($this->siteConfigs);
				}
			} catch (Exception $ex) {
				$this->siteConfigs = [];
			}

		}

		// TODO beforeFilterでも定義しているので整理する
		if ($this->name === 'CakeError') {

			$this->uses = null;

			// サブサイト用のエラー
			try {
				$Site = ClassRegistry::init('Site');
				$site = $Site->findByUrl($this->request->url);
				if (!empty($site['Site']['name'])) {
					$this->layoutPath = $site['Site']['name'];
					if ($site['Site']['name'] === 'mobile') {
						$this->helpers[] = 'BcMobile';
					} elseif ($site['Site']['name'] === 'smartphone') {
						$this->helpers[] = 'BcSmartphone';
					}
				}
			} catch (Exception $e) {
			}
		}

		// DebugKit プラグインが有効な場合、DebugKit Toolbar を表示
		if (CakePlugin::loaded('DebugKit') && !in_array('DebugKit.Toolbar', $this->components)) {
			$this->components[] = 'DebugKit.Toolbar';
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
	 * @return    void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();

		// index.php をつけたURLの場合、base の値が正常でなくなり、
		// 内部リンクが影響を受けておかしくなってしまうため強制的に Not Found とする
		if(preg_match('/\/index\.php\//', $this->request->base)) {
			$this->notFound();
		}

		$isRequestView = $this->request->is('requestview');
		$isUpdate = $this->request->is('update');
		$isAdmin = $this->request->is('admin');
		$isInstall = $this->request->is('install');
		$isMaintenance = $this->request->is('maintenance');

		// 設定されたサイトURLとリクエストされたサイトURLが違う場合は設定されたサイトにリダイレクト
		if ($isAdmin) {
			$cmsUrl = Configure::read('BcEnv.cmsUrl');
			if ($cmsUrl) {
				$siteUrl = Configure::read('BcEnv.cmsUrl');
			} elseif ($this->request->is('ssl')) {
				$siteUrl = Configure::read('BcEnv.sslUrl');
			} else {
				$siteUrl = Configure::read('BcEnv.siteUrl');
			}
			if ($siteUrl && siteUrl() != $siteUrl) {
				$webrootReg = '/^' . preg_quote($this->request->webroot, '/') . '/';
				$this->redirect($siteUrl . preg_replace($webrootReg, '', Router::reverse($this->request, false)));
			}
		}

		// メンテナンス
		if (!$this->request->is('ajax') && !empty($this->siteConfigs['maintenance']) && (Configure::read('debug') < 1) && !$isMaintenance && !$isAdmin && !BcUtil::isAdminUser()) {
			if (!empty($this->request->params['return']) && !empty($this->request->params['requested'])) {
				return;
			}

			$redirectUrl = '/maintenance';
			if ($this->request->params['Site']['alias']) {
				$redirectUrl = '/' . $this->request->params['Site']['alias'] . $redirectUrl;
			}
			$this->redirect($redirectUrl);
		}

		// セキュリティ設定
		$this->Security->blackHoleCallback = '_blackHoleCallback';
		$csrfExpires = Configure::read('BcSecurity.csrfExpires');
		if (!$csrfExpires) {
			$csrfExpires = "+4 hours";
		}
		$this->Security->csrfExpires = $csrfExpires;
		if (!BC_INSTALLED || $isUpdate) {
			$this->Security->validatePost = false;
		}
		if ($isAdmin) {
			$this->Security->validatePost = false;
			$corePlugins = Configure::read('BcApp.corePlugins');
			if (BC_INSTALLED && (!$this->plugin || in_array($this->plugin, $corePlugins)) && Configure::read('debug') === 0) {
				$this->Security->csrfCheck = true;
			} else {
				$this->Security->csrfCheck = false;
			}
			// SSLリダイレクト設定
			if (Configure::read('BcApp.adminSsl')) {
				$adminSslMethods = array_filter(get_class_methods(get_class($this)), [$this, '_adminSslMethods']);
				if ($adminSslMethods) {
					$this->Security->requireSecure = $adminSslMethods;
				}
			}
		}
		// 検索ボタンがtype=imageで作成されている場合は座標情報のパラメータ、
		// fileアップロードボタンにサイズ制限指定がなされている場合はhiddenで生成されるMAX_FILE_SIZE値が、
		// セキュリティコンポーネントにより制限されるため解除対象フィールドに設定する
		$this->Security->unlockedFields = array_merge($this->Security->unlockedFields, ['x', 'y', 'MAX_FILE_SIZE']);

		// 送信データの文字コードを内部エンコーディングに変換
		$this->__convertEncodingHttpInput();

		// $this->request->query['url'] の調整
		// 環境によって？キーにamp;が付加されてしまうため
		if (isset($this->request->query) && is_array($this->request->query)) {
			foreach($this->request->query as $key => $val) {
				if (strpos($key, 'amp;') === 0) {
					$this->request->query[substr($key, 4)] = $val;
					unset($this->request->query[$key]);
				}
			}
		}

		// コンソールから利用される場合、$isInstall だけでは判定できないので、BC_INSTALLED も判定に入れる
		if ((!BC_INSTALLED || $isInstall || $isUpdate) && $this->name !== 'CakeError') {
			$this->theme = Configure::read('BcApp.defaultAdminTheme');
			return;
		}

		// テーマ内プラグインのテンプレートをテーマに梱包できるようにプラグインパスにテーマのパスを追加
		// ===============================================================================
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
			Configure::write('pluginPaths', am([$pluginThemePath], $pluginPaths));
		}

		// 認証設定
		if (isset($this->BcAuthConfigure)) {
			$authConfig = [];
			if (!empty($this->request->params['prefix'])) {
				$currentAuthPrefix = $this->request->params['prefix'];
			} else {
				$currentAuthPrefix = 'front';
			}
			$authPrefixSettings = Configure::read('BcAuthPrefix');
			foreach($authPrefixSettings as $key => $authPrefixSetting) {
				if (isset($authPrefixSetting['alias']) && $authPrefixSetting['alias'] == $currentAuthPrefix) {
					$authConfig = $authPrefixSetting;
					$authConfig['auth_prefix'] = $authPrefixSetting['alias'];
					break;
				}
				if ($this->request->params['action'] !== 'back_agent') {
					if ($key == $currentAuthPrefix) {
						$authConfig = $authPrefixSetting;
						$authConfig['auth_prefix'] = $key;
						break;
					}
				}
			}
			if ($authConfig) {
				$this->BcAuthConfigure->setting($authConfig);
			} else {
				$this->BcAuth->setSessionKey('Auth.' . Configure::read('BcAuthPrefix.admin.sessionKey'));
			}

			// =================================================================
			// ユーザーの存在チェック
			// ログイン中のユーザーを管理側で削除した場合、ログイン状態を削除する必要がある為
			// =================================================================
			$user = $this->BcAuth->user();
			if ($user && $authConfig && (empty($authConfig['type']) || $authConfig['type'] === 'Form')) {
				$userModel = $authConfig['userModel'];
				$User = ClassRegistry::init($userModel);
				if (strpos($userModel, '.') !== false) {
					list($plugin, $userModel) = explode('.', $userModel);
				}
				if ($userModel && !empty($this->{$userModel})) {
					$nameField = 'name';
					if (!empty($authConfig['username'])) {
						$nameField = $authConfig['username'];
					}
					$conditions = [
						$userModel . '.id' => $user['id'],
						$userModel . '.' . $nameField => $user[$nameField]
					];
					if (isset($User->belongsTo['UserGroup'])) {
						$UserGroup = ClassRegistry::init('UserGroup');
						$userGroups = $UserGroup->find('all', ['conditions' => ['UserGroup.auth_prefix LIKE' => '%' . $authConfig['auth_prefix'] . '%'], 'recursive' => -1]);
						$userGroupIds = Hash::extract($userGroups, '{n}.UserGroup.id');
						$conditions[$userModel . '.user_group_id'] = $userGroupIds;
					}
					if (!$User->find('count', [
						'conditions' => $conditions,
						'recursive' => -1])) {
						$this->Session->delete(BcAuthComponent::$sessionKey);
					}
				}
			}
		}

		if ($this->request->is('ajax') || isset($this->BcAuth) && $this->BcAuth->user()) {
			// キャッシュ対策
			$this->response->header([
				'Cache-Control' => 'no-cache, must-revalidate, post-check=0, pre-check=0',
				'Pragma' => 'no-cache',
			]);
		}

		if (!$isRequestView) {
			return;
		}

		// テーマ、レイアウトとビュー用サブディレクトリの設定
		$this->setAdminTheme();
		$this->setTheme();
		if (isset($this->request->params['prefix']) && $this->name !== 'CakeError') {
			$this->layoutPath = str_replace('_', '/', $this->request->params['prefix']);
			$this->subDir = str_replace('_', '/', $this->request->params['prefix']);
		}
		if (!$isAdmin && !empty($this->request->params['Site']['name'])) {
			$agentSetting = Configure::read('BcAgent.' . $this->request->params['Site']['device']);
			if ($agentSetting && !empty($agentSetting['helper'])) {
				$this->helpers[] = $agentSetting['helper'];
			}
			if (isset($this->request->params['Site'])) {
				$this->layoutPath = $this->request->params['Site']['name'];
				$this->subDir = $this->request->params['Site']['name'];
			}
		}

		// 権限チェック
		if (!isset($this->BcAuth)) {
			return;
		}
		if (!isset($User->belongsTo['UserGroup'])) {
			return;
		}
		if (!isset($this->request->params['prefix'])) {
			return;
		}
		if (!isset($this->request->params['action'])) {
			return;
		}
		if (!empty($this->request->params['Site']['name']) || !empty($this->request->params['requested'])) {
			return;
		}

		if ($this->BcAuth->allowedActions && in_array($this->request->params['action'], $this->BcAuth->allowedActions)) {
			return;
		}

		$user = $this->BcAuth->user();
		$Permission = ClassRegistry::init('Permission');
		if (!$user) {
			return;
		}

		if (!$Permission->check($this->request->url, $user['user_group_id'])) {
			$this->BcMessage->setError(__d('baser', '指定されたページへのアクセスは許可されていません。'));
			$this->redirect($this->BcAuth->loginRedirect);
		}
	}

	/**
	 * テーマをセットする
	 * $this->theme にセットする事
	 *
	 * 優先順位
	 * $this->request->params['Site']['theme'] > $site->theme > $this->siteConfigs['theme']
	 *
	 * @return void
	 */
	protected function setTheme()
	{
		$theme = null;
		if (!empty($this->request->params['Site']['theme'])) {
			$theme = $this->request->params['Site']['theme'];
		}
		if (!$theme) {
			$site = BcSite::findCurrent();
			if (!empty($site->theme)) {
				$theme = $site->theme;
			}
		}
		if (!$theme && !empty($this->siteConfigs['theme'])) {
			$theme = $this->siteConfigs['theme'];
		}
		if (!$theme && BcUtil::isAdminSystem() && $this->adminTheme) {
			$theme = $this->adminTheme;
		}
		$this->theme = $theme;
	}

	/**
	 * 管理画面用テーマをセットする
	 * $this->adminTheme にセットする事
	 *
	 * 優先順位
	 * $this->siteConfigs['admin_theme'] > Configure::read('BcApp.adminTheme')
	 *
	 * @return void
	 */
	protected function setAdminTheme()
	{
		$adminTheme = Configure::read('BcApp.adminTheme');
		if (!$adminTheme && !empty($this->siteConfigs['admin_theme'])) {
			$adminTheme = $this->siteConfigs['admin_theme'];
		}
		$this->adminTheme = $this->siteConfigs['admin_theme'] = $adminTheme;
	}

	/**
	 * 管理画面用のメソッドを取得（コールバックメソッド）
	 *
	 * @param string $var
	 * @return bool
	 */
	protected function _adminSslMethods($var)
	{
		return strpos($var, 'admin_') === 0;
	}

	/**
	 * beforeRender
	 *
	 * @return    void
	 */
	public function beforeRender()
	{
		parent::beforeRender();

		$favoriteBoxOpened = false;
		if (BcUtil::isAdminSystem()) {
			$this->__updateFirstAccess();
			if (!empty($this->BcAuth) && !empty($this->request->url) && $this->request->url !== 'update') {
				if ($this->BcAuth->user()) {
					if ($this->Session->check('Baser.favorite_box_opened')) {
						$favoriteBoxOpened = $this->Session->read('Baser.favorite_box_opened');
					} else {
						$favoriteBoxOpened = true;
					}
				}
			}
		} else {
			// テーマのヘルパーをセット
			if (BC_INSTALLED) {
				$this->setThemeHelpers();
				// ショートコード
				App::uses('BcShortCodeEventListener', 'Event');
				CakeEventManager::instance()->attach(new BcShortCodeEventListener());
			}
		}

		// テンプレートの拡張子
		// RSSの場合、RequestHandlerのstartupで強制的に拡張子を.ctpに切り替えられてしまう為、
		// beforeRenderでも再設定する仕様にした
		$this->ext = Configure::read('BcApp.templateExt');

		// モバイルでは、mobileHelper::afterLayout をフックしてSJISへの変換が必要だが、
		// エラーが発生した場合には、afterLayoutでは、エラー用のビューを持ったviewクラスを取得できない。
		// 原因は、エラーが発生する前のcontrollerがviewを登録してしまっている為。
		// エラー時のview登録にフックする場所はここしかないのでここでviewの登録を削除する
		if ($this->name === 'CakeError') {
			ClassRegistry::removeObject('view');
			$this->response->disableCache();
		}

		$this->__loadDataToView();
		$this->set('favoriteBoxOpened', $favoriteBoxOpened);
		$this->set('isSSL', $this->request->is('ssl'));
		$this->set('safeModeOn', ini_get('safe_mode'));
		$this->set('baserVersion', $this->getBaserVersion());
		$this->set('siteConfig', $this->siteConfigs);
		if (isset($this->siteConfigs['widget_area'])) {
			$this->set('widgetArea', $this->siteConfigs['widget_area']);
		}
	}

	/**
	 * 初回アクセスメッセージ用のフラグを更新する
	 *
	 * @return void
	 */
	private function __updateFirstAccess()
	{
		// 初回アクセスメッセージ表示設定
		if (!empty($this->request->params['admin']) && !empty($this->siteConfigs['first_access'])) {
			$data = ['SiteConfig' => ['first_access' => false]];
			$SiteConfig = ClassRegistry::init('SiteConfig', 'Model');
			$SiteConfig->saveKeyValue($data);
		}
	}

	/**
	 * Securityコンポーネントのブラックホールからのコールバック
	 *
	 * フォーム改ざん対策・CSRF対策・SSL制限・HTTPメソッド制限などへの違反が原因で
	 * Securityコンポーネントに"ブラックホールされた"場合の動作を指定する
	 *
	 * @param string $err エラーの種類
	 * @return void
	 * @throws BadRequestException
	 */
	public function _blackHoleCallback($err)
	{

		$errorMessages = [
			'auth' => __d('baser', 'バリデーションエラーまたはコントローラ/アクションの不一致によるエラーです。'),
		];

		$message = __d('baser', '不正なリクエストです。もしくは、システムが受信できるデータ上限より大きなデータが送信された可能性があります。');

		if (array_key_exists($err, $errorMessages)) {
			$message .= "(type:{$err})" . $errorMessages[$err];
		}

		throw new BadRequestException($message);
	}

	/**
	 * NOT FOUNDページを出力する
	 *
	 * @return    void
	 * @throws    NotFoundException
	 */
	public function notFound()
	{
		throw new NotFoundException(__d('baser', '見つかりませんでした。'));
	}

	/**
	 * 配列の文字コードを変換する
	 *
	 * @param array $data 変換前データ
	 * @param string $outenc 変換後の文字コード
	 * @return array 変換後データ
	 */
	protected function _autoConvertEncodingByArray($data, $outenc)
	{
		foreach($data as $key => $value) {

			if (is_array($value)) {
				$data[$key] = $this->_autoConvertEncodingByArray($value, $outenc);
				continue;
			}

			if (!isset($this->request->params['prefix']) || $this->request->params['prefix'] !== 'mobile') {
				$inenc = mb_detect_encoding($value);
			} else {
				$inenc = 'SJIS';
			}

			if ($inenc != $outenc) {
				// 半角カナは一旦全角に変換する
				$value = mb_convert_kana($value, 'KV', $inenc);
				$value = mb_convert_encoding($value, $outenc, $inenc);
				$data[$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * View用のデータを読み込む。
	 * beforeRenderで呼び出される
	 *
	 * @return    void
	 */
	private function __loadDataToView()
	{
		$this->set('subMenuElements', $this->subMenuElements);    // サブメニューエレメント
		$this->set('crumbs', $this->crumbs);                    // パンくずなび
		$this->set('search', $this->search);
		$this->set('help', $this->help);
		$this->set('preview', $this->preview);

		if (!empty($this->request->params['prefix'])) {
			$currentPrefix = $this->request->params['prefix'];
		} else {
			$currentPrefix = 'front';
		}
		$this->set('currentPrefix', $currentPrefix);

		$user = BcUtil::loginUser();
		$sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');

		$authPrefix = Configure::read('BcAuthPrefix.' . $currentPrefix);
		if ($authPrefix) {
			$currentPrefixUser = BcUtil::loginUser($currentPrefix);
			if ($currentPrefixUser) {
				$user = $currentPrefixUser;
				$sessionKey = BcUtil::getLoginUserSessionKey();
			}
		}

		/* ログインユーザー */
		if (BC_INSTALLED && $user && $this->name !== 'Installations' && !Configure::read('BcRequest.isUpdater') && !Configure::read('BcRequest.isMaintenance') && $this->name !== 'CakeError') {
			$this->set('user', $user);
			if (!empty($this->request->params['admin'])) {
				$this->set('favorites', $this->Favorite->find('all', ['conditions' => ['Favorite.user_id' => $user['id']], 'order' => 'Favorite.sort', 'recursive' => -1]));
			}
		}

		$currentUserAuthPrefixes = [];
		if ($this->Session->check('Auth.' . $sessionKey . '.UserGroup.auth_prefix')) {
			$currentUserAuthPrefixes = explode(',', $this->Session->read('Auth.' . $sessionKey . '.UserGroup.auth_prefix'));
		}
		$this->set('currentUserAuthPrefixes', $currentUserAuthPrefixes);

		/* 携帯用絵文字データの読込 */
		// TODO 実装するかどうか検討する
		/* if (isset($this->request->params['prefix']) && $this->request->params['prefix'] == 'mobile' && !empty($this->EmojiData)) {
		  $emojiData = $this->EmojiData->find('all');
		  $this->set('emoji',$this->Emoji->EmojiData($emojiData));
		  } */
	}

	/**
	 * baserCMSのバージョンを取得する
	 *
	 * @param string $plugin プラグイン名
	 * @return string Baserバージョン
	 */
	protected function getBaserVersion($plugin = '')
	{
		return getVersion($plugin);
	}

	/**
	 * テーマのバージョン番号を取得する
	 *
	 * @param string $theme テーマ名
	 * @return string
	 */
	protected function getThemeVersion($theme)
	{
		$path = WWW_ROOT . 'theme' . DS . $theme . DS . 'VERSION.txt';
		if (!file_exists($path)) {
			return false;
		}
		$versionFile = new File($path);
		$versionData = $versionFile->read();
		$aryVersionData = explode("\n", $versionData);
		if (empty($aryVersionData[0])) {
			return false;
		}

		return $aryVersionData[0];
	}

	/**
	 * DBのバージョンを取得する
	 *
	 * @param string $plugin プラグイン名
	 * @return string
	 */
	protected function getSiteVersion($plugin = '')
	{
		if (!$plugin) {
			if (!isset($this->siteConfigs['version'])) {
				return '';
			}
			return preg_replace("/baserCMS ([0-9.]+?[\sa-z]*)/is", "$1", $this->siteConfigs['version']);
		}
		$Plugin = ClassRegistry::init('Plugin');
		return $Plugin->field('version', ['name' => $plugin]);
	}

	/**
	 * CakePHPのバージョンを取得する
	 *
	 * @return string Baserバージョン
	 */
	protected function getCakeVersion()
	{
		$versionFile = new File(CAKE_CORE_INCLUDE_PATH . DS . CAKE . 'VERSION.txt');
		$versionData = $versionFile->read();
		$lines = explode("\n", $versionData);
		$version = null;
		foreach($lines as $line) {
			if (preg_match('/^([0-9.]+)$/', $line, $matches)) {
				$version = $matches[1];
				break;
			}
		}
		if (!$version) {
			return false;
		}
		return $version;
	}

	/**
	 * http経由で送信されたデータを変換する
	 * とりあえず、UTF-8で固定
	 *
	 * @return    void
	 * @access    private
	 */
	private function __convertEncodingHttpInput()
	{
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
	 * @param string $to 送信先アドレス
	 * @param string $title タイトル
	 * @param mixed $body 本文
	 * @param array $options オプション
	 *    - bool agentTemplate : テンプレートの配置場所についてサイト名をサブフォルダとして利用するかどうか（初期値：true）
	 * @return bool 送信結果
	 */
	public function sendMail($to, $title = '', $body = '', $options = [])
	{
		$dbg = debug_backtrace();
		if (!empty($dbg[1]['function']) && $dbg[1]['function'] === 'invokeArgs') {
			$this->notFound();
		}
		$options = array_merge([
			'agentTemplate' => true,
			'template' => 'default'
		], $options);

		/*** Controller.beforeSendEmail ***/
		$event = $this->dispatchEvent('beforeSendMail', [
			'options' => $options
		]);
		if ($event !== false) {
			$this->request->data = $event->result === true? $event->data['data'] : $event->result;
			if (!empty($event->data['options'])) {
				$options = $event->data['options'];
			}
		}

		if (!empty($this->siteConfigs['smtp_host'])) {
			$transport = 'Smtp';
			$host = $this->siteConfigs['smtp_host'];
			$port = ($this->siteConfigs['smtp_port'])? $this->siteConfigs['smtp_port'] : 25;
			$username = ($this->siteConfigs['smtp_user'])? $this->siteConfigs['smtp_user'] : null;
			$password = ($this->siteConfigs['smtp_password'])? $this->siteConfigs['smtp_password'] : null;
			$tls = $this->siteConfigs['smtp_tls'] && ($this->siteConfigs['smtp_tls'] == 1);
		} else {
			$transport = 'Mail';
			$host = 'localhost';
			$port = 25;
			$username = null;
			$password = null;
			$tls = null;
		}

		$config = [
			'transport' => $transport,
			'host' => $host,
			'port' => $port,
			'username' => $username,
			'password' => $password,
			'tls' => $tls
		];

		/**
		 * CakeEmailでは、return-path の正しい設定のためには additionalParameters を設定する必要がある
		 * @url http://norm-nois.com/blog/archives/2865
		 */
		if (!empty($this->siteConfigs['mail_additional_parameters'])) {
			$config = Hash::merge($config, ['additionalParameters' => $this->siteConfigs['mail_additional_parameters']]);
		}
		if (!empty($options['additionalParameters'])) {
			$config = Hash::merge($config, ['additionalParameters' => $options['additionalParameters']]);
		}
		$cakeEmail = new CakeEmail($config);

		// charset
		if (!empty($this->siteConfigs['mail_encode'])) {
			$encode = $this->siteConfigs['mail_encode'];
		} else {
			$encode = 'UTF-8';
		}

		// ISO-2022-JPの場合半角カナが文字化けしてしまうので全角に変換する
		if ($encode === 'ISO-2022-JP') {
			$title = mb_convert_kana($title, 'KV', 'UTF-8');
			if (is_string($body)) {
				$body = mb_convert_kana($body, 'KV', 'UTF-8');
			} elseif (isset($body['message']) && is_array($body['message'])) {
				foreach($body['message'] as $key => $val) {
					if (is_string($val)) {
						$body['message'][$key] = mb_convert_kana($val, 'KV', 'UTF-8');
					}
				}
			}
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
			$bcc = [];
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
			foreach($bcc as $val) {
				if (Validation::email(trim($val))) {
					$cakeEmail->addBcc(trim($val));
				}
			}
			unset($bcc);
		}

		//cc 'mail@example.com,mail2@example.com'
		if (!empty($options['cc'])) {
			// 文字列の場合
			$cc = [];
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
			foreach($cc as $val) {
				if (Validation::email(trim($val))) {
					$cakeEmail->addCc($val);
				}
			}
			unset($cc);
		}

		$toAddress = null;
		try {
			// to 送信先アドレス (最初の1人がTOで残りがBCC)
			if (strpos($to, ',') !== false) {
				$_to = explode(',', $to);
				$i = 0;
				if (count($_to) >= 1) {
					foreach($_to as $val) {
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
		} catch (Exception $e) {
			$this->BcMessage->setError($e->getMessage() . ' ' . __d('baser', '送信先のメールアドレスが不正です。'));
			return false;
		}

		// 件名
		$cakeEmail->subject($title);

		//From
		$from = '';
		if (!empty($options['from'])) {
			$from = $options['from'];
		} else {
			if (!empty($this->siteConfigs['email'])) {
				$from = $this->siteConfigs['email'];
				if (strpos($from, ',') !== false) {
					$from = strstr($from, ',', true);
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
				$fromName = Configure::read('BcApp.title');
			}
		}

		try {
			$cakeEmail->from($from, $fromName);
		} catch (Exception $e) {
			$this->BcMessage->setError($e->getMessage() . ' ' . __d('baser', '送信元のメールアドレスが不正です。'));
			return false;
		}

		//Reply-To
		if (!empty($options['replyTo'])) {
			$replyTo = $options['replyTo'];
		} else {
			$replyTo = $from;
		}
		$cakeEmail->replyTo($replyTo);

		//Return-Path
		if (!empty($options['returnPath'])) {
			$returnPath = $options['returnPath'];
			$cakeEmail->returnPath($returnPath);
		}

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

			$subDir = $plugin = '';
			// インストール時にSiteは参照できない
			if ($options['agentTemplate'] && !empty($this->request->params['Site']['name'])) {
				$subDir = $this->request->params['Site']['name'];
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
				$cakeEmail->viewVars(['body' => $body]);
			}
		} else {
			$content = $body;
		}

		// $attachments tmp file path
		$attachments = [];
		if (!empty($options['attachments'])) {
			if (!is_array($options['attachments'])) {
				$attachments = [$options['attachments']];
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
	 * @param array $filterModels
	 * @param array $extraOption オプション
	 * @return    void
	 * @access    public
	 */
	protected function setViewConditions($filterModels = [], $extraOption = [])
	{
		$option = am(['type' => 'post', 'session' => true], $extraOption);
		if ($option['type'] === 'post' && $option['session'] == true) {
			$this->_saveViewConditions($filterModels, $option);
		} elseif ($option['type'] === 'get') {
			$option['session'] = false;
		}
		$this->_loadViewConditions($filterModels, $option);
	}

	/**
	 * 画面の情報をセッションに保存する
	 *
	 * @param array $filterModels
	 * @param array $options オプション
	 * @return    void
	 * @access    protected
	 */
	protected function _saveViewConditions($filterModels = [], $options = [])
	{
		$_options = ['action' => '', 'group' => ''];
		$options = am($_options, $options);
		extract($options);

		if (!is_array($filterModels)) {
			$filterModels = [$filterModels];
		}

		if (!$action) {
			$action = $this->request->action;
		}

		$contentsName = $this->name . Inflector::classify($action);
		if ($group) {
			$contentsName .= "." . $group;
		}

		foreach($filterModels as $model) {
			if (isset($this->request->data[$model])) {
				$this->Session->write("Baser.viewConditions.{$contentsName}.filter.{$model}", $this->request->data[$model]);
			}
		}

		if (!empty($this->request->params['named'])) {
			if ($this->Session->check("Baser.viewConditions.{$contentsName}.named")) {
				$named = array_merge($this->Session->read("Baser.viewConditions.{$contentsName}.named"), $this->request->params['named']);
			} else {
				$named = $this->request->params['named'];
			}
			$this->Session->write("Baser.viewConditions.{$contentsName}.named", $named);
		}
	}

	/**
	 * 画面の情報をセッションから読み込む
	 *
	 * @param array $filterModels
	 * @param array|string $options オプション
	 * @return void
	 * @access    protected
	 */
	protected function _loadViewConditions($filterModels = [], $options = [])
	{
		$_options = ['default' => [], 'action' => '', 'group' => '', 'type' => 'post', 'session' => true];
		$options = am($_options, $options);
		$named = [];
		$filter = [];
		extract($options);

		if (!is_array($filterModels)) {
			$model = (string)$filterModels;
			$filterModels = [$filterModels];
		} else {
			$model = (string)$filterModels[0];
		}

		if (!$action) {
			$action = $this->request->action;
		}

		$contentsName = $this->name . Inflector::classify($action);
		if ($group) {
			$contentsName .= "." . $group;
		}

		if ($type === 'post' && $session) {
			foreach($filterModels as $model) {
				if ($this->Session->check("Baser.viewConditions.{$contentsName}.filter.{$model}")) {
					$filter = $this->Session->read("Baser.viewConditions.{$contentsName}.filter.{$model}");
				} elseif (!empty($default[$model])) {
					$filter = $default[$model];
				} else {
					$filter = [];
				}
				$this->request->data[$model] = $filter;
			}
			$named = [];
			if (!empty($default['named'])) {
				$named = $default['named'];
			}
			if ($this->Session->check("Baser.viewConditions.{$contentsName}.named")) {
				$named = array_merge($named, $this->Session->read("Baser.viewConditions.{$contentsName}.named"));
			}
		} elseif ($type === 'get') {
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
	 * @param string $fieldName フィールド名
	 * @param mixed $values 値
	 * @param array $options オプション
	 * @return    array
	 */
	protected function convertSelectTextCondition($fieldName, $values, $options = [])
	{
		$_options = ['type' => 'string', 'conditionType' => 'or'];
		$options = am($_options, $options);
		$conditions = [];
		extract($options);

		if ($type === 'string' && !is_array($value)) {
			$values = explode(',', str_replace('\'', '', $values));
		}
		if (!empty($values) && is_array($values)) {
			foreach($values as $value) {
				$conditions[$conditionType][] = [$fieldName . ' LIKE' => "%'" . $value . "'%"];
			}
		}
		return $conditions;
	}

	/**
	 * BETWEEN 条件を生成
	 *
	 * @param string $fieldName フィールド名
	 * @param mixed $value 値
	 * @return array
	 */
	protected function convertBetweenCondition($fieldName, $value)
	{
		if (strpos($value, '-') === false) {
			return false;
		}
		list($start, $end) = explode('-', $value);
		if (!$start) {
			$conditions[$fieldName . ' <='] = $end;
		} elseif (!$end) {
			$conditions[$fieldName . ' >='] = $start;
		} else {
			$conditions[$fieldName . ' BETWEEN ? AND ?'] = [$start, $end];
		}
		return $conditions;
	}

	/**
	 * ランダムなパスワード文字列を生成する
	 *
	 * @param int $len 文字列の長さ
	 * @return string パスワード
	 */
	protected function generatePassword($len = 8)
	{
		mt_srand((double)microtime() * 1000000);
		$seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$password = "";
		while($len--) {
			$pos = mt_rand(0, 61);
			$password .= $seed[$pos];
		}
		return $password;
	}

	/**
	 * 認証完了後処理
	 *
	 * @param array $user 認証されたユーザー情報
	 * @return    bool
	 */
	public function isAuthorized($user)
	{

		if (!isset($user['UserGroup']['auth_prefix'])) {
			return true;
		}
		$authPrefix = explode(',', $user['UserGroup']['auth_prefix']);
		if (!empty($this->request->params['prefix'])) {
			$currentPrefix = $this->request->params['prefix'];
		} else {
			$currentPrefix = 'front';
		}
		return (in_array($currentPrefix, $authPrefix));

	}

	/**
	 * Returns the referring URL for this request.
	 *
	 * @param string $default Default URL to use if HTTP_REFERER cannot be read from headers
	 * @param bool $local If true, restrict referring URLs to local server
	 * @return string Referring URL
	 * @link http://book.cakephp.org/view/430/referer
	 */
	public function referer($default = null, $local = false)
	{
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
				if ($return[0] !== '/') {
					$return = '/' . $return;
				}
				return $return;
			}

			if (!$local) {
				return $ref;
			}
		}

		if ($default != null) {
			return $default;
		}
		return '/';
	}

	/**
	 * リクエストされた画面に対しての認証用ユーザーモデルを取得する
	 *
	 * @return mixed string Or false
	 */
	protected function getUserModel()
	{
		if (!isset($this->BcAuth)) {
			return false;
		}
		if (!isset($this->BcAuth->authenticate['Form']['userModel'])) {
			return false;
		}

		return $this->BcAuth->authenticate['Form']['userModel'];
	}

	/**
	 * Redirects to given $url, after turning off $this->autoRender.
	 * Script execution is halted after the redirect.
	 *
	 * @param mixed $url A string or array-based URL pointing to another location within the app, or an absolute URL
	 * @param int $status Optional HTTP status code (eg: 404)
	 * @param bool $exit If true, exit() will be called after the redirect
	 * @return void if $exit = false. Terminates script if $exit = true
	 */
	public function redirect($url, $status = null, $exit = true)
	{
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
	 */
	public function requestAction($url, $extra = [])
	{
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
	 * @return void
	 */
	public function admin_ajax_save_favorite_box($open = '')
	{
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
	 */
	public function admin_ajax_batch()
	{
		$this->_checkSubmitToken();
		$method = $this->request->data['ListTool']['batch'];

		if ($this->request->data['ListTool']['batch_targets']) {
			foreach($this->request->data['ListTool']['batch_targets'] as $key => $batchTarget) {
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
	 * @param string $key キー
	 * @param mixed $open 1 Or ''
	 * @return void
	 */
	public function admin_ajax_save_search_box($key, $open = '')
	{
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
	 * @return mixed Returns the return value of the called action
	 */
	public function setAction($action)
	{
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
		$return = call_user_func_array([$this, $action], $args);
		$this->request->action = $_action;
		return $return;
		// <<<
	}

	/**
	 * テーマ用のヘルパーをセットする
	 * 管理画面では読み込まない
	 *
	 * @return void
	 */
	protected function setThemeHelpers()
	{
		if (!empty($this->request->params['admin'])) {
			return;
		}

		$themeHelpersPath = WWW_ROOT . 'theme' . DS . Configure::read('BcSite.theme') . DS . 'Helper';
		$Folder = new Folder($themeHelpersPath);
		$files = $Folder->read(true, true);
		if (empty($files[1])) {
			return;
		}

		foreach($files[1] as $file) {
			$file = str_replace('-', '_', $file);
			$this->helpers[] = Inflector::camelize(basename($file, 'Helper.php'));
		}
	}

	/**
	 * Ajax用のエラーを出力する
	 *
	 * @param int $errorNo エラーのステータスコード
	 * @param mixed $message エラーメッセージ
	 * @return void
	 */
	public function ajaxError($errorNo = 500, $message = '')
	{
		header('HTTP/1.1 ' . $errorNo);
		if (!$message) {
			exit;
		}

		if (!is_array($message)) {
			exit($message);
		}

		$aryMessage = [];
		foreach($message as $value) {
			if (is_array($value)) {
				$aryMessage[] = implode('<br />', $value);
			} else {
				$aryMessage[] = $value;
			}
		}
		exit(implode('<br />', $aryMessage));
	}

	/**
	 * メッセージをビューにセットする
	 *
	 * @param string $message メッセージ
	 * @param bool $alert 警告かどうか
	 * @param bool $saveDblog Dblogに保存するか
	 * @param bool $setFlash flash message に保存するか
	 * @return void
	 * @deprecated 5.0.0 since 4.1.5 BcMessage に移行
	 */
	protected function setMessage($message, $alert = false, $saveDblog = false, $setFlash = true)
	{
		$this->BcMessage->set($message, $alert, $saveDblog, $setFlash);
	}

	/**
	 * イベントを発火
	 *
	 * @param string $name イベント名
	 * @param array $params パラメータ
	 * @param array $options オプション
	 * @return mixed
	 */
	public function dispatchEvent($name, $params = [], $options = [])
	{
		$dbg = debug_backtrace();
		if(!empty($dbg[1]['function']) && $dbg[1]['function'] == 'invokeArgs') {
			$this->notFound();
		}
		$options = array_merge([
			'modParams' => 0,
			'plugin' => $this->plugin,
			'layer' => 'Controller',
			'class' => $this->name
		], $options);
		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch($name, $this, $params, $options);
	}

	/**
	 * Token の key を取得
	 * CSRF対策のためにフォームのトークンを入手するためのもの
	 * adminと表画面でアクションを分離するために、取得部分を共通化
	 *
	 * @return string
	 */
	protected function getToken()
	{
		return $this->request->params['_Token']['key'];
	}

	/**
	 * リクエストメソッドとトークンをチェックする
	 *
	 * - GETでのアクセスの場合 not found
	 * - トークンが送信されていない場合 not found
	 */
	protected function _checkSubmitToken()
	{
		if (strtoupper($_SERVER['REQUEST_METHOD']) === 'GET' || empty($_POST['_Token']['key']) && empty($_POST['data']['_Token']['key'])) {
			throw new NotFoundException();
		}
	}

	/**
	 * リファラチェックを行う
	 *
	 * @return bool
	 */
	protected function _checkReferer()
	{
		$siteDomain = BcUtil::getCurrentDomain();
		if (empty($_SERVER['HTTP_REFERER'])) {
			return false;
		}
		$refererDomain = BcUtil::getDomain($_SERVER['HTTP_REFERER']);
		if (!preg_match('/^' . preg_quote($siteDomain, '/') . '/', $refererDomain)) {
			throw new NotFoundException();
		}
		return true;
	}
}
