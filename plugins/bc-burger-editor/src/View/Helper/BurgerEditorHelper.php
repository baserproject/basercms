<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\View\Helper;

use BaserCore\Model\Table\SitesTable;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\View;

class BurgerEditorHelper extends Helper
{

	public static $configJSON = '';    // bgeconfig.jsonのパス
	public static $addonDir = [];    // Addonフォルダパス
	public static $imageFileBaseDir = '';    // 画像フォルダパス
	public static $imageFileBaseURL = '';    // 画像フォルダURL
	public static $imageFileList = [];    // 画像ファイル一覧
	public static $otherFileBaseDir = '';    // 画像フォルダパス
	public static $otherFileBaseURL = '';    // 画像フォルダURL
	public static $otherFileList = [];    // ファイル一覧
	public static $staticPanelDir = '';        // 静的ブロックパネルフォルダパス

	public static $imageFileMaxId = 0;    // 画像ファイル最大ID
	public static $otherFileMaxId = 0;    // その他ファイル最大ID

	public static $bgeConfig = [];    // ブロッククラス設定オプション

	public static $useType = [];
	public static $useBlock = [];
	private $loadingStyle = true;

	/**
	 * コンストラクタ
	 *
	 * @return void
	 * @access public
	 */
	public function __construct(View $View, $settings = [])
	{
		// property として定義すると、baserCMS 5.0系と 5.1系の両方に対応できなくなるためここで定義
		$this->helpers = ['Html', 'BaserCore.BcTime', 'BaserCore.BcBaser', 'BaserCore.BcUpload', 'BcBurgerEditor.BurgerEditor'];

		parent::__construct($View, $settings);
		if (BcUtil::isAdminSystem()) {
			self::setSelfValue();
		}
	}

	public static function setSelfValue()
	{

		self::$configJSON = dirname(dirname(dirname(__FILE__))) . DS . 'bgeconfig.json';
		self::$addonDir = BurgerEditorUtil::getAddonPath();
		self::$imageFileBaseDir = realpath(WWW_ROOT) . DS . 'files' . DS . 'bgeditor' . DS . 'img' . DS;
		self::$otherFileBaseDir = realpath(WWW_ROOT) . DS . 'files' . DS . 'bgeditor' . DS . 'other' . DS;

		$baseUrl = Router::url('/');
		self::$imageFileBaseURL = $baseUrl . 'files/bgeditor/img/';
		self::$otherFileBaseURL = $baseUrl . 'files/bgeditor/other/';

		// 静的ファイル設置ディレクトリ
		$staticDirName = Inflector::underscore(preg_replace('/Helper$/', '', __CLASS__));
		self::$staticPanelDir = WWW_ROOT . $staticDirName . DS . $staticDirName . DS . 'panel' . DS;

		// フォルダがない場合はinit処理を実行する
		if (!file_exists(self::$imageFileBaseDir) || !file_exists(self::$otherFileBaseDir)) {
			/** @var \BcBurgerEditor\Plugin $plugin */
			$plugin = Plugin::getCollection()->get('BcBurgerEditor');
			$plugin->init();
		}

		// 設定値により、ユーザ別にファイル場所を設置
		if (!Configure::read("Bge.fileShare")) {
			$user = BcUtil::loginUser();
			$userId = $user['id'];
			self::$imageFileBaseDir .= $userId . DS;
			self::$otherFileBaseDir .= $userId . DS;
			self::$imageFileBaseURL .= $userId . '/';
			self::$otherFileBaseURL .= $userId . '/';
			if (!file_exists(self::$imageFileBaseDir)) {
				mkdir(self::$imageFileBaseDir);
				chmod(self::$imageFileBaseDir, 0777);
			}
			if (!file_exists(self::$otherFileBaseDir)) {
				mkdir(self::$otherFileBaseDir);
				chmod(self::$otherFileBaseDir, 0777);
			}
		}

		// ブロックclass設定ファイル取得
		if (file_exists(self::$addonDir[0] . "block" . DS . 'option.php')) {
			include self::$addonDir[0] . "block" . DS . 'option.php';
			self::$bgeConfig['blockClassOption'] = $bgBlockConfig;
		}

		// bgeconfig.jsonの読み込み
		if (file_exists(self::$configJSON)) {
			$configJSONString = file_get_contents(self::$configJSON);
			$configJSONData = json_decode($configJSONString, TRUE);
			if (!empty(self::$bgeConfig['blockClassOption'])) {
				self::$bgeConfig['blockClassOption'] = Hash::merge(self::$bgeConfig['blockClassOption'], $configJSONData["bg-block-config"]);
			}
			self::$bgeConfig['ckeditorConfig'] = $configJSONData["ckeditor-config"];
			if (!empty($configJSONData["flag"])) {
				self::$bgeConfig['flag'] = $configJSONData["flag"];
			}
		}

		self::$bgeConfig['api'] = [
			"imgList" => Router::url(['prefix' => 'Admin', 'plugin' => 'BcBurgerEditor', 'controller' => 'BurgerEditor', 'action' => 'img_list']),
			"imgUpload" => Router::url(['prefix' => 'Admin', 'plugin' => 'BcBurgerEditor', 'controller' => 'BurgerEditor', 'action' => 'img_upload']),
			"imgDelete" => Router::url(['prefix' => 'Admin', 'plugin' => 'BcBurgerEditor', 'controller' => 'BurgerEditor', 'action' => 'img_delete']),
			"fileList" => Router::url(['prefix' => 'Admin', 'plugin' => 'BcBurgerEditor', 'controller' => 'BurgerEditor', 'action' => 'file_list']),
			"fileUpload" => Router::url(['prefix' => 'Admin', 'plugin' => 'BcBurgerEditor', 'controller' => 'BurgerEditor', 'action' => 'file_upload']),
			"fileDelete" => Router::url(['prefix' => 'Admin', 'plugin' => 'BcBurgerEditor', 'controller' => 'BurgerEditor', 'action' => 'file_delete']),
		];

		self::$bgeConfig['utility'] = [
			"googleMapsApiKey" => BurgerEditorUtil::getGoogleMapApiKey(),
			"cssList" => self::getCSSList(),
		];

		self::$bgeConfig['cmsVersion'] = self::getVersionOfSystem();
		self::$bgeConfig['types'] = self::typeVersionList();

	}

