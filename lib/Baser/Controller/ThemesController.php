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

App::uses('BcZip', 'Lib');

/**
 * Class ThemesController
 *
 * @property Page $Page
 * @property Theme $Theme
 * @property SiteConfig $SiteConfig
 * @property BcManagerComponent $BcManager
 */
class ThemesController extends AppController
{

	/**
	 * コントローラー名
	 * @var string
	 * @access    public
	 */
	public $name = 'Themes';

	/**
	 * モデル
	 * @var array
	 */
	public $uses = ['Theme', 'Page', 'SiteConfig'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcManager'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcForm'];

	/**
	 * ThemesController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeRequest $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->crumbs = [
			['name' => __d('baser', 'テーマ管理'), 'url' => ['controller' => 'themes', 'action' => 'index']]
		];
	}

	/**
	 * テーマをアップロードして適用する
	 */
	public function admin_add()
	{
		$this->pageTitle = __d('baser', 'テーマアップロード');
		$this->subMenuElements = ['themes'];
		if (!$this->request->is(['post', 'put'])) {
			return;
		}

		if ($this->Theme->isOverPostSize()) {
			$this->BcMessage->setError(
				__d(
					'baser',
					'送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
					ini_get('post_max_size')
				)
			);
		}
		if (empty($this->request->data['Theme']['file']['tmp_name'])) {
			$message = __d('baser', 'ファイルのアップロードに失敗しました。');
			if (!empty($this->request->data['Theme']['file']['error']) && $this->request->data['Theme']['file']['error'] == 1) {
				$message .= __d('baser', 'サーバに設定されているサイズ制限を超えています。');
			}
			$this->BcMessage->setError($message);
			return;
		}

		$name = $this->request->data['Theme']['file']['name'];
		move_uploaded_file($this->request->data['Theme']['file']['tmp_name'], TMP . $name);
		$BcZip = new BcZip();
		if (!$BcZip->extract(TMP . $name, BASER_THEMES)) {
			$msg = __d('baser', 'アップロードしたZIPファイルの展開に失敗しました。');
			$msg .= "\n" . $BcZip->error;
			$this->BcMessage->setError($msg);
			return;
		}
		unlink(TMP . $name);
		$this->BcMessage->setInfo('テーマファイル「' . $name . '」を追加しました。');
		$this->redirect(['action' => 'index']);
	}

	/**
	 * テーマ一覧
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->pageTitle = __d('baser', 'テーマ一覧');
		$themes = BcUtil::getThemeList();
		$datas = [];
		$currentTheme = null;
		foreach($themes as $themename) {
			if ($themename !== 'core' && $themename !== '_notes') {
				if ($themename == $this->siteConfigs['theme']) {
					$currentTheme = $this->_loadThemeInfo($themename);
				} else {
					$datas[] = $this->_loadThemeInfo($themename);
				}
			}
		}

		$this->set('datas', $datas);
		$this->set('currentTheme', $currentTheme);
		$this->set('defaultDataPatterns', $this->BcManager->getDefaultDataPatterns($this->siteConfigs['theme'], ['useTitle' => false]));
		$this->subMenuElements = ['themes'];
		$this->help = 'themes_index';
	}

	/**
	 * baserマーケットのテーマデータを取得する
	 */
	public function admin_ajax_get_market_themes()
	{

		$baserThemes = [];

		$cachePath = 'views' . DS . 'baser_market_themes.rss';
		if (Configure::read('debug') > 0) {
			clearCache('baser_market_themes', 'views', '.rss');
		}
		$baserThemes = cache($cachePath);
		if (!$baserThemes) {
			$Xml = new Xml();
			try {
				$context = stream_context_create(array('ssl'=>array(
					'allow_self_signed'=> true,
					'verify_peer' => false,
				)));
				libxml_set_streams_context($context);
				$baserThemes = simplexml_load_file(Configure::read('BcApp.marketThemeRss'));
			} catch (Exception $ex) {
			}
			if ($baserThemes) {
				$baserThemes = $Xml->toArray($baserThemes->channel);
				$baserThemes = $baserThemes['channel']['item'];
				cache($cachePath, BcUtil::serialize($baserThemes));
				chmod(CACHE . $cachePath, 0666);
			} else {
				$baserThemes = [];
			}
		} else {
			$baserThemes = BcUtil::unserialize($baserThemes);
		}

		$this->set('baserThemes', $baserThemes);

	}

