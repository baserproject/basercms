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
use Cake\Http\Exception\NotFoundException;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\ContentFoldersServiceInterface;

/**
 * Class ContentFoldersController
 *
 * フロント用のフォルダ コントローラー
 *
 * @package BaserCore.Controller
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
     * @param  ContentsServiceInterface $contentService
     * @param  ContentFoldersServiceInterface $contentFolderService
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(ContentFoldersServiceInterface $contentFolderService, ContentsServiceInterface $contentService)
    {
        if (empty($this->request->getParam('entityId'))) {
            throw new NotFoundException();
        }
        $contentFolder = $contentFolderService->get($this->request->getParam('entityId'));
        $contentService->setTreeConfig('scope', ['site_root' => false] + $contentService->getConditionAllowPublish());
        $children = [];
        if ($contentService->getChildren($contentFolder->content->id)) {
            $children = $contentService->getChildren($contentFolder->content->id)->order(['lft']);
        }
        $contentService->setTreeConfig('scope', [null]);
        if ($this->BcFrontContents->preview && !empty($this->request->getData('Contents'))) {
            $contentFolder->content = $this->request->getData('Contents');
        }
        $this->set(compact('contentFolder', 'children'));
        $folderTemplate = !empty($contentFolder->folder_template) ? $contentFolder->folder_template : $contentFolderService->getParentTemplate($this->request->getParam('Content.id'), 'folder');
        $this->set('editLink', ['admin' => true, 'plugin' => 'BaserCore', 'controller' => 'content_folders', 'action' => 'edit', $contentFolder->id, 'content_id' => $contentFolder->content->id]);
        $this->render($folderTemplate);
    }
}