	public static function getImageList()
	{
		$dir = new BcFolder(self::$imageFileBaseDir);
		$tmpList = [];
		$files = $dir->find();
		foreach($files as $file) {
			if ($file == ".DS_Store") continue;
			if (preg_match('/(__midium|__small|__org)\.[a-z0-9]+$/i', $file)) {
				continue;
			}

			$path = $dir->pwd();
			if (substr($path, -1) != DS) {
				$path = $path . DS;
			}
			$fileKey = filemtime($path . $file);
			if (preg_match('/^(\d+)__/', $file, $matches) && isset($matches[1])) {
				$fileKey = intval($matches[1]) * 100000 + 2000000000;
			}
			while(1) {
				if (!isset($tmpList[$fileKey])) break;
				$fileKey++;
			}
			$tmpList[$fileKey] = $path . $file;

			// ファイルID取得
			$fileId = self::getFileId($file);
			if (self::$imageFileMaxId < $fileId) self::$imageFileMaxId = $fileId;
		}
		krsort($tmpList);
		self::$imageFileList = array_values($tmpList);
		return self::$imageFileList;
	}

	public static function getFileList()
	{
		$dir = new BcFolder(self::$otherFileBaseDir);
		$tmpList = [];
		$files = $dir->find();
		foreach($files as $file) {
			$path = $dir->pwd();
			if (substr($path, -1) != DS) {
				$path = $path . DS;
			}
			$fileKey = filemtime($path . $file);
			while(1) {
				if (!isset($tmpList[$fileKey])) break;
				$fileKey++;
			}
			$tmpList[$fileKey] = $path . $file;

			// ファイルID取得
			$fileId = self::getFileId($file);
			if (self::$otherFileMaxId < $fileId) self::$otherFileMaxId = $fileId;
		}
		krsort($tmpList);
		self::$otherFileList = array_values($tmpList);
		return self::$otherFileList;
	}

