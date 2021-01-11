<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class Site
 *
 * サブサイトモデル
 *
 * @package Baser.Model
 */
class Site extends AppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];


	/**
	 * 保存時にエイリアスが変更されたかどうか
	 *
	 * @var bool
	 */
	private $__changedAlias = false;

	/**
	 * Site constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'name' => [
				['rule' => ['notBlank'], 'message' => __d('baser', '識別名称を入力してください。')],
				['rule' => ['maxLength', 50], 'message' => __d('baser', '識別名称は50文字以内で入力してください。')],
				['rule' => ['alphaNumericPlus'], 'message' => __d('baser', '識別名称は、半角英数・ハイフン（-）・アンダースコア（_）で入力してください。')],
				['rule' => ['duplicate'], 'message' => __d('baser', '既に利用されている識別名称です。別の名称に変更してください。')]],
			'display_name' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'サブサイト名を入力してください。')],
				['rule' => ['maxLength', 50], 'message' => __d('baser', 'サブサイト名は50文字以内で入力してください。')]],
			'alias' => [
				['rule' => ['maxLength', 50], 'message' => __d('baser', 'エイリアスは50文字以内で入力してください。')],
				['rule' => ['alphaNumericPlus', ['/', '.']], 'message' => __d('baser', 'エイリアスは、半角英数・ハイフン（-）・アンダースコア（_）・スラッシュ（/）・ドット（.）で入力してください。')],
				['rule' => ['duplicate'], 'message' => __d('baser', '既に利用されているエイリアス名です。別の名称に変更してください。')],
				['rule' => ['aliasSlashChecks'], 'message' => __d('baser', 'エイリアスには先頭と末尾にスラッシュ（/）は入力できず、また、連続して入力する事もできません。')]],
			'title' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'サブサイトタイトルを入力してください。')],
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'サブサイトタイトルは255文字以内で入力してください。')]]
		];
	}

	/**
	 * エイリアスのスラッシュをチェックする
	 *
	 * - 連続してスラッシュは入力できない
	 * - 先頭と末尾にスラッシュは入力できない
	 * @param $check
	 * @return bool
	 */
	public function aliasSlashChecks($check)
	{
		$alias = $check[key($check)];
		if (preg_match('/(^\/|[\/]{2,}|\/$)/', $alias)) {
			return false;
		}
		return true;
	}

	/**
	 * 公開されている全てのサイトを取得する
	 *
	 * @return array
	 */
	public function getPublishedAll()
	{
		$conditions = ['Site.status' => true];
		$sites = $this->find('all', ['conditions' => $conditions]);
		$main = $this->getRootMain();
		if ($sites) {
			array_unshift($sites, $main);
		} else {
			$sites = [$main];
		}
		return $sites;
	}

	/**
	 * サイトリストを取得
	 *
	 * @param bool $mainSiteId メインサイトID
	 * @param array $options
	 *    `excludeIds` 除外するID（初期値：なし）
	 * @return array
	 */
	public function getSiteList($mainSiteId = null, $options = [])
	{
		$options = array_merge([
			'excludeIds' => []
		], $options);

		// EVENT Site.beforeGetSiteList
		$event = $this->dispatchEvent('beforeGetSiteList', [
			'options' => $options
		]);
		if ($event !== false) {
			$options = $event->result === true? $event->data['options'] : $event->result;
		}

		$conditions = ['Site.status' => true];
		if (!is_null($mainSiteId)) {
			$conditions['Site.main_site_id'] = $mainSiteId;
		}

		$rootMain = [];
		$excludeKey = false;
		$includeKey = true;

		if (isset($options['excludeIds'])) {
			if (!is_array($options['excludeIds'])) {
				$options['excludeIds'] = [$options['excludeIds']];
			}
			$excludeKey = array_search(0, $options['excludeIds']);
			if ($excludeKey !== false) {
				unset($options['excludeIds'][$excludeKey]);
			}
			if ($options['excludeIds']) {
				$conditions[]['NOT']['Site.id'] = $options['excludeIds'];
			}
		}

		if (isset($options['includeIds'])) {
			if (!is_array($options['includeIds'])) {
				$options['includeIds'] = [$options['includeIds']];
			}
			$includeKey = array_search(0, $options['includeIds']);
			if ($includeKey !== false) {
				unset($options['includeIds'][$includeKey]);
			}
			if ($options['includeIds']) {
				$conditions[]['Site.id'] = $options['includeIds'];
			}
		}

		if ($includeKey !== false && $excludeKey === false && is_null($mainSiteId)) {
			$rootMainTmp = $this->getRootMain();
			$rootMain = [$rootMainTmp['Site']['id'] => $rootMainTmp['Site']['display_name']];
		}
		return $rootMain + $this->find('list', ['fields' => ['id', 'display_name'], 'conditions' => $conditions]);
	}

	/**
	 * メインサイトのデータを取得する
	 *
	 * @param mixed $options 取得するフィールド
	 * @return array
	 */
	public function getRootMain($options = [])
	{
		$options += [
			'fields' => []
		];
		// =============================================================
		// テストの際、Fixture のロード前に、設定 BcSite を、DBから読む為、
		// テストデータが利用できないので、テストの際には、直接DBより取得させる
		// =============================================================
		if ($this->useDbConfig == 'test') {
			$SiteConfig = ClassRegistry::init('SiteConfig');
			$siteConfigs = $SiteConfig->findExpanded();
		} else {
			loadSiteConfig();
			$siteConfigs = Configure::read('BcSite');
		}
		$site = ['Site' => [
			'id' => 0,
			'main_site_id' => null,
			'name' => null,
			'display_name' => $siteConfigs['main_site_display_name'],
			'title' => $siteConfigs['name'],
			'alias' => null,
			'theme' => $siteConfigs['theme'],
			'status' => !$siteConfigs['maintenance'],
			'use_subdomain' => false,
			'domain_type' => false,
			'relate_main_site' => null,
			'device' => null,
			'same_main_url' => false,
			'auto_redirect' => false,
			'auto_link' => false,
			'lang' => null,
			'created' => null,
			'modified' => null
		]];
		if ($options['fields']) {
			if (!is_array($options['fields'])) {
				$options['fields'] = [$options['fields']];
			}
			$siteTmp = [];
			foreach($options['fields'] as $field) {
				$siteTmp[$field] = $site['Site'][$field];
			}
			$site = ['Site' => $siteTmp];
		}
		return $site;
	}

	/**
	 * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
	 *
	 * @param $contentId
	 * @return array|null
	 */
	public function getRelatedContents($contentId)
	{
		$Content = ClassRegistry::init('Content');
		$data = $Content->find('first', ['conditions' => ['Content.id' => $contentId]]);
		$isMainSite = $this->isMain($data['Site']['id']);

		$conditions = ['Site.status' => true];
		if (is_null($data['Site']['main_site_id'])) {
			$conditions['Site.main_site_id'] = 0;
			$mainSiteContentId = $data['Content']['id'];
		} else {
			$conditions['or'] = [
				['Site.main_site_id' => $data['Site']['main_site_id']],
				['Site.id' => $data['Site']['main_site_id']]
			];
			if ($isMainSite) {
				$conditions['or'][] = ['Site.main_site_id' => $data['Site']['id']];
			}
			if ($data['Content']['main_site_content_id']) {
				$mainSiteContentId = $data['Content']['main_site_content_id'];
			} else {
				$mainSiteContentId = $data['Content']['id'];
			}
		}
		$fields = ['id', 'name', 'alias', 'display_name', 'main_site_id'];
		$sites = $this->find('all', ['fields' => $fields, 'conditions' => $conditions, 'order' => 'main_site_id']);
		if ($data['Site']['main_site_id'] == 0) {
			$sites = array_merge([$this->getRootMain(['fields' => $fields])], $sites);
		}
		$conditions = [
			'or' => [
				['Content.id' => $mainSiteContentId],
				['Content.main_site_content_id' => $mainSiteContentId]
			]
		];
		if ($isMainSite) {
			$conditions['or'][] = ['Content.main_site_content_id' => $data['Content']['id']];
		}
		$relatedContents = $Content->find('all', ['conditions' => $conditions, 'recursive' => -1]);
		foreach($relatedContents as $relatedContent) {
			foreach($sites as $key => $site) {
				if ($relatedContent['Content']['site_id'] == $site['Site']['id']) {
					$sites[$key]['Content'] = $relatedContent['Content'];
					break;
				}
			}
		}
		return $sites;
	}

	/**
	 * メインサイトかどうか判定する
	 *
	 * @param $id
	 * @return bool
	 */
	public function isMain($id)
	{
		if ($id == null) {
			$id = 0;
		}
		return (bool)$this->children($id);
	}

	/**
	 * サブサイトを取得する
	 *
	 * @param $id
	 * @param array $options
	 * @return array|null
	 */
	public function children($id, $options = [])
	{
		$options = array_merge([
			'conditions' => [
				'Site.main_site_id' => $id
			],
			'recursive' => -1
		], $options);
		return $this->find('all', $options);
	}

	/**
	 * After Save
	 *
	 * @param bool $created
	 * @param array $options
	 */
	public function afterSave($created, $options = [])
	{
		parent::afterSave($created, $options);
		App::uses('AuthComponent', 'Controller/Component');
		$user = AuthComponent::user();
		$ContentFolder = ClassRegistry::init('ContentFolder');
		if ($created) {
			$ContentFolder->saveSiteRoot(null, [
				'site_id' => $this->id,
				'name' => ($this->data['Site']['alias'])? $this->data['Site']['alias'] : $this->data['Site']['name'],
				'parent_id' => 1,
				'title' => $this->data['Site']['title'],
				'self_status' => $this->data['Site']['status'],
				'author_id' => $user['id'],
				'site_root' => true,
				'layout_template' => 'default'
			]);
		} else {
			$ContentFolder->saveSiteRoot($this->id, [
				'name' => ($this->data['Site']['alias'])? $this->data['Site']['alias'] : $this->data['Site']['name'],
				'title' => $this->data['Site']['title'],
				'self_status' => $this->data['Site']['status'],
			], $this->__changedAlias);
		}
		if (!empty($this->data['Site']['main'])) {
			$data = $this->find('first', ['conditions' => ['Site.main' => true, 'Site.id <>' => $this->id], 'recursive' => -1]);
			if ($data) {
				$data['Site']['main'] = false;
				$this->save($data, ['validate' => false, 'callbacks' => false]);
			}
		}
		$this->__changedAlias = false;
	}

	/**
	 * After Delete
	 */
	public function afterDelete()
	{
		parent::afterDelete();
		$Content = ClassRegistry::init('Content');
		$id = $Content->field('id', [
			'Content.site_id' => $this->id,
			'Content.site_root' => true
		]);

		$children = $Content->children($id, false);
		foreach($children as $child) {
			$child['Content']['site_id'] = 0;
			// バリデートすると name が変換されてしまう
			$Content->save($child, false);
		}

		$children = $Content->children($id, true);
		foreach($children as $child) {
			$Content->softDeleteFromTree($child['Content']['id']);
		}

		$softDelete = $Content->softDelete(null);
		$Content->softDelete(false);
		$Content->removeFromTree($id, true);
		$Content->softDelete($softDelete);
	}

	/**
	 * プレフィックスを取得する
	 *
	 * @param mixed $id | $data
	 * @return mixed
	 */
	public function getPrefix($id)
	{
		if (!is_array($id)) {
			$data = $this->find('first', ['fields' => ['name', 'alias'], 'conditions' => ['Site.id' => $id], 'recursive' => -1]);
		} else {
			$data = $id;
		}
		if (!$data) {
			return '';
		}
		if (isset($data['Site'])) {
			$data = $data['Site'];
		}
		if (empty($data['name'])) {
			return '';
		}
		$prefix = $data['name'];
		if ($data['alias']) {
			$prefix = $data['alias'];
		}
		return $prefix;
	}

	/**
	 * サイトのルートとなるコンテンツIDを取得する
	 *
	 * @param $id
	 * @return mixed
	 */
	public function getRootContentId($id)
	{
		if ($id == 0) {
			return 1;
		}
		$Content = ClassRegistry::init('Content');
		return $Content->field('id', ['Content.site_root' => true, 'Content.site_id' => $id]);
	}

	/**
	 * URLよりサイトを取得する
	 *
	 * @param string $url
	 * @return array|bool|null
	 */
	public function findByUrl($url)
	{
		if ($url === false || $url === "") {
			return $this->getRootMain();
		}
		$params = explode('/', $url);
		if (empty($params[0])) {
			return false;
		}
		$site = $this->find('first', ['conditions' => [
			'or' => [
				'Site.name' => $params[0],
				'Site.alias' => $params[0]
			]
		], 'recursive' => -1]);
		if (!$site) {
			$site = $this->getRootMain();
		}
		return $site;
	}

	/**
	 * メインサイトを取得する
	 *
	 * @param int $id
	 * @return array|null
	 */
	public function getMain($id)
	{
		$mainSiteId = $this->field('main_site_id', [
			'Site.id' => $id
		]);
		if ($mainSiteId == 0) {
			return $this->getRootMain();
		}
		return $this->find('first', ['conditions' => [
			'Site.main_site_id' => $mainSiteId
		], 'recursive' => -1]);
	}

	/**
	 * After Find
	 *
	 * @param mixed $results
	 * @param bool $primary
	 * @return mixed
	 */
	public function afterFind($results, $primary = false)
	{
		$results = parent::afterFind($results, $primary = false);
		$this->dataIter($results, function(&$entity, &$model) {
			if (isset($entity['Site']['alias']) && $entity['Site']['alias'] === '' && !empty($entity['Site']['name'])) {
				$entity['Site']['alias'] = $entity['Site']['name'];
			}
		});
		return $results;
	}

	/**
	 * 選択可能なデバイスの一覧を取得する
	 *
	 * @param int $mainSiteId メインサイトID
	 * @param int $currentSiteId 現在のサイトID
	 * @return array
	 */
	public function getSelectableDevices($mainSiteId, $currentSiteId)
	{
		$agents = Configure::read('BcAgent');
		$devices = ['' => __d('baser', '指定しない')];
		$selected = $this->find('list', [
			'fields' => ['id', 'device'],
			'conditions' => [
				'Site.main_site_id' => $mainSiteId,
				'Site.id <>' => $currentSiteId
			]
		]);
		foreach($agents as $key => $agent) {
			if (in_array($key, $selected)) {
				continue;
			}
			$devices[$key] = $agent['name'];
		}
		return $devices;
	}

	/**
	 * 選択可能が言語の一覧を取得する
	 *
	 * @param int $mainSiteId メインサイトID
	 * @param int $currentSiteId 現在のサイトID
	 * @return array
	 */
	public function getSelectableLangs($mainSiteId, $currentSiteId)
	{
		$langs = Configure::read('BcLang');
		$devices = ['' => __d('baser', '指定しない')];
		$selected = $this->find('list', [
			'fields' => ['id', 'lang'],
			'conditions' => [
				'Site.main_site_id' => $mainSiteId,
				'Site.id <>' => $currentSiteId
			]
		]);
		foreach($langs as $key => $lang) {
			if (in_array($key, $selected)) {
				continue;
			}
			$devices[$key] = $lang['name'];
		}
		return $devices;
	}

	/**
	 * デバイス設定をリセットする
	 *
	 * @return bool
	 */
	public function resetDevice()
	{
		$sites = $this->find('all', ['recursive' => -1]);
		$result = true;
		if ($sites) {
			$this->getDataSource()->begin();
			foreach($sites as $site) {
				$site['Site']['device'] = '';
				$site['Site']['auto_link'] = false;
				if (!$site['Site']['lang']) {
					$site['Site']['same_main_url'] = false;
					$site['Site']['auto_redirect'] = false;
				}
				$this->set($site);
				if (!$this->save()) {
					$result = false;
				}
			}
		}
		if (!$result) {
			$this->getDataSource()->rollback();
		} else {
			$this->getDataSource()->commit();
		}
		return $result;
	}

	/**
	 * 言語設定をリセットする
	 *
	 * @return bool
	 */
	public function resetLang()
	{
		$sites = $this->find('all', ['recursive' => -1]);
		$result = true;
		if ($sites) {
			$this->getDataSource()->begin();
			foreach($sites as $site) {
				$site['Site']['lang'] = '';
				if (!$site['Site']['device']) {
					$site['Site']['same_main_url'] = false;
					$site['Site']['auto_redirect'] = false;
				}
				$this->set($site);
				if (!$this->save()) {
					$result = false;
				}
			}
		}
		if (!$result) {
			$this->getDataSource()->rollback();
		} else {
			$this->getDataSource()->commit();
		}
		return $result;
	}

	/**
	 * Before Save
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = [])
	{
		if (!empty($this->data[$this->alias]['id']) && !empty($this->data[$this->alias]['alias'])) {
			$oldAlias = $this->field('alias', ['Site.id' => $this->data[$this->alias]['id']]);
			if ($oldAlias != $this->data[$this->alias]['alias']) {
				$this->__changedAlias = true;
			}
		}
		return true;
	}

}
