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

namespace BcContentLink\Controller;

use BaserCore\Controller\BcFrontAppController;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcContentLink\Service\ContentLinksService;
use BcContentLink\Service\ContentLinksServiceInterface;

/**
 * Class ContentLinksController
 *
 * リンク コントローラー
 */
class ContentLinksController extends BcFrontAppController
{

    /**
     * initialize
     *
     * フロントエンド表示に必要な BcFrontContentsComponent をロードする。
     *
     * @throws \Exception
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents');
    }

    /**
     * コンテンツリンクを表示する
     *
     * ビューの処理にて、設定リンク先のURLにリダイレクトする。
     *
     * @param ContentLinksService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function view(ContentLinksServiceInterface $service)
    {
        $contentLink = $service->get(
            $this->request->getParam('entityId'),
            ['status' => 'publish']
        );
        $this->set(compact('contentLink'));
    }

}
