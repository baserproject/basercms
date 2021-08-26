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

use Cake\Event\EventInterface;
use BaserCore\Service\ContentFolderServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Http\Response;

/**
 * Class ContentFoldersController
 *
 * フォルダ コントローラー
 *
 * @package BaserCore.Controller
 */
class ContentFoldersController extends BcAdminAppController
{

    /**
     * initialize
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        // $this->loadComponent('BaserCore.BcAuth');
        // $this->loadComponent('BaserCore.BcAuthConfigure');
        $this->loadComponent('BaserCore.BcContents', ['useForm' => true]);
        $this->Security->setConfig('unlockedActions', ['add']);
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // $this->loadModel('BaserCore.Pages');
        $this->loadModel('BaserCore.ContentFolders');
        // $this->BcAuth->allow('view');
    }

    /**
     * コンテンツを登録する
     *
     * @return Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(ContentFolderServiceInterface $contentFolderService)
    {
        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$contentFolder = $contentFolderService->create($this->request->getData())) {
            $this->ajaxError(500, __d('baser', '保存中にエラーが発生しました。'));
            exit;
        }

        $this->BcMessage->setSuccess(
            sprintf(
                __d('baser', 'フォルダ「%s」を追加しました。'),
                $contentFolder->content->title,
            ),
            true,
            false
        );

        return $this->response->withType("application/json")->withStringBody(json_encode($contentFolder->content));
    }

    /**
     * コンテンツを更新する
     *
     * @return void
     */
    public function admin_edit($entityId)
    {
        $this->setTitle(__d('baser', 'フォルダ編集'));
        if ($this->request->is(['post', 'put'])) {
            if ($this->ContentFolder->isOverPostSize()) {
                $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit', $entityId]);
            }
            if ($this->ContentFolder->save($this->request->data, ['reconstructSearchIndices' => true])) {
                clearViewCache();
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'フォルダ「%s」を更新しました。'), $this->request->getData('Content.title')));
                $this->redirect([
                    'plugin' => '',
                    'controller' => 'content_folders',
                    'action' => 'edit',
                    $entityId
                ]);
            } else {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        } else {
            $this->request->data = $this->ContentFolder->read(null, $entityId);
            if (!$this->request->data) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
            }
        }

        $theme = [$this->siteConfigs['theme']];
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findById($this->request->getData('Content.site_id'))->first();
        if (!empty($site) && $site->theme && $site->theme != $this->siteConfigs['theme']) {
            $theme[] = $site->theme;
        }
        $site = $sites->findById($this->request->getData('Content.site_id'))->first();
        $this->set('folderTemplateList', $this->ContentFolder->getFolderTemplateList($this->request->getData('Content.id'), $theme));
        $this->set('pageTemplateList', $this->Page->getPageTemplateList($this->request->getData('Content.id'), $theme));
        $this->set('publishLink', $this->Content->getUrl($this->request->getData('Content.url'), true, $site->useSubDomain));
    }

    /**
     * コンテンツを削除する
     *
     * @return bool
     */
    public function admin_delete()
    {
        if (empty($this->request->getData('entityId'))) {
            return false;
        }
        if ($this->ContentFolder->delete($this->request->getData('entityId'))) {
            return true;
        }
        return false;
    }

    /**
     * コンテンツを表示する
     *
     * @return void
     */
    public function view()
    {
        if (empty($this->request->getParam('entityId'))) {
            $this->notFound();
        }
        $data = $this->ContentFolder->find('first', ['conditions' => ['ContentFolder.id' => $this->request->getParam('entityId')]]);
        if (empty($data)) {
            $this->notFound();
        }
        $this->ContentFolder->Content->Behaviors->Tree->settings['Content']['scope'] = ['Content.site_root' => false] + $this->ContentFolder->Content->getConditionAllowPublish();
        // 公開期間を条件に入れている為、キャッシュをオフにしないとキャッシュが無限増殖してしまう
        $this->ContentFolder->Content->Behaviors->unload('BcCache');
        $children = $this->ContentFolder->Content->children($data['Content']['id'], true, [], 'lft');
        $this->ContentFolder->Content->Behaviors->load('BcCache');
        $this->ContentFolder->Content->Behaviors->Tree->settings['Content']['scope'] = null;
        if ($this->BcContents->preview && !empty($this->request->getData('Content'))) {
            $data['Content'] = $this->request->getData('Content');
        }
        $this->set(compact('data', 'children'));
        $folderTemplate = $data['ContentFolder']['folder_template'];
        if (!$folderTemplate) {
            $folderTemplate = $this->ContentFolder->getParentTemplate($data['Content']['id'], 'folder');
        }
        $this->set('editLink', ['admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $data['ContentFolder']['id'], 'content_id' => $data['Content']['id']]);
        $this->render($folderTemplate);
    }

}
