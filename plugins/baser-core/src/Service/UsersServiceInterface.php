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

use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;

/**
 * Interface UsersServiceInterface
 * @package BaserCore\Service
 */
interface UsersServiceInterface
{

    /**
     * ユーザーを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * ユーザー一覧を取得
     * @param ServerRequest $request
     * @return Query
     */
    public function getIndex(ServerRequest $request): Query;

    /**
     * 新しいデータの初期値を取得する
     * @return EntityInterface
     */
    public function getNew(): User;

   /**
     * 新規登録する
     * @param ServerRequest $request
     * @return EntityInterface|false
     */
    public function create(ServerRequest $request);

    /**
     * 編集する
     * @param EntityInterface $target
     * @param ServerRequest $request
     * @return mixed
     */
    public function update(EntityInterface $target, ServerRequest $request);

    /**
     * 削除する
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

}
