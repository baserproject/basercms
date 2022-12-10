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

namespace BcUploader\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * アップロードカテゴリコントローラー
 */
class UploaderFilesController extends BcApiController
{

    /**
     * [ADMIN] Ajaxファイルアップロード
     */
    public function upload(UploaderFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $entity = null;
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser', 'ファイル「{0}」を追加しました。', $entity->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploaderFile' => $entity,
            'message' => $message,
            'errors' => $entity ?? $entity->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'uploaderFile', 'errors']);
    }
}