	/**
	 *
	 */
	public static function getCSSList()
	{
		$cssList = [];
		$webroot = Configure::read('BcEnv.siteUrl');
		// テーマCSSフォルダ内のckeditor.cssを読む このCSSの中にcommon.cssなど必要なCSSをインポートする
		if (file_exists(WWW_ROOT . 'css' . DS . 'ckeditor.css')) {
			$cssList[] = $webroot . 'css/ckeditor.css';
		}

		// コンテンツが所属しているテーマを判定
		$theme = self::getThemeByContent();
		// テーマが優先
		if (file_exists(WWW_ROOT . DS . $theme . DS . 'css' . DS . 'bge_style.css')) {
			$path = WWW_ROOT . DS . $theme . DS . 'css' . DS . 'bge_style.css';
			$cssList[] = $webroot . $theme . '/css/bge_style.css' . BurgerEditorUtil::getSuffix($path);
			// テーマになくてwebroot/cssにあれば
		} elseif (file_exists(WWW_ROOT . 'css' . DS . 'bge_style.css')) {
			$path = WWW_ROOT . 'css' . DS . 'bge_style.css';
			$cssList[] = $webroot . 'css/bge_style.css' . BurgerEditorUtil::getSuffix($path);
			// themeになく、webroot/cssにもない場合、プラグイン標準のファイルを読み込む
		} else {
			$path = WWW_ROOT . 'app' . DS . 'Plugin' . DS . 'BcBurgerEditor' . DS . 'webroot' . DS . 'css' . DS . 'bge_style.css';
			$cssList[] = '/bc_burger_editor/css/bge_style.css' . BurgerEditorUtil::getSuffix($path);
		}
		return $cssList;
	}

	/**
	 * baserCMSのバージョンを取得する
	 *
	 * /lib/Baser/VERSION.txt に記述されているバージョン
	 *
	 * @return string version
	 */
	public static function getVersionOfSystem()
	{
		// /lib/Baser/VERSION.txt までのパスを取得
		$path = BASER . 'VERSION.txt';
		$versionFile = new BcFile($path);
		$versionData = $versionFile->read();
		$aryVersionData = explode("\n", $versionData);
		if (!empty($aryVersionData[0])) {
			// 例: 3.0.11-dev
			return trim($aryVersionData[0]);
		} else {
			return null;
		}
	}

	/**
	 * baserCMSのメジャーバージョンを整数で取得する
	 *
	 * @return int major version
	 */
	public static function getMajorVersionOfSystem()
	{
		$majorVersion = self::getVersionOfSystem();
		return intval($majorVersion);
	}

	/**
	 *  タイプの読み込み
	 *
	 * @param String $typeName タイプ名
	 */
	public static function type($typeName)
	{

		// バージョン設定
		$version = "0.0.0";
		$typePath = BurgerEditorUtil::getTypePath($typeName);
		if (!$typePath) {
			return false;
		}

		if (file_exists($typePath . 'version.php')) {
			// バージョンを設定しているファイルを読み込んで
			// $version変数を上書きする
			include $typePath . 'version.php';
		}

		echo '<div data-bgt="' . h($typeName) . '" data-bgt-ver="' . h($version) . '" class="bgt-container bgt-' . h($typeName) . '-container">';
		include $typePath . 'value.php';
		echo '</div>';
		if (!in_array($typeName, self::$useType)) self::$useType[] = $typeName;
	}

	/**
	 *  タイプのリスト
	 *
	 * @param String $typeName タイプ名
	 */
	public static function typeVersionList()
	{

		$path = self::$addonDir;
		$blockList = [];
		foreach($path as $addonDir) {
			if (!is_dir($addonDir . 'type')) {
				continue;
			}
			if ($dh = opendir($addonDir . 'type' . DS)) {
				while(($typeName = readdir($dh)) !== false) {
					if ($typeName == '.' || $typeName == '..' || !is_dir($addonDir . 'type' . DS . $typeName)) continue;
					$version = "0.0.0";
					$tmpl = '';
					$blockList[$typeName] = [];

					if (file_exists($addonDir . 'type' . DS . $typeName . DS . 'version.php')) {
						// バージョンを設定しているファイルを読み込んで $version 変数を上書きする
						include $addonDir . 'type' . DS . $typeName . DS . 'version.php';
					}
					$blockList[$typeName]['version'] = $version;

					if (file_exists($addonDir . 'type' . DS . $typeName . DS . 'value.php')) {
						ob_start();
						self::type($typeName);
						$tmpl = ob_get_contents();
						ob_end_clean();
					}
					$blockList[$typeName]['tmpl'] = $tmpl;
				}
				closedir($dh);
			}
		}
		return $blockList;
	}