	/**
	 * 初期データセットを読み込む
	 *
	 * @return void
	 */
	public function admin_load_default_data_pattern()
	{
		if (empty($this->request->data['Theme']['default_data_pattern'])) {
			$this->BcMessage->setError(__d('baser', '不正な操作です。'));
			$this->redirect('index');
			return;
		}
		$result = $this->_load_default_data_pattern($this->request->data['Theme']['default_data_pattern']);
		if (!$result) {
			if (!CakeSession::check('Message.flash.message')) {
				$this->BcMessage->setError(__d('baser', '初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。'));
			}
			$this->redirect('index');
			return;
		}

		$this->BcMessage->setInfo(__d('baser', '初期データの読み込みが完了しました。'));
		$this->redirect('index');
	}

	/**
	 * コアの初期データを読み込む
	 *
	 * @return void
	 */
	public function admin_reset_data()
	{
		$this->_checkSubmitToken();
		$result = $this->_load_default_data_pattern('core.default', $this->siteConfigs['theme']);
		if (!$result) {
			$this->BcMessage->setError(__d('baser', '初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。'));
			$this->redirect('/admin');
			return;
		}

		$this->BcMessage->setInfo(__d('baser', '初期データの読み込みが完了しました。'));
		$this->redirect('/admin');
	}

	/**
	 * 初期データを読み込む
	 *
	 * @param string $dbDataPattern 初期データのパターン
	 * @param string $currentTheme テーマ名
	 * @return bool
	 */
	protected function _load_default_data_pattern($dbDataPattern, $currentTheme = '')
	{
		list($theme, $pattern) = explode('.', $dbDataPattern);
		if (!$this->BcManager->checkDefaultDataPattern($pattern, $theme)) {
			$this->BcMessage->setError(__d('baser', '初期データのバージョンが違うか、初期データの構造が壊れています。'));
			return false;
		}
		$adminTheme = Configure::read('BcSite.admin_theme');
		$excludes = ['plugins', 'dblogs', 'users'];
		/* データを削除する */
		$this->BcManager->resetAllTables(null, $excludes);
		$result = true;
		/* コアデータ */
		if (!$this->BcManager->loadDefaultDataPattern('default', null, $pattern, $theme, 'core', $excludes)) {
			$result = false;
			$this->log(sprintf(__d('baser', '%s の初期データのロードに失敗しました。'), $dbDataPattern));
		}

		/* プラグインデータ */
		$corePlugins = Configure::read('BcApp.corePlugins');
		$plugins = array_merge($corePlugins, BcUtil::getCurrentThemesPlugins());

		foreach($plugins as $plugin) {
			$this->BcManager->loadDefaultDataPattern('default', null, $pattern, $theme, $plugin, $excludes);
		}
		if (!$result) {
			/* 指定したデータセットでの読み込みに失敗した場合、コアのデータ読み込みを試みる */
			if (!$this->BcManager->loadDefaultDataPattern('default', null, 'default', 'core', 'core', $excludes)) {
				$this->log(__d('baser', 'コアの初期データのロードに失敗しました。'));
				$result = false;
			}
			foreach($corePlugins as $corePlugin) {
				if (!$this->BcManager->loadDefaultDataPattern('default', null, 'default', 'core', $corePlugin, $excludes)) {
					$this->log(__d('baser', 'コアのプラグインの初期データのロードに失敗しました。'));
					$result = false;
				}
			}
			if ($result) {
				$this->BcMessage->setError(__d('baser', '初期データの読み込みに失敗しましたので baserCMSコアの初期データを読み込みました。'));
			} else {
				$this->BcMessage->setError(__d('baser', '初期データの読み込みに失敗しました。データが不完全な状態です。正常に動作しない可能性があります。'));
			}
		}

		clearAllCache();

		// メール受信テーブルの作成
		App::uses('MailMessage', 'Mail.Model');
		$MailMessage = new MailMessage();
		if (!$MailMessage->reconstructionAll()) {
			$this->log(__d('baser', 'メールプラグインのメール受信用テーブルの生成に失敗しました。'));
			$result = false;
		}
		clearAllCache();
		ClassRegistry::flush();
		BcSite::flash();

		if ($currentTheme) {
			$siteConfigs = ['SiteConfig' => ['theme' => $currentTheme]];
			$this->SiteConfig->saveKeyValue($siteConfigs);
		}

		if (!$this->Page->createAllPageTemplate()) {
			$result = false;
			$this->log(
				__d('baser', '初期データの読み込み中にページテンプレートの生成に失敗しました。') .
				__d('baser', '「Pages」フォルダに書き込み権限が付与されていない可能性があります。') .
				__d('baser', '権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。')
			);
		}
		// システムデータの初期化
		// TODO $this->BcManager->initSystemData() は、$this->Page->createAllPageTemplate() の
		// 後に呼出さないと $this->Page の実体が何故か AppModel にすりかわってしまい、
		// createAllPageTemplate メソッドが呼び出せないので注意
		if (!$this->BcManager->initSystemData(null, ['excludeUsers' => true, 'adminTheme' => $adminTheme])) {
			$result = false;
			$this->log(__d('baser', 'システムデータの初期化に失敗しました。'));
		}
		// ユーザーデータの初期化
		$User = ClassRegistry::init('User');
		$UserGroup = ClassRegistry::init('UserGroup');
		$adminGroupId = $UserGroup->field('id', ['UserGroup.name' => 'admins']);
		$users = $User->find('all', ['recursive' => -1]);
		foreach($users as $userData) {
			$userData['User']['user_group_id'] = $adminGroupId;
			unset($userData['User']['password']);
			if (!$User->save($userData)) {
				$result = false;
				$this->log(__d('baser', 'ユーザーデータの初期化に失敗しました。手動で各ユーザーのユーザーグループの設定を行なってください。'));
			}
			if (!$User->applyDefaultFavorites($userData['User']['id'], $userData['User']['user_group_id'])) {
				$result = false;
				$this->log(__d('baser', 'ユーザーのよく使う項目データの初期化に失敗しました。手動で各ユーザーのよく使う項目の設定を行なってください。'));
			}
		}
		$Db = ConnectionManager::getDataSource('default');
		if ($Db->config['datasource'] === 'Database/BcPostgres') {
			$Db->updateSequence();
		}
		// システム基本設定の更新
		$siteConfigs = ['SiteConfig' => [
			'email' => $this->siteConfigs['email'],
			'google_analytics_id' => $this->siteConfigs['google_analytics_id'],
			'first_access' => null,
			'version' => $this->siteConfigs['version']
		]];
		$this->SiteConfig->saveKeyValue($siteConfigs);


		return $result;

	}

