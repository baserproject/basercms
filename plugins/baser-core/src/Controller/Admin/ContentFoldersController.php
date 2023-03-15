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
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class ContentFoldersController
 */
class ContentFoldersController extends BcAdminAppController
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
            'entityVarName' => 'contentFolder',
            'useForm' => true
        ]);
    }

    /**
     * コンテンツを更新する
     *
     * @param ContentFoldersAdminServiceInterface $service
     * @param int $id
     * @checked
     * @unitTest
     * @noTodo
     */
    public function edit(ContentFoldersAdminServiceInterface $service, $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser_core', '無効なIDです。'));
            return $this->redirect(['controller' => 'contents', 'action' => 'index']);
        }
        $contentFolder = $service->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (BcUtil::isOverPostSize()) {
                $this->BcMessage->setError(__d('baser_core', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit', $id]);
            }
            try {
                $contentFolder = $service->update($contentFolder, $this->request->getData(), ['reconstructSearchIndices' => true]);
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'フォルダ「%s」を更新しました。'), $contentFolder->content->title));
                return $this->redirect(['action' => 'edit', $id]);
            } catch (PersistenceFailedException $e) {
                $contentFolder = $e->getEntity();
                $this->BcMessage->setError(__d('base_core', '入力エラーが発生しました。入力内容を確認してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForEdit($contentFolder));
    }
}
