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

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Service\Admin\ContentManageService;
use BaserCore\Controller\Component\BcContentsComponent;
use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\Service\Admin\ContentManageServiceInterface;

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
 */

class ContentsController extends BcAdminAppController
{
    /**
     * SiteConfigsTrait
     */
    use SiteConfigsTrait;

    /**
     * initialize
     * ログインページ認証除外
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
        $this->Security->setConfig('unlockedActions', ['index']);
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
     * @noTodo
     * @unitTest
     */
    public function index(ContentManageServiceInterface $contentManage, SiteManageServiceInterface $siteManage)
    {
        $currentSiteId = $siteManage->getCurrentSite()->id;
        $sites = $siteManage->getSiteList();
        if ($sites) {
            if (!$this->request->getQuery('site_id') || !in_array($this->request->getQuery('site_id'), array_keys($sites))) {
                reset($sites);
                $this->request = $this->request->withQueryParams(['site_id' =>key($sites)]);
            }
        } else {
            $this->request = $this->request->withQueryParams(['site_id' => null]);
        }
        $currentListType = $this->request->getQuery('list_type') ?? 1;

        $this->setViewConditions('Contents', ['default' => [
            'query' => [
                'num' => $siteManage->getSiteConfig('admin_list_num'),
                'site_id' => $currentSiteId,
                'list_type' => $currentListType,
                'sort' => 'id',
                'direction' => 'asc',
                'action' => $this->request->getParam('action'),
            ]
        ]]);

        if ($this->request->is('ajax')) {
            $this->ajax_index($contentManage);
        }
        $this->ContentFolders->getEventManager()->on($this->ContentFolders);
        $this->set('contentTypes', $this->BcContents->getTypes());
        $this->set('authors', $this->Users->getUserList());
        $this->set('folders', $contentManage->getContentFolderList($currentSiteId, ['conditions' => ['site_root' => false]]));
        $this->set('sites', $sites);
    }

    /**
     * コンテンツ一覧のajax処理部分
     *
     * @param  ContentManageService $contentManage
     * @return void
     * @checked
     * @unitTest
     */
    protected function ajax_index($contentManage): void
    {
            $this->viewBuilder()->disableAutoLayout();
            $dataset = $contentManage->getAdminAjaxIndex($this->request->getQueryParams());
            $template = key($dataset);
            $datas = array_shift($dataset);
            if($this->request->getQuery('action') == "index") {
                    switch($this->request->getQuery('list_type')) {
                        case 1:
                            // 並び替え最終更新時刻をリセット
                            // $this->SiteConfigs->resetContentsSortLastModified();
                            break;
                        case 2:
                            $datas = $this->paginate($datas);
                            $this->request = $this->request->withQueryParams(['conditions' => $contentManage->getTableConditions($this->request->getQueryParams())]);
                            // EVENT Contents.searchIndex
                            // TODO: うまく動かない
                            $event = $this->dispatchLayerEvent('searchIndex', [
                                'request' => $this->request
                            ]);
                            if ($event !== false) {
                                $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
                            }
                            break;
                    }
            }
            $this->set('datas', $datas);
            Configure::write('debug', 0);
            $this->render($template);
            return;
    }
    /**
     * ゴミ箱内のコンテンツ一覧を表示する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_index(ContentManageServiceInterface $contentManage, SiteManageServiceInterface $siteManage)
    {
        $this->setAction('index', $contentManage, $siteManage);
        if (!$this->request->is('ajax')) {
            $this->render('index');
        }
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
    public function add(ContentManageServiceInterface $contentManage, $alias = false)
    {

        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        $srcContent = [];
        // TODO: 一旦alias無視
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
        $contentManage->create($this->request->getData());
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
     *
     * @return boolean
     */
    public function admin_ajax_delete()
    {
        $this->autoRender = false;
        if (empty($this->request->getData('contentId'))) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_delete($this->request->getData('contentId'), false)) {
            $this->ajaxError(500, __d('baser', '削除中にエラーが発生しました。'));
            return false;
        }

