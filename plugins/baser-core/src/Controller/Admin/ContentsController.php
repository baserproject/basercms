<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\ORM\ResultSet;
use Cake\Core\Configure;
use Nette\Utils\DateTime;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\SiteConfigTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Service\SiteServiceInterface;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Service\BcAdminServiceInterface;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Controller\Component\BcContentsComponent;

/**
 * Class ContentsController
 *
 * 統合コンテンツ管理 コントローラー
 *
 * baserCMS内のコンテンツを統合的に管理する
 *
 * @package BaserCore.Controller
 * @property ContentsTable $Contents
 * @property BcAuthComponent $BcAuth
 * @property SiteConfigsTable $SiteConfigs
 * @property SitesTable $Sites
 * @property UsersTable $Users
 * @property ContentFoldersTable $ContentFolders
 * @property BcContentsComponent $BcContents
 * @property BcMessageComponent $BcMessage
 */

class ContentsController extends BcAdminAppController
{
    /**
     * SiteConfigTrait
     */
    use SiteConfigTrait;

    /**
     * initialize
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcContents');
    }

    /**
     * beforeFilter
     *
     * @return void
     * @checked
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadModel('BaserCore.Sites');
        $this->loadModel('BaserCore.SiteConfigs');
        $this->loadModel('BaserCore.ContentFolders');
        $this->loadModel('BaserCore.Users');
        $this->loadModel('BaserCore.Contents');
        $this->Security->setConfig('unlockedActions', ['delete', 'trash_empty']);
        // TODO 未実装のためコメントアウト
        /* >>>
        // $this->BcAuth->allow('view');
        <<< */
    }

    /**
     * コンテンツ一覧
     * @param integer $parentId
     * @param void
     * @checked
     * @unitTest
     */
    public function index(ContentServiceInterface $contentService, SiteServiceInterface $siteService)
    {
        $currentSiteId = $this->request->getAttribute('currentSite')->id;
        $sites = $siteService->getList();
        if ($sites) {
            if (!$this->request->getQuery('site_id') || !in_array($this->request->getQuery('site_id'), array_keys($sites))) {
                reset($sites);
                $this->request = $this->request->withQueryParams(Hash::merge($this->request->getQueryParams(), ['site_id' =>key($sites)]));
            }
        } else {
            $this->request = $this->request->withQueryParams(Hash::merge($this->request->getQueryParams(),  ['site_id' => null]));
        }
        $currentListType = $this->request->getQuery('list_type') ?? 1;

        $this->setViewConditions('Contents', ['default' => [
            'query' => [
                'num' => $siteService->getSiteConfig('admin_list_num'),
                'site_id' => $currentSiteId,
                'list_type' => $currentListType,
                'sort' => 'id',
                'direction' => 'asc',
            ]
        ]]);
        if($this->request->getParam('action') == "index") {
            switch($this->request->getQuery('list_type')) {
                case 1:
                    // TODO: 未実装
                    // 並び替え最終更新時刻をリセット
                    // $this->SiteConfigs->resetContentsSortLastModified();
                    break;
                case 2:
                    $this->request = $this->request->withQueryParams(
                        Hash::merge(
                            $this->request->getQueryParams(),
                            $contentService->getTableConditions($this->request->getQueryParams())
                        ));
                    // EVENT Contents.searchIndex
                    $event = $this->dispatchLayerEvent('searchIndex', [
                        'request' => $this->request
                    ]);
                    if ($event !== false) {
                        $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
                    }
                    break;
            }
        }
        $this->ContentFolders->getEventManager()->on($this->ContentFolders);
        $this->set('contents', $this->_getContents($contentService));
        $this->set('template', $this->_getTemplate());
        $this->set('folders', $contentService->getContentFolderList($currentSiteId, ['conditions' => ['site_root' => false]]));
        $this->set('sites', $sites);
    }

    /**
     * リクエストに応じたコンテンツを返す
     *
     * @param  ContentServiceInterface $contentService
     * @return Query|ResultSet
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getContents($contentService)
    {
        switch($this->request->getParam('action')) {
            case 'index':
                switch($this->request->getQuery('list_type')) {
                    case 1:
                        return $contentService->getTreeIndex($this->request->getQueryParams());
                    case 2:
                        return $this->paginate($contentService->getTableIndex($this->request->getQueryParams()));
                    default:
                        return $contentService->getEmptyIndex();
                }
            case 'trash_index':
                return $contentService->getTrashIndex($this->request->getQueryParams(), 'threaded')->order(['site_id', 'lft']);
            default:
                return $contentService->getEmptyIndex();
        }
    }

    /**
     * リクエストに応じたテンプレートを取得する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getTemplate(): string
    {
        switch($this->request->getParam('action')) {
            case 'index':
                switch($this->request->getQuery('list_type')) {
                    case 1:
                        return 'index_tree';
                    case 2:
                        return 'index_table';
                    default:
                        $this->BcMessage->setError(__d('baser', '指定されたテンプレートは存在しません'));
                        return 'index_tree';
                }
            case 'trash_index':
                return 'index_trash';
            default:
                $this->BcMessage->setError(__d('baser', '指定されたテンプレートは存在しません'));
                return 'index_tree';
        }
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_index(ContentServiceInterface $contentService, SiteServiceInterface $siteService)
    {
        $this->setAction('index', $contentService, $siteService);
        $this->render('index');
    }

    /**
     * ゴミ箱のコンテンツを戻す
     *
     * @return mixed Site Id Or false
     */
    public function admin_ajax_trash_return()
    {
        if (empty($this->request->getData('id'))) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $this->autoRender = false;

        // EVENT Contents.beforeTrashReturn
        $this->dispatchLayerEvent('beforeTrashReturn', [
            'data' => $this->request->getData('id')
        ]);

        $siteId = $this->Content->trashReturn($this->request->getData('id'));

        // EVENT Contents.afterTrashReturn
        $this->dispatchLayerEvent('afterTrashReturn', [
            'data' => $this->request->getData('id')
        ]);

        return $siteId;
    }

    /**
     * 新規コンテンツ登録（AJAX）
     *
     * @return void
     */
    public function add(ContentServiceInterface $contentService, $alias = false)
    {

        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        $srcContent = [];
        if ($alias) {
            if ($this->request->getData('Content.alias_id')) {
                $conditions = ['id' => $this->request->getData('Content.alias_id')];
            } else {
                $conditions = [
                    'plugin' => $this->request->getData('Content.plugin'),
                    'type' => $this->request->getData('Content.type')
                ];
            }
            $srcContent = $this->Content->find('first', ['conditions' => $conditions, 'recursive' => -1]);
            if ($srcContent) {
                $this->request = $this->request->withData('Content.alias_id', $srcContent['Content']['id']);
                $srcContent = $srcContent['Content'];
            }

            if (empty($this->request->getData('Content.parent_id')) && !empty($this->request->getData('Content.url'))) {
                $this->request = $this->request->withData('Content.parent_id', $this->Content->copyContentFolderPath($this->request->getData('Content.url'), $this->request->getData('Content.site_id')));
            }

        }

        $user = $currentUser = BcUtil::loginUser('Admin');
        $this->request = $this->request->withData('author_id', $user->id);
        $contentService->create($this->request->getData());
        $this->Content->create(false);
        $data = $this->Content->save($this->request->data);
        if (!$data) {
            $this->ajaxError(500, __d('baser', '保存中にエラーが発生しました。'));
            exit;
        }

        if ($alias) {
            $message = Configure::read('BcContents.items.' . $this->request->getData('Content.plugin') . '.' . $this->request->getData('Content.type') . '.title') .
                sprintf(__d('baser', '「%s」のエイリアス「%s」を追加しました。'), $srcContent['title'], $this->request->getData('Content.title'));
        } else {
            $message = Configure::read('BcContents.items.' . $this->request->getData('Content.plugin') . '.' . $this->request->getData('Content.type') . '.title') . '「' . $this->request->getData('Content.title') . '」を追加しました。';
        }
        $this->BcMessage->setSuccess($message, true, false);
        exit(json_encode($data['Content']));
    }

    /**
     * コンテンツ編集
     *
     * @return void
     */
    public function admin_edit()
    {
        $this->setTitle(__d('baser', 'コンテンツ編集'));
        if (!$this->request->data) {
            $this->request->data = $this->Content->find('first', ['conditions' => ['Content.id' => $this->request->params['named']['content_id']]]);
            if (!$this->request->data) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
            }
        } else {
            if ($this->Content->save($this->request->data)) {
                $message = Configure::read('BcContents.items.' . $this->request->getData('Content.plugin') . '.' . $this->request->getData('Content.type') . '.title') .
                    sprintf(__d('baser', '「%s」を更新しました。'), $this->request->getData('Content.title'));
                $this->BcMessage->setSuccess($message);
                $this->redirect([
                    'plugin' => null,
                    'controller' => 'contents',
                    'action' => 'edit',
                    'content_id' => $this->request->params['named']['content_id'],
                    'parent_id' => $this->request->params['named']['parent_id']
                ]);
            } else {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        }
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findById($this->request->getData('Content.site_id'))->first();
        $this->set('publishLink', $this->Content->getUrl($this->request->getData('Content.url'), true, $site->useSubDomain));
    }

    /**
     * エイリアスを編集する
     *
     * @param $id
     * @throws Exception
     */
    public function admin_edit_alias($id)
    {

        $this->setTitle(__d('baser', 'エイリアス編集'));
        if ($this->request->is(['post', 'put'])) {
            if ($this->Content->isOverPostSize()) {
                $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit_alias', $id]);
            }
            if ($this->Content->save($this->request->data)) {
                $srcContent = $this->Content->find('first', ['conditions' => ['Content.id' => $this->request->getData('Content.alias_id')], 'recursive' => -1]);
                $srcContent = $srcContent['Content'];
                $message = Configure::read('BcContents.items.' . $srcContent['plugin'] . '.' . $srcContent['type'] . '.title') .
                    sprintf(__d('baser', '「%s」のエイリアス「%s」を編集しました。'), $srcContent['title'], $this->request->getData('Content.title'));
                $this->BcMessage->setSuccess($message);
                $this->redirect([
                    'plugin' => null,
                    'controller' => 'contents',
                    'action' => 'edit_alias',
                    $id
                ]);
            } else {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        } else {
            $this->request->data = $this->Content->find('first', ['conditions' => ['Content.id' => $id]]);
            if (!$this->request->data) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
            }
            $srcContent = $this->Content->find('first', ['conditions' => ['Content.id' => $this->request->getData('Content.alias_id')], 'recursive' => -1]);
            $srcContent = $srcContent['Content'];
        }

        $this->set('srcContent', $srcContent);
        $this->BcContents->settingForm($this, $this->request->getData('Content.site_id'), $this->request->getData('Content.id'));
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findById($this->request->getData('Content.site_id'))->first();
        $this->set('publishLink', $this->Content->getUrl($this->request->getData('Content.url'), true, $site->useSubDomain));

    }

    /**
     * コンテンツ削除（論理削除）
     *  @param  ContentServiceInterface $contentService
     * @return Response|null
     * @checked
     * @unitTest
     */
    public function delete(ContentServiceInterface $contentService)
    {
        $this->disableAutoRender();
        // コンテンツIDチェック
        if($this->request->is('ajax')) {
            $useFlashMessage = false;
            if (empty($id = $this->request->getData('contentId'))) {
                $this->ajaxError(500, __d('baser', '無効な処理です。'));
            }
        } else {
            $useFlashMessage = true;
            if (empty($id = $this->request->getData('Content.id'))) {
                $this->notFound();
            }
        }
        // TODO:
        // EVENT Contents.beforeDelete
        // $this->dispatchLayerEvent('beforeDelete', [
        //     'data' => $id
        // ]);
        try {
            $content = $contentService->get($id);
            $typeName = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type . '.title');
            $result = $contentService->treeDelete($id);
        } catch (\Exception $e) {
            $result = false;
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        // TODO:
        // EVENT Contents.afterDelete
        // $this->dispatchLayerEvent('afterDelete', [
        //     'data' => $id
        // ]);
        if ($result) {
            $trashMessage = $typeName . sprintf(__d('baser', '「%s」をゴミ箱に移動しました。'), $content->title);
            $aliasMessage = sprintf(__d('baser', '%s のエイリアス「%s」を削除しました。'), $typeName, $content->title);
            $this->BcMessage->setSuccess($content->alias_id ? $aliasMessage : $trashMessage, true, $useFlashMessage);
        } else {
            if($this->request->is('ajax')) {
                $this->ajaxError(500, __d('baser', '削除中にエラーが発生しました。'));
            } else {
                $this->BcMessage->setError('削除中にエラーが発生しました。');
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * 一括削除
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_del(ContentServiceInterface $contentService, $ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                // FIXME: _deleteを消したので、修正する
                $this->_delete($contentService, $id, false);
            }
        }
        return true;
    }

    /**
     * 一括公開
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_publish($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_changeStatus($id, true);
            }
        }
        return true;
    }

    /**
     * 一括非公開
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_unpublish($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_changeStatus($id, false);
            }
        }
        return true;
    }

    /**
     * 公開状態を変更する
     *
     * @return bool
     */
    public function admin_ajax_change_status()
    {
        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        switch($this->request->getData('status')) {
            case 'publish':
                $result = $this->_changeStatus($this->request->getData('contentId'), true);
                break;
            case 'unpublish':
                $result = $this->_changeStatus($this->request->getData('contentId'), false);
                break;
        }
        return $result;
    }

    /**
     * 公開状態を変更する
     *
     * @param int $id
     * @param bool $status
     * @return bool|mixed
     */
    protected function _changeStatus($id, $status)
    {
        // EVENT Contents.beforeChangeStatus
        $this->dispatchLayerEvent('beforeChangeStatus', ['id' => $id, 'status' => $status]);

        $content = $this->Content->find('first', ['conditions' => ['Content.id' => $id], 'recursive' => -1]);
        if (!$content) {
            return false;
        }
        unset($content['Content']['lft']);
        unset($content['Content']['rght']);
        $content['Content']['self_publish_begin'] = '';
        $content['Content']['self_publish_end'] = '';
        $content['Content']['self_status'] = $status;
        $result = (bool)$this->Content->save($content, false);

        // EVENT Contents.afterChangeStatus
        $this->dispatchLayerEvent('afterChangeStatus', ['id' => $id, 'result' => $result]);

        return $result;
    }

    /**
     * ゴミ箱を空にする
     * @param ContentServiceInterface $contentService
     * @return Response|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_empty(ContentServiceInterface $contentService)
    {
        $this->disableAutoRender();
        if (!$this->request->getData()) {
            $this->notFound();
        }
        $contents = $contentService->getTrashIndex()->order(['plugin', 'type']);

        // EVENT Contents.beforeTrashEmpty
        $this->dispatchLayerEvent('beforeTrashEmpty', [
            'data' => $contents
        ]);
        if ($contents) {
            $result = $this->_deleteTrash($contentService, $contents);
        }
        // EVENT Contents.afterTrashEmpty
        $this->dispatchLayerEvent('afterTrashEmpty', [
            'data' => $result
        ]);
        return $this->redirect(['action' => "trash_index"]);
    }

    /**
     * ゴミ箱を物理削除する
     *
     * @param  Query $contents
     * @param  ContentService $contentService
     * @return bool $result
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _deleteTrash($contentService, $contents)
    {
        if(!empty($contents) && !empty($contentService)) {
            foreach($contents as $content) {
                $pluginName = $content->plugin;
                $modelName = $content->type;
                $service = $pluginName . '\\Service\\' . $modelName . 'ServiceInterface';
                if(interface_exists($service)) {
                    $target = $this->getService($service);
                } else {
                    $target = $this->getTableLocator()->get($pluginName . Inflector::pluralize($modelName));
                }
                if($target) {
                    // Contents以外のモデルでの削除
                    $result = $target->delete($content->entity_id);
                }
            }
            // Contentsモデルでの全体削除
            if ($count = $contentService->hardDeleteAll(new DateTime('NOW'))) {
                $this->BcMessage->setSuccess($count . "個のコンテンツがゴミ箱から削除されました", true, false);
            }
            return $result;
        }
    }

    /**
     * コンテンツ表示
     *
     * @param $plugin
     * @param $type
     */
    public function view($plugin, $type)
    {
        $data = ['Content' => $this->request->getParam('Content')];
        if ($this->BcContents->preview && $this->request->data) {
            $data = $this->request->data;
        }
        $this->set('data', $data);
        if (!$data['Content']['alias_id']) {
            $this->set('editLink', ['admin' => true, 'plugin' => '', 'controller' => 'contents', 'action' => 'edit', 'content_id' => $data['Content']['id']]);
        } else {
            $this->set('editLink', ['admin' => true, 'plugin' => '', 'controller' => 'contents', 'action' => 'edit_alias', $data['Content']['id']]);
        }
    }

    /**
     * リネーム
     *
     * 新規登録時の初回リネーム時は、name にも保存する
     */
    public function admin_ajax_rename()
    {
        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $data = [
            'Content' => [
                'id' => $this->request->getData('id'),
                'title' => $this->request->getData('newTitle'),
                'parent_id' => $this->request->getData('parentId'),
                'type' => $this->request->getData('type'),
                'site_id' => $this->request->getData('siteId')
            ]
        ];
        if (!$this->Content->save($data, ['firstCreate' => !empty($this->request->getData('first'))])) {
            $this->ajaxError(500, __d('baser', '名称変更中にエラーが発生しました。'));
            return false;
        }

        $this->BcMessage->setSuccess(
            sprintf(
                '%s%s',
                Configure::read(
                    sprintf(
                        'BcContents.items.%s.%s.title',
                        $this->request->getData('plugin'),
                        $this->request->getData('type')
                    )
                ),
                sprintf(
                    __d('baser', '「%s」を「%s」に名称変更しました。'),
                    $this->request->getData('oldTitle'),
                    $this->request->getData('newTitle')
                )
            ),
            true,
            false
        );
        Configure::write('debug', 0);
        return $this->Content->getUrlById($this->Content->id);
    }

    /**
     * 並び順を移動する
     */
    public function admin_ajax_move()
    {

        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $this->Content->id = $this->request->getData('currentId');
        if (!$this->Content->exists()) {
            $this->ajaxError(500, __d('baser', 'データが存在しません。'));
        }

        if ($this->SiteConfig->isChangedContentsSortLastModified($this->request->getData('listDisplayed'))) {
            $this->ajaxError(500, __d('baser', 'コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。'));
        }

        if (!$this->Content->isMovable($this->request->getData('currentId'), $this->request->getData('targetParentId'))) {
            $this->ajaxError(500, __d('baser', '同一URLのコンテンツが存在するため処理に失敗しました。（現在のサイトに存在しない場合は、関連サイトに存在します）'));
        }

        // EVENT Contents.beforeMove
        $event = $this->dispatchLayerEvent('beforeMove', [
            'data' => $this->request->data
        ]);
        if ($event !== false) {
            $this->request->data = $event->getResult() === true? $event->getData('data') : $event->getResult();
        }

        $data = $this->request->data;

        $beforeUrl = $this->Content->field('url', ['Content.id' => $data['currentId']]);

        $result = $this->Content->move(
            $data['currentId'],
            $data['currentParentId'],
            $data['targetSiteId'],
            $data['targetParentId'],
            $data['targetId']
        );

        if ($data['currentParentId'] == $data['targetParentId']) {
            // 親が違う場合は、Contentモデルで更新してくれるが同じ場合更新しない仕様のためここで更新する
            $this->SiteConfig->updateContentsSortLastModified();
        }

        if (!$result) {
            $this->ajaxError(500, __d('baser', 'データ保存中にエラーが発生しました。'));
            return false;
        }

        // EVENT Contents.afterAdd
        $this->dispatchLayerEvent('afterMove', [
            'data' => $result
        ]);
        $this->BcMessage->set(
            sprintf(__d('baser', "コンテンツ「%s」の配置を移動しました。\n%s > %s"),
                $result['Content']['title'],
                urldecode($beforeUrl),
                urldecode($result['Content']['url'])
            ),
            false,
            true,
            false
        );

        return json_encode($this->Content->getUrlById($result['Content']['id'], true));

    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     *
     * @return mixed
     */
    public function admin_exists_content_by_url()
    {
        $this->autoRender = false;
        if (!$this->request->getData('url')) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        Configure::write('debug', 0);
        return $this->Content->existsContentByUrl($this->request->getData('url'));
    }

    /**
     * 指定したIDのコンテンツが存在するか確認する
     * ゴミ箱のものは無視
     *
     * @param $id
     */
    public function admin_ajax_exists($id)
    {
        $this->autoRender = false;
        Configure::write('debug', 0);
        return $this->Content->exists($id);
    }

    /**
     * サイトに紐付いたフォルダリストを取得
     * @param ContentServiceInterface $contentService
     * @param $siteId
     */
    public function admin_ajax_get_content_folder_list(ContentServiceInterface $contentService, $siteId)
    {
        $this->autoRender = false;
        Configure::write('debug', 0);
        return json_encode(
            $contentService->getContentFolderList(
                (int)$siteId,
                [
                    'conditions' => ['Content.site_root' => false]
                ]
            )
        );
    }

    /**
     * コンテンツ情報を取得する
     */
    public function ajax_contents_info(ContentServiceInterface $contentService)
    {
        $this->autoLayout = false;
        $this->set('sites', $contentService->getContensInfo());
    }

    public function ajax_get_full_url($id)
    {
        $this->autoRender = false;
        Configure::write('debug', 0);
        return $this->response->withType("application/json")->withStringBody($this->Contents->getUrlById($id, true));
    }
}
