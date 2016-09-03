<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サブサイトモデル
 *
 * @package Baser.Model
 */
class Site extends AppModel {

/**
 * ビヘイビア
 *
 * @var array
 */
	public $actsAs = ['BcCache'];
		
/**
 * バリデーション
 *
 * @var array
 */
	public $validate = [
		'name' => [
			[
				'rule' => ['notBlank'],
				'message' => '識別名称を入力してください。'
			],
			[
				'rule' => ['maxLength', 50],
				'message' => '識別名称は50文字以内で入力してください。'
			],
            [
            	'rule' => ['alphaNumericPlus'],
                'message' => '識別名称は、半角英数・ハイフン（-）・アンダースコア（_）で入力してください。'
            ],
            [
            	'rule' => ['duplicate'],
            	'message' => '既に利用されている識別名称です。別の名称に変更してください。'
			]
		],
		'display_name' => [
			[
				'rule' => ['notBlank'],
				'message' => 'サブサイト名を入力してください。'
			],
			[
				'rule' => ['maxLength', 50],
				'message' => 'サブサイト名は50文字以内で入力してください。'
			]
		],
		'alias' => [
			[
				'rule' => ['maxLength', 50],
				'message' => 'エイリアスは50文字以内で入力してください。'
			],
            [
            	'rule' => ['alphaNumericPlus', ['/', '.']],
                'message' => 'エイリアスは、半角英数・ハイフン（-）・アンダースコア（_）・スラッシュ（/）・ドット（.）で入力してください。'
 			],
            [
            	'rule' => ['duplicate'],
            	'message' => '既に利用されているエイリアス名です。別の名称に変更してください。'
			]
		],
		'title' => [
			[
				'rule' => ['notBlank'],
				'message' => 'サブサイトタイトルを入力してください。'
			],
			[
				'rule' => ['maxLength', 255],
				'message' => 'サブサイトタイトルは255文字以内で入力してください。'
			]
		]
	];

/**
 * 公開されている全てのサイトを取得する
 * 
 * @return array
 */
	public function getPublishedAll() {
		$conditions = ['Site.status' => true];
		$sites = $this->find('all', ['conditions' => $conditions]);
		$main = $this->getMain();
		if($sites) {
			array_unshift($sites, $main);
		} else {
			$sites = [$main];
		}
		return $sites;
	}
	
/**
 * サイトリストを取得
 *
 * @param bool $mainOnly
 * @return array
 */
    public function getSiteList($mainSiteId = null) {
    	$conditions = ['Site.status' => true];
    	if(!is_null($mainSiteId)) {
    		$conditions['Site.main_site_id'] = $mainSiteId;
    	}
		$main = $this->getMain();
    	return [$main['Site']['id'] => $main['Site']['display_name']] + $this->find('list', ['fields' => ['id', 'display_name'], 'conditions' => $conditions]);
    }
	
/**
 * メインサイトのデータを取得する
 * 
 * @return array
 */
	public function getMain($options = []) {
		$options += [
			'fields' => []	
		];
		// =============================================================
		// テストの際、Fixture のロード前に、設定 BcSite を、DBから読む為、
		// テストデータが利用できないので、テストの際には、直接DBより取得させる
		// =============================================================
		if($this->useDbConfig == 'test') {
			$SiteConfig = ClassRegistry::init('SiteConfig');
			$siteConfigs = $SiteConfig->findExpanded();
		} else {
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
			'relate_main_site' => null,
			'created' => null,
			'modified' => null
		]];
		if($options['fields']) {
			if(!is_array($options['fields'])) {
				$options['fields'] = [$options['fields']];
			}
			$siteTmp = [];
			foreacH($options['fields'] as $field) {
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
	public function getRelatedContents($contentId) {
		$Content = ClassRegistry::init('Content');
		$data = $Content->find('first', ['conditions' => ['Content.id' => $contentId]]);
		$isMainSite = $this->isMain($data['Site']['id']);

		$conditions = ['Site.status' => true];
		if(is_null($data['Site']['main_site_id'])){
			$conditions['Site.main_site_id'] = 0;
			$mainSiteContentId = $data['Content']['id'];
		} else {
			$conditions['or'] = [
				['Site.main_site_id' => $data['Site']['main_site_id']],
				['Site.id' => $data['Site']['main_site_id']]
		];
		if($isMainSite) {
			$conditions['or'][] = ['Site.main_site_id' => $data['Site']['id']];
		}
			if($data['Content']['main_site_content_id']) {
			$mainSiteContentId = $data['Content']['main_site_content_id'];
			} else {
				$mainSiteContentId = $data['Content']['id'];
			}
		}
		$fields = ['id', 'name', 'alias', 'display_name', 'main_site_id'];
		$sites = $this->find('all', ['fields' => $fields, 'conditions' => $conditions, 'order' => 'main_site_id']);
		if($data['Site']['main_site_id'] == 0) {
			$sites = array_merge([$this->getMain(['fields' => $fields])], $sites);
		}
		$conditions = [
			'or' => [
				['Content.id' => $mainSiteContentId],
				['Content.main_site_content_id' => $mainSiteContentId]
			]
		];
		if($isMainSite) {
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
	public function isMain($id) {
		if($id == null) {
			$id = 0;
		}
		return (bool) $this->children($id);
	}

/**
 * サブサイトを取得する
 *
 * @param $id
 * @param array $options
 * @return array|null
 */
	public function children($id, $options = []) {
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
	public function afterSave($created, $options = []) {
		parent::afterSave($created, $options);
		App::uses('AuthComponent',  'Controller/Component');
		$user = AuthComponent::user();
		$ContentFolder = ClassRegistry::init('ContentFolder');
		if($created) {
			$ContentFolder->saveSiteRoot(null, [
				'site_id'	=> $this->id,
				'name'		=> ($this->data['Site']['alias'])? $this->data['Site']['alias']: $this->data['Site']['name'],
				'parent_id'	=> 1,
				'title'		=> $this->data['Site']['title'],
				'self_status'	=> $this->data['Site']['status'],
				'author_id' => $user['id'],
				'site_root'	=> true,
				'layout_template' => 'default'
			]);
		} else {
			$ContentFolder->saveSiteRoot($this->id, [
				'name'		=> ($this->data['Site']['alias'])? $this->data['Site']['alias']: $this->data['Site']['name'],
				'title'		=> $this->data['Site']['title'],
				'self_status'	=> $this->data['Site']['status'],
		  ]);
		}
		if(!empty($this->data['Site']['main'])) {
			$data = $this->find('first', ['conditions' => ['Site.main' => true, 'Site.id <>' => $this->id], 'recursive' => -1]);
			if($data) {
				$data['Site']['main'] = false;
				$this->save($data, array('validate' => false, 'callbacks' => false));
			}
		}
	}

/**
 * After Delete
 */
	public function afterDelete() {
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
	
		$Content->softDelete(false);
		$Content->removeFromTree($id, true);
	}

/**
 * プレフィックスを取得する
 *
 * @param mixed $id | $data
 * @return mixed
 */
	public function getPrefix($id) {
		if(!is_array($id)) {
			$data = $this->find('first', ['fields' => ['name', 'alias'], 'conditions' => ['Site.id' => $id], 'recursive' => -1]);
		} else {
			$data = $id;
		}
		if(!$data) {
			return '';
		}
		if(isset($data['Site'])) {
			$data = $data['Site'];
		}
		if(empty($data['name'])) {
			return '';
		}
		$prefix = $data['name'];
		if($data['alias']) {
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
	public function getRootContentId($id) {
		if($id == 0) {
			return 1;
		}
		$Content = ClassRegistry::init('Content');
		return $Content->field('id', ['Content.site_root' => true, 'Content.site_id' => $id]);
	}

}