<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\PermissionGroup;
use BaserCore\Model\Entity\UserGroup;
use BaserCore\Model\Table\PermissionGroupsTable;
use BaserCore\Model\Table\PluginsTable;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PermissionGroupsService
 *
 * @property PermissionGroupsTable $PermissionGroups
 * @property UserGroupsTable $UserGroups
 */
class PermissionGroupsService implements PermissionGroupsServiceInterface
{

	/**
	 * Trait
	 */
	use BcContainerTrait;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->PermissionGroups = TableRegistry::getTableLocator()->get('BaserCore.PermissionGroups');
		$this->UserGroups = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
	}

	/**
     * アクセスルールグループを単一取得
     *
     * @param int $id
     * @param int $userGroupId
     * @return EntityInterface
     * @unitTest
     * @noTodo
     * @checked
     */
	public function get(int $id, int $userGroupId = null)
	{
		$options = [];
		if (!is_null($userGroupId)) {
			$options = [
				'contain' => [
					'Permissions' => function($q) use ($userGroupId) {
						return $q->where(['Permissions.user_group_id' => $userGroupId]);
					}]
			];
		}
		return $this->PermissionGroups->get($id, $options);
	}

	/**
	 * 初期データを取得
	 *
	 * @return EntityInterface
	 */
	public function getNew(string $prefix)
	{
		return $this->PermissionGroups->newEntity([
			'type' => $prefix
		]);
	}

	/**
	 * アクセスルールグループを更新する
	 *
	 * @param EntityInterface $entity
	 * @param array $postData
	 * @return EntityInterface
	 */
	public function create(array $postData)
	{
		$entity = $this->PermissionGroups->patchEntity($this->PermissionGroups->newEmptyEntity(), $postData);
		return $this->PermissionGroups->saveOrFail($entity);
	}

	/**
     * アクセスルールグループを更新する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @noTodo
     * @checked
     * @unitTest
     */
	public function update(EntityInterface $entity, array $postData)
	{
		$entity = $this->PermissionGroups->patchEntity($entity, $postData);
		return $this->PermissionGroups->saveOrFail($entity);
	}

    /**
     * アクセスルールグループの一覧を取得する
     *
     * @param int $userGroupId
     * @param array $queryParams
     * @return Query
     * @notodo
     * @unitTest
     * @checked
     */
	public function getIndex(int $userGroupId, array $queryParams): Query
	{
	    $options = array_merge([
	        'list_type' => null,
	        'permission_amount' => false
        ], $queryParams);

		$query = $this->PermissionGroups->find();

		if (!is_null($options['list_type'])) {
			$query->where(['type' => $queryParams['list_type']]);
		}

		if($options['permission_amount']) {
            $query->contain(['Permissions'])
                ->leftJoinWith('Permissions', function($q) use ($userGroupId) {
                    return $q->where(['Permissions.user_group_id' => $userGroupId]);
                })
                ->select(['amount' => $query->func()->count('Permissions.id')])
                ->group(['PermissionGroups.id'])
                ->enableAutoFields();
		}
		return $query;
	}

	/**
	 * アクセスグループを削除する
	 *
	 * @param int $id
	 */
	public function delete(int $id): bool
	{
		$entity = $this->get($id);
		return $this->PermissionGroups->delete($entity);
	}

	/**
     * アクセスルールグループのリストを取得する
     *
     * @param array $options
     * @return array
     * @noTodo
     * @unitTest
     * @checked
     */
	public function getList(array $options = [])
	{
		$query = $this->PermissionGroups->find('list');
		if (!empty($options['type'])) {
			$query->where(['type' => $options['type']]);
		}
		return $query->all()->toArray();
	}

	/**
	 * プラグインを指定してアクセスルールを構築する
	 *
	 * @param string $plugin
	 */
	public function buildByPlugin(string $plugin)
	{
		$userGroups = $this->UserGroups->find()->where(['id <>' => Configure::read('BcApp.adminGroupId')])->all();
		foreach($userGroups as $userGroup) {
			$this->build($userGroup->id, $plugin);
		}
	}

	/**
     * ユーザーグループを指定してアクセスグループを構築する
     *
     * @param int $userGroupId
     */
	public function buildByUserGroup(int $userGroupId)
	{
		// 有効なプラグインをキャッシュなしで強制的に取得する
		$plugins = array_merge([0 => 'BaserCore'], Hash::extract(BcUtil::getEnablePlugins(true), '{n}.name'));
		foreach($plugins as $plugin) {
			$this->build($userGroupId, $plugin);
		}
	}

	/**
	 * ユーザーを指定してアクセスルールを再構築する
	 *
	 * @param int $userGroupId
	 * @return bool
	 */
	public function rebuildByUserGroup(int $userGroupId)
	{
		$this->deleteByUserGroup($userGroupId);
		$this->buildByUserGroup($userGroupId);
		return true;
	}

	/**
     * ユーザーを指定してアクセスルールを削除する
     *
     * @param int $userGroupId
     * @noTodo
     * @unitTest
     */
	public function deleteByUserGroup(int $userGroupId)
	{
		$this->PermissionGroups->Permissions->deleteAll(['user_group_id' => $userGroupId]);
	}

	/**
	 * プラグインを指定してアクセスルールを削除する
	 *
	 * @param string $plugin
	 */
	public function deleteByPlugin(string $plugin)
	{
		$permissionGroups = $this->PermissionGroups->find()->where(['PermissionGroups.plugin' => $plugin])->all();
		foreach($permissionGroups as $group) {
			$this->PermissionGroups->delete($group);
		}
	}

	/**
	 * アクセスルールを全て構築する
	 */
	public function buildAll()
	{
	    $userGroupsService = $this->getService(UserGroupsServiceInterface::class);
		$userGroups = $userGroupsService->getIndex(['exclude_admin' => true])->all();
		foreach($userGroups as $userGroup) {
			$this->buildByUserGroup($userGroup->id);
		}
        foreach(Configure::read('BcPrefixAuth') as $key => $value) {
            $this->buildDefaultEtcRuleGroup($key, $value['name']);
        }
	}

	/**
	 * アクセスルールを構築する
	 *
	 * @param int $userGroupId
	 * @param string $plugin
	 * @return bool
	 */
	public function build(int $userGroupId, string $plugin)
	{
		$pluginPath = BcUtil::getPluginPath($plugin);
		if (file_exists($pluginPath . 'config' . DS . 'permission.php')) {
			try {
				Configure::load($plugin . '.permission', 'baser');
			} catch (BcException $e) {
				return false;
			}
		} else {
			foreach(Configure::read('BcPrefixAuth') as $key => $prefix) {
				$this->buildAllowAllMethodByPlugin($userGroupId, $plugin, $key, $prefix['name']);
			}
		}

		$permissionsService = $this->getService(PermissionsServiceInterface::class);
		$settings = Configure::read('permission');
		if (!$settings) return false;

		$result = true;
		foreach($settings as $ruleGroupName => $setting) {

			// PermissionGroup 存在確認、なければ作成
			$name = isset($setting['title'])? $setting['title'] : $ruleGroupName;
			$query = $this->PermissionGroups->find()->where(['name' => $name, 'type' => $setting['type']]);
			if ($query->count()) {
				$permissionGroup = $query->first();
			} else {
				$permissionGroup = new PermissionGroup([
					'name' => $name,
					'type' => $setting['type'],
					'plugin' => $plugin
				]);
				$permissionGroup = $this->PermissionGroups->save($permissionGroup);
			}

            // ゲストの場合、Front のタイプじゃない場合は作成しない
            if($userGroupId === 0 && $setting['type'] !== 'Front') continue;

			if (!$setting['items']) continue;

			// Permission 作成
			foreach($setting['items'] as $ruleName => $item) {
				$permissionsService->create([
					'name' => isset($item['title'])? $item['title'] : $ruleName,
					'permission_group_id' => $permissionGroup->id,
					'user_group_id' => $userGroupId,
					'url' => $item['url'],
					'auth' => $item['auth'],
					'method' => $item['method'],
					'status' => true,
				]);
			}
		}

		Configure::delete('permission');
		return $result;
	}

	/**
	 * 指定したプラグインについて全てを許可するアクセスルールを構築する
	 *
	 * @param int $userGroupId
	 * @param string $plugin
	 * @param string $type
	 * @param string $typeName
	 */
	public function buildAllowAllMethodByPlugin(int $userGroupId, string $plugin, string $type, string $typeName)
	{
		/** @var PluginsTable $pluginsTable */
		$pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
		$pluginConfig = $pluginsTable->getPluginConfig($plugin);
		$permissionGroupName = $pluginConfig->name . ' ' . $typeName;
		$query = $this->PermissionGroups->findByName($permissionGroupName);
		if ($query->count()) {
			$permissionGroup = $query->first();
		} else {
			$permissionGroup = new PermissionGroup([
				'name' => $permissionGroupName,
				'type' => $type,
				'plugin' => $plugin
			]);
			$permissionGroup = $this->PermissionGroups->save($permissionGroup);
		}

		$permissionsService = $this->getService(PermissionsServiceInterface::class);
		$url = '/baser/' . Inflector::underscore($type) . '/' . Inflector::dasherize($plugin) . '/*';
		$permissionsService->create([
			'name' => __d('baser_core', 'フルアクセス'),
			'permission_group_id' => $permissionGroup->id,
			'user_group_id' => $userGroupId,
			'url' => $url,
			'auth' => true,
			'method' => '*',
			'status' => true,
		]);
	}

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @return array
     */
	public function getControlSource(string $field, array $options = []): array
    {
        /** @var UserGroupsService $userGroupsService */
        $userGroupsService = $this->getService(UserGroupsServiceInterface::class);

        if($field === 'user_group_id') {

            $userGroups = $userGroupsService->getIndex([
                'finder' => 'list',
                'exclude_admin' => true
            ])->all()->toArray();
            if(!Configure::read('BcPrefixAuth.Front.disabled')) {
                $userGroups = ['0' => __d('baser_core', 'ゲスト')] + $userGroups;
            }
            return $userGroups;

        } elseif($field === 'auth_prefix') {

            $prefixes = BcUtil::getAuthPrefixList();
            /** @var UserGroup $userGroup */
            if(isset($options['user_group_id'])) {
                $available = [];
                if((int) $options['user_group_id'] !== 0) {
                    $userGroup = $userGroupsService->get($options['user_group_id']);
                    $available = $userGroup->getAuthPrefixArray();
                }
                if(!Configure::read('BcPrefixAuth.Front.disabled')) {
                    $available[] = 'Front';
                }
                foreach($prefixes as $key => $prefix) {
                    if(!in_array($key, $available)) {
                        unset($prefixes[$key]);
                    }
                }
            }
            return $prefixes;

        }
        return [];
    }

    /**
     * アクセスグループを利用可能なユーザーグループの最小のIDを取得する
     *
     * @return false|mixed
     */
    public function getAvailableMinUserGroupId()
    {
        /** @var UserGroupsService $userGroupsService */
        $userGroupsService = $this->getService(UserGroupsServiceInterface::class);
        $userGroup = $userGroupsService->getIndex([
            'exclude_admin' => true,
            'order' => 'id'
        ])->first();
        if($userGroup) {
            return $userGroup->id;
        } else {
            return false;
        }
    }

    /**
     * デフォルトのその他のルールグループを作成する
     *
     * タイプを指定してタイプごとに作る
     *
     * @param string $type
     * @param string $name
     * @return EntityInterface|false
     */
    public function buildDefaultEtcRuleGroup(string $type, string $name)
    {
        $permissionGroup = new PermissionGroup([
            'name' => __d('baser_core', '{0} その他', $name),
            'type' => $type,
            'plugin' => null
        ]);
        return $this->PermissionGroups->save($permissionGroup);
    }

}
