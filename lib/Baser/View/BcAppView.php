<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * view 拡張クラス
 *
 * @package Baser.View
 * @property \BcAdminHelper $BcAdmin
 * @property \BcArrayHelper $BcArray
 * @property \BcBaserHelper $BcBaser
 * @property \BcCacheHelper $BcCache
 * @property \BcCkeditorHelper $BcCkeditor
 * @property \BcContentsHelper $BcContents
 * @property \BcCsvHelper $BcCsv
 * @property \BcFormHelper $BcForm
 * @property \BcFormTableHelper $BcFormTable
 * @property \BcFreezeHelper $BcFreeze
 * @property \BcGooglemapsHelper $BcGooglemaps
 * @property \BcHtmlHelper $BcHtml
 * @property \BcLayoutHelper $BcLayout
 * @property \BcListTableHelper $BcListTable
 * @property \BcMobileHelper $BcMobile
 * @property \BcPageHelper $BcPage
 * @property \BcSearchBoxHelper $BcSearchBox
 * @property \BcSmartphoneHelper $BcSmartphone
 * @property \BcTextHelper $BcText
 * @property \BcTimeHelper $BcTime
 * @property \BcUploadHelper $BcUpload
 * @property \BcWidgetAreaHelper $BcWidgetArea
 * @property \BcXmlHelper $BcXml
 * @property \BlogHelper $Blog
 * @property \FeedHelper $Feed
 * @property \MaildataHelper $Maildata
 * @property \MailfieldHelper $Mailfield
 * @property \MailformHelper $Mailform
 * @property \MailHelper $Mail
 * @property \UploaderHelper $Uploader
 */
class BcAppView extends View
{

	/**
	 * ページタイトル
	 *
	 * @var string
	 */
	public $pageTitle = null;

	/**
	 * エレメントキャッシュ
	 *
	 * @var string
	 */
	public $elementCache = '_cake_element_';

	/**
	 * テンプレートファイル一覧出力用
	 * デバッグモード２で利用
	 * @var array
	 */
	protected $_viewFilesLog = [];

	/**
	 * List of variables to collect from the associated controller
	 *
	 * @var array
	 */
	protected $_passedVars = [
		'viewVars', 'autoLayout', 'ext', 'helpers', 'view', 'layout', 'name', 'theme',
		'layoutPath', 'viewPath', 'request', 'plugin', 'passedArgs', 'cacheAction',
		'subDir', 'adminTheme', 'pageTitle', 'content', 'site'
	];

	/**
	 * Return all possible paths to find view files in order
	 *
	 * @param string $plugin Optional plugin name to scan for view files.
	 * @param boolean $cached Set to true to force a refresh of view paths.
	 * @return array paths
	 */
	protected function _paths($plugin = null, $cached = true)
	{

		if ($plugin === null && $cached === true && !empty($this->_paths)) {
			return $this->_paths;
		}
		$paths = [];
		$viewPaths = App::path('View');
		$corePaths = array_merge(App::core('View'), App::core('Console/Templates/skel/View'));

		if (!empty($plugin)) {
			$count = count($viewPaths);
			for($i = 0; $i < $count; $i++) {
				if (!in_array($viewPaths[$i], $corePaths)) {
					$paths[] = $viewPaths[$i] . 'Plugin' . DS . $plugin . DS;
				}
			}
			$paths = array_merge($paths, App::path('View', $plugin));
		}

		$paths = array_unique(array_merge($paths, $viewPaths));

		// CUSTOMIZE ADD 2013/08/17 ryuring
		// >>>
		$adminThemePaths = [];
		$webroot = Configure::read('App.www_root');
		if (!empty($this->adminTheme)) {
			foreach($paths as $path) {
				if (strpos($path, DS . 'Plugin' . DS) === false) {
					if ($plugin) {
						$adminThemePaths[] = $path . 'Themed' . DS . $this->adminTheme . DS . 'Plugin' . DS . $plugin . DS;
					}
					$adminThemePaths[] = $path . 'Themed' . DS . $this->adminTheme . DS;
				}
			}
			$adminThemePaths = array_merge([$webroot . 'theme' . DS . $this->adminTheme . DS], $adminThemePaths);
		}
		// <<<

		if (!empty($this->theme)) {
			$themePaths = [];
			foreach($paths as $path) {
				if (strpos($path, DS . 'Plugin' . DS) === false) {
					if ($plugin) {
						$themePaths[] = $path . 'Themed' . DS . $this->theme . DS . 'Plugin' . DS . $plugin . DS;
					}
					$themePaths[] = $path . 'Themed' . DS . $this->theme . DS;
				}
			}

			// CUSTOMIZE MODIFY 2013/08/17 ryuring
			// >>>
			//$paths = array_merge($themePaths, $paths);
			// --
			$themePaths = array_merge([$webroot . 'theme' . DS . $this->theme . DS], $themePaths);
			$paths = array_merge($themePaths, $adminThemePaths, $paths);
			// <<<
		}

		// CUSTOMIZE ADD 2013/08/26 ryuring
		// Baserディレクトリのパスの優先順位を下げる
		// >>>
		$baserPaths = [];
		foreach($paths as $key => $path) {
			if (strpos($path, BASER) !== false) {
				unset($paths[$key]);
				$baserPaths[] = $path;
			}
		}
		$paths = array_merge($paths, $baserPaths);
		// <<<

		$paths = array_merge($paths, $corePaths);
		if ($plugin !== null) {
			return $paths;
		}
		return $this->_paths = $paths;
	}

