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

use ArrayObject;
use DateTime;
use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
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
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('email')
            ->email('email', true, __d('baser', 'Eメールの形式が不正です。'))
            ->maxLength('email', 255, __d('baser', 'Eメールは255文字以内で入力してください。'))
            ->notEmptyString('email', __d('baser', 'Eメールを入力してください。'));
        return $validator;
    }

    /**
     * Validation New
     * @param Validator $validator
     * @return Validator
     */
    public function validationChange(Validator $validator): Validator
    {
        $validator
            ->scalar('password')
            ->minLength('password', 6, __d('baser', 'パスワードは6文字以上で入力してください。'))
            ->maxLength('password', 255, __d('baser', 'パスワードは255文字以内で入力してください。'))
            ->add('password', [
                'passwordAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus', ' \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*'],
                    'provider' => 'bc',
                    'message' => __d('baser', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')
            ]])
            ->add('password', [
                'passwordConfirm' => [
                    'rule' => ['confirm', ['password_1', 'password_2']],
                    'provider' => 'bc',
                    'message' => __d('baser', __d('baser', 'パスワードが同じものではありません。'))
        ]]);

        return $validator;
    }

    /**
     * 有効なパスワード変更情報を取得する
     *
     * @param [type] $requestKey
     * @return void
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


}
