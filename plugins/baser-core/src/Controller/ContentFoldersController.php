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

namespace BaserCore\Controller;

use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\Front\ContentFoldersFrontServiceInterface;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;

/**
 * Class ContentFoldersController
 *
 * フロント用のフォルダ コントローラー
 */
class ContentFoldersController extends BcFrontAppController
{

    /**
     * initialize
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents');
    }

    /**
     * コンテンツを表示する
     * @param  ContentFoldersFrontServiceInterface $service
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(ContentFoldersFrontServiceInterface $service)
    {
        // コンテンツフォルダのindex機能を使用しないときは403にする
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfig = $siteConfigsService->get();
        if (!empty($siteConfig->use_contents_folder_forbidden)) {
            throw new \Cake\Http\Exception\ForbiddenException(__d('baser_core', 'indexページが見つかりませんでした。'));
            exit;
        }
        $contentFolder = $service->get(
            $this->getRequest()->getAttribute('currentContent')->entity_id,
            ['status' => 'publish']
        );
        $this->set($service->getViewVarsForView($contentFolder, $this->getRequest()));
        $this->render($service->getTemplateForView($contentFolder));
    }

}
