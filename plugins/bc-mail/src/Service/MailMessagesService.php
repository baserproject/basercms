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
use BaserCore\Error\BcException;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcMail\Model\Entity\MailContent;
use BcMail\Model\Entity\MailMessage;
use BcMail\Model\Table\MailMessagesTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * MailMessagesService
 * @property BcDatabaseService $BcDatabaseService
 * @property MailMessagesTable $MailMessages
 */
class MailMessagesService implements MailMessagesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Constructor
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $this->MailMessages = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
    }

    /**
     * メールメッセージの初期セットアップを実行する
     *
     * 利用する前に必ず実行しなければならない
     *
     * @param int $mailContentId
     * @checked
     * @noTodo
     */
    public function setup(int $mailContentId, $postData = [])
    {
        $this->MailMessages->setup($mailContentId, $postData);
    }

    /**
     * メールメッセージを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function get(int $id)
    {
        return $this->MailMessages->get($id);
    }

    /**
     * メールメッセージの一覧を取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     */
    public function getIndex(array $queryParams = [])
    {
        $options = array_merge([
            'limit' => null,
            'direction' => 'DESC',    // 並び方向
            'order' => 'created',    // 並び順対象のフィールド
        ], $queryParams);
        $query = $this->MailMessages->find();
        if ($options['order']) {
            $query->order("{$options['order']} {$options['direction']}");
            unset($options['order'], $options['direction']);
        }
        if (!empty($options['limit'])) {
            $query->limit($options['limit']);
            unset($options['limit']);
        }
        return $query;
    }

    /**
     * 新規データ作成
     *
     * @param EntityInterface|MailContent $mailContent
     * @param array|MailMessage $postData
     * @return EntityInterface
     */
    public function create(EntityInterface $mailContent, $postData)
    {
        if (!$postData instanceof EntityInterface) {
            // newEntity() だと配列が消えてしまうため、エンティティクラスで直接変換
            $entity = new MailMessage($postData, ['source' => 'BcMail.MailMessages']);
        } else {
            $entity = $postData;
        }
        $validateEntity = $this->MailMessages->patchEntity($this->MailMessages->newEmptyEntity(), $entity->toArray());
        if (!$validateEntity->getErrors()) {
            $mailFieldsTable = TableRegistry::getTableLocator()->get('BcMail.MailFields');
            $mailFields = $mailFieldsTable->find()->where(['MailFields.mail_content_id' => $mailContent->id, 'MailFields.use_field' => true])->all();
            $this->MailMessages->convertToDb($mailFields, $entity);
            if ($mailContent->save_info) {
                return $this->MailMessages->saveOrFail($entity);
            }
        }
        return $entity;
    }

    /**
     * メールメッセージを編集する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface|null
     */
    public function update(EntityInterface $entity, array $postData): ?EntityInterface
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core', '送信できるデータ量を超えています。合計で {0} 以内のデータを送信してください。', ini_get('post_max_size')));
        }
        $entity = $this->MailMessages->patchEntity($entity, $postData);
        return $this->MailMessages->saveOrFail($entity);
    }

    /**
     * メールメッセージを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(int $id): bool
    {
        $blogPost = $this->MailMessages->get($id);
        return $this->MailMessages->delete($blogPost);
    }

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->MailMessages->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * メッセージフィールドを追加する
     *
     * @param int $mailContentId
     * @param string $fieldName
     * @return bool
     * @checked
     * @noTodo
     */
    public function addMessageField(int $mailContentId, string $fieldName): bool
    {
        $table = $this->MailMessages->createTableName($mailContentId);
        return $this->BcDatabaseService->addColumn($table, $fieldName, 'text');
    }

    /**
     * テーブル名を生成する
     * int型でなかったら強制終了
     * @param int $mailContentId
     * @return string
     * @checked
     * @noTodo
     */
    public function createTableName(int $mailContentId)
    {
        $mailContentId = (int)$mailContentId;
        if (!is_int($mailContentId)) {
            throw new BcException(__d('baser_core', 'MailMessageService::createTableName() の引数 $mailContentId は int 型しか受けつけていません。'));
        }
        return 'mail_message_' . $mailContentId;
    }

    /**
     * メッセージテーブルを作成する
     *
     * @param int $mailContentId
     * @return boolean
     * @checked
     * @noTodo
     */
    public function createTable(int $mailContentId)
    {
        $schema = [
            'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        ];
        $table = $this->MailMessages->createTableName($mailContentId);
        if ($this->BcDatabaseService->tableExists($table)) {
            $this->BcDatabaseService->dropTable($table);
        }
        return $this->BcDatabaseService->createTable($table, $schema);
    }

    /**
     * メッセージテーブルを削除する
     *
     * @param int $mailContentId
     * @return boolean
     * @checked
     * @noTodo
     */
    public function dropTable(int $mailContentId)
    {
        $table = $this->MailMessages->createTableName($mailContentId);
        if (!$this->BcDatabaseService->tableExists($table)) {
            return true;
        }
        return $this->BcDatabaseService->dropTable($table);
    }

    /**
     * メッセージファイルのフィールドを削除する
     *
     * @param int $mailContentId
     * @param string $field
     * @return array|bool
     * @checked
     * @noTodo
     */
    public function deleteMessageField(int $mailContentId, string $field)
    {
        $table = $this->MailMessages->createTableName($mailContentId);
        return $this->BcDatabaseService->removeColumn($table, $field);
    }

    /**
     * メッセージファイルのフィールドを編集する
     *
     * @param int $mailContentId
     * @param string $oldFieldName
     * @param string $newfieldName
     * @return array|bool
     * @checked
     * @noTodo
     */
    public function renameMessageField(int $mailContentId, string $oldFieldName, string $newfieldName)
    {
        $table = $this->MailMessages->createTableName($mailContentId);
        return $this->BcDatabaseService->renameColumn($table, $oldFieldName, $newfieldName);
    }


    /**
     * メッセージ保存用テーブルのフィールドを最適化する
     * 初回の場合、id/created/modifiedを追加する
     * 2回目以降の場合は、最後のカラムに追加する
     *
     * @param int $mailContentId
     * @return boolean
     * @checked
     * @noTodo
     */
    public function construction(int $mailContentId): bool
    {
        $mailFieldClass = TableRegistry::getTableLocator()->get('BcMail.MailFields');
        // フィールドリストを取得
        $mailFields = $mailFieldClass->find()->where(['MailFields.mail_content_id' => $mailContentId])->all();
        if (!$this->BcDatabaseService->tableExists($this->MailMessages->createTableName($mailContentId))) {
            /* 初回の場合 */
            $this->createTable($mailContentId);
            $this->construction($mailContentId);
        } else {
            /* 2回目以降の場合 */
            $messageFields = TableRegistry::getTableLocator()
                ->get('BaserCore.App')
                ->getConnection()
                ->getSchemaCollection()
                ->describe($this->MailMessages->createTableName($mailContentId))->columns();
            foreach($mailFields as $mailField) {
                if (!in_array($mailField->field_name, $messageFields)) {
                    $this->addMessageField($mailContentId, $mailField->field_name);
                }
            }
        }
        return true;
    }

    /**
     * 初期値の設定をする
     *
     * @param int $mailContentId
     * @param array $params
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function getNew(int $mailContentId, array $params): EntityInterface
    {
        /** @var MailFieldsService $mailFieldsService */
        $mailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        $mailFields = $mailFieldsService->getIndex($mailContentId, ['use_field' => true]);

        $messageArray = [];
        if ($mailFields) {
            foreach($mailFields as $mailField) {
                // 対象フィールドがあれば、バリデートグループごとに配列に格納する
                if (is_null($mailField->default_value) || $mailField->default_value === "") continue;
                if ($mailField->type === 'multi_check') {
                    $messageArray[$mailField['field_name']][0] = $mailField->default_value;
                } else {
                    $messageArray[$mailField['field_name']] = $mailField->default_value;
                }
            }
        }

        if ($params) {
            foreach($params as $key => $value) {
                $messageArray[$key] = h(base64UrlsafeDecode($value));
            }
        }
        return $this->MailMessages->newEntity($messageArray, ['validate' => false]);
    }

    /**
     * 自動変換
     * 確認画面で利用される事も踏まえてバリデートを通す為の
     * 可能な変換処理を行う。
     *
     * @param array $data
     * @return array $data
     * @checked
     * @noTodo
     */
    public function autoConvert(int $mailContentId, array $data): array
    {
        /** @var MailFieldsService $mailFieldsService */
        $mailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        $mailFields = $mailFieldsService->getIndex($mailContentId, ['use_field' => true]);

        foreach($mailFields as $mailField) {
            if (!$mailField['use_field']) continue;
            $value = null;
            if (isset($data[$mailField->field_name]) && $data[$mailField->field_name] !== "") {
                $value = $data[$mailField->field_name];
            }
            if ($value !== null) {
                // 半角処理
                if ($mailField->auto_convert === 'CONVERT_HANKAKU') {
                    $value = mb_convert_kana($value, 'a');
                }
                // 全角処理
                if ($mailField->auto_convert === 'CONVERT_ZENKAKU') {
                    $value = mb_convert_kana($value, 'AK');
                }
                // サニタイズ
                if (!is_array($value)) {
                    $value = str_replace('<!--', '&lt;!--', $value);
                }
                // TRIM
                if (!is_array($value)) {
                    $value = trim($value);
                }
            }
            $data[$mailField->field_name] = $value;
        }
        return $data;
    }

}
