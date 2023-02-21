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

namespace BcCustomContent\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Service\CustomLinksServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * CustomLinksController
 */
class CustomLinksController extends BcApiController
{

    /**
     * カスタムリンク編集API
     *
     * @param CustomLinksServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     */
    public function edit(CustomLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        try {
            $entity = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', 'カスタムリンク「{0}」を更新しました。', $entity->title);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $entity = $e->getEntity();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        } catch (\Throwable $e) {
            $entity = $e->getEntity();
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'customLink' => $entity,
            'errors' => $entity->getErrors(),
        ]);

        $this->viewBuilder()->setOption('serialize', ['customLink', 'message', 'errors']);
    }

    /**
     * カスタムリンクの親のリストを取得する
     *
     * @param CustomLinksServiceInterface $service
     * @param int $tableId
     */
    public function get_parent_list(CustomLinksServiceInterface $service, int $tableId)
    {
        $parentList = $service->getControlSource('parent_id', ['tableId' => $tableId]);
        $this->set(['parentList' => $parentList]);
        $this->viewBuilder()->setOption('serialize', ['parentList']);
    }

}