	/**
	 * テーマ情報を読み込む
	 *
	 * @param string $themename テーマ名
	 * @return array
	 */
	protected function _loadThemeInfo($themename)
	{
		$path = WWW_ROOT . 'theme';
		$title = $description = $author = $url = $screenshot = '';
		$theme = [];
		if (file_exists($path . DS . $themename . DS . 'config.php')) {
			include $path . DS . $themename . DS . 'config.php';
		}
		if (file_exists($path . DS . $themename . DS . 'screenshot.png')) {
			$theme['screenshot'] = true;
		} else {
			$theme['screenshot'] = false;
		}
		$theme['name'] = $themename;
		$theme['title'] = $title;
		$theme['description'] = $description;
		$theme['author'] = $author;
		$theme['url'] = $url;
		$theme['version'] = $this->getThemeVersion($theme['name']);
		return $theme;
	}

	/**
	 * テーマをコピーする
	 *
	 * @param string $theme
	 * @return void
	 */
	public function admin_ajax_copy($theme)
	{
		$this->_checkSubmitToken();
		if (!$theme) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$result = $this->_copy($theme);
		if (!$result) {
			$this->ajaxError(500, __d('baser', 'テーマフォルダのアクセス権限を見直してください。'));
			return;
		}

		exit(true);
	}

	/**
	 * テーマをコピーする
	 *
	 * @param string $theme
	 * @return array|bool
	 */
	protected function _copy($theme)
	{
		$basePath = WWW_ROOT . 'theme' . DS;
		$newTheme = $theme . '_copy';
		while(true) {
			if (!is_dir($basePath . $newTheme)) {
				break;
			}
			$newTheme .= '_copy';
		}
		$folder = new Folder();
		$result = $folder->copy([
			'from' => $basePath . $theme,
			'to' => $basePath . $newTheme,
			'mode' => 0777,
			'skip' => ['_notes']
		]);
		if (!$result) {
			return false;
		}

		$this->Theme->saveDblog('テーマ「' . $theme . '」をコピーしました。');
		return $this->_loadThemeInfo($newTheme);
	}

