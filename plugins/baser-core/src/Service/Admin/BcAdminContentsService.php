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
use BaserCore\Model\Entity\Content;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;

/**
 * BcAdminContentsService
 */
class BcAdminContentsService implements BcAdminContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 編集画面用のデータを取得
     * BcAdminContentsComponent より呼び出される
     * @param Content $content
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $content): array
    {
        $options = [];
        if ($content->type === 'ContentFolder') $options['excludeId'] = $content->id;
        $related = false;
        if (($content->site->relate_main_site && $content->main_site_content_id && $content->alias_id) ||
            $content->site->relate_main_site && $content->main_site_content_id && $content->type == 'ContentFolder') {
            $related = true;
        }

        /** @var ContentsServiceInterface $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $publishLink = $contentsService->isAllowPublish($content)? $contentsService->getUrl($content->url, false, $content->site->use_subdomain) : null;

        /* @var \BaserCore\Service\SitesService $sitesService */
        $sitesService = $this->getService(SitesServiceInterface::class);

        return [
            'content' => $content,
            'related' => $related,
            'currentSiteId' => $content->site_id,
            'mainSiteId' => $content->site->main_site_id,
            'publishLink' => $publishLink,
            'parentContents' => $contentsService->getContentFolderList($content->site_id, $options),
            'fullUrl' => $contentsService->getUrl($content->url, true, $content->site->use_subdomain),
            'authorList' => $this->getService(UsersServiceInterface::class)->getList(),
            'sites' => $sitesService->getList(),
            'relatedContents' => $sitesService->getRelatedContents($content->id),
            'layoutTemplates' => $this->getLayoutTemplates($content)
        ];
    }

    /**
     * レイアウトテンプレートを取得する
     * @param Content $content
     * @return []|array
     * @checked
     * @noTodo
     */
    public function getLayoutTemplates(EntityInterface $content)
    {
        $theme = $content->site->theme;
        $templates = BcUtil::getTemplateList('layout', [$content->plugin, $theme]);
        $contentsService = $this->getService(ContentsAdminServiceInterface::class);
        if ($content->id != 1) {
            $parentTemplate = $contentsService->getParentLayoutTemplate($content->id);
            if (in_array($parentTemplate, $templates)) {
                unset($templates[$parentTemplate]);
            }
            $templates = array_merge($templates, ['' => __d('baser_core', '親フォルダの設定に従う') . '（' . $parentTemplate . '）']);
        }
        return $templates;
    }

}
