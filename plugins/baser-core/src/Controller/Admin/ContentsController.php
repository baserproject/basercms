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
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\SiteConfigTrait;
use BaserCore\Model\Table\ContentsTable;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Service\SiteServiceInterface;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Service\BcAdminServiceInterface;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Controller\Component\BcAdminContentsComponent;

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
 * @property BcAdminContentsComponent $BcAdminContents
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
        $this->loadComponent('BaserCore.BcAdminContents');
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
        $this->Security->setConfig('unlockedActions', ['delete', 'batch']);
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
        $this->set('contents', $this->getContents($contentService));
        $this->set('template', $this->getTemplate());
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
    protected function getContents($contentService)
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
    protected function getTemplate(): string
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
     *
     * @param  ContentServiceInterface $contentService
     * @param  SiteServiceInterface $siteService
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
     * @param  ContentServiceInterface $contentService
     * @param  int $id
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_return(ContentServiceInterface $contentService, $id)
    {
        if (empty($id)) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $this->disableAutoRender();
        // EVENT Contents.beforeTrashReturn
        $this->dispatchLayerEvent('beforeTrashReturn', [
            'data' => $id
        ]);
        if ($restored = $contentService->restore($id)) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'ゴミ箱「%s」を戻しました。'), $restored->title));
            return $this->redirect(['action' => 'trash_index']);
        } else {
            $this->BcMessage->setError('ゴミ箱から戻す事に失敗しました。');
        }
        // EVENT Contents.afterTrashReturn
        $this->dispatchLayerEvent('afterTrashReturn', [
            'data' => $id
        ]);
    }

    /**
     * コンテンツ編集
     *
     * @param  int $id
     * @param  ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentServiceInterface $contentService, $id)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }

        $content = $contentService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $content = $contentService->update($content, $this->request->getData('Content'));
            if (!$content->hasErrors()) {
                $message = Configure::read('BcContents.items.' . $this->request->getData('Content.plugin') . '.' . $this->request->getData('Content.type') . '.title') .
                sprintf(__d('baser', '「%s」を更新しました。'), $this->request->getData('Content.title'));
                $this->BcMessage->setSuccess($message);
                return $this->redirect(['action' => 'edit', $content->id]);
            } else {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        }
        $this->request = $this->request->withData("Content", $content);
        $this->set('content', $content);
        $this->set('publishLink', $contentService->getUrl($content->url, true, $content->site->useSubDomain));
    }

    /**
     * エイリアスを編集する
     *
     * @param $id
     * @param  ContentServiceInterface $contentService
     * @return Response|null
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit_alias(ContentServiceInterface $contentService, $id)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $alias = $contentService->get($id);
        if ($this->request->is(['post', 'put'])) {
            // if ($this->Content->isOverPostSize()) {
            //     $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
            //     $this->redirect(['action' => 'edit_alias', $id]);
            // }
            try {
                $newAlias = $contentService->update($alias, $this->request->getData('Content'));
                if (!$newAlias->hasErrors()) {
                    $content = $contentService->get($newAlias->alias_id);
                    $message = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type . '.title') .
                    sprintf(__d('baser', '「%s」のエイリアス「%s」を編集しました。'), $content->title, $newAlias->title);
                    $this->BcMessage->setSuccess($message);
                    $this->redirect(['action' => 'edit_alias', $id]);
                } else {
                    $this->BcMessage->setError($newAlias->getErrors());
                }
            } catch (\Exception $e) {
                $this->BcMessage->setError("保存中にエラーが発生しました。入力内容を確認してください。\n" . $e->getMessage());
            }
        } else {
            $this->request = $this->request->withData('Content', $alias);
            if (!$this->request->getData()) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(['action' => 'index']);
            }
            $content = $contentService->get($alias->alias_id);
        }
        $this->set('content', $content);
        $this->BcAdminContents->settingForm($this, $this->request->getData('Content.site_id'), $this->request->getData('Content.id'));
    }

    /**
	 * コンテンツ削除（論理削除）
     * @param  ContentServiceInterface $contentService
	 */
	public function delete(ContentServiceInterface $contentService)
	{
        $this->disableAutoRender();
        $this->viewBuilder()->disableAutoLayout();
		if (empty($this->request->getData())) {
			$this->notFound();
		}
        if ($this->request->is(['post', 'put', 'delete'])) {
            //     // TODO:
            //     // EVENT Contents.beforeDelete
            //     // $this->dispatchLayerEvent('beforeDelete', [
            //     //     'data' => $id
            //     // ]);
            // TODO: フォルダー以外実装時に汎用化する
            $content = $this->request->getData('Content') ?? $this->request->getData('ContentFolder.content');
            $entity = $contentService->get($content['id']);
            if ($contentService->delete($content['id'])) {
                //     // TODO:
                //     // EVENT Contents.afterDelete
                //     // $this->dispatchLayerEvent('afterDelete', [
                //     //     'data' => $id
                //     // ]);
                $typeName = Configure::read('BcContents.items.' . $entity->plugin . '.' . $entity->type . '.title');
                $trashMessage = $typeName . sprintf(__d('baser', '「%s」をゴミ箱に移動しました。'), $entity->title);
                $aliasMessage = sprintf(__d('baser', '%s のエイリアス「%s」を削除しました。'), $typeName, $entity->title);
                $this->BcMessage->setSuccess($entity->alias_id ? $aliasMessage : $trashMessage, true);
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError('削除中にエラーが発生しました。');
            }
        } else {
            $this->BcMessage->setError('不正なリクエストです。');
        }
	}

    /**
     * batch
     *
     * @param  ContentServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(ContentServiceInterface $contentService)
    {
        $this->disableAutoRender();
        $allowMethod = [
            'publish' => '公開',
            'unpublish' => '非公開',
            'delete' => '削除',
        ];

        $method = $this->request->getData('ListTool.batch');
        if (!isset($allowMethod[$method])) {
            return;
        }

        $methodText = $allowMethod[$method];

        foreach($this->request->getData('ListTool.batch_targets') as $id) {
            $content = $contentService->get($id);
            if ($contentService->$method($id)) {
                $this->BcMessage->setSuccess(
                    sprintf(__d('baser', 'コンテンツ「%s」 を %sしました。'), $content->name, $methodText),
                    true,
                    false
                );
            }
        }
        return $this->response->withStringBody('true');
    }

    /**
     * ゴミ箱を空にする
     * @see bcTreeの処理はApi/trash_emptyに移行
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

        // EVENT Contents.beforetrash_empty
        $this->dispatchLayerEvent('beforetrash_empty', [
            'data' => $contents
        ]);
        if ($contents) {
            $result = true;
            foreach($contents as $content) {
                if(!$contentService->hardDeleteWithAssoc($content->id)) {
                    $result = false;
                    $this->BcMessage->setError(__d('baser', 'ゴミ箱の削除に失敗しました'));
                }
            }
        }
        // EVENT Contents.aftertrash_empty
        $this->dispatchLayerEvent('aftertrash_empty', [
            'data' => $result
        ]);
        return $this->redirect(['action' => "trash_index"]);
    }

    /**
     * コンテンツ情報を取得する
     * @param ContentServiceInterface $contentService
     * @checked
     * @noTodo
     * @unitTest
     *
     */
    public function ajax_contents_info(ContentServiceInterface $contentService)
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->set('sites', $contentService->getContentsInfo());
    }
}