	/**
	 * Returns filename of given action's template file (.ctp) as a string.
	 * CamelCased action names will be under_scored! This means that you can have
	 * LongActionNames that refer to long_action_names.ctp views.
	 *
	 * @param string $name Controller action to find template filename for
	 * @return string Template filename
	 * @throws MissingViewException when a view file could not be found.
	 */
	protected function _getViewFileName($name = null)
	{

		$subDir = null;

		if (!is_null($this->subDir)) {
			$subDir = $this->subDir . DS;
		}

		if ($name === null) {
			$name = $this->view;
		}

		// CUSTOMIZE ADD 2012/04/11 ryuring
		// プレフィックスが設定されている場合は、プレフィックスを除外する
		// >>>
		$prefix = '';
		if (!empty($this->request->params['prefix'])) {
			$prefix = $this->request->params['prefix'];
		}
		if ($prefix && preg_match('/^' . $prefix . '_/', $name)) {
			$name = str_replace($prefix . '_', '', $name);
		} elseif (preg_match('/^admin_/', $name)) {
			// プレフィックスをadminとしてすり替え
			$name = str_replace('admin_', '', $name);
		}
		// >>>

		$name = str_replace('/', DS, $name);
		list($plugin, $name) = $this->pluginSplit($name);

		// CUSTOMIZE ADD 2012/04/11 ryuring
		// CakeErrorの場合はサブフォルダを除外
		// >>>
		if ($this->name == 'CakeError' && preg_match('/^Errors\/?/', $this->viewPath)) {
			$subDir = $this->subDir . DS;
			$this->subDir = null;

			if ($this->layoutPath == 'rss') {
				$this->layoutPath = null;
				$this->viewPath = 'Errors';
				$this->response->type('html');
			}

			$exception = $this->get('error');
			if ($exception && $exception instanceof MissingConnectionException) {
				$this->layout = 'missing_connection';
			}
		}
		// <<<
		// CUSTOMIZE ADD 2013/08/25 ryuring
		// イベントを追加
		// >>>
		$event = $this->dispatchEvent('beforeGetViewFileName', ['name' => $name], ['class' => '', 'plugin' => '']);
		if ($event !== false) {
			$name = ($event->result === null || $event->result === true)? $event->data['name'] : $event->result;
		}
		$event = $this->dispatchEvent('beforeGetViewFileName', ['name' => $name]);
		if ($event !== false) {
			$name = ($event->result === null || $event->result === true)? $event->data['name'] : $event->result;
		}
		// <<<

		if (strpos($name, DS) === false && $name[0] !== '.') {
			// CUSTOMIZE MODIFY 2016/06/01 ryuring
			// サブフォルダが存在しない場合にはサブフォルダなしのパスを利用するようにした
			// >>>
			//$name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
			// ---
			$name = [
				$this->viewPath . DS . $subDir . Inflector::underscore($name),
				$this->viewPath . DS . Inflector::underscore($name),
			];
			// <<<
		} elseif (strpos($name, DS) !== false) {
			if ($name[0] === DS || $name[1] === ':') {
				if (is_file($name)) {
					return $name;
				}
				$name = trim($name, DS);
			} elseif ($name[0] === '.') {
				$name = substr($name, 3);
				// CUSTOMIZE MODIFY 2013/08/21 ryuring
				// サブフォルダが適用されない為調整
				// CUSTOMIZE MODIFY 2016/06/01 ryuring
				// サブフォルダが存在しない場合にはサブフォルダなしのパスを利用するようにした
				// >>>
				/*
				} elseif (!$plugin || $this->viewPath !== $this->name) {
					$name = $this->viewPath . DS . $subDir . $name;
				*/
				// ---
			} else {
				$name = [
					$this->viewPath . DS . $subDir . $name,
					$this->viewPath . DS . $name
				];
				// <<<
			}
		}
		$paths = $this->_paths($plugin);
		$exts = $this->_getExtensions();

		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 拡張子優先順位よりもパスの優先順位を優先する仕様に変更
		// CUSTOMIZE MODIFY 2016/06/01 ryuring
		// サブフォルダが存在しない場合にはサブフォルダなしのパスを利用するようにした
		// >>>
		/*
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $name . $ext)) {
					return $path . $name . $ext;
		  		}
		  	}
		}
		*/
		// ---
		if (is_array($name)) {
			$names = $name;
		} else {
			$names[] = $name;
		}
		foreach($names as $name) {
			foreach($paths as $path) {
				foreach($exts as $ext) {
					if (file_exists($path . $name . $ext)) {
						return $path . $name . $ext;
					}
				}
			}
		}
		// <<<

		$defaultPath = $paths[0];

		if ($this->plugin) {
			$pluginPaths = App::path('plugins');
			foreach($paths as $path) {
				if (strpos($path, $pluginPaths[0]) === 0) {
					$defaultPath = $path;
					break;
				}
			}
		}
		throw new MissingViewException(['file' => $defaultPath . $name . $this->ext]);
	}

