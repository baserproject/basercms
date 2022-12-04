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

namespace BcMail\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcMail\Model\Entity\MailContent;
use BcMail\Model\Entity\MailMessage;
use Cake\Datasource\EntityInterface;

/**
 * MailMessagesServiceInterface
 */
interface MailMessagesServiceInterface
{

    /**
     * メールメッセージの初期セットアップを実行する
     *
     * 利用する前に必ず実行しなければならない
     *
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setup(int $mailContentId);

    /**
     * メールメッセージを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id);

    /**
     * メールメッセージの一覧を取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []);

    /**
     * 新規データ作成
     *
     * @param EntityInterface|MailContent $mailContent
     * @param array|MailMessage $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(EntityInterface $mailContent, $postData);

    /**
     * メッセージフィールドを追加する
     *
     * @param int $mailContentId
     * @param string $fieldName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addMessageField(int $mailContentId, string $fieldName): bool;

    /**
     * テーブル名を生成する
     * int型でなかったら強制終了
     * @param int $mailContentId
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createTableName(int $mailContentId);

    /**
     * メッセージテーブルを作成する
     *
     * @param int $mailContentId
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createTable(int $mailContentId);

    /**
     * メッセージテーブルを削除する
     *
     * @param int $mailContentId
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function dropTable(int $mailContentId);

    /**
     * メッセージファイルのフィールドを削除する
     *
     * @param int $mailContentId
     * @param string $field
     * @return array|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteMessageField(int $mailContentId, string $field);

    /**
     * メッセージファイルのフィールドを編集する
     *
     * @param int $mailContentId
     * @param string $oldFieldName
     * @param string $newfieldName
     * @return array|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function renameMessageField(int $mailContentId, string $oldFieldName, string $newfieldName);

    /**
     * メッセージ保存用テーブルのフィールドを最適化する
     * 初回の場合、id/created/modifiedを追加する
     * 2回目以降の場合は、最後のカラムに追加する
     *
     * @param int $mailContentId
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function construction(int $mailContentId): bool;

    /**
     * 初期値の設定をする
     *
     * @param int $mailContentId
     * @param array $params
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $mailContentId, array $params): EntityInterface;

    /**
     * 自動変換
     * 確認画面で利用される事も踏まえてバリデートを通す為の
     * 可能な変換処理を行う。
     *
     * @param array $data
     * @return array $data
     * @checked
     * @noTodo
     * @unitTest
     */
    public function autoConvert(int $mailContentId, array $data): array;

    /**
     * メールメッセージを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool;

}
