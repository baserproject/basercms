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
use Cake\ORM\Query;

/**
 * Interface PageServiceInterface
 * @package BaserCore\Service
 */
interface PageServiceInterface
{

    /**
     * 固定ページを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * 固定ページをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     */
    public function getTrash($id);

    // /**
    //  * 新規登録する
    //  * @param array $data
    //  * @return EntityInterface
    //  * @throws \Cake\ORM\Exception\PersistenceFailedException
    //  */
    // public function create(string $message): EntityInterface;

    // /**
    //  * DBログ一覧を取得
    //  * @param array $queryParams
    //  * @return Query
    //  */
    // public function getIndex(array $queryParams): Query;

    /**
	 * 本文にbaserが管理するタグを追加する
	 *
	 * @param string $id ID
	 * @param string $contents 本文
	 * @param string $title タイトル
	 * @param string $description 説明文
	 * @param string $code コード
	 * @return string 本文の先頭にbaserCMSが管理するタグを付加したデータ
	 */
	public function addBaserPageTag($id, $contents, $title, $description, $code);

    /**
     * 固定ページテンプレートリストを取得する
     *
     * @param int $contentId
     * @param array|string $plugins
     * @return array
     */
    public function getPageTemplateList($contentId, $plugins);
}
