<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツヘルパ
 *
 * @package Baser.View.Helper
 * @property Content $_Content
 * @property Permission $_Permission
 */
class BcContentsHelper extends AppHelper
{

	/**
	 * Helper
	 *
	 * @var array
	 */
	public $helpers = ['BcBaser'];

	/**
	 * Content Model
	 *
	 * @var Content
	 */
	protected $_Content = null;
	protected $_Permission = null;

	/**
	 * Constructor.
	 *
	 * @return    void
	 * @access    public
	 */
	public function __construct(View $View, $settings = [])
	{
		parent::__construct($View, $settings);
		$this->_Content = ClassRegistry::init('Content');
		$this->_Permission = ClassRegistry::init('Permission');
		if (BcUtil::isAdminSystem()) {
			$this->setup();
		}
	}

	/**
	 * セットアップ
	 */
	public function setup()
	{
		$settings = $this->_View->get('contentsSettings');

		if (!$settings) {
			return;
		}

		$existsTitles = $this->_getExistsTitles();
		$user = BcUtil::loginUser('admin');

		foreach($settings as $type => $setting) {

			// title
			if (empty($setting['title'])) {
				$setting['title'] = $type;
			}

			// omitViewAction
			if (empty($setting['omitViewAction'])) {
				$setting['omitViewAction'] = false;
			}

			// exists
			if (empty($setting['multiple'])) {
				$setting['multiple'] = false;
				if (array_key_exists($setting['plugin'] . '.' . $type, $existsTitles)) {
					$setting['exists'] = true;
					$setting['existsTitle'] = $existsTitles[$setting['plugin'] . '.' . $type];
				} else {
					$setting['exists'] = false;
					$setting['existsTitle'] = '';
				}
			}

			// icon
			if (!empty($setting['icon'])) {
				if (preg_match('/\.(png|jpg|gif)$/', $setting['icon'])) {
					$setting['url']['icon'] = $this->_getIconUrl($setting['plugin'], $setting['type'], $setting['icon']);
				}
			} else {
				// 後方互換のため判定を入れる（v4.2.0）
				if (Configure::read('BcSite.admin_theme') === Configure::read('BcApp.defaultAdminTheme')) {
					$setting['icon'] = $setting['icon'] = 'bca-icon--file';
				} else {
					$setting['url']['icon'] = $this->_getIconUrl($setting['plugin'], $setting['type'], null);
				}
			}

			// routes
			foreach(['manage', 'add', 'edit', 'delete', 'copy', 'dblclick'] as $method) {
				if (empty($setting['routes'][$method]) && !in_array($method, ['add', 'copy', 'manage', 'dblclick'])) {
					$setting['routes'][$method] = ['admin' => true, 'controller' => 'contents', 'action' => $method];
				}
				if (!empty($setting['routes'][$method])) {
					$route = $setting['routes'][$method];
					$setting['url'][$method] = Router::url($route);
				}
			}
			// disabled
			if(!empty($setting['url']['add'])) {
				$setting['addDisabled'] = !($this->_Permission->check($setting['url']['add'], $user['user_group_id']));
			} else {
				$setting['addDisabled'] = true;
			}
			$settings[$type] = $setting;
		}
		$this->settings = $settings;
	}

	/**
	 * アクションが利用可能かどうか確認する
	 *
	 * @param string $type コンテンツタイプ
	 * @param string $action アクション
	 * @param int $entityId コンテンツを特定するID
	 */
	public function isActionAvailable($type, $action, $entityId)
	{
		$user = BcUtil::loginUser('admin');
		if (!isset($this->settings[$type]['url'][$action])) {
			return false;
		}
		$url = $this->settings[$type]['url'][$action] . '/' . $entityId;
		return $this->_Permission->check($url, $user['user_group_id']);
	}

