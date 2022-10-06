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

use BaserCore\Service\Admin\ContentFoldersAdminServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

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
        $this->loadComponent('BaserCore.BcAdminContents', ['entityVarName' => 'contentFolder']);
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
        $this->loadModel('BaserCore.ContentFolders');
        // $this->BcAuth->allow('view');
    }

    /**
     * コンテンツを更新する
     *
     * @return void
     * @checked
     * @unitTest
     * @note(value="clearViewCacheが動作しないため一旦コメントアウト")
     */
    public function edit(ContentFoldersAdminServiceInterface $contentFolderService, $id = null)
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
                $contentFolder = $contentFolderService->update($contentFolder, $this->request->getData('ContentFolders'), ['reconstructSearchIndices' => true]);
                // TODO ucmitz 動作しないため一旦コメントアウト
                // clearViewCache();
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'フォルダ「%s」を更新しました。'), $contentFolder->content->title));
                return $this->redirect(['action' => 'edit', $id]);
            } catch (\Exception $e) {
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        }
        $this->set($contentFolderService->getViewVarsForEdit($contentFolder));
    }
}
