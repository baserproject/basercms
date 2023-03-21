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
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcMail\Model\Table\MailContentsTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * MailContentsService
 * @property MailContentsTable $MailContents
 */
class MailContentsService implements MailContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Construct
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->MailContents = TableRegistry::getTableLocator()->get("BcMail.MailContents");
    }

    /**
     * 初期値を取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function getNew()
    {
        return $this->MailContents->newEntity([
            'sender_name' => __d('baser_core', '送信先名を入力してください'),
            'subject_user' => __d('baser_core', 'お問い合わせ頂きありがとうございます'),
            'subject_admin' => __d('baser_core', 'お問い合わせを頂きました'),
            'layout_template' => 'default',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'use_description' => true,
            'ssl_on' => false,
            'save_info' => true
        ], [
            'validate' => false,
        ]);
    }

    /**
     * メールフォーム登録
     *
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     */
    public function create(array $postData, $options = []): ?EntityInterface
    {
        $mailContent = $this->getNew();
        $mailContent = $this->MailContents->patchEntity($mailContent, $postData, $options);
        $this->MailContents->getConnection()->begin();
        /* @var \BcMail\Model\Entity\MailContent $mailContent */
        try {
            $mailContent = $this->MailContents->saveOrFail($mailContent);
            /** @var MailMessagesService $mailMessagesService */
            $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
            if (!$mailMessagesService->createTable($mailContent->id)) {
                $this->MailContents->getConnection()->rollback();
                throw new BcException(__d('baser_core', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。'));
            }
        } catch (\Throwable $e) {
            $this->MailContents->getConnection()->rollback();
            throw $e;
        }
        $this->MailContents->getConnection()->commit();
        return $mailContent;
    }

    /**
     * メールコンテンツを更新する
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     */
    public function update(EntityInterface $entity, array $postData): ?EntityInterface
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core', '送信できるデータ量を超えています。合計で {0} 以内のデータを送信してください。', ini_get('post_max_size')));
        }
        if (empty($postData['sender_1_'])) {
            $postData['sender_1'] = '';
        }
        $entity = $this->MailContents->patchEntity($entity, $postData);
        /* @var \BcMail\Model\Entity\MailContent $mailContent */
        return $this->MailContents->saveOrFail($entity);
    }

    /**
     * メールフォームを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(int $id): bool
    {
        $entity = $this->get($id, ['contain' => []]);
        /** @var MailMessagesService $mailMessagesService */
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $this->MailContents->getConnection()->begin();
        try {
            $mailMessagesService->dropTable($id);
            $result = $this->MailContents->delete($entity);
            $this->MailContents->getConnection()->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->MailContents->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * メールコンテンツを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function get(int $id, array $options = [])
    {
        $options = array_merge([
            'contain' => [
                'Contents' => ['Sites'],
                'MailFields'
            ]
        ], $options);
        return $this->MailContents->get($id, ['contain' => $options['contain']]);
    }

    /**
     * メールコンテンツ一覧を取得する
     *
     * @return Query
     * @checked
     * @noTodo
     */
    public function getIndex(array $queryParams = []): Query
    {
        $options = array_merge([
            'num' => null,
            'limit' => null,
            'direction' => 'DESC',    // 並び方向
            'order' => 'posted',    // 並び順対象のフィールド
            'sort' => null,
            'id' => null,
            'no' => null,
            'status' => null,
            'contain' => ['Contents']
        ], $queryParams);

        if (!empty($options['num'])) $options['limit'] = $options['num'];
        if (!empty($options['sort'])) $options['order'] = $options['sort'];
        unset($options['num'], $options['sort']);

        // ステータス
        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->MailContents->Contents->getConditionAllowPublish();
        }

        if (is_null($options['contain'])) {
            $fields = $this->MailContents->getSchema()->columns();
            $query = $this->MailContents->find()->contain('Contents')->select($fields);
        }else{
            $query = $this->MailContents->find()->contain($options['contain']);
        }

        if (!is_null($options['limit'])) $query->limit($options['limit']);
        return $query->where($conditions);
    }

    /**
     * リストデータ取得
     * @return array
     *
     * @checked
     * @noTodo
     * @checked
     */
    public function getList()
    {
        return $this->MailContents->find('list', [
            'keyField' => 'id',
            'valueField' => 'content.title'
        ])->contain(['Contents'])->toArray();
    }

    /**
     * ブログをコピーする
     *
     * @param array $postData
     * @return EntityInterface $result
     * @checked
     * @unitTest
     */
    public function copy($postData)
    {
        return $this->MailContents->copy(
            $postData['entity_id'],
            $postData['parent_id'],
            $postData['title'],
            BcUtil::loginUser()->id,
            $postData['site_id']
        );
    }

}