	/**
	 * シングルコンテンツで既に登録済のタイトルを取得する
	 * @return array
	 */
	protected function _getExistsTitles()
	{
		$items = Configure::read('BcContents.items');
		// シングルコンテンツの存在チェック
		$conditions = [];
		foreach($items as $name => $settings) {
			foreach($settings as $type => $setting) {
				if (empty($setting['multiple'])) {
					$conditions['or'][] = [
						'Content.plugin' => $name,
						'Content.type' => $type,
						'Content.alias_id' => null
					];
				}
			}
		}
		$this->_Content->Behaviors->unload('SoftDelete');
		$contents = $this->_Content->find('all', ['fields' => ['plugin', 'type', 'title'], 'conditions' => $conditions, 'recursive' => -1]);
		$this->_Content->Behaviors->load('SoftDelete');
		$existContents = [];
		foreach($contents as $content) {
			$existContents[$content['Content']['plugin'] . '.' . $content['Content']['type']] = $content['Content']['title'];
		}
		return $existContents;
	}

	/**
	 * アイコンのURLを取得する
	 * @param $type
	 * @param $file
	 * @param null $suffix
	 * @return string
	 */
	public function _getIconUrl($plugin, $type, $file, $suffix = null)
	{
		$imageBaseUrl = Configure::read('App.imageBaseUrl');
		if ($file) {
			if ($plugin != 'Core') {
				$file = $plugin . '.' . $file;
			}
		} else {
			$icon = 'admin/icon_' . Inflector::underscore($type) . $suffix . '.png';
			$defaultIcon = 'admin/icon_content' . $suffix . '.png';
			if ($plugin == 'Core') {
				$iconPath = WWW_ROOT . $imageBaseUrl . DS . $icon;
				if (file_exists($iconPath)) {
					$file = $icon;
				} else {
					$file = $defaultIcon;
				}
			} else {
				try {
					$pluginPath = CakePlugin::path($plugin) . 'webroot' . DS;
				} catch (Exception $e) {
					throw new ConfigureException(__d('baser', 'プラグインの BcContent 設定が間違っています。'));
				}
				$iconPath = $pluginPath . str_replace('/', DS, $imageBaseUrl) . $icon;
				if (file_exists($iconPath)) {
					$file = $plugin . '.' . $icon;
				} else {
					$file = $defaultIcon;
				}
			}
		}
		return $this->assetUrl($file, ['pathPrefix' => $imageBaseUrl]);
	}

	/**
	 * コンテンツ設定を Json 形式で取得する
	 * @return string
	 */
	public function getJsonSettings()
	{
		return json_encode($this->settings);
	}

	/**
	 * データが公開状態にあるか確認する
	 *
	 * @param array $data コンテンツデータ
	 * @param bool $self コンテンツ自身の公開状態かどうか
	 * @return mixed
	 */
	public function isAllowPublish($data, $self = false)
	{
		return $this->_Content->isAllowPublish($data, $self);
	}

	/**
	 * コンテンツIDよりフルURLを取得する
	 *
	 * @param int $id コンテンツID
	 * @return mixed
	 */
	public function getUrlById($id, $full = false)
	{
		return $this->_Content->getUrlById($id, $full);
	}

	/**
	 * コンテンツ管理上のURLを元に正式なURLを取得する
	 *
	 * @param string $url コンテンツ管理上のURL
	 * @param bool $full http からのフルのURLかどうか
	 * @param bool $useSubDomain サブドメインを利用しているかどうか
	 * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
	 * @return string URL
	 */
	public function getUrl($url, $full = false, $useSubDomain = false, $base = true)
	{
		return $this->_Content->getUrl($url, $full, $useSubDomain, $base);
	}

	/**
	 * プレフィックスなしのURLを取得する
	 *
	 * @param string $url
	 * @param int $siteId
	 * @return mixed
	 */
	public function getPureUrl($url, $siteId)
	{
		return $this->_Content->pureUrl($url, $siteId);
	}

