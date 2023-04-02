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

namespace BcContentLink\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcContentLink\Service\ContentLinksServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Throwable;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentLinksController
 *
 * リンク コントローラー
 */
class ContentLinksController extends BcApiController
{

    /**
     * コンテンツリンク取得
     *
     * /baser/api/bc-content-link/content_links/view/{id}.json
     *
     * クエリーパラーメーター
     * - status: string 公開ステータス（初期値：publish）
     *  - `publish` 公開されたページ
     *  - `` 全て
     *
     * @param ContentLinksServiceInterface $service
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }

        $queryParams = array_merge($queryParams, [
            'status' => 'publish'
        ]);

        $contentLink = $message = null;
        try {
            $contentLink = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'contentLink' => $contentLink,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['contentLink', 'message']);
    }

}