	/**
	 * テーマを削除する　(ajax)
	 *
	 * @param string $theme
	 * @return void
	 */
	public function admin_ajax_delete($theme)
	{
		$this->_checkSubmitToken();
		if (!$theme) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		if (!$this->_del($theme)) {
			$this->ajaxError(500, __d('baser', 'テーマフォルダを手動で削除してください。'));
			exit;
		}
		clearViewCache();
		exit(true);
	}

	/**
	 * データを削除する
	 *
	 * @param string $theme テーマ名
	 * @return bool
	 */
	protected function _del($theme)
	{
		$path = WWW_ROOT . 'theme' . DS . $theme;
		$folder = new Folder();
		if (!$folder->delete($path)) {
			return false;
		}
		$siteConfig = ['SiteConfig' => $this->siteConfigs];
		if ($theme == $siteConfig['SiteConfig']['theme']) {
			$siteConfig['SiteConfig']['theme'] = '';
			$this->SiteConfig->saveKeyValue($siteConfig);
		}
		return true;
	}

	/**
	 * テーマを削除する
	 *
	 * @param string $theme
	 * @return void
	 */
	public function admin_del($theme)
	{
		$this->_checkSubmitToken();
		if (!$theme) {
			$this->notFound();
		}
		$siteConfig = ['SiteConfig' => $this->siteConfigs];
		$path = WWW_ROOT . 'theme' . DS . $theme;
		$folder = new Folder();
		$folder->delete($path);
		if ($theme == $siteConfig['SiteConfig']['theme']) {
			$siteConfig['SiteConfig']['theme'] = '';
			$this->SiteConfig->saveKeyValue($siteConfig);
		}
		clearViewCache();
		$this->BcMessage->setInfo('テーマ「' . $theme . '」を削除しました。');
		$this->redirect(['action' => 'index']);
	}

	/**
	 * テーマを適用する
	 *
	 * @param string $theme
	 * @return void
	 */
	public function admin_apply($theme)
	{
		$this->_checkSubmitToken();
		if (!$theme) {
			$this->notFound();
		}

		$this->_applyTheme($theme);
		$this->redirect(['action' => 'index']);

	}

	protected function _applyTheme($theme)
	{

		$plugins = BcUtil::getCurrentThemesPlugins();
		// テーマ梱包のプラグインをアンインストール
		foreach($plugins as $plugin) {
			$this->BcManager->uninstallPlugin($plugin);
		}

		$siteConfig['SiteConfig']['theme'] = $theme;
		$this->SiteConfig->saveKeyValue($siteConfig);
		clearViewCache();

		$info = [];
		$themePath = BASER_THEMES . $theme . DS;

		$Folder = new Folder($themePath . 'Plugin');
		$files = $Folder->read(true, true, false);
		if (!empty($files[0])) {
			$info = array_merge($info, [
				__d('baser', 'このテーマは下記のプラグインを同梱しています。')
			]);
			foreach($files[0] as $file) {
				$info[] = '	・' . $file;
			}
		}

		Configure::write('BcSite.theme', $theme);
		$plugins = BcUtil::getCurrentThemesPlugins();

		App::build(['Plugin' => array_merge([BASER_THEMES . $theme . DS . 'Plugin' . DS], App::path('Plugin'))]);
		// テーマ梱包のプラグインをインストール
		foreach($plugins as $plugin) {
			$this->BcManager->installPlugin($plugin);
		}

		$path = BcUtil::getDefaultDataPath('Core', $theme);
		if (strpos($path, '/theme/' . $theme . '/') !== false) {
			if ($info) {
				$info = array_merge($info, ['']);
			}
			$info = array_merge($info, [
				__d('baser', 'このテーマは初期データを保有しています。'),
				__d('baser', 'Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。'),
			]);
		}

		if (!$this->Page->createAllPageTemplate()) {
			$message = [
				__d('baser', 'テーマ変更中にページテンプレートの生成に失敗しました。'),
				__d('baser', '「Pages」フォルダに書き込み権限が付与されていない可能性があります。'),
				__d('baser', '権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。')
			];
			if ($info) {
				$message = array_merge($message, [''], $info);
			}
			$this->BcMessage->setError(implode("\n", $message));
			return true;
		}

		$message = ['テーマ「' . $theme . '」を適用しました。'];
		if ($info) {
			$message = array_merge($message, [''], $info);
		}
		$this->BcMessage->setInfo(implode("\n", $message));
		return true;

	}

