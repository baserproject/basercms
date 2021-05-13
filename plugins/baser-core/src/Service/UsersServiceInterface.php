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
    public function getNew(): EntityInterface;

}
