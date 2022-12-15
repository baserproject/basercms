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

namespace BcThemeFile\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcThemeFile\Service\ThemeFoldersService;
use BcThemeFile\Service\ThemeFoldersServiceInterface;

/**
 * テーマフォルダコントローラー
 */
class ThemeFoldersController extends BcApiController
{

    /**
     * テーマフォルダのバッチ処理
     *
     * 指定したテーマフォルダに対して削除の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete' 以外の値であれば500エラーを発生させる
     *
     * @param ThemeFoldersService $service
     * @checked
     * @noTodo
     */
    public function batch(ThemeFOldersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除',
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            if(is_dir($targets[0])) {
                $fullpath = $targets[0];
            } else {
                $fullpath = dirname($targets[0]);
            }
            $names = $service->getNamesByFullpath($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                __d('baser', 'フォルダー {0} 内の「{1}」を {2} しました。', $fullpath, implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
