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

namespace BaserCore\Controller\Admin;

use BaserCore\Controller\Component\BcAdminContentsComponent;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentsController
 *
 * 統合コンテンツ管理 コントローラー
 *
 * baserCMS内のコンテンツを統合的に管理する
 *
 * @property BcAdminContentsComponent $BcAdminContents
 * @property BcMessageComponent $BcMessage
 */

class ContentsController extends BcAdminAppController
{
    /**
     * initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', [
            'entityVarName' => 'content',
            'useForm' => true
        ]);
    }

    /**
     * beforeFilter
     *
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['delete', 'batch', 'trash_return']);
    }

    /**
     * コンテンツ一覧
     * @param  ContentsAdminServiceInterface $service
     * @param  SiteConfigsServiceInterface $siteConfigService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentsAdminServiceInterface $service, SiteConfigsServiceInterface $siteConfigService)
    {
        $request = $this->getRequest();
        if($request->getAttribute('currentSite')) {
            $this->setRequest(
                $request->withQueryParams(array_merge(
                    $request->getQueryParams(),
                    ['site_id' => $request->getAttribute('currentSite')->id]
                ))
            );
        }

        $this->setViewConditions('Contents', ['default' => ['query' => [
            'limit' => BcSiteConfig::get('admin_list_num'),
            'site_id' => 1,
            'list_type' => 1,
        ]]]);

        switch($this->getRequest()->getQuery('list_type')) {
            case 1:
                // 並び替え最終更新時刻をリセット
                $siteConfigService->resetValue('contents_sort_last_modified');
                $contents = $service->getTreeIndex($this->getRequest()->getQueryParams());
                break;
            case 2:
                $this->setViewConditions('Contents', ['default' => [
                    'query' => [
                        'num' => $siteConfigService->getValue('admin_list_num'),
                        'sort' => 'id',
                        'direction' => 'asc',
                    ]
                ]]);

                // EVENT Contents.searchIndex
                $event = $this->dispatchLayerEvent('searchIndex', [
                    'request' => $this->getRequest()
                ]);
                if ($event !== false) {
                    $this->setRequest(($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult());
                }
                try {
                    $contents = $this->paginate($service->getTableIndex($this->getRequest()->getQueryParams()));
                } catch(NotFoundException $e) {
                    // 1ページ目以外で最後のレコードを削除した際に発生するので1ページに戻す
                    $paging = $this->getRequest()->getAttribute('paging');
                    if($paging['Contents']['requestedPage'] !== 1) {
                        return $this->redirect(['?' => array_merge($this->getRequest()->getQueryParams(), [
                            'page' => 1,
                        ])]);
                    }
                }

                break;
        }

        $this->setRequest($this->getRequest()->withData('ViewSetting.list_type', $this->getRequest()->getQuery('list_type')));
        $this->set($service->getViewVarsForIndex($this->getRequest(), $contents));
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する
     *
     * @param  ContentsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_index(ContentsAdminServiceInterface $service)
    {
        $this->setViewConditions('Contents', ['default' => [
            'query' => [
                'site_id' => $this->request->getAttribute('currentSite')? $this->request->getAttribute('currentSite')->id : 1,
            ]
        ], 'get' => true]);
        $contents = $service->getTrashIndex($this->request->getQueryParams(), 'threaded')->order(['site_id', 'lft']);
        $this->set($service->getViewVarsForTrashIndex($contents));
    }

    /**
     * エイリアスを編集する
     * @param ContentsServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit_alias(ContentsServiceInterface $service, $id)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $alias = $service->get($id);
        if ($this->request->is(['post', 'put'])) {
            if (BcUtil::isOverPostSize()) {
                $this->BcMessage->setError(__d('baser_core', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit_alias', $id]);
            }
            try {
                $alias = $service->update($alias, $this->request->getData('content'));
                $content = $service->get($alias->alias_id);
                $message = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type . '.title') .
                    sprintf(__d('baser_core', '「%s」のエイリアス「%s」を編集しました。'), $content->title, $alias->title);
                $this->BcMessage->setSuccess($message);
                $this->redirect(['action' => 'edit_alias', $id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $alias = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', "保存中にエラーが発生しました。入力内容を確認してください。") . "\n" . $alias->getErrors());
            }
        } else {
            $this->request = $this->request->withData('Contents', $alias);
            if (!$this->request->getData()) {
                $this->BcMessage->setError(__d('baser_core', '無効な処理です。'));
                $this->redirect(['action' => 'index']);
            }
        }
        $alias->content = clone $alias;
        $this->set('content', $alias);
    }

    /**
     * ゴミ箱のコンテンツを戻す
     *
     * @param  ContentsServiceInterface $service
     * @param  int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_return(ContentsServiceInterface $service, $id)
    {
        if (empty($id)) {
            $this->ajaxError(500, __d('baser_core', '無効な処理です。'));
        }
        $this->disableAutoRender();

        // EVENT Contents.beforeTrashReturn
        $this->dispatchLayerEvent('beforeTrashReturn', [
            'data' => $id
        ]);

        if ($restored = $service->restore($id)) {
            // EVENT Contents.afterTrashReturn
            $this->dispatchLayerEvent('afterTrashReturn', [
                'data' => $id
            ]);
            $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'ゴミ箱「%s」を戻しました。'), $restored->title));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->BcMessage->setError(__d('baser_core', 'ゴミ箱から戻す事に失敗しました。'));
            return $this->redirect(['action' => 'trash_index']);
        }
    }

    /**
	 * コンテンツ削除（論理削除）
     * @param  ContentsServiceInterface $service
	 */
	public function delete(ContentsServiceInterface $service)
	{
        if ($this->request->is(['post', 'put', 'delete'])) {
            $id = $this->request->getData('content.id');

            // EVENT Contents.beforeDelete
            $this->dispatchLayerEvent('beforeDelete', [
                'data' => $id
            ]);

            /* @var \BaserCore\Model\Entity\Content $content */
            $content = $service->get($id);
            if ($service->deleteRecursive($id)) {

                // EVENT Contents.afterDelete
                $this->dispatchLayerEvent('afterDelete', [
                    'data' => $id
                ]);

                $typeName = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type . '.title');
                if(!$content->alias_id) {
                    $message = $typeName . sprintf(__d('baser_core', '「%s」をゴミ箱に移動しました。'), $content->title);
                } else {
                    $message = sprintf(__d('baser_core', '%s のエイリアス「%s」を削除しました。'), $typeName, $content->title);
                }
                $this->BcMessage->setSuccess($message, true);
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError(__d('baser_core', '削除中にエラーが発生しました。'));
            }
        } else {
            $this->BcMessage->setError(__d('baser_core', '不正なリクエストです。'));
        }
	}

}
