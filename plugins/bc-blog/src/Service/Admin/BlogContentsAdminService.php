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

namespace BcBlog\Service\Admin;

use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BcBlog\Service\BlogContentsService;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;

/**
 * BlogContentsAdminService
 */
class BlogContentsAdminService extends BlogContentsService implements BlogContentsAdminServiceInterface
{

    /**
     * 編集画面用の view 変数を取得
     * 
     * @param EntityInterface $blogContent
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $blogContent)
    {
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $blogContent = $this->BlogContents->constructEyeCatchSize($blogContent);
        $publishLink = null;
        if ($blogContent->content->status) {
            $publishLink = $contentsService->getUrl(
                $blogContent->content->url,
                true,
                $blogContent->content->site->useSubDomain
            );
        }
        return [
            'blogContent' => $blogContent,
            'publishLink' => $publishLink,
            'editorEnterBr' => BcSiteConfig::get('editor_enter_br')
        ];
    }

}
