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

use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Service\ContentFolderServiceInterface;

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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents');
        $this->Security->setConfig('unlockedFields', ['ContentFolder.content.eyecatch', 'ContentFolder.content.eyecatch_', 'ContentFolder.content.eyecatch_delete']);
    }

    /**
     * beforeFilter
     *
     * @return void
     * @checked
     * @unitTest
     * @note(value="テーマを実装するまでTODO解消できない")
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // $this->loadModel('BaserCore.Pages');
        $this->loadModel('BaserCore.ContentFolders');
        // $this->BcAuth->allow('view');
    }

    /**
     * コンテンツを更新する
     *
     * @return void
     * @checked
     * @unitTest
     * @note(value="検索インデックス生成処理を実装するまでTODOが解消できない")
     */
    public function edit(ContentFolderServiceInterface $contentFolderService, $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            return $this->redirect(['controller' => 'contents', 'action' => 'index']);
        }
        $contentFolder = $contentFolderService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (BcUtil::isOverPostSize()) {
                $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit', $id]);
            }
            try {
                // postしたcontentEntitiesの情報をContentFolderに統一する
                $this->request = $this->request->withData('ContentFolder.content', array_merge($this->request->getData('ContentFolder.content'), $this->request->getData('Content')));
                $contentFolder = $contentFolderService->update($contentFolder, $this->request->getData('ContentFolder'));
                // TODO: afterSaveで$optionにreconstructSearchIndicesを渡す if ($ContentFolders->save($this->request->getData(), ['reconstructSearchIndices' => true])) {
                // clearViewCache(); TODO: 動作しないため一旦コメントアウト
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'フォルダ「%s」を更新しました。'), $contentFolder->content->title));
                return $this->redirect(['action' => 'edit', $id]);
            } catch (\Exception $e) {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        }
        $this->request = $this->request->withData("ContentFolder", $contentFolder);
        $this->set('contentFolder', $contentFolder);
        $contentEntities = [
            'ContentFolder' => $contentFolder,
            'Content' => $contentFolder->content,
        ];
        $this->set('contentEntities', $contentEntities);
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
        if ($this->BcAdminContents->preview && !empty($this->request->getData('Content'))) {
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