	/**
	 *  ブロックの読み込み
	 *
	 * @param String $typeName タイプ名
	 */
	public function defaultBlock($blockPathList)
	{
		foreach($blockPathList as $block) {
			$blockName = basename($block);
			if (!in_array($blockName, self::$useBlock)) self::$useBlock[] = $blockName;
			echo '<div data-bgb="' . h($blockName) . '" class="bgb-' . h($blockName) . '">';
			include $block . 'index.php';
			echo '</div>' . "\n\n";

			// ブロックのパネル画像の静的ファイルを生成していて、オリジナルより古い場合は削除する
			if (file_exists(self::$staticPanelDir . $blockName . '.svg')) {
				if (filemtime(self::$staticPanelDir . $blockName . '.svg') <
					filemtime($block . 'panel.svg')
				) {
					unlink(self::$staticPanelDir . $blockName . '.svg');
				}
			} elseif (file_exists(self::$staticPanelDir . $blockName . '.png')) {
				if (filemtime(self::$staticPanelDir . $blockName . '.png') <
					filemtime($block . 'panel.png')
				) {
					unlink(self::$staticPanelDir . $blockName . '.png');
				}
			}
		}
	}

	public function inputArea()
	{
		if (!self::$useType) trigger_error("ブロックの読み込みが完了していません。");
		foreach(self::$useType as $type) {
			echo '<div class="Type' . h($type) . '">';
			include BurgerEditorUtil::getTypePath($type) . 'input.php';
			echo '</div>' . "\n\n";
		}
	}

	public function panelArea()
	{
		if (!self::$useType) trigger_error("ブロックの読み込みが完了していません。");

		$addonDir = self::$addonDir;
		$bgCategory = [];
		$bgCategoryTmp = [];
		foreach($addonDir as $path) {
			// ブロックカテゴリファイルの取得（bgeconfig.jsonがあった場合は上書きマージする）
			$categoryPath = $path . "block" . DS . 'category.php';
			if (file_exists($categoryPath)) {
				include $categoryPath;
				$bgCategoryTmp = Hash::merge($bgCategoryTmp, $bgCategory);
			}
		}
		$bgCategory = $bgCategoryTmp;

		if (file_exists(self::$configJSON)) {
			$configJSONString = file_get_contents(self::$configJSON);
			$configJSONData = json_decode($configJSONString, TRUE);
			$bgCategory = Hash::merge($bgCategory, $configJSONData["bg-category"]);
		}

		// start output
		echo '<div class="bg-block-selection">';
		echo '<div class="bg-blocks">';
		echo '<dl>';

		foreach($bgCategory as $categoryName => $blockList) {
			echo '<dt>' . h($categoryName) . '</dt>';
			echo '<dd>';
			echo '<ul>' . "\n";
			foreach($blockList as $blockName => $block) {
				if ($block === null) {
					continue;
				}
				if (in_array($blockName, self::$useBlock)) {
					echo '<li data-bge-block="' . h($blockName) . '">';
					// svg優先でロード
					$blockPath = BurgerEditorUtil::getBlockPath($blockName);
					if (file_exists($blockPath . 'panel.svg')) {
						$imgSrc = file_get_contents($blockPath . 'panel.svg');
						echo '<figure>';
						echo '<div>' . $imgSrc . '</div>';
						echo '<figcaption>' . $block . '</figcaption>';
						echo '</figure>' . "\n";
					} elseif (file_exists($blockPath . 'panel.png')) {
						$imgSrc = $this->assetUrl(['admin' => false, 'controller' => 'burger_editor', 'action' => 'panel', $blockName . '.png']);
						echo '<figure>';
						echo '<div style="background-image: url(' . $imgSrc . ');" role="image"></div>';
						echo '<figcaption>' . $block . '</figcaption>';
						echo '</figure>' . "\n";
					} else {
						echo h($block) . '（画像無し）';
					}
					echo '</li>' . "\n";
				}
			}
			echo '</ul>';
			echo '</dd>';
		}

		echo '</dl>';
		echo '</div>';
		echo '</div>';

	}

	/**
	 * 初期処理ファイル読み込み
	 */
	public function initArea()
	{
		if (!self::$useType) trigger_error("ブロックの読み込みが完了していません。");

		foreach(self::$useType as $type) {
			$typeInitPath = BurgerEditorUtil::getTypePath($type) . 'init.php';
			if (file_exists($typeInitPath)) {
				echo '<div class="Init' . h($type) . '">';
				include $typeInitPath;
				echo '</div>' . "\n\n";
			}
		}

	}


