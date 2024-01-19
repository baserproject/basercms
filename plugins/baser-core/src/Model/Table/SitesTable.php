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

use ArrayObject;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\ContentsService;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcLang;
use BaserCore\Utility\BcUtil;
use BaserCore\Utility\BcAgent;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use BaserCore\Model\Entity\Site;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\ResultSetInterface;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Utility\BcAbstractDetector;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\ContentFoldersServiceInterface;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class Site
 *
 * サイトモデル
 * @method Site newEntity($data = null, array $options = [])
 */
class SitesTable extends AppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Contents
     *
     * @var ContentsTable $Contents
     */
    public $Contents;

    /**
     * 保存時にエイリアスが変更されたかどうか
     *
     * @var bool
     */
    private $changedAlias = false;

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
        $this->setTable('sites');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->setDisplayField('display_name');
        $this->Contents = TableRegistry::getTableLocator()->get("BaserCore.Contents");
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
        $validator->setProvider('site', 'BaserCore\Model\Validation\SiteValidation');

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->maxLength('name', 50, __d('baser_core', '識別名称は50文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser_core', '識別名称を入力してください。'))
            ->notEmptyString('name', __d('baser_core', '識別名称を入力してください。'))
            ->add('name', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に利用されている識別名称です。別の名称に変更してください。')
                ]])
            ->add('name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '識別名称は、半角英数・ハイフン（-）・アンダースコア（_）で入力してください。')
                ]]);
        $validator
            ->scalar('display_name')
            ->maxLength('display_name', 50, __d('baser_core', 'サイト名は50文字以内で入力してください。'))
            ->requirePresence('display_name', 'create', __d('baser_core', 'サイト名を入力してください。'))
            ->notEmptyString('display_name', __d('baser_core', 'サイト名を入力してください。'));
        $validator
            ->scalar('alias')
            ->maxLength('alias', 50, __d('baser_core', 'エイリアスは50文字以内で入力してください。'))
            ->requirePresence('alias', 'create', __d('baser_core', 'エイリアスを入力してください。'))
            ->allowEmptyString('alias')
            ->add('alias', [
                'aliasUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に利用されているエイリアス名です。別の名称に変更してください。')
                ]])
            ->add('alias', [
                'aliasSlashChecks' => [
                    'rule' => 'aliasSlashChecks',
                    'provider' => 'site',
                    'message' => __d('baser_core', 'エイリアスには先頭と末尾にスラッシュ（/）は入力できず、また、連続して入力する事もできません。')
                ]])
            ->add('alias', [
                'nameCheckContentExists' => [
                    'rule' => 'checkContentExists',
                    'provider' => 'site',
                    'message' => __d('baser_core', 'コンテンツ管理上にエイリアスと同名のコンテンツ、またはフォルダが存在するため利用できません。別の名称にするか、コンテンツ、またはフォルダをリネームしてください。')
                ]]);
        $validator
            ->scalar('title')
            ->maxLength('title', 255, __d('baser_core', 'サイトタイトルは255文字以内で入力してください。'))
            ->requirePresence('title', 'create', __d('baser_core', 'サイトタイトルを入力してください。'))
            ->notEmptyString('title', __d('baser_core', 'サイトタイトルを入力してください。'));
        return $validator;
    }

    /**
     * 公開されている全てのサイトを取得する
     *
     * @return ResultSetInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getPublishedAll(): ResultSetInterface
    {
        return $this->find()->where(['status' => true])->all();
    }

    /**
     * サイトリストを取得
     *
     * @param bool $mainSiteId メインサイトID
     * @param array $options
     *  - `excludeIds` : 除外するID（初期値：なし）
     *  - `status` : 有効かどうか（初期値：true）
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList($mainSiteId = null, $options = [])
    {
        $options = array_merge([
            'excludeIds' => [],
            'status' => true
        ], $options);

        $conditions = [];
        if (!is_null($options['status'])) {
            $conditions = ['status' => $options['status']];
        }

        if (!is_null($mainSiteId)) {
            $conditions['main_site_id'] = $mainSiteId;
        }

        if (isset($options['excludeIds'])) {
            if (!is_array($options['excludeIds'])) {
                $options['excludeIds'] = [$options['excludeIds']];
            }
            $excludeKey = array_search(0, $options['excludeIds']);
            if ($excludeKey !== false) {
                unset($options['excludeIds'][$excludeKey]);
            }
            if ($options['excludeIds']) {
                $conditions[]['id NOT IN'] = $options['excludeIds'];
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
                $conditions[]['id IN'] = $options['includeIds'];
            }
        }
        $this->setDisplayField('display_name');
        return $this->find('list')->where($conditions)->toArray();
    }

    /**
     * メインサイトのデータを取得する
     *
     * @param mixed $options
     *  - `fields` : 取得するフィールド
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRootMain($options = [])
    {
        return $this->find()->where(['main_site_id IS' => null])->first();
    }

    /**
     * メインサイトかどうか判定する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isMain(int $id)
    {
        return !$this->find()->where(['main_site_id' => $id])->all()->isEmpty();
    }

    /**
     * サイトを取得する
     *
     * @param $id
     * @param array $options
     * @return ResultSetInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function children($id, $options = [])
    {
        $options = array_merge_recursive([
            'conditions' => [
                'main_site_id' => $id
            ]
        ], $options);
        return $this->find()->where($options['conditions'])->all();
    }

    /**
     * After Save
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        /* @var ContentFoldersService $contentFolderService */
        $contentFolderService = $this->getService(ContentFoldersServiceInterface::class);
        $contentFolderService->saveSiteRoot($entity, $this->changedAlias);
        $this->getEventManager()->off('Model.beforeSave');
        $this->getEventManager()->off('Model.afterSave');
        if (!empty($entity->main)) {
            $site = $this->find()->where(['Site.main' => true, 'Site.id <>' => $this->id])->first();
            if ($site) {
                $site->main = false;
                $this->save($site, ['validate' => false]);
            }
        }
        $this->changedAlias = false;
    }

    /**
     * After Delete
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $content = $this->Contents->find()->where(['Contents.site_id' => $entity->id, 'Contents.site_root' => true])->first();
        /* @var ContentsService $contentService */
        $contentService = $this->getService(ContentsServiceInterface::class);

        $children = $this->children($entity->id);
        if($children) {
            $eventManager = $this->getEventManager();
            $beforeSaveEventListener = BcUtil::offEvent($eventManager, 'Model.beforeSave');
            $afterSaveEventListener = BcUtil::offEvent($eventManager, 'Model.afterSave');
            foreach($children as $child) {
                $childRootContent = $contentService->get($this->getRootContentId($child->id));
                $contentService->update($childRootContent, ['id' => $childRootContent->id, 'parent_id' => 1]);
                $child->main_site_id = 1;
                $this->save($child);
            }
            BcUtil::onEvent($eventManager, 'Model.beforeSave', $beforeSaveEventListener);
            BcUtil::onEvent($eventManager, 'Model.afterSave', $afterSaveEventListener);
        }

        $children = $contentService->getChildren($content->id, ['site_id' => $entity->id]);
        if (isset($children)) {

            $eventManager = $this->Contents->getEventManager();
            $afterSaveEventListener = BcUtil::offEvent($eventManager, 'Model.afterSave');
            foreach($children as $child) {
                $child->site_id = 1;
                $this->Contents->save($child);
            }
            BcUtil::onEvent($eventManager, 'Model.afterSave', $afterSaveEventListener);

            $children = $contentService->getChildren($content->id);
            foreach($children as $child) {
                $contentService->delete($child->id);
            }
        }
        /* @var ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        if (!$contentsService->hardDelete($content->id)) return false;
        return true;
    }

    /**
     * プレフィックスを取得する
     *
     * @param mixed $id | $data
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPrefix($id)
    {
        if (is_null($id)) return '';

        $site = $this->find()->select(['name', 'alias'])->where(['id' => $id])->first();
        if (!$site) {
            return false;
        }
        $prefix = $site->name;
        if ($site->alias) {
            $prefix = $site->alias;
        }
        return $prefix;
    }

    /**
     * サイトのルートとなるコンテンツIDを取得する
     *
     * @param $id
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRootContentId($id)
    {
        if ($id == 1) {
            return 1;
        }
        $Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contents = $Contents->find()->select(['id'])->where(['Contents.site_root' => true, 'Contents.site_id' => $id]);
        if (!$contents->all()->isEmpty()) return $contents->first()->id;
        return 1;
    }

    /**
     * URLよりサイトを取得する
     *
     * @param string $url
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByUrl($url)
    {
        if (!$url) {
            $url = '/';
        }
        $parseUrl = parse_url($url);
        if (empty($parseUrl['path'])) {
            return $this->getRootMain();
        }
        $url = $parseUrl['path'];
        $url = preg_replace('/(^\/|\/$)/', '', $url);
        $urlAry = explode('/', $url);
        $domain = BcUtil::getCurrentDomain();
        $subDomain = BcUtil::getSubDomain();
        $where = [];
        for($i = count($urlAry); $i > 0; $i--) {
            $where['or'][] = ['alias' => implode('/', $urlAry)];
            if ($subDomain) {
                $where['or'][] = [
                    'domain_type' => 1,
                    'alias' => $subDomain . '/' . implode('/', $urlAry),
                ];
            }
            if ($domain) {
                $where['or'][] = [
                    'domain_type' => 2,
                    'alias' => $domain . '/' . implode('/', $urlAry),
                ];
            }
            unset($urlAry[$i - 1]);
        }
        if ($subDomain) {
            $where['or'][] = [
                'domain_type' => 1,
                'alias' => $subDomain,
            ];
        }
        if ($domain) {
            $where['or'][] = [
                'domain_type' => 2,
                'alias' => $domain,
            ];
        }
        $result = $this->find()->where($where)->orderBy(['alias DESC']);
        if ($result->count()) {
            return $result->first();
        } else {
            return $this->getRootMain();
        }
    }

    /**
     * URLに関連するメインサイトを取得する
     * @param $url
     * @return array|EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMainByUrl($url)
    {
        $site = $this->findByUrl($url);
        if ($site->main_site_id) {
            return $this->find()->where(['id' => $site->main_site_id])->first();
        }
        return null;
    }

    /**
     * URLに関連するサブサイトを取得する
     * @param $url
     * @param false $sameMainUrl
     * @param BcAbstractDetector|null $agent
     * @param BcAbstractDetector|null $lang
     * @return mixed|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSubByUrl(
        $url,
        $sameMainUrl = false,
        BcAbstractDetector $agent = null,
        BcAbstractDetector $lang = null
    )
    {
        $SiteConfigsService = new SiteConfigsService();
        $currentSite = $this->findByUrl($url);
        $sites = $this->find()->all();

        if (!$lang) {
            $lang = BcLang::findCurrent();
        }
        if (!$agent) {
            $agent = BcAgent::findCurrent();
        }

        // 言語の一致するサイト候補に絞り込む
        $langSubSites = [];
        if ($lang && $SiteConfigsService->getValue('use_site_lang_setting')) {
            foreach($sites as $site) {
                if (!$site->status) {
                    continue;
                }
                if (!$sameMainUrl || ($sameMainUrl && $site->same_main_url)) {
                    if ($site->lang == $lang->name && $currentSite->id == $site->main_site_id) {
                        $langSubSites[] = $site;
                        break;
                    }
                }
            }
        }
        if ($langSubSites) {
            $subSites = $langSubSites;
        } else {
            $subSites = $sites;
        }
        if ($agent && $SiteConfigsService->getValue('use_site_device_setting')) {
            foreach($subSites as $subSite) {
                if (!$subSite->status) {
                    continue;
                }
                if (!$sameMainUrl || ($sameMainUrl && $subSite->same_main_url)) {
                    if ($subSite->device == $agent->name && $currentSite->id == $subSite->main_site_id) {
                        return $subSite;
                    }
                }
            }
        }
        if ($langSubSites) {
            return $langSubSites[0];
        }
        return null;
    }

    /**
     * メインサイトを取得する
     *
     * @param int $id
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMain($id)
    {
        $currentSite = $this->find()->where(['id' => $id])->first();
        if (!$currentSite) {
            return false;
        }
        if (is_null($currentSite->main_site_id)) {
            return $this->getRootMain();
        }
        $mainSite = $this->find()->where([
            'id' => $currentSite->main_site_id
        ])->first();
        if (!$mainSite) {
            return false;
        }
        return $mainSite;
    }

    /**
     * 選択可能なデバイスの一覧を取得する
     *
     * 現在のサイトとすでに利用されいているデバイスは除外する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSelectableDevices($mainSiteId, $currentSiteId)
    {
        $agents = Configure::read('BcAgent');
        $devices = ['' => __d('baser_core', '指定しない')];
        $this->setDisplayField('device');
        $conditions = [
            'id IS NOT' => $currentSiteId
        ];
        if ($mainSiteId) {
            $conditions['main_site_id'] = $mainSiteId;
        } else {
            $conditions['main_site_id IS'] = null;
        }
        $selected = $this->find('list')
            ->where($conditions)->toArray();
        foreach($agents as $key => $agent) {
            if (in_array($key, $selected)) {
                continue;
            }
            $devices[$key] = $agent['name'];
        }
        return $devices;
    }

    /**
     * 選択可能な言語の一覧を取得する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSelectableLangs($mainSiteId, $currentSiteId)
    {
        $langs = Configure::read('BcLang');
        $devices = ['' => __d('baser_core', '指定しない')];
        $this->setDisplayField('lang');
        $conditions = [
            'id IS NOT' => $currentSiteId
        ];
        if ($mainSiteId) {
            $conditions['main_site_id'] = $mainSiteId;
        } else {
            $conditions['main_site_id IS'] = null;
        }
        $selected = $this->find('list')
            ->where($conditions)->toArray();
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetDevice()
    {
        $sites = $this->find()->all();
        $result = true;
        if ($sites) {
            $this->getConnection()->begin();
            foreach($sites as $site) {
                $site->device = '';
                $site->auto_link = false;
                if (!$site->lang) {
                    $site->same_main_url = false;
                    $site->auto_redirect = false;
                }
                if (!$this->save($site)) {
                    $result = false;
                }
            }
        }
        if (!$result) {
            $this->getConnection()->rollback();
        } else {
            $this->getConnection()->commit();
        }
        return $result;
    }

    /**
     * 言語設定をリセットする
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetLang()
    {
        $sites = $this->find()->all();
        $result = true;
        if ($sites) {
            $this->getConnection()->begin();
            foreach($sites as $site) {
                $site->lang = '';
                if (!$site->device) {
                    $site->same_main_url = false;
                    $site->auto_redirect = false;
                }
                if (!$this->save($site)) {
                    $result = false;
                }
            }
        }
        if (!$result) {
            $this->getConnection()->rollback();
        } else {
            $this->getConnection()->commit();
        }
        return $result;
    }

    /**
     * Before Save
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // エイリアスに変更があったかチェックする
        if ($entity->id && $entity->alias) {
            $oldSite = $this->find()->where(['id' => $entity->id])->first();
            if ($oldSite && $oldSite->alias !== $entity->alias) {
                $this->changedAlias = true;
            }
        }
        return true;
    }

    /**
     * 保存時に管理画面のセッション中のカレントサイトと同じデータの保存の場合、
     * セッションにも反映する
     *
     * @param EntityInterface $entity
     * @param array $options
     * @return bool
     * @checked
     * @noTodo
     */
    public function save(
        EntityInterface $entity,
        array $options = []
    ): EntityInterface|false {
        $success = parent::save($entity, $options);
        $request = Router::getRequest();
        if($request) {
            $session = Router::getRequest()->getSession();
            $currentSite = $session->read('BcApp.Admin.currentSite');
            if ($currentSite && $success->id === $currentSite->id) {
                $session->write('BcApp.Admin.currentSite', $success);
            }
        }
        return $success;
    }

}
