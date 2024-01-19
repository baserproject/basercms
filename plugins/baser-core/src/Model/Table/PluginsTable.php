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

namespace BaserCore\Model\Table;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use Cake\Validation\Validator;

/**
 * Class PluginsTable
 */
class PluginsTable extends AppTable
{
    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50, __d('baser_core', 'プラグイン名は50文字以内としてください。'))
            ->notEmptyString('name', __d('baser_core', 'プラグイン名は必須です。'))
            ->add('name', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '指定のプラグインは既に使用されています。')
                ]])
            ->add('name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'プラグイン名は半角英数字とハイフン、アンダースコアのみが利用可能です。')
                ]]);

        $validator
            ->scalar('title')
            ->maxLength('title', 50, __d('baser_core', 'プラグインタイトルは50文字以内としてください。'));

        return $validator;
    }

    /**
     * プラグイン情報を取得する
     *
     * @param string $name プラグイン名
     * @return \BaserCore\Model\Entity\Plugin|\Cake\Datasource\EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getPluginConfig($name)
    {
        if (!$name) $name = 'BaserCore';
        $pluginName = Inflector::camelize($name, '-');

        // プラグインのバージョンを取得
        $corePlugins = array_merge(Configure::read('BcApp.core'), Configure::read('BcApp.corePlugins'));
        if (in_array($pluginName, $corePlugins)) {
            $core = true;
            $version = BcUtil::getVersion();
        } else {
            $core = false;
            $version = BcUtil::getVersion($pluginName);
        }

        $pluginPath = BcUtil::getPluginPath($name);
        if (file_exists($pluginPath . 'screenshot.png')) {
            $hasScreenshot = true;
        } else {
            $hasScreenshot = false;
        }

        $result = $this->find()
            ->orderBy(['priority'])
            ->where(['name' => $pluginName])
            ->first();

        if ($result) {
            $pluginRecord = $result;
            $this->patchEntity($pluginRecord, [
                'update' => false,
                'core' => $core,
                'permission' => 1,
                'registered' => true,
                'screenshot' => $hasScreenshot
            ]);
            if (BcUtil::verpoint($pluginRecord->version) < BcUtil::verpoint($version) &&
                !in_array($pluginRecord->name, Configure::read('BcApp.corePlugins'))
            ) {
                $pluginRecord->update = true;
            }
        } else {
            $pluginRecord = $this->newEntity([
                'id' => '',
                'name' => $pluginName,
                'created' => '',
                'version' => $version,
                'status' => false,
                'update' => false,
                'core' => $core,
                'permission' => 1,
                'registered' => false,
                'db_init' => false,
                'screenshot' => $hasScreenshot
            ]);
        }

        // 設定ファイル読み込み
        $appConfigPath = $pluginPath . 'config.php';
        if (file_exists($appConfigPath)) {
        	$config = include $appConfigPath;
        	if(is_array($config)) {
				$this->patchEntity($pluginRecord, include $appConfigPath);
			}
        }
        if($name === 'BaserCore') $pluginRecord->title = 'BaserCore';
        return $pluginRecord;
    }

    /**
     * プラグインをインストールする
     *
     * @param $name
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function install($name): bool
    {
        $recordExists = $this->find()->where(['name' => $name])->count();
        $plugin = $this->getPluginConfig($name);
        if (!$recordExists) {
            $corePlugins = Configure::read('BcApp.corePlugins');
            if (in_array($name, $corePlugins)) {
                $version = BcUtil::getVersion();
            } else {
                $version = BcUtil::getVersion($name);
            }
            $query = $this->find();
            $priority = $query->select(['max' => $query->func()->max('priority')])->first();
            $plugin->version = ($version)? $version : null;
            $plugin->priority = $priority->max + 1;
            $plugin->db_init = true;
            $plugin->status = true;
        } else {
            $plugin->db_init = true;
            $plugin->status = true;
        }
        if ($this->save($plugin)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * プラグインをアンインストールする
     *
     * @param $name
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function uninstall($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        if (!$targetPlugin) return true;
        \Cake\Core\Plugin::getCollection()->remove($name);
        return $this->delete($targetPlugin);
    }

    /**
     * プラグインを無効化する
     *
     * @param $name
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function detach($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        if ($targetPlugin === null) {
            return false;
        }
        $targetPlugin->status = false;
        $result = $this->save($targetPlugin);
        \Cake\Core\Plugin::getCollection()->remove($name);
        BcUtil::clearAllCache();
        return $result !== false;
    }

    /**
     * プラグインを有効化する
     *
     * @param $name
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function attach($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        if ($targetPlugin === null) {
            return false;
        }
        $targetPlugin->status = true;
        $result = $this->save($targetPlugin);
        BcUtil::clearAllCache();
        return $result !== false;
    }

    /**
     * データベースをアップデートする
     * @param string $name
     * @param string $version
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update($name, $version): bool
    {
        if (!$name || $name === 'BaserCore') {
            $siteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
            return (bool)$siteConfigs->saveKeyValue(['version' => $version]);
        } else {
            $plugin = $this->find()->select()->where(['name' => $name])->first();
            if ($plugin) {
                $plugin->version = $version;
                return (bool)$this->save($plugin);
            } else {
                return true;
            }
        }
    }

}
