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
namespace BaserCore\Service;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\Utility\Hash;

/**
 * ContentsAdminService
 */
class ContentsAdminService extends ContentsService implements ContentsAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 編集画面用のデータを取得
     * BcAdminContentsComponent より呼び出される
     * @param $content
     * @param $name
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit($content)
    {
        $options = [];
        if ($content->type === 'ContentFolder') $options['excludeId'] = $content->id;
        $related = false;
        if (($content->site->relate_main_site && $content->main_site_content_id && $content->alias_id) ||
            $content->site->relate_main_site && $content->main_site_content_id && $content->type == 'ContentFolder') {
            $related = true;
        }
        return [
            'content' => $content,
            'related' => $related,
            'currentSiteId' => $content->site_id,
            'mainSiteId' => $content->site->main_site_id,
            'publishLink' => $content->url,
            'parentContents' => $this->getContentFolderList($content->site_id, $options),
            'fullUrl' => $this->getUrl($content->url, true, $content->site->use_subdomain),
            'authorList' => $this->getService(UsersServiceInterface::class)->getList()
        ];
    }

    /**
     * 一覧画面用のデータを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex()
    {
        return [
            'typeList' => $this->getTypes(),
            'authorList' => $this->getService(UsersServiceInterface::class)->getList(),
            'isContentDeletable' => $this->isContentDeletable()
        ];
    }

    /**
     * 登録されているタイプの一覧を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTypes(): array
    {
        $createdItems = BcUtil::getContentsItem();
        $types = [];
        foreach($createdItems as $key => $value) {
            $types[$key] = $value['title'];
        }
        return $types;
    }

    /**
     * コンテンツが削除可能かどうか
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isContentDeletable(): bool
    {
        $loginUser = BcUtil::loginUser();
        if(!$loginUser) return false;
        $userGroups = $loginUser->user_groups;
        if ($userGroups) {
            $userGroupIds = Hash::extract($userGroups, '{n}.id');
            if ($this->getService(PermissionsServiceInterface::class)->check(BcUtil::getPrefix() . '/baser-core/contents/delete', $userGroupIds)) {
                return true;
            }
        }
        return false;
    }
}