	/**
	 * 現在のURLを元に指定したサブサイトのURLを取得する
	 *
	 * @param string $siteName
	 * @return mixed|string
	 */
	public function getCurrentRelatedSiteUrl($siteName)
	{
		if (empty($this->request->params['Site'])) {
			return '';
		}
		$url = $this->getPureUrl('/' . $this->request->url, $this->request->params['Site']['id']);
		$Site = ClassRegistry::init('Site');
		$site = $Site->find('first', ['conditions' => ['Site.name' => $siteName], 'recursive' => -1]);
		if (!$site) {
			return '';
		}
		$prefix = $Site->getPrefix($site);
		if ($prefix) {
			$url = '/' . $prefix . $url;
		}
		return $url;
	}

	/**
	 * コンテンツリストをツリー構造で取得する
	 *
	 * @param int $id カテゴリID
	 * @param int $level 関連データの階層
	 * @param array $options
	 * @return array
	 */
	public function getTree($id = 1, $level = null, $options = [])
	{
		$options = array_merge([
			'type' => '',
			'order' => ['Content.site_id', 'Content.lft']
		], $options);
		$conditions = array_merge($this->_Content->getConditionAllowPublish(), ['Content.id' => $id]);
		$content = $this->_Content->find('first', ['conditions' => $conditions, 'cache' => false]);
		if (!$content) {
			return [];
		}
		$conditions = array_merge($this->_Content->getConditionAllowPublish(), [
			'Content.site_root' => false,
			'rght <' => $content['Content']['rght'],
			'lft >' => $content['Content']['lft']
		]);
		if ($level) {
			$level = $level + $content['Content']['level'] + 1;
			$conditions['Content.level <'] = $level;
		}
		if (!empty($options['type'])) {
			$conditions['Content.type'] = ['ContentFolder', $options['type']];
		}
		if (!empty($options['conditions'])) {
			$conditions = array_merge($conditions, $options['conditions']);
		}
		// CAUTION CakePHP2系では、fields を指定すると正常なデータが取得できない
		return $this->_Content->find('threaded', [
			'order' => $options['order'],
			'conditions' => $conditions,
			'recursive' => 0,
			'cache' => false
		]);
	}