	/**
	 * BurgerEditor 出力
	 */
	public function editor($fieldName, $options = [])
	{
		$inputId = $fieldName;
		$draftId = $options['editorDraftField']?? null;

		$editorHtml = '<h2 style="text-align:left;">コンテンツ編集エリア</h2>';
		$editorHtml .= $this->getView()->BcAdminForm->hidden($fieldName, ['id' => $inputId]);
		$this->getView()->BcAdminForm->unlockField($fieldName);

		// データ領域はここで出力
		$editorHtml .= '<div id="ValueMigrationMessage"></div>';

		// 下書き機能を利用するかチェック
		if (!empty($options['editorDraftField'])) {
			$context = $this->getView()->BcAdminForm->context();
			$draftVal = $context->val($options['editorDraftField']);
			if ($draftId) {
				$editorHtml .= '<div class="draft-btn clearfix"' . ((empty($options['editorUseDraft']))? 'style="display:none"' : '') . '>';
				$editorHtml .= '<div class="draft-tab-btn">';
				$editorHtml .= '<a id="CbeHonkouBtn" class="on">本稿モード</a>';
				$editorHtml .= '<a id="CbeSoukouBtn">下書きモード</a>';
				$editorHtml .= '</div>';
				$editorHtml .= '<div class="draft-copy-btn">';
				$editorHtml .= '<a id="CbeHonkouCopyBtn">本稿を下書きにコピー</a>';
				$editorHtml .= '<a id="CbeSoukouCopyBtn">下書きを本稿にコピー</a>';
				$editorHtml .= '</div>';
				$editorHtml .= '</div>';
				$editorHtml .= $this->getView()->BcAdminForm->hidden($options['editorDraftField'], ['id' => $draftId]);
				$this->getView()->BcAdminForm->unlockField($options['editorDraftField']);
				$editorHtml .= '<div id="DraftArea" class="bge-view-value bge_content bge-contents" hidden></div>';
			}
		}

		self::$bgeConfig['utility']['mainFieldId'] = $inputId;
		self::$bgeConfig['utility']['draftFieldId'] = $draftId;
		self::$bgeConfig['setting'] = Configure::read('Bge');

		$editorHtml .= '<script id="bge-config" type="application/json">';
		$editorHtml .= json_encode(self::$bgeConfig);
		$editorHtml .= '</script>';

		$editorHtml .= '<div id="ValueArea" class="bge-view-value bge_content bge-contents"></div>';

		$editorHtml .= $this->getView()->cell('BcBurgerEditor.BurgerEditor', [
			$inputId,
			$draftId
		])->render();

		// load読み込み
		ob_start();
		foreach(self::$useType as $type) {
			$typeLoadPath = BurgerEditorUtil::getTypePath($type) . 'load.php';
			if (file_exists($typeLoadPath)) {
				include $typeLoadPath;
			}
		}
		$editorHtml .= ob_get_clean();

		$this->BcBaser->css([
			'admin/ckeditor/editor',
			'BcBurgerEditor.admin/burger_editor'
		], false);

		$this->BcBaser->js([
			'vendor/ckeditor/ckeditor',
			'vendor/ckeditor/adapters/jquery',
			'BcBurgerEditor.admin/burger_editor',
		], ['inline' => false]);

		// ユーザ(サイト制作者)定義CSSの自動読込
		$cssList = [];
		$cssList[] = "BcBurgerEditor.bge_style_default";
		$cssList[] = self::getCSSList();
		$this->BcBaser->css($cssList, false);
		return $editorHtml;
	}

	/**
	 * ファイル名からIDを取得する
	 *
	 * @param string $fileName ファイル名
	 * @return mixed (int|null)
	 */
	static protected function getFileId($fileName)
	{
		preg_match("/^(\d+)__/", $fileName, $matches);
		if (isset($matches[1])) return $matches[1];
		return null;
	}


	/**
	 * 記事に埋め込まれた画像を表示する
	 *
	 * @param array $post blogpost
	 * @param array $options option
	 * @return    string    imgタグ
	 */
	public function postImage($post, $options = [])
	{

		$imgUrl = $this->getPostImage($post, $options);

		if (empty($imgUrl)) {
			return null;
		}

		if (isset($options['number'])) {
			unset($options['number']);
		}


		echo $this->Html->image($imgUrl, $options);
	}