        return true;
    }

    /**
     * コンテンツ削除（論理削除）
     */
    public function admin_delete()
    {
        if (empty($this->request->getData('Content.id'))) {
            $this->notFound();
        }
        if ($this->_delete($this->request->getData('Content.id'), true)) {
            $this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
        } else {
            $this->BcMessage->setError('削除中にエラーが発生しました。');
        }
    }

    /**
     * コンテンツを削除する（論理削除）
     *
     * ※ エイリアスの場合は直接削除
     *
     * @param int $id
     * @param bool $useFlashMessage
     * @return bool
     */
    protected function _delete($id, $useFlashMessage = false)
    {
        $content = $this->Content->find('first', ['conditions' => ['Content.id' => $id], 'recursive' => -1]);
        if (!$content) {
            return false;
        }
        $content = $content['Content'];
        $typeName = Configure::read('BcContents.items.' . $content['plugin'] . '.' . $content['type'] . '.title');

        // EVENT Contents.beforeDelete
        $this->dispatchLayerEvent('beforeDelete', [
            'data' => $id
        ]);

        if (!$content['alias_id']) {
            $result = $this->Content->softDeleteFromTree($id);
            $message = $typeName . sprintf(__d('baser', '「%s」をゴミ箱に移動しました。'), $content['title']);
        } else {
            $softDelete = $this->Content->softDelete(null);
            $this->Content->softDelete(false);
            $result = $this->Content->removeFromTree($id, true);
            $this->Content->softDelete($softDelete);
            $message = sprintf(__d('baser', '%s のエイリアス「%s」を削除しました。'), $typeName, $content['title']);
        }
        if ($result) {
            $this->BcMessage->setSuccess($message, true, $useFlashMessage);
        }

        // EVENT Contents.afterDelete
        $this->dispatchLayerEvent('afterDelete', [
            'data' => $id
        ]);

        return $result;
    }

    /**
     * 一括削除
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_del($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_delete($id, false);
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
     *
     * @return bool
     */
    public function admin_ajax_trash_empty()
    {
        if (!$this->request->data) {
            $this->notFound();
        }
        $this->autoRender = false;
        $this->Content->softDelete(false);
        $contents = $this->Content->find('all', ['conditions' => ['Content.deleted'], 'order' => ['Content.plugin', 'Content.type'], 'recursive' => -1]);
        $result = true;

        // EVENT Contents.beforeTrashEmpty
        $this->dispatchLayerEvent('beforeTrashEmpty', [
            'data' => $contents
        ]);

        if ($contents) {
            foreach($contents as $content) {
                if (!empty($this->BcContents->getConfig('settings')['items'][$content['Content']['type']]['routes']['delete'])) {
                    $route = $this->BcContents->getConfig('settings')['items'][$content['Content']['type']]['routes']['delete'];
                } else {
                    $route = $this->BcContents->getConfig('settings')['items']['Default']['routes']['delete'];
                }
                if (!$this->requestAction($route, ['data' => [
                    'contentId' => $content['Content']['id'],
                    'entityId' => $content['Content']['entity_id'],
                ]])) {
                    $result = false;
                }
            }
        }

        // EVENT Contents.afterTrashEmpty
        $this->dispatchLayerEvent('afterTrashEmpty', [
            'data' => $result
        ]);

        return $result;
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
     * プラグイン等と関連付けられていない素のコンテンツをゴミ箱より消去する
     *
     * @param $id
     * @return bool
     */
    public function admin_empty()
    {
        if (empty($this->request->getData('contentId'))) {
            return false;
        }
        $softDelete = $this->Content->softDelete(null);
        $this->Content->softDelete(false);
        $result = $this->Content->removeFromTree($this->request->getData('contentId'), true);
        $this->Content->softDelete($softDelete);
        return $result;
    }

    /**
     * サイトに紐付いたフォルダリストを取得
     * @param ContentManageServiceInterface $contentManage
     * @param $siteId
     */
    public function admin_ajax_get_content_folder_list(ContentManageServiceInterface $contentManage, $siteId)
    {
        $this->autoRender = false;
        Configure::write('debug', 0);
        return json_encode(
            $contentManage->getContentFolderList(
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
    public function ajax_contents_info(ContentManageServiceInterface $contentManage)
    {
        $this->autoLayout = false;
        $this->set('sites', $contentManage->getContensInfo());
    }

    public function admin_ajax_get_full_url($id)
    {
        $this->autoRender = false;
        Configure::write('debug', 0);
        return $this->Content->getUrlById($id, true);
    }
}