	/**
	 * 親コンテンツを取得する
	 *
	 * - 引数なしで現在のコンテンツの親情報を取得
	 * - $id を指定して取得する事ができる
	 * - $direct を false に設定する事で、最上位までの親情報を取得
	 *
	 * @param bool $direct 直接の親かどうか
	 * @return mixed false|array
	 */
	public function getParent($id = null, $direct = true)
	{
		if (!$id && !empty($this->request->params['Content']['id'])) {
			$id = $this->request->params['Content']['id'];
		}
		if (!$id) {
			return false;
		}
		$siteId = $this->_Content->field('site_id', ['Content.id' => $id]);
		if ($direct) {
			$parent = $this->_Content->getParentNode($id);
			if ($parent && $parent['Content']['site_id'] == $siteId) {
				return $parent;
			} else {
				return false;
			}
		} else {
			$parents = $this->_Content->getPath($id);
			if ($parents) {
				$result = [];
				foreach($parents as $parent) {
					if ($parent['Content']['id'] != $id && $parent['Content']['site_id'] == $siteId) {
						$result[] = $parent;
					}
				}
				if ($result) {
					return $result;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	/**
	 * サイト連携データかどうか確認する
	 *
	 * @param array $data コンテンツデータ
	 * @return bool
	 */
	public function isSiteRelated($data)
	{
		if (($data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['alias_id']) ||
			$data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['type'] == 'ContentFolder') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 関連サイトのコンテンツを取得
	 *
	 * @param int $id コンテンツID
	 * @return array | false
	 */
	public function getRelatedSiteContents($id = null, $options = [])
	{
		$options = array_merge([
			'excludeIds' => []
		], $options);
		$this->_Content->unbindModel(['belongsTo' => ['User']]);
		if (!$id) {
			if (!empty($this->request->params['Content'])) {
				$content = $this->request->params['Content'];
				if ($content['main_site_content_id']) {
					$id = $content['main_site_content_id'];
				} else {
					$id = $content['id'];
				}
			} else {
				return false;
			}
		}
		return $this->_Content->getRelatedSiteContents($id, $options);
	}

	/**
	 * 関連サイトのリンク情報を取得する
	 *
	 * @param int $id
	 * @return array
	 */
	public function getRelatedSiteLinks($id = null, $options = [])
	{
		$options = array_merge([
			'excludeIds' => []
		], $options);
		$contents = $this->getRelatedSiteContents($id, $options);
		$urls = [];
		if ($contents) {
			foreach($contents as $content) {
				$urls[] = [
					'prefix' => $content['Site']['name'],
					'name' => $content['Site']['display_name'],
					'url' => $content['Content']['url']
				];
			}
		}
		return $urls;
	}

	/**
	 * フォルダリストを取得する
	 *
	 * @param int $siteId
	 * @param array $options
	 * @return array|bool
	 */
	public function getContentFolderList($siteId = null, $options = [])
	{
		return $this->_Content->getContentFolderList($siteId, $options);
	}

	/**
	 * サイトIDからサイトルートとなるコンテンツを取得する
	 *
	 * @param int $siteId
	 * @return array
	 */
	public function getSiteRoot($siteId)
	{
		return $this->_Content->getSiteRoot($siteId);
	}

	/**
	 * サイトIDからサイトルートとなるコンテンツIDを取得する
	 *
	 * @param int $siteId
	 * @return string|bool
	 */
	public function getSiteRootId($siteId)
	{
		$content = $this->getSiteRoot($siteId);
		if ($content) {
			return $content['Content']['id'];
		} else {
			return false;
		}
	}

	/**
	 * コンテンツが編集可能かどうか確認
	 *
	 * @param array $data コンテンツ、サイト情報を格納した配列
	 * @return bool
	 */
	public function isEditable($data = null)
	{
		if (!$data) {
			if (!isset($this->request->data['Content']) && !isset($this->request->data['Site'])) {
				return false;
			}
			$content = $this->request->data['Content'];
			$site = $this->request->data['Site'];
		} else {
			if (isset($data['Content'])) {
				$content = $data['Content'];
			} else {
				return false;
			}
			if (isset($data['Site'])) {
				$site = $data['Site'];
			} else {
				return false;
			}
		}
		// サイトルートの場合は編集不可
		if ($content['site_root']) {
			return false;
		}
		// サイトルート以外で、管理ユーザーの場合は、強制的に編集可
		if (BcUtil::isAdminUser()) {
			return true;
		}
		// エイリアスを利用してメインサイトと自動連携する場合、親サイトに関連しているコンテンツ（＝子サイト）
		if ($site['relate_main_site'] && $content['main_site_content_id']) {
			// エイリアス、または、フォルダの場合は編集不可
			if ($content['alias_id'] || $content['type'] == 'ContentFolder') {
				return false;
			}
		}
		return true;
	}

	/**
	 * エンティティIDからコンテンツの情報を取得
	 *
	 * @param int $id エンティティID
	 * @param string $contentType コンテンツタイプ
	 * ('Page','MailContent','BlogContent','ContentFolder')
	 * @param string $field 取得したい値
	 *  'name','url','title'など　初期値：Null
	 *  省略した場合配列を取得
	 * @return array|string|bool
	 */
	public function getContentByEntityId($id, $contentType, $field = null)
	{
		$conditions = array_merge($this->_Content->getConditionAllowPublish(), ['type' => $contentType, 'entity_id' => $id]);
		return $this->_getContent($conditions, $field);
	}

	/**
	 * urlからコンテンツの情報を取得
	 *
	 * @param string $url
	 * @param string $contentType コンテンツタイプ
	 * ('Page','MailContent','BlogContent','ContentFolder')
	 * @param string $field 取得したい値
	 *  'name','url','title'など　初期値：Null
	 *  省略した場合配列を取得
	 * @return array|string|bool
	 */
	public function getContentByUrl($url, $contentType, $field = null)
	{
		$conditions = array_merge($this->_Content->getConditionAllowPublish(), ['type' => $contentType, 'url' => $url]);
		return $this->_getContent($conditions, $field);
	}

	private function _getContent($conditions, $field)
	{
		$content = $this->_Content->find('first', ['conditions' => $conditions, 'order' => ['Content.id'], 'cache' => false]);
		if (!empty($content)) {
			if ($field) {
				return $content ['Content'][$field];
			} else {
				return $content;
			}
		} else {
			return false;
		}
	}


	/**
	 * IDがコンテンツ自身の親のIDかを判定する
	 *
	 * @param int $id コンテンツ自身のID
	 * @param int $parentId 親として判定するID
	 * @return bool
	 */
	public function isParentId($id, $parentId)
	{
		$parentIds = $this->_Content->getPath($id, ['id'], -1);
		if (!$parentIds) {
			return false;
		}
		$parentIds = Hash::extract($parentIds, '{n}.Content.id');
		if ($parentIds && in_array($parentId, $parentIds)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 現在のコンテンツが属するフォルダまでのフルパスを取得する
	 * フォルダ名称部分にはフォルダ編集画面へのリンクを付与する
	 * コンテンツ編集画面で利用
	 *
	 * @return string
	 */
	public function getCurrentFolderLinkedUrl()
	{
		return $this->getFolderLinkedUrl($this->request->data);
	}

	/**
	 * 対象コンテンツが属するフォルダまでのフルパスを取得する
	 * フォルダ名称部分にはフォルダ編集画面へのリンクを付与する
	 * コンテンツ編集画面で利用
	 *
	 * @param array $content コンテンツデータ
	 * @return string
	 */
	public function getFolderLinkedUrl($content)
	{
		if(!empty($content['Content']['url'])) {
			$urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $content['Content']['url']));
			unset($urlArray[count($urlArray) - 1]);
		} elseif($content['Content']['parent_id']) {
			$parent = $this->_Content->findById($content['Content']['parent_id']);
			$urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $parent['Content']['url']));
		}
		if(count($urlArray) === 1 && !$urlArray[0]) {
			$urlArray = [];
		}

		if ($content['Site']['same_main_url']) {
			$site = BcSite::findById($content['Site']['main_site_id']);
			array_shift($urlArray);
			if ($site->alias) {
				$urlArray = explode('/', $site->alias) + $urlArray;
			}
		}
		if ($content['Site']['use_subdomain']) {
			$host = $this->getUrl('/' . $urlArray[0] . '/', true, $content['Site']['use_subdomain']);
			array_shift($urlArray);
		} else {
			$host = $this->getUrl('/', true, $content['Site']['use_subdomain']);
		}

		$checkUrl = '/';
		$Content = ClassRegistry::init('Content');
		foreach($urlArray as $key => $value) {
			$checkUrl .= $value . '/';
			$entityId = $Content->field('entity_id', ['Content.url' => $checkUrl]);
			$urlArray[$key] = $this->BcBaser->getLink(urldecode($value), ['admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $entityId], ['forceTitle' => true]);
		}
		$folderLinkedUrl = $host;
		if ($urlArray) {
			$folderLinkedUrl .= implode('/', $urlArray) . '/';
		}
		return $folderLinkedUrl;
	}

	/**
	 * フォルダかどうか確認する
	 * @return bool
	 */
	public function isFolder()
	{
		if (BcUtil::isAdminSystem() || !isset($this->request->params['Content']['type'])) {
			return false;
		}
		return ($this->request->params['Content']['type'] === 'ContentFolder');
	}

}