	/**
	 * Finds an element filename, returns false on failure.
	 *
	 * @param string $name The name of the element to find.
	 * @return mixed Either a string to the element filename or false when one can't be found.
	 */
	protected function _getElementFileName($name)
	{

		// CUSTOMIZE ADD 2013/08/27 ryuring
		// イベントを追加
		// >>>
		$event = $this->dispatchEvent('beforeGetElementFileName', ['name' => $name], ['class' => '', 'plugin' => '']);
		if ($event !== false) {
			$name = ($event->result === null || $event->result === true)? $event->data['name'] : $event->result;
		}
		$event = $this->dispatchEvent('beforeGetElementFileName', ['name' => $name]);
		if ($event !== false) {
			$name = ($event->result === null || $event->result === true)? $event->data['name'] : $event->result;
		}
		// <<<

		list($plugin, $name) = $this->pluginSplit($name);

		// CUSTOMIZE ADD 2013/08/26 ryuring
		// サブフォルダを追加
		// 2014/02/24 追記
		// サブフォルダ内にテンプレートが存在しない場合上位階層のテンプレートも検索する仕様に変更
		// >>>
		$names = [$name];
		if ($this->subDir) {
			if (preg_match('/^\.\.\/([^\/]+)\/(.+)$/', $name, $matches)) {
				// Elements ではなく、コントローラー内のテンプレートを参照する場合の処理
				// BlogBaserHelper::blogPosts() で、ブログコントローラー内のテンプレートを参照している為（posts等）
				// TODO テンプレートの場所を Elements 内に移動するべき
				$name = '..' . DS . $matches[1] . DS . $this->subDir . DS . $matches[2];
			} else {
				$name = $this->subDir . DS . $name;
			}
			array_unshift($names, $name);
		}
		// <<<

		$paths = $this->_paths($plugin);
		$exts = $this->_getExtensions();

		// CUSTOMIZE MODIFY 2014/02/24 ryuring
		// サブフォルダ内にテンプレートが存在しない場合上位階層のテンプレートも検索する仕様に変更
		// >>>
		/*foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . 'Elements' . DS . $name . $ext)) {
					return $path . 'Elements' . DS . $name . $ext;
				}
			}
		}*/
		// ---
		foreach($names as $name) {
			foreach($exts as $ext) {
				foreach($paths as $path) {
					if (file_exists($path . 'Elements' . DS . $name . $ext)) {
						return $path . 'Elements' . DS . $name . $ext;
					}
				}
			}
		}
		// <<<

		return false;
	}