	/**
	 * 記事に埋め込まれた画像のパスを取得する(ブログにのみ対応)
	 *
	 * @param array $post blogpost
	 * @param array $options option
	 * @return string filepath
	 */
	public function getPostImage($post, $options = [])
	{

		if (!isset($post['BlogPost']['detail']) || empty($post['BlogPost']['detail'])) {
			return null;
		}

		if (isset($options['number']) && !is_int($options['number'])) {
			return null;
		}


		$detail = $post['BlogPost']['detail'];
		$number = (isset($options['number']))? $options['number'] : 0;
		$images = [];

		preg_match_all('/<img.*src\s*=\s*[\"|\'](.*?)[\"|\'].*>/i', $detail, $images);

		if (!isset($images[1]) || count($images[1]) == 0) {
			return null;
		}

		if (isset($number) && !isset($images[1][$number])) {
			return null;
		}


		return $images[1][$number];

	}


	/**
	 * 記事が所属するsiteIdから、どのテーマファイルがロードされているかを取得する
	 *
	 * @return string theme
	 */
	private static function getThemeByContent()
	{
		if(BcUtil::isAdminSystem()) {
			$request = Router::getRequest();
			$site = $request->getAttribute('currentSite');
			if (!empty($site) && !empty($site->theme)) {
				return $site->theme;
			} else {
				return '';
			}
		}

		$request = Router::getRequest();

		$controller = $request->getParam('controller');
		$action = $request->getParam('action');
		$pass = $request->getParam('pass');
		$content = $request->getAttribute('currentContent');

		if (!in_array($controller, ['Pages', 'BlogPosts'])) return '';
		if (!in_array($action, ['add', 'edit'])) return '';

		//固定ページの場合はsite_idを取得
		if ($controller == 'Pages' && empty($content)) {
			$Content = TableRegistry::getTableLocator()->get('BaserCore.Contents');
			$entityId = $pass[0];
			$conditions = ['entity_id' => $entityId, 'type' => 'Page'];
			$entity = $Content->find()->where($conditions)->first();
			$siteId = $entity->site_id;
		} else {
			//後々Pageの方でもCotent属性が得られるようになったらこちらの処理に自動で結合される
			$siteId = $content->site_id;
		}
		/** @var SitesTable $Sites */
		$Sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
		$entity = $Sites->get($siteId);

		if ($entity) {
			return $entity->theme;
		} else {
			return '';
		}
	}

	/**
	 * スタイルの読み込みを防止する
	 * @return void
	 */
	public function preventLoadingStyle()
	{
		$this->loadingStyle = false;
	}

	/**
	 * スタイルを読み込むかどうか
	 * @return false
	 */
	public function shouldLoadStyle()
	{
		return $this->loadingStyle;
	}

	/**
	 * レスポンス用のファイルリストとページネーションを作成する
	 * @param array $fileList
	 * @param int $targetPage
	 * @param int $selectedFileId
	 * @param int $pageNum
	 *
	 * @return array 該当ページ分のファイルリスト(data)とページネーション情報(pagination)を含む
	 */
	public static function getFileListWithPagination($fileList, $targetPage, $selectedFileId, $pageNum){
		$startIndex = 0;
		$currentPage = 1;

		if(is_null($targetPage) && $selectedFileId){
			// 選択済み画像ページを表示（初期表示時限定）
			foreach($fileList as $key => $file){
				if($file['fileId'] === $selectedFileId){
					$currentPage = intdiv((int)$key, $pageNum) + 1;
					$startIndex = ($currentPage - 1) * $pageNum;
				}
			}
		}elseif($targetPage >= 1){
			// 指定ページを表示
			$startIndex = ($targetPage - 1) * $pageNum;
			if(count($fileList) <= $startIndex){
				// ページ数分の要素がない場合は先頭ページを表示
				$startIndex = 0;
				$currentPage = 1;
			}else{
				$currentPage = $targetPage;
			}
		}

		$pageList = array_slice($fileList, $startIndex, $pageNum);

		// 最大ページ数
		$imagePaginationMaxPage = intdiv(count($fileList)-1, $pageNum) + 1;

		return [
			'data' => $pageList,
			'pagination' => [
				'pageMaxNumber' => $imagePaginationMaxPage,
				'currentPageNumber' => $currentPage,
				'selectedFileId' => $selectedFileId,
			],
		];
	}
}
