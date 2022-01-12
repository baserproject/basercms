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

namespace BaserCore\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use DateTime;
use BaserCore\Model\Entity\PasswordRequest;
use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersTable
 * @package BaserCore\Model\Table
 * @property BelongsTo $UserGroups
 * @method User get($primaryKey, $options = [])
 * @method User newEntity($data = null, array $options = [])
 * @method User[] newEntities(array $data, array $options = [])
 * @method User|bool save(EntityInterface $entity, $options = [])
 * @method User patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method User[] patchEntities($entities, array $data, array $options = [])
 * @method User findOrCreate($search, callable $callback = null, $options = [])
 * @mixin TimestampBehavior
 */
class PasswordRequestsTable extends Table
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('email')
            ->email('email', true, __d('baser', 'Eメールの形式が不正です。'))
            ->maxLength('email', 255, __d('baser', 'Eメールは255文字以内で入力してください。'))
            ->requirePresence('email', 'create', __d('baser', 'Eメールを入力してください。'))
            ->notEmptyString('email', __d('baser', 'Eメールを入力してください。'));
        return $validator;
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
    public function getEnableRequestData($requestKey)
    {
        return $this->find('all')
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
     * @param PasswordRequest $passwordRequest
     * @param string $password
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updatePassword(PasswordRequest $passwordRequest, string $password)
    {

        $passwordRequest = $this->find('all')
            ->where([
                'id' => $passwordRequest->id,
                'used' => 0,
            ])
            ->first();

        if ($passwordRequest === null) {
            return false;
        }
        $passwordRequest->used = 1;
        $this->save($passwordRequest);

        $users = TableRegistry::get('BaserCore.Users');
        $user = $users->find('all')
            ->where(['Users.id' => $passwordRequest->user_id])
            ->first();
        $user->password = $password;
        return $users->save($user);
    }


}