	/**
	 * Returns layout filename for this template as a string.
	 *
	 * @param string $name The name of the layout to find.
	 * @return string Filename for layout file (.ctp).
	 * @throws MissingLayoutException when a layout cannot be located
	 */
	protected function _getLayoutFileName($name = null)
	{

		if ($name === null) {
			$name = $this->layout;
		}
		$subDir = null;

		// CUSTOMIZE ADD 2013/08/25 ryuring
		// >>>
		$event = $this->dispatchEvent('beforeGetLayoutFileName', ['name' => $name], ['class' => '', 'plugin' => '']);
		if ($event !== false) {
			$name = ($event->result === null || $event->result === true)? $event->data['name'] : $event->result;
		}
		$event = $this->dispatchEvent('beforeGetLayoutFileName', ['name' => $name]);
		if ($event !== false) {
			$name = ($event->result === null || $event->result === true)? $event->data['name'] : $event->result;
		}
		// <<<

		if (!is_null($this->layoutPath)) {
			$subDir = $this->layoutPath . DS;
		}
		list($plugin, $name) = $this->pluginSplit($name);
		$paths = $this->_paths($plugin);

		// CUSTOMIZE MODIFY 2016/06/01 ryuring
		// サブフォルダが存在しない場合にはサブフォルダなしのパスを利用するようにした
		// >>>
		//$file = 'Layouts' . DS . $subDir . $name;
		// ---
		$files = [
			'Layouts' . DS . $subDir . $name,
			'Layouts' . DS . $name
		];
		// <<<

		$exts = $this->_getExtensions();

		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 拡張子優先順位よりもパスの優先順位を優先する仕様に変更
		// >>>
		/*
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $file . $ext)) {
					return $path . $file . $ext;
				}
			}
		}
		*/
		// ---
		foreach($files as $file) {
			foreach($paths as $path) {
				foreach($exts as $ext) {
					if (file_exists($path . $file . $ext)) {
						return $path . $file . $ext;
					}
				}
			}
		}
		// <<<

		throw new MissingLayoutException(['file' => $paths[0] . $file . $this->ext]);
	}

	/**
	 * Get the extensions that view files can use.
	 *
	 * @return array Array of extensions view files use.
	 */
	protected function _getExtensions()
	{
		$this->ext = Configure::read('BcApp.templateExt');
		$exts = [$this->ext];
		if ($this->ext !== '.ctp') {
			$exts[] = '.ctp';
		}
		return $exts;
	}

	/**
	 * イベントを発火
	 *
	 * @param string $name
	 * @param array $params
	 * @return mixed
	 */
	public function dispatchEvent($name, $params = [], $options = [])
	{

		// CakeEmailより呼び出される場合、AppViewを直接呼び出す為、$this->nameに値が入らない。
		// その際、View.beforeRenderをループで呼び出してしまうので、イベントを実行しない。
		if (!$this->name) {
			return false;
		}

		$options = array_merge([
			'modParams' => 0,
			'plugin' => $this->plugin,
			'layer' => 'View',
			'class' => $this->name
		], $options);
		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch($name, $this, $params, $options);
	}

	/**
	 * Sandbox method to evaluate a template / view script in.
	 *
	 * @param string $viewFn Filename of the view
	 * @param array $dataForView Data to include in rendered view.
	 *    If empty the current View::$viewVars will be used.
	 * @return string Rendered output
	 */
	public function evaluate($viewFile, $dataForView)
	{
		return $this->_evaluate($viewFile, $dataForView);
	}

	/**
	 * Sandbox method to evaluate a template / view script in.
	 *
	 * @param string $viewFile Filename of the view
	 * @param array $dataForView Data to include in rendered view.
	 *    If empty the current View::$viewVars will be used.
	 * @return string Rendered output
	 */
	protected function _evaluate($viewFile, $dataForView)
	{
		// ADD 2016/05/12 ryuring
		// デバッグモード２でテンプレート一覧を出力する為に追加
		// >>>
		if (Configure::read('debug') > 1) {
			$this->_viewFilesLog[] = $viewFile;
		}
		// <<<
		return parent::_evaluate($viewFile, $dataForView);
	}

}
