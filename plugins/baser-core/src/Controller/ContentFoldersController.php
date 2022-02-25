<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller;

use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\BcAppController;
use Cake\Http\Exception\NotFoundException;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Service\ContentFolderServiceInterface;

/**
 * Class ContentFoldersController
 *
 * フロント用のフォルダ コントローラー
 *
 * @package BaserCore.Controller
 */
class ContentFoldersController extends AppController
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
     * @param  ContentServiceInterface $contentService
     * @param  ContentFolderServiceInterface $contentFolderService
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(ContentFolderServiceInterface $contentFolderService, ContentServiceInterface $contentService)
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
        if ($this->BcFrontContents->preview && !empty($this->request->getData('Content'))) {
            $contentFolder->content = $this->request->getData('Content');
        }
        $this->set(compact('contentFolder', 'children'));
        $folderTemplate = $contentFolder->folder_template ?? $contentFolderService->getParentTemplate($this->request->getParam('Content.id'), 'folder');
        $this->set('editLink', ['admin' => true, 'plugin' => 'BaserCore', 'controller' => 'content_folders', 'action' => 'edit', $contentFolder->id, 'content_id' => $contentFolder->content->id]);
        $this->render($folderTemplate);
    }
}
