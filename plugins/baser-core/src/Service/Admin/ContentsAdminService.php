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

use BaserCore\Form\ContentsSearchForm;
use BaserCore\Model\Entity\Content;
use BaserCore\Service\ContentsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
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
            'publishLink' => $this->isAllowPublish($content)? $content->url : null,
            'parentContents' => $this->getContentFolderList($content->site_id, $options),
            'fullUrl' => $this->getUrl($content->url, true, $content->site->use_subdomain),
            'authorList' => $this->getService(UsersServiceInterface::class)->getList()
        ];
    }

    /**
     * content_actions エレメント用の変数を取得
     * @param $content
     * @param $currentAction
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForContentActions($content, $currentAction)
    {
        return [
            'isAvailablePreview' => $this->_isAvailablePreview($content),
            'isAvailableDelete' => $this->_isAvailableDelete($content),
            'currentAction' => $currentAction,
            'isAlias' => (bool) $content->alias_id
        ];
    }

    /**
     * プレビューが利用可能か確認
     * @param Content $content
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _isAvailablePreview($content)
    {
        $setting = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type);
        return (!empty($setting['preview']));
    }

    /**
     * コンテンツが削除可能が確認
     * @param Content $content
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _isAvailableDelete($content)
    {
        if(!BcUtil::loginUser()->user_groups) return false;
        $path = BcUtil::getPrefix() . 'baser-core/contents/delete';
        $userGroupIds = Hash::extract(BcUtil::loginUser()->user_groups, '{n}.id');
        $service = $this->getService(PermissionsServiceInterface::class);
        return $service->check($path, $userGroupIds) && !$content->site_root;
    }

    /**
     * 一覧画面用のデータを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(ServerRequest $request, $contents)
    {
        $templates = [1 => 'index_tree', 2 => 'index_table'];
        $currentUser = BcUtil::loginUser();
        $isUseMoveContents = false;
        foreach ($currentUser->user_groups as $group) {
          if ($isUseMoveContents = $group->use_move_contents) break;
        }
        $contentsSearch = new ContentsSearchForm();
        $contentsSearch->setData($request->getQuery());
        return [
            'typeList' => $this->getTypes(),
            'authorList' => $this->getService(UsersServiceInterface::class)->getList(),
            'isContentDeletable' => $this->isContentDeletable(),
            'folders' => $this->getContentFolderList($request->getQuery('site_id'), ['conditions' => ['site_root' => false]]),
            'template' => $templates[$request->getQuery('list_type')],
            'editInIndexDisabled' => false,  // 2022/10/16 ryuring 今の所未実装のため false 固定,
            'isUseMoveContents' => $isUseMoveContents,
            'contents' => $contents,
            'contentsSearch' => $contentsSearch
        ];
    }

    /**
     * ゴミ箱用の view 変数を生成する
     * @param $contents
     * @return array
     * @noTodo
     * @checked
     */
    public function getViewVarsForTrashIndex($contents)
    {
        return [
            'contents' => $contents,
            'isContentDeletable' => $this->isContentDeletable(),
            'isUseMoveContents' => false,
            'editInIndexDisabled' => false
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
