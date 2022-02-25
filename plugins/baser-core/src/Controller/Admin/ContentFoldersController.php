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
                $this->request = $this->request->withData('ContentFolder.content', $this->request->getData('Content'));
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
}
