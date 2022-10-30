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
use BcContentLink\Service\ContentLinksService;
use BcContentLink\Service\ContentLinksServiceInterface;
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
     * コンテンツリンクを登録する（認証要）
     *
     * /baser/api/bc-content-link/content_links/add.json
     *
     * ### POSTデータ
     * - content
     *  - parent_id: 親のコンテンツID
     *  - title: タイトル
     *  - plugin: プラグイン名
     *  - type: コンテンツタイプ名
     *  - site_id: サイトID
     *
     * ### レスポンス
     * - contentLink: コンテンツリンクエンティティ
     * - content: コンテンツエンティティ
     * - message: メッセージ
     * - errors: エラーが発生した場合の詳細内容
     *
     * @param ContentLinksService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function add(ContentLinksServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser', 'リンク「{0}」を追加しました。', $entity->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。\n");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'contentLink' => $entity,
            'content' => $entity->content,
            'message' => $message,
            'errors' => $entity->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'contentLink',
            'content',
            'message',
            'errors'
        ]);
    }

}
