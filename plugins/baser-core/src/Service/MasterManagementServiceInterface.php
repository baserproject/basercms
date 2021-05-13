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

use Cake\Http\ServerRequest;
use Cake\Datasource\EntityInterface;

/**
 * Interface MasterManagementServiceInterface
 * @package BaserCore\Service
 */
interface MasterManagementServiceInterface
{

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

    /**
     * 新規追加画面用のデータを取得する
     * @return array
     */
    public function getIndexDisplayData(): array;

    /**
     * 新規追加画面用のデータを取得する
     * @return array
     */
    public function getAddDisplayData(): array;

    /**
     * 編集画面用のデータを取得する
     * @return array
     */
    public function getEditDisplayData(): array;

}
