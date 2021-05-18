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

namespace BaserCore\Service;

use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;

/**
 * Class UserGroupManageService
 * @package BaserCore\Service
 * @property UserGroupsTable $UserGroups
 */
class UserGroupManageService extends UserGroupsService implements UserGroupManageServiceInterface
{
    /**
     * ユーザーグループを取得する
     * @param int $id
     * @return EntityInterface
     */

    public function get($id): EntityInterface
    {
        return parent::get($id);
    }

    /**
     * ユーザーグループ全件取得する
     * @param array $options
     * @return Query
     */
    public function getIndex($options = []): Query
    {
        return parent::getIndex($options);
    }

    /**
     * 新規登録する
     * @param ServerRequest $request
     * @return EntityInterface|false
     */
    public function create(ServerRequest $request)
    {
        return parent::create($request);
    }

    /**
     * 編集する
     * @param EntityInterface $target
     * @param ServerRequest $request
     * @return mixed
     */
    public function update(EntityInterface $target, ServerRequest $request)
    {
        return parent::update($target, $request);
    }

    /**
     * 削除する
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return parent::delete($id);
    }
}
