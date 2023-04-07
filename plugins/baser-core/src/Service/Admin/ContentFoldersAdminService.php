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

namespace BaserCore\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;

/**
 * ContentFoldersAdminService
 */
class ContentFoldersAdminService extends ContentFoldersService implements ContentFoldersAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 編集画面に必要なデータを取得する
     * @param int $id
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $contentFolder): array
    {
        $theme = [Inflector::camelize(Configure::read('BcApp.coreFrontTheme'))];
        $site = $this->getService(SitesServiceInterface::class)
            ->findById($contentFolder->content->site_id)
            ->first();
        if (!empty($site) && $site->theme) {
            $theme[] = $site->theme;
        }
        return [
            'contentFolder' => $contentFolder,
            'folderTemplateList' => $this->getFolderTemplateList($contentFolder->content->id, $theme),
            'pageTemplateList' => $this->getService(PagesServiceInterface::class)
                ->getPageTemplateList($contentFolder->content->id, $theme)
        ];
    }

}
