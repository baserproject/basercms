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

use Cake\Core\Plugin;
use Cake\Datasource\QueryInterface;
use Cake\ORM\Table;
use Exception;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Http\ServerRequest;
use BaserCore\Utility\BcUtil;
use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Content;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentsService
 * @property ContentsTable $Contents
 * @checked
 * @noTodo
 * @unitTest
 */
class ContentsService implements ContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Contents
     *
     * @var ContentsTable
     */
    public ContentsTable|Table $Contents;

    /**
     * Sites
     *
     * @var SitesTable
     */
    public SitesTable|Table $Sites;

    /**
     * Construct
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->Contents = TableRegistry::getTableLocator()->get("BaserCore.Contents");
        $this->Sites = TableRegistry::getTableLocator()->get("BaserCore.Sites");
    }

    /**
     * 新しいデータの初期値を取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->Contents->newEntity([]);
    }

    /**
     * リストデータを取得
     *
     * @param array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->Contents->find('list',
        keyField: 'id',
        valueField: 'title')->toArray();
    }

    /**
     * 新規登録する
     * 対応しない
     *
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData): ?EntityInterface
    {
        return null;
    }

    /**
     * コンテンツを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id, array $queryParams = []): EntityInterface
    {
        $queryParams = array_merge([
            'status' => '',
            'contain' => ['Sites']
        ], $queryParams);

        $conditions = [];
        if ($queryParams['status'] === 'publish') {
            $conditions = $this->Contents->getConditionAllowPublish();
        }
        return $this->Contents->get($id,
        contain: $queryParams['contain'],
        conditions: $conditions);
    }

    /**
     * ゴミ箱のコンテンツを取得する
     *
     * @param int $id
     * @return EntityInterface|array
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id)
    {
        return $this->Contents->getTrash($id);
    }

    /**
     * コンテンツの子要素を取得する
     *
     * @param int $id
     * @param array $conditions
     * @return Query|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getChildren($id, $conditions = [])
    {
        try {
            $query = $this->Contents->find('children', for: $id, order: ['Contents.lft' => 'ASC'])
                ->where($conditions);
        } catch (\Exception) {
            return null;
        }
        return $query->all()->isEmpty()? null : $query;
    }

    /**
     * getTreeIndex
     *
     * @param array $queryParams
     * @return Query
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getTreeIndex(array $queryParams): Query
    {
        unset(
            $queryParams['folder_id'],
            $queryParams['name'],
            $queryParams['type'],
            $queryParams['self_status'],
            $queryParams['author_id'],
            $queryParams['limit'],
            $queryParams['withTrash']
        );
        return $this->getIndex($queryParams, 'threaded')->orderBy(['lft']);
    }

    /**
     * テーブルインデックス用の条件を返す
     *
     * @param Query $query
     * @param array $queryParams
     * @return Query $query
     * @checked
     * @noTodo
     */
    public function setConditions(Query $query, array $queryParams): Query
    {
        $params = array_merge([
            'id' => null,
            'type' => null,
            'type!' => null,
            'parent_id' => null,
            'author_id' => null,
            'site_id' => null,
            'title' => null,
            'name' => null,
            'status' => null,
            'folder_id' => null,
        ], $queryParams);

        $conditions = [];

        // type
        if(!empty($params['id'])) $conditions['Contents.id'] = $params['id'];
        // type
        if(!empty($params['type'])) {
            if($params['type'] === 'ContentAlias') {
                $conditions['Contents.alias_id IS NOT'] = null;
            } else {
                $conditions['Contents.type'] = $params['type'];
            }
        }
        if(!empty($params['type!'])) $conditions['Contents.type IS NOT'] = $params['type!'];
        // author_id
        if(!empty($params['parent_id'])) $conditions['Contents.parent_id'] = $params['parent_id'];
        // author_id
        if(!empty($params['author_id'])) $conditions['Contents.author_id'] = $params['author_id'];
        // site_id
        if(!empty($params['site_id'])) $conditions['Contents.site_id'] = $params['site_id'];
        // title
        if(!empty($params['title'])) $conditions['Contents.title LIKE'] = '%' . $params['title'] . '%';

        // name
        if(!empty($params['name']) ) {
            $conditions[] = ['OR' => [
                'Contents.name LIKE' => '%' . $params['name'] . '%',
                'Contents.title LIKE' => '%' . $params['name'] . '%'
            ]];
        }

        // status
        if (!is_null($params['status'])) {
            if($params['status'] === 'publish') {
                $conditions = array_merge($conditions, $this->Contents->getConditionAllowPublish());
            } elseif($params['status'] === 'unpublish') {
                $conditions['NOT'] = $this->Contents->getConditionAllowPublish();
            }
        }

        // folder_id
        if (!is_null($params['folder_id']) && $params['folder_id']) {
            $folder = $this->Contents->find()->select(['lft', 'rght'])->where(['id' => $queryParams['folder_id']])->first();
            $conditions[] = ['Contents.rght <' => $folder->rght, 'Contents.lft >' => $folder->lft];
        }

        return $query->where($conditions);
    }

    /**
     * コンテンツ管理の一覧用のデータを取得
     *
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = [], ?string $type = "all"): Query
    {
        $queryParams = array_merge([
            'contain' => ['Sites'],
        ], $queryParams);

        if (is_null($queryParams['contain']))
            $queryParams['contain'] = [];

        $query = $this->Contents->find($type)->contain($queryParams['contain']);

        if (!empty($queryParams['withTrash'])) {
            $query = $query->applyOptions(['withDeleted']);
        }

        if (!empty($queryParams['limit'])) {
            $query = $query->limit($queryParams['limit']);
        }

        unset($queryParams['limit'], $queryParams['withTrash'], $queryParams['contain']);
        return $this->setConditions($query, $queryParams);
    }

    /**
     * テーブル用のコンテンツ管理の一覧データを取得
     *
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableIndex(array $queryParams): Query
    {
        return $this->getIndex($queryParams);
    }

    /**
     * getTrashIndex
     *
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrashIndex(array $queryParams = [], string $type = "all"): Query
    {
        $queryParams = array_merge($queryParams, ['withTrash' => true]);
        return $this->getIndex($queryParams, $type)->where(['deleted_date IS NOT NULL']);
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     *
     * @param int $siteId
     * @param array $options
     * @return array|bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getContentFolderList($siteId = null, $options = [])
    {
        $options = array_merge([
            'excludeId' => null
        ], $options);

        $conditions = [
            'type' => 'ContentFolder',
            'alias_id IS NULL'
        ];

        if (!is_null($siteId)) {
            $conditions['site_id'] = $siteId;
        }
        if ($options['excludeId']) {
            $conditions['id <>'] = $options['excludeId'];
        }
        if (!empty($options['conditions'])) {
            $conditions = array_merge($conditions, $options['conditions']);
        }
        $folders = $this->Contents->find('treeList', valuePath: 'title')->where([$conditions]);
        if ($folders) {
            return $this->convertTreeList($folders->all()->toArray());
        }
        return false;
    }

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     *
     * @param array $nodes
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function convertTreeList($nodes)
    {
        if (!$nodes) {
            return [];
        }
        foreach($nodes as $key => $value) {
            if (preg_match("/^([_]+)/i", $value, $matches)) {
                $value = preg_replace("/^[_]+/i", '', $value);
                $prefix = str_replace('_', '　　　', $matches[1]);
                $value = $prefix . '└' . $value;
            }
            $nodes[$key] = $value;
        }
        return $nodes;
    }

    /**
     * aliasを作成する
     *
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function alias(array $postData)
    {
        if (empty($postData['alias_id'])) throw new \Exception(__d('baser_core', 'エイリアスIDを指定してください。'));
        if (empty($postData['parent_id']) && !empty($postData['url'])) {
            $postData['parent_id'] = $this->Contents->copyContentFolderPath($postData['url'], $postData['site_id']);
        }
        $data = array_merge($this->get($postData['alias_id'], ['contain' => []])->toArray(), $postData);
        unset(
            $data['id'],
            $data['lft'],
            $data['rght'],
            $data['level'],
            $data['pubish_begin'],
            $data['publish_end'],
            $data['created_date'],
            $data['created'],
            $data['modified'],
            $data['site'],
            $data['site_root']
        );
        $alias = $this->Contents->newEntity($data);
        $alias->name = $postData['name'] ?? $postData['title'];
        $alias->created_date = \Cake\I18n\DateTime::now();
        $alias->author_id = BcUtil::loginUser()->id ?? null;
        return $this->Contents->saveOrFail($alias);
    }

    /**
     * コンテンツ情報を論理削除する
     *
     * ※ エイリアスの場合は直接削除
     * 削除前に検索インデックスを削除するが、削除前でないと、ContentFolder の子の取得ができないため。
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id): bool
    {
        /* @var Content $content */
        $content = $this->get($id);
        $this->Contents->disableUpdatingSystemData();
        $this->Contents->updatingRelated = false;

        if(!$content->alias_id) {
            $this->deleteSearchIndex($id);
            $this->Contents->deleteRelateSubSiteContent($content);
            $this->Contents->deleteAlias($content);
            // 最上位に移動して保存
            $content->parent_id = null;
            $content->url = '';
            $content->status = false;
            $content->self_status = false;
            // lft rght は unset しないと自動更新できない
            unset($content->lft);
            unset($content->rght);
            $this->Contents->save($content, ['validate' => false]);

            // TreeBehavior　をオフにした上で、一旦階層構造から除外したい上でゴミ箱に移動（論理削除）
            $this->Contents->Behaviors()->unload('Tree');
            $result = $this->Contents->delete($content);
            $this->Contents->Behaviors()->load('Tree');
        } else {
            // エイリアスの場合直接削除
            $result = $this->Contents->hardDelete($content);
        }

        $this->Contents->enableUpdatingSystemData();
        $this->Contents->updatingRelated = true;
        return $result;
    }

    /**
     * コンテンツ情報を削除する
     *
     * @param int $id
     * @param bool $enableTree (デフォルト:false) TreeBehaviorの有無
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDelete($id, $enableTree = false): bool
    {
        $content = $this->Contents->find()->where(['id' => $id])->first();
        if ($content && empty($content->deleted_date)) {
            $this->delete($content->id);
        }
        $content = $this->getTrash($id);
        // 2022/10/20 ryuring
        // 原因不明の下記のエラーが出てしまったが、sleep() を実行する事で回避できた。根本的な解決に至らず
        // デバッガで１行ずつステップ実行すると成功したため sleep() で回避できることに気づいた
        // Cannot commit transaction - rollback() has been already called in the nested transaction
        sleep(1);
        $this->Contents->Behaviors()->unload('Tree');
        $result = $this->Contents->hardDelete($content);
        $this->Contents->Behaviors()->load('Tree');
        return $result;
    }

    /**
     * コンテンツ情報と紐付いてるモデルを物理削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDeleteWithAssoc($id): bool
    {
        /* @var Content $content */
        $content = $this->getTrash($id);
        $service = $content->plugin . '\\Service\\' . Inflector::pluralize($content->type) . 'ServiceInterface';
        $table = $content->plugin . '\\Model\\Table\\' . Inflector::pluralize($content->type) . 'Table';
        $isPluginEnabled = Plugin::isLoaded($content->plugin);
        if ($isPluginEnabled && interface_exists($service) && method_exists($service, 'delete')) {
            $target = $this->getService($service);
            return $target->delete($content->entity_id);
        } elseif ($isPluginEnabled && class_exists($table)) {
            $tableName = Inflector::pluralize($content->type);
            $target = TableRegistry::getTableLocator()->get($content->plugin . '.' . $tableName);
            $entity = $target->find()->where([$tableName . '.id' => $content->entity_id])->first();
            return $target->delete($entity);
        } else {
            return $this->hardDelete($id);
        }
    }

    /**
     * 該当するコンテンツ情報をすべて論理削除する
     *
     * @param array $conditions
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteAll(array $conditions = []): int
    {
        $conditions = array_merge(['deleted_date IS NULL'], $conditions);
        return $this->Contents->deleteAll($conditions);
    }

    /**
     * 指定日時以前の該当する論理削除されたコンテンツ情報をすべて物理削除する
     *
     * @param \Datetime $dateTime
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hardDeleteAll(\Datetime $dateTime): int
    {
        return $this->Contents->hardDeleteAll($dateTime);
    }

    /**
     * 論理削除されたコンテンツを復元する
     *
     * @param int $id
     * @return EntityInterface|array|null $trash
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restore($id)
    {
        $trash = $this->getTrash($id);

        // ゴミ箱に入っているデータは、lft / rght が null のため、
        // TreeBehavior が有効だと エラーとので一旦無効化したうえでレストア
        $this->Contents->Behaviors()->unload('Tree');
        $this->Contents->disableUpdatingSystemData();
        $this->Contents->updatingRelated = false;
        $content = $this->Contents->restore($trash)? $trash : null;
        $this->Contents->updatingRelated = true;
        $this->Contents->enableUpdatingSystemData();
        $this->Contents->Behaviors()->load('Tree', ['level' => 'level']);

        if ($content) {
            // lft / rght が null の場合、新規登録の場合でないと正常な値が割り振られないため、
            // 重複しない値を割り振ってから、parent_id を元に、level / left / rght をTreeBehavior に更新してもらう。
            $max = $this->Contents->getMax('rght');
            $siteRoot = $this->getSiteRoot($content->site_id);
            $result = $this->update($content, [
                'id' => $content->id,
                'name' => $content->name,
                'level' => null,
                'parent_id' => $siteRoot->id,
                'lft' => $max + 1,
                'rght' => $max + 2
            ]);
            $this->saveSearchIndex($id);
            return $result;
        } else {
            return null;
        }
    }

    /**
     * ゴミ箱内のコンテンツをすべて元に戻す
     *
     * @param array $queryParams
     * @return int $count
     * @checked
     * @noTodo
     * @unitTest
     */
    public function restoreAll(array $queryParams = []): int
    {
        $count = 0;
        $trash = $this->getTrashIndex($queryParams);
        foreach($trash as $entity) {
            if ($this->Contents->restore($entity)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * コンテンツ情報を取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentsInfo()
    {
        $sites = $this->Sites->getPublishedAll();
        $contentsInfo = [];
        foreach($sites as $key => $site) {
            $contentsInfo[$key]['published'] = $this->Contents->find()
                ->where(['site_id' => $site->id, 'status' => true])
                ->count();
            $contentsInfo[$key]['unpublished'] = $this->Contents->find()
                ->where(['site_id' => $site->id, 'status' => false])
                ->count();
            $contentsInfo[$key]['total'] = $contentsInfo[$key]['published'] + $contentsInfo[$key]['unpublished'];
            $contentsInfo[$key]['display_name'] = $site->display_name;
        }
        return $contentsInfo;
    }

    /**
     * ツリー構造より論理削除する
     *
     * @param $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function softDeleteFromTree($id)
    {
        // TODO ucmitz キャッシュ系が有効化されてからsoftDeleteFromTreeを使用する
        $this->softDelete(true);
        $this->Behaviors->unload('BcCache');
        $this->Behaviors->unload('BcUpload');
        $result = $this->deleteRecursive($id);
        $this->Behaviors->load('BcCache');
        $this->Behaviors->load('BcUpload');
        $this->delAssockCache();
        return $result;
    }

    /**
     * 再帰的に論理削除
     * ※ エイリアスの場合は直接削除
     *
     * @param int $id
     * @return void
     * @return bool $result
     * @checked
     * @noTodo
     * @unitTest
     * @throws Exception
     */
    public function deleteRecursive($id): bool
    {
        if (!$id) {
            throw new Exception('idが指定されてません');
        }
        $parent = $this->get($id);

        if ($children = $this->getChildren($id)) {
            // 親から消していくとTreeBehaviorにより削除重複が起きるため、子要素から削除する
            $target = array_reverse(array_merge([$parent], $children->toArray()));
        } else {
            $target = [$parent];
        }

        foreach($target as $node) {
            $result = $this->delete($node->id);
        }
        return $result;
    }

    /**
     * 直属の親フォルダのレイアウトテンプレートを取得する
     *
     * @param int $id
     * @param int|null $parentId
     * @return string $parentTemplate|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getParentLayoutTemplate(?int $id, int $parentId = null)
    {
        if (!$id) {
            if($parentId) {
                $id = $parentId;
            } else {
                return false;
            }
        }
        $contents = $this->Contents->find('path', for: $id)->all()->toArray();
        $contents = array_reverse($contents);
        unset($contents[0]);
        if (!$contents) {
            return false;
        }
        $parentTemplates = Hash::extract($contents, '{n}.layout_template');
        foreach($parentTemplates as $parentTemplate) {
            if ($parentTemplate) {
                break;
            }
        }
        return $parentTemplate;
    }

    /**
     * コンテンツIDよりURLを取得する
     *
     * @param int $id
     * @return string URL
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getUrlById($id, $full = false)
    {
        if (!is_numeric($id)) return '';
        try {
            $data = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }
        return $data? $this->getUrl($data->url, $full, $data->site->use_subdomain) : "";
    }

    /**
     * コンテンツ管理上のURLを元に正式なURLを取得する
     *
     * ドメインからのフルパスでない場合、デフォルトでは、
     * サブフォルダ設置時等の baseUrl（サブフォルダまでのパス）は含まない
     *
     * @param string $url コンテンツ管理上のURL
     * @param bool $full http からのフルのURLかどうか
     * @param bool $useSubDomain サブドメインを利用しているかどうか
     * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
     * @return string URL
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getUrl($url, $full = false, $useSubDomain = false, $base = false)
    {
        if(preg_match('/^http/', $url)) $full = false;
        if ($useSubDomain && !is_array($url)) {
            $subDomain = '';
            $site = $this->Sites->findByUrl($url);
            $originUrl = $url;
            if ($site) {
                $subDomain = $site->alias;
                $originUrl = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/', $url);
            }
            if ($full) {
                if ($site) {
                    $fullUrl = BcUtil::topLevelUrl(false) . $originUrl;
                    if ($site->domain_type == 1) {
                        $mainDomain = BcUtil::getMainDomain();
                        $fullUrlArray = explode('//', $fullUrl);
                        $fullPassArray = explode('/', $fullUrlArray[1]);
                        unset($fullPassArray[0]);
                        $url = $fullUrlArray[0] . '//' . $subDomain . '.' . $mainDomain . '/' . implode('/', $fullPassArray);
                    } elseif ($site->domain_type == 2) {
                        $fullUrlArray = explode('//', $fullUrl);
                        $urlArray = explode('/', $fullUrlArray[1]);
                        unset($urlArray[0]);
                        if ($site->same_main_url) {
                            $mainSite = $this->Sites->findById($site->main_site_id)->first();
                            $subDomain = $mainSite->alias;
                        }
                        $url = $fullUrlArray[0] . '//' . $subDomain . '/' . implode('/', $urlArray);
                    }
                } else {
                    $url = preg_replace('/\/$/', '', Configure::read('BcEnv.siteUrl')) . $originUrl;
                }
            } else {
                $url = $originUrl;
            }
        } else {
            if (BcUtil::isInstalled()) {
                if (!is_array($url)) {
                    $site = $this->Sites->findByUrl($url);
                    if ($site && $site->same_main_url) {
                        $mainSite = $this->Sites->findById($site->main_site_id)->first();
                        $alias = $mainSite->alias;
                        if ($alias) {
                            $alias = '/' . $alias;
                        }
                        $url = $alias . $site->getPureUrl($url);
                    }
                }
            }
            if ($full) {
                $mainDomain = BcUtil::getMainDomain();
                $fullUrlArray = explode('//', Configure::read('BcEnv.siteUrl'));
                $siteDomain = preg_replace('/\/$/', '', $fullUrlArray[1]);
                if (preg_match('/^www\./', $siteDomain) && str_replace('www.', '', $siteDomain) === $mainDomain) {
                    $mainDomain = $siteDomain;
                }
                $url = $fullUrlArray[0] . '//' . $mainDomain . Router::url($url);
            }
        }
        $url = preg_replace('/\/index$/', '/', $url);
        if (!$full && $base) {
            $url = Router::url($url);
        }
        return $url;
    }

    /**
     * コンテンツ情報を更新する
     *
     * @param EntityInterface $target
     * @param array $postData
     * @param array $options
     * @return EntityInterface|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function update(EntityInterface $target, array $postData, array $options = []): ?EntityInterface
    {
        $content = $this->Contents->patchEntity($target, $postData, $options);
        return $this->Contents->saveOrFail($content, ['atomic' => false]);
    }

    /**
     * コピーする
     *
     * @param $id
     * @param $newTitle
     * @param $newAuthorId
     * @param $entityId
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id, $entityId, $newTitle, $newAuthorId, $newSiteId = null)
    {
        $content = $this->get($id);
        $url = $content->url;
        if (!is_null($newSiteId) && $content->site_id != $newSiteId) {
            $content->site_id = $newSiteId;
            // $content->parent_id = $this->copyContentFolderPath($url, $newSiteId);
        }
        unset($content->id);
        unset($content->modified_date);
        unset($content->created);
        unset($content->modified);
        unset($content->main_site_content);
        if ($newTitle) {
            $content->title = $newTitle;
        } else {
            $content->title = sprintf(__d('baser_core', '%s のコピー'), $content->title);
        }
        $content->self_publish_begin = null;
        $content->self_publish_end = null;
        $content->self_status = false;
        $content->author_id = $newAuthorId;
        $content->created_date = date('Y-m-d H:i:s');
        $content->entity_id = $entityId;
        unset($data['Site']);
        $this->create($data);
        return $this->save($data);
    }

    /**
     * 公開状態にする
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish($id): EntityInterface
    {
        $content = $this->get($id);
        $content->self_publish_begin = null;
        $content->self_publish_end = null;
        $content->self_status = true;
        $result = $this->Contents->save($content);
        if ($result) $this->saveSearchIndex($id);
        return $result;
    }

    /**
     * 非公開状態にする
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish($id): EntityInterface
    {
        $content = $this->get($id);
        $content->self_publish_begin = null;
        $content->self_publish_end = null;
        $content->self_status = false;
        $result = $this->Contents->save($content);
        if ($result) $this->saveSearchIndex($id);
        return $result;
    }

    /**
     * exists
     *
     * @param int $id
     * @param bool $withTrash ゴミ箱の物も含めるか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function exists($id, $withTrash = false): bool
    {
        if ($withTrash) {
            $exists = !$this->getIndex(['id' => $id, 'withTrash' => true])->all()->isEmpty();
        } else {
            $exists = !$this->getIndex(['id' => $id])->all()->isEmpty();
        }
        return $exists;
    }

    /**
     * コンテンツを移動する
     *
     * 基本的に targetId の上に移動する前提となる
     * targetId が空の場合は、同親中、一番下に移動する
     *
     * @param array $origin
     * @param array $target
     * @return EntityInterface|bool|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move($origin, $target)
    {
        if (!$this->exists($origin['id'])) {
            throw new BcException(__d('baser_core', 'データが存在しません。'));
        } elseif (!$this->isMovable($origin['id'], $target['parentId'])) {
            throw new BcException(__d('baser_core', '同一URLのコンテンツが存在するため処理に失敗しました。（現在のサイトに存在しない場合は、関連サイトに存在します）'));
        }

        $this->moveRelateSubSiteContent($origin['id'], $target['parentId'], $target['id']);
        $targetSort = $this->Contents->getOrderSameParent($target['id'], $target['parentId']);
        if ($origin['parentId'] != $target['parentId']) {
            $content = $this->get($origin['id']);
            // 親を変更
            /* @var Content $content */
            $content = $this->update($content, [
                'id' => $origin['id'],
                'name' => $content->name,
                'title' => $content->title,
                'plugin' => $content->plugin,
                'type' => $content->type,
                'parent_id' => $target['parentId'],
                'site_id' => $target['siteId']
            ]);
            // フォルダにコンテンツがない場合、targetId が空で一番後を指定の場合は、親を変更して終了
            if (!$targetSort || !$target['id']) {
                return $content;
            }
            $currentSort = $this->Contents->getOrderSameParent(null, $target['parentId']);
        } else {
            $currentSort = $this->Contents->getOrderSameParent($origin['id'], $target['parentId']);
        }
        // 親変更後のオフセットを取得
        $offset = $targetSort - $currentSort;
        if ($origin['parentId'] == $target['parentId'] && $target['id'] && $offset > 0) {
            $offset--;
        }
        // オフセットを元に移動
        $result = $this->Contents->moveOffset($origin['id'], $offset);
        if ($result) $this->saveSearchIndex($origin['id']);
        return $result;
    }

    /**
     * 検索インデックスを生成する
     *
     * 対象が ContentFolder の場合は、子の検索インデックスも更新する
     * 子の検索インデックス更新時には、親の status を引き継ぐ
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveSearchIndex($id)
    {
        if (!Plugin::isLoaded('BcSearchIndex')) return;
        /* @var Content $currentContent */
        $currentContent = $this->get($id);
        $contents = [$currentContent];
        if ($currentContent->type === 'ContentFolder') {
            $contents = array_merge(
                $contents,
                $this->Contents->find('children', for: $currentContent->id)
                    ->select(['plugin', 'type', 'entity_id'])
                    ->orderBy('lft')
                    ->all()
                    ->toArray()
            );
        }
        $tables = [];
        $this->Contents->getConnection()->begin();
        foreach($contents as $content) {
            if (!isset($tables[$content->type])) {
                $tables[$content->type] = TableRegistry::getTableLocator()->get(
                    $content->plugin . '.' . Inflector::pluralize($content->type)
                );
            }
            if ($content->type === 'ContentFolder' || !$tables[$content->type]->hasBehavior('BcSearchIndexManager')) continue;
            $entity = $tables[$content->type]->get($content->entity_id, contain: 'Contents');
            $entity->setDirty('id', true);
            if ($currentContent->type === 'ContentFolder') {
                $entity->content->status = $currentContent->status;
            }
            if(!$tables[$content->type]->save($entity)) {
                $this->Contents->getConnection()->rollback();
            }
        }
        $this->Contents->getConnection()->commit();
    }

    /**
     * 検索インデックスを削除する
     *
     * 対象が ContentFolder の場合は、子の検索インデックスも削除する
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteSearchIndex($id)
    {
        if (!Plugin::isLoaded('BcSearchIndex')) return;
        /* @var Content $currentContent */
        $currentContent = $this->Contents->get($id);
        $contents = [$currentContent];
        if ($currentContent->type === 'ContentFolder' && $this->Contents->hasBehavior('Tree')) {
            $contents = array_merge(
                $contents,
                $this->Contents->find('children', for: $currentContent->id)
                    ->select(['plugin', 'type', 'entity_id'])
                    ->orderBy('lft')
                    ->all()
                    ->toArray()
            );
        }
        $tables = [];
        $this->Contents->getConnection()->begin();
        foreach($contents as $content) {
            if (!isset($tables[$content->type])) {
                $tables[$content->type] = TableRegistry::getTableLocator()->get(
                    $content->plugin . '.' . Inflector::pluralize($content->type)
                );
            }
            if ($content->type === 'ContentFolder' || !$tables[$content->type]->hasBehavior('BcSearchIndexManager')) continue;
            if(!$tables[$content->type]->deleteSearchIndex($content->entity_id)) {
                $this->Contents->getConnection()->rollback();
                return;
            }
        }
        $this->Contents->getConnection()->commit();
    }

    /**
     * メインサイトの場合、連携設定がされている子サイトも移動する
     *
     * @param $data
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function moveRelateSubSiteContent($mainCurrentId, $mainTargetParentId, $mainTargetId)
    {
        $data = $this->get($mainCurrentId);
        // 自身がエイリアスか確認し、エイリアスの場合は終了
        if (!empty($data->alias_id) || !isset($data->site_id) || !isset($data->type)) {
            return true;
        }
        // メインサイトか確認し、メインサイトでない場合は終了
        if (!$this->Sites->isMain($data->site_id)) {
            return true;
        }
        // 連携設定となっている小サイトを取得
        $sites = $this->Sites->find()->where(['main_site_id' => $data->site_id, 'relate_main_site' => true]);
        if ($sites->all()->isEmpty()) {
            return true;
        }
        $result = true;
        foreach($sites as $site) {
            // 自信をメインコンテンツとしているデータを取得
            // currentの設定
            try {
                /* @var Content $currentEntity */
                $currentEntity = $this->Contents->find()->where(['main_site_content_id' => $mainCurrentId, 'site_id' => $site->id])->firstOrFail();
                $current['id'] = $currentEntity->id;
                $current['parentId'] = $currentEntity->parent_id;
            } catch (\Exception $e) {
                continue;
            }
            // targetの設定
            if (!empty($targetEntity)) {
                unset($targetEntity);
                unset($target);
            }
            try {
                if ($mainTargetId) {
                    /* @var Content $targetEntity */
                    $targetEntity = $this->Contents->find()->where(['main_site_content_id' => $mainTargetId, 'site_id' => $site->id])->first();
                    if ($targetEntity) {
                        $target['id'] = $targetEntity->id;
                        $target['parentId'] = $targetEntity->parent_id;
                        $target['siteId'] = $targetEntity->site_id;
                    } else {
                        // ターゲットが見つからない場合は親IDより取得
                        $targetEntity = $this->Contents->find()->where(['main_site_content_id' => $mainTargetParentId, 'site_id' => $site->id])->firstOrFail();
                        if ($targetEntity) {
                            $target['id'] = $targetEntity->id;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
            if (!$this->move($current, $target)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * 移動元のコンテンツと移動先のディレクトリから移動が可能かチェックする
     *
     * @param int $currentId int 移動元コンテンツID
     * @param int $targetParentId int 移動先コンテンツID (ContentFolder)
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isMovable($currentId, $targetParentId)
    {
        $currentContent = $this->get($currentId);
        if ($currentContent->parent_id === $targetParentId) {
            return true;
        }
        $parentCuntent = $this->get($targetParentId);

        // 指定コンテンツが存在しない
        if (!$currentContent || !$parentCuntent) {
            return false;
        }

        $parentId = $parentCuntent->id;

        // 関連コンテンツで移動先と同じ階層のフォルダを確認
        $childrenSite = $this->Sites->children($currentContent->site_id, [
            'conditions' => ['relate_main_site' => true]
        ]);
        if ($childrenSite) {
            $pureUrl = $this->Contents->pureUrl($parentCuntent->url, $parentCuntent->site_id);
            foreach($childrenSite as $site) {
                $site = $this->Sites->findById($site->id)->first();
                $url = $site->makeUrl(new ServerRequest(['url' => $pureUrl]));
                $id = $this->Contents->find()->select('id')->where(['url' => $url]);
                if ($id) {
                    $parentId = $id;
                }
            }
        }
        // 移動先に同一コンテンツが存在するか確認
        $movedContent = $this->Contents->find()
            ->where(['parent_id' => $parentId, 'name' => $currentContent->name, 'id !=' => $currentContent->id])
            ->first();
        if ($movedContent) {
            return false;
        }
        return true;
    }

    /**
     * ID を指定して公開状態かどうか判定する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isPublishById($id)
    {
        return $this->Contents->isPublishById($id);
    }

    /**
     * 公開状態を取得する
     *
     * @param Content $content コンテンツデータ
     * @return bool 公開状態
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAllowPublish($content, $self = false)
    {
        $fields = [
            'status' => 'status',
            'publish_begin' => 'publish_begin',
            'publish_end' => 'publish_end'
        ];
        if ($self) {
            foreach($fields as $key => $field) {
                $fields[$key] = 'self_' . $field;
            }
        }
        $allowPublish = $content[$fields['status']];
        // 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
        $invalidBegin = $content[$fields['publish_begin']] instanceof \Cake\I18n\DateTime && $content[$fields['publish_begin']]->isFuture();
        $invalidEnd = $content[$fields['publish_end']] instanceof \Cake\I18n\DateTime && $content[$fields['publish_end']]->isPast();
        if ($invalidBegin || $invalidEnd) {
            $allowPublish = false;
        }
        return $allowPublish;
    }

    /**
     * サイトルートコンテンツを取得する
     *
     * @param int $siteId
     * @return EntityInterface|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getSiteRoot($siteId)
    {
        return $this->Contents->find()->where(['site_id' => $siteId, 'site_root' => true])->first();
    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     *
     * @param $url
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function existsContentByUrl($url)
    {
        return (bool)$this->Contents->find()->where(['url' => $url])->count();
    }

    /**
     * タイトル、URL、公開状態が更新されているか確認する
     *
     * @param int $id コンテンツID
     * @param array $newData 新しいコンテンツデータ
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function isChangedStatus($id, $newData)
    {
        try {
            $before = $this->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return true;
        }
        $beforeStatus = $this->Contents->isPublish($before->self_status, $before->self_publish_begin, $before->self_publish_end);
        $afterStatus = $this->Contents->isPublish($newData['self_status'], $newData['self_publish_begin'], $newData['self_publish_end']);
        if ($beforeStatus != $afterStatus || $before->title != $newData['title'] || $before->url != $newData['url']) {
            return true;
        }
        return false;
    }

    /**
     * TreeBehaviorの設定値を更新する
     *
     * @param string $targetConfig
     * @param array $conditions
     * @return TreeBehavior
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setTreeConfig($targetConfig, $conditions)
    {
        return $this->Contents->behaviors()->Tree->setConfig($targetConfig, $conditions);
    }

    /**
     * 公開済の conditions を取得
     *
     * @return array 公開条件（conditions 形式）
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getConditionAllowPublish()
    {
        return $this->Contents->getConditionAllowPublish();
    }

    /**
     * 条件に基づいて指定したフィールドの隣のデータを所得する
     *
     * @param array $options
     * @return array $neighbors
     * @throws BcException site_idがない場合Exceptionを投げる
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNeighbors(array $options)
    {
        if (empty($options['conditions']) || !array_key_exists('Contents.site_id', $options['conditions'])) {
            throw new BcException(__d('baser_core', 'site_idを指定してください。'));
        };
        $fieldName = $options['field'];
        $previous = $this->Contents->find()
            ->contain('Sites')
            ->orderBy(['Contents.lft' => 'DESC'])
            ->where(['Contents.' . $fieldName . ' <' => $options['value']]);
        $next = $this->Contents->find()
            ->contain('Sites')
            ->orderBy(['Contents.lft' => 'ASC'])
            ->where(['Contents.' . $fieldName . ' >' => $options['value']]);
        if (isset($options['conditions'])) {
            $previous = $previous->where($options['conditions']);
            $next = $next->where($options['conditions']);
        }
        if (isset($options['order'])) {
            $previous = $previous->orderBy($options['order']);
            $next = $next->orderBy($options['order']);
        }
        return ['prev' => $previous->first(), 'next' => $next->first()];
    }

    /**
     * エンコードされたURLをデコードせずにパースする
     * ※DBのレコードがエンコードされたまま保存されてる場合があるためその値を取得する際にデコードが邪魔になる際使用する
     *
     * @param string $fullUrl
     * @return array $parsedUrl
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function encodeParsedUrl($fullUrl)
    {
        $parsedUrl = parse_url($fullUrl);
        $directory = explode('/', $parsedUrl['path']);
        if ($parsedUrl['host'] !== 'localhost') {
            $parsedUrl['subDomain'] = explode('.', $parsedUrl['host'])[0];
            array_splice($directory, 1, 0, [$parsedUrl['subDomain']]);
        }
        $parsedUrl['path'] = "";
        foreach($directory as $key => $dir) {
            // デコードされたpathをエンコード
            if ($key !== 0) $parsedUrl['path'] .= "/" . rawurlencode(rawurldecode($dir));
        }
        return $parsedUrl;
    }

    /**
     * ツリー構造のパスを取得する
     *
     * @param string $id
     * @return QueryInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPath($id): QueryInterface
    {
        return $this->Contents->find('path', for: $id);
    }

    /**
     * 一括処理
     *
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch($method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->Contents->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->{$method}($id)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定してタイトルリストを取得する
     *
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitlesById($ids): array
    {
        return $this->Contents->find('list')->select(['id', 'title'])->where(['id IN' => $ids])->toArray();
    }

    /**
     * リネーム処理
     *
     * @param EntityInterface $content
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rename($content, $postData)
    {
        if (empty($postData['id'])) {
            throw new BcException(__d('baser_core', '送信データが不正です。'));
        }
        $newContent = array_merge($content->toArray(), ['title' => $postData['title']]);
        unset($newContent['site']);
        $options = ['validate' => false];
        if (!empty($postData['first'])) {
            unset($newContent['name']);
            $options['firstCreate'] = true;
        }
        try {
            $result = $this->update($content, $newContent, $options);
            if ($result) $this->saveSearchIndex($content->id);
            return $result;
        } catch (BcException $e) {
            throw $e;
        }
    }

    /**
     * ServerRequest インスタンスに指定したコンテンツデータを現在のコンテンツとしてセットする
     *
     * @param string $type
     * @param int $entityId
     * @param ServerRequest $request
     * @return false|ServerRequest
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCurrentToRequest(string $type, int $entityId, ServerRequest $request)
    {
        $content = $this->Contents->findByType($type, $entityId);
        if (!$content) return false;
        return $request->withAttribute('currentContent', $content);
    }

    /**
     * 前のコンテンツを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPrev(int $id)
    {
        $current = $this->get($id);
        $query = $this->Contents->find()->orderBy(['Contents.lft DESC']);
        $query->where([
            'Contents.lft <' => $current->lft,
            'Contents.site_id' => $current->site_id,
            $this->Contents->getConditionAllowPublish()
        ]);
        if($current->level) {
            $query->where(['Contents.level' => $current->level]);
        } else {
            $query->where(['Contents.level IS' => null]);
        }
        return $query->first();
    }

    /**
     * 次のコンテンツを取得する
     *
     * @param int $id
     * @return array|EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNext(int $id)
    {
        $current = $this->get($id);
        $query = $this->Contents->find()->orderBy(['Contents.lft']);
        $query->where([
            'Contents.lft >' => $current->lft,
            'Contents.site_id' => $current->site_id,
            $this->Contents->getConditionAllowPublish()
        ]);
        if($current->level) {
            $query->where(['Contents.level' => $current->level]);
        } else {
            $query->where(['Contents.level IS' => null]);
        }
        return $query->first();
    }

    /**
     * グローバルナビ用のコンテンツ一覧を取得する
     *
     * @param int $id
     * @return \Cake\Datasource\ResultSetInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getGlobalNavi(int $id)
    {
        $current = $this->get($id);
        $root = $this->Contents->find()->where([
            'Contents.site_id' => $current->site_id,
            'Contents.site_root' => true,
            $this->Contents->getConditionAllowPublish()
        ])->first();
        if(!$root) return false;
        $query = $this->Contents->find('children', for: $root->id, direct: true);
        return $query->where([
            'Contents.exclude_menu' => false,
            $this->Contents->getConditionAllowPublish()
        ])->all();
    }

    /**
     * パンくず用のコンテンツ一覧を取得する
     *
     * @param int $id
     * @return \Cake\Datasource\ResultSetInterface
     * @checked
     * @noTodo
     */
    public function getCrumbs(int $id)
    {
        $query = $this->Contents->find('path', for: $id);
        return $query->where([
            'Contents.exclude_menu' => false,
            $this->Contents->getConditionAllowPublish()
        ])->all();
    }

    /**
     * ローカルナビ用のコンテンツ一覧を取得する
     *
     * @param int $id
     * @return \Cake\Datasource\ResultSetInterface|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLocalNavi(int $id)
    {
        $parent = $this->getParent($id);
        if (!$parent) return;
        $query = $this->Contents->find('children', for: $parent->id, direct: true);
        return $query->where([
            'Contents.exclude_menu' => false,
            $this->Contents->getConditionAllowPublish()
        ])->all();
    }

    /**
     * 親コンテンツを取得する
     *
     * @param int $id
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getParent(int $id)
    {
        $current = $this->get($id, ['status' => 'publish']);
        if(!$current->parent_id) {
            return false;
        }
        return $this->get($current->parent_id);
    }

}