	/**
	 * 初期データセットをダウンロードする
	 */
	public function admin_download_default_data_pattern()
	{
		if (!extension_loaded('zip')) {
			$this->notFound();
		}

		$this->autoRender = false;
		set_time_limit(0);
		ini_set('memory_limit', -1);

		/* コアのCSVを生成 */
		$tmpDir = TMP . 'csv' . DS;
		$distPath = TMP . 'default.zip';
		$Folder = new Folder();
		$Folder->create($tmpDir);
		emptyFolder($tmpDir);
		clearAllCache();
		$excludes = ['plugins', 'dblogs', 'users', 'favorites'];
		$this->_writeCsv('core', $tmpDir, $excludes);
		/* プラグインのCSVを生成 */
		$plugins = CakePlugin::loaded();
		foreach($plugins as $plugin) {
			$Folder->create($tmpDir . $plugin);
			emptyFolder($tmpDir . $plugin);
			$this->_writeCsv($plugin, $tmpDir . $plugin . DS);
		}
		/* site_configsの編集 (email / google_analytics_id / version) */
		$targets = ['email', 'google_analytics_id', 'version'];
		$path = $tmpDir . 'site_configs.csv';
		$fp = fopen($path, 'a+');
		$records = [];
		while(($record = fgetcsvReg($fp, 10240)) !== false) {
			if (in_array($record[1], $targets)) {
				$record[2] = '';
			}
			$records[] = '"' . implode('","', $record) . '"';
		}
		ftruncate($fp, 0);
		fwrite($fp, implode("\n", $records));

		/* ZIPに固めてダウンロード */
		$bcZip = new BcZip();
		$bcZip->create($tmpDir, $distPath);

		header("Cache-Control: no-store");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=" . basename($distPath) . ";");
		header("Content-Length: " . filesize($distPath));
		while (ob_get_level()) { ob_end_clean(); }
		echo readfile($distPath);

		emptyFolder($tmpDir);
		unlink($distPath);
	}

	/**
	 * CSVファイルを書きだす
	 *
	 * @param string $configKeyName
	 * @param string $path
	 * @return boolean
	 */
	function _writeCsv($plugin, $path, $exclude = [])
	{

		$pluginTables = [];
		if ($plugin !== 'core') {
			$pluginPath = BcUtil::getSchemaPath($plugin);
			$Folder = new Folder($pluginPath);
			$files = $Folder->read(true, true, false);
			$pluginTables = $files[1];
			foreach($pluginTables as $key => $pluginTable) {
				if (preg_match('/^(.+)\.php$/', $pluginTable, $matches)) {
					$pluginTables[$key] = $matches[1];
				} else {
					unset($pluginTables[$key]);
				}
			}
		}

		$pluginKey = Inflector::underscore($plugin);
		$db = ConnectionManager::getDataSource('default');
		$db->cacheSources = false;
		$tables = $db->listSources();
		$tableList = getTableList();
		$result = true;
		foreach($tables as $table) {
			if (($plugin === 'core' && in_array($table, $tableList['core'])) || ($plugin !== 'core' && in_array($table, $tableList['plugin']))) {
				$table = str_replace($db->config['prefix'], '', $table);
				if (in_array($table, $exclude)) {
					continue;
				}
				if ($pluginKey !== 'core' && !in_array($table, $pluginTables)) {
					continue;
				}
				if (!$db->writeCsv([
					'path' => $path . $table . '.csv',
					'encoding' => 'UTF-8',
					'init' => false,
					'plugin' => ($plugin === 'core')? null : $plugin
				])) {
					$result = false;
				}
			}
		}
		return $result;
	}

	/**
	 * ダウンロード
	 */
	public function admin_download()
	{
		if (!extension_loaded('zip')) {
			$this->notFound();
		}

		$this->autoRender = false;
		$tmpDir = TMP . 'theme' . DS;
		$orgPath = BASER_THEMES . $this->siteConfigs['theme'] . DS;
		$sourcePath = $tmpDir . $this->siteConfigs['theme'];
		$distPath = $sourcePath . '.zip';

		$Folder = new Folder();
		$Folder->create($tmpDir);
		$Folder->copy([
			'from' => $orgPath,
			'to' => $sourcePath,
			'chmod' => 0777
		]);
		$bcZip = new BcZip();
		$bcZip->create($sourcePath, $distPath);

		header("Cache-Control: no-store");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=" . basename($distPath) . ";");
		header("Content-Length: " . filesize($distPath));
		while (ob_get_level()) { ob_end_clean(); }
		echo readfile($distPath);

		$Folder->delete($tmpDir);
	}
}
