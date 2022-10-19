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

namespace BaserCore\Service\Front;

use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * ContentFoldersFrontService
 */
class ContentFoldersFrontService extends ContentFoldersService implements ContentFoldersFrontServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * フロント表示用の view 変数を取得する
     * @param EntityInterface $contentFolder
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForView(EntityInterface $contentFolder, ServerRequest $request): array
    {
        if ($request->is(['patch', 'post', 'put']) && BcUtil::isAdminUser()) {
            $editLink = null;
            $contentFolder = $this->ContentFolders->patchEntity($contentFolder, $request->getData());
            $contentFolder->content = $this->Contents->saveTmpFiles($request->getData('content'), mt_rand(0, 99999999));
        } else {
            $editLink = [
                'admin' => true,
                'plugin' => 'BaserCore',
                'controller' =>
                'content_folders',
                'action' => 'edit',
                $contentFolder->id,
                'content_id' => $contentFolder->content->id
            ];
        }
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $contentsService->setTreeConfig('scope', ['site_root' => false] + $contentsService->getConditionAllowPublish());
        $children = [];
        if ($contentsService->getChildren($contentFolder->content->id)) {
            $children = $contentsService->getChildren($contentFolder->content->id)->order(['lft']);
        }
        $contentsService->setTreeConfig('scope', null);
        return [
            'contentFolder' => $contentFolder,
            'children' => $children,
            'editLink' => $editLink
        ];
    }

    /**
     * フロント用のテンプレートを取得する
     * @param $contentFolder
     * @return string
     * @checked
     * @noTodo
     */
    public function getTemplateForView($contentFolder): string
    {
        return !empty($contentFolder->folder_template) ?
            $contentFolder->folder_template :
            $this->getParentTemplate($contentFolder->content->id, 'folder');
    }

    /**
     * プレビュー用のセットアップをする
     * @param Controller $controller
     * @checked
     * @noTodo
     */
    public function setupPreviewForView(Controller $controller): void
    {
        $request = $controller->getRequest();
        $contentFolder = $this->get(
            $request->getAttribute('currentContent')->entity_id,
            ['status' => 'publish']
        );
        $controller->viewBuilder()->setTemplate($this->getTemplateForView($contentFolder));
        $controller->set($this->getViewVarsForView($contentFolder, $request));
    }

}
