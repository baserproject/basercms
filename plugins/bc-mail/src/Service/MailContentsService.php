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
            'sender_name' => __d('baser', '送信先名を入力してください'),
            'subject_user' => __d('baser', 'お問い合わせ頂きありがとうございます'),
            'subject_admin' => __d('baser', 'お問い合わせを頂きました'),
            'layout_template' => 'default',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'use_description' => true,
            'auth_captcha' => false,
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
                throw new BcException(__d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。'));
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
            throw new BcException(__d('baser', '送信できるデータ量を超えています。合計で {0} 以内のデータを送信してください。', ini_get('post_max_size')));
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
        $entity = $this->get($id);
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
    public function get(int $id)
    {
        return $this->MailContents->get($id, ['contain' => [
            'Contents' => ['Sites'],
            'MailFields'
        ]]);
    }

    public function getIndex()
    {

    }

    public function getList()
    {

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
