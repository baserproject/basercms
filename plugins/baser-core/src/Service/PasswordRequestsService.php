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

namespace BaserCore\Service;

use BaserCore\Mailer\PasswordRequestMailer;
use BaserCore\Model\Entity\PasswordRequest;
use BaserCore\Model\Table\PasswordRequestsTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use DateTime;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PasswordRequestsService
 */
class PasswordRequestsService implements PasswordRequestsServiceInterface
{

    /**
     * PasswordRequestsTable
     * @var PasswordRequestsTable|\Cake\ORM\Table
     */
    public PasswordRequestsTable|\Cake\ORM\Table $PasswordRequests;

    /**
     * コンストラクタ
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->PasswordRequests = TableRegistry::getTableLocator()->get('BaserCore.PasswordRequests');
    }

    /**
     * 空の新規エンティティを取得する
     * 
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->PasswordRequests->newEmptyEntity();
    }

    /**
     * 単一のエンティティを取得する
     * 
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->PasswordRequests->get($id);
    }

    /**
     * パスワードリクエストを発行する
     *
     * ユーザーIDとリクエストキーを設定してパスワードリクエストを発行しDBに保存する。
     * 発行時には、パスワード再発行用のURLを記載したメールを送信する。
     *
     * @param EntityInterface|PasswordRequest $entity
     * @param array $postData
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update($entity, $postData): ?EntityInterface
    {
        $passwordRequest = $this->PasswordRequests->patchEntity($entity, $postData);
        $usersTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $user = $usersTable->find()
            ->where(['Users.email' => $postData['email']])
            ->first();
        if (empty($user)) return false;
        $passwordRequest->user_id = $user->id;
        $passwordRequest->used = 0;
        $passwordRequest->setRequestKey();
        $result = $this->PasswordRequests->saveOrFail($passwordRequest);
        (new PasswordRequestMailer())->deliver($user, $passwordRequest);
        return $result;
    }

    /**
     * 有効なパスワード変更情報を取得する
     *
     * @param [type] $requestKey
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEnableRequestData($requestKey): ?EntityInterface
    {
        return $this->PasswordRequests->find()
            ->where([
                'PasswordRequests.request_key' => $requestKey,
                'PasswordRequests.created >' => new DateTime('-1 days'),
                'PasswordRequests.used' => 0,
            ])
            ->first();
    }

    /**
     * パスワードを変更する
     * 
     * @param EntityInterface|PasswordRequest $passwordRequest
     * @param array $postData
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updatePassword($passwordRequest, $postData): ?EntityInterface
    {
        $db = $this->PasswordRequests->getConnection();
        $db->begin();
        try {
            $usersTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
            $user = $usersTable->find()
                ->where(['Users.id' => $passwordRequest->user_id])
                ->first();
            $user = $usersTable->patchEntity(
                $user,
                $postData,
                ['validate' => 'passwordUpdate']
            );
            $result = $usersTable->saveOrFail($user);
            $passwordRequest->used = 1;
            $this->PasswordRequests->saveOrFail($passwordRequest);
            $db->commit();
            return $result;
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $db->rollback();
            throw $e;
        }
    }

}
