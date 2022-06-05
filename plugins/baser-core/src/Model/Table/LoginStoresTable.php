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

namespace BaserCore\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Security;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\ORM\RulesChecker;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class LoginStoresTable
 * @package BaserCore\Model\Table
 */
class LoginStoresTable extends Table
{
    /**
     * key name
     * @var string
     */
    const KEY_NAME = 'LoginStoreKey';

    /**
     * expire
     * @var string
     */
    const EXPIRE = '+1 year';

    /**
     * key length
     * @var int
     */
    private $keyLength = 100;

    /**
     * initialize
     * @param array $config
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
     * buildRules
     * @param RulesChecker $rules
     * @return RulesChecker
     * @checked
     * @noTodo
     * @unitTest
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['store_key'], 'キーが重複しています。'));
        return $rules;
    }

    /**
     * ログインキー追加
     *
     * @param string $prefix ログイン対象
     * @param int $user_id ユーザID
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addKey(string $prefix, int $user_id): EntityInterface
    {
        $allready = $this->find('all')
            ->where([
                'prefix' => $prefix,
                'user_id' => $user_id,
            ])->first();
        if ($allready !== null) {
            return $this->refresh($prefix, $user_id);
        }

        $loginStore = $this->newEmptyEntity();
        $loginStore->prefix = $prefix;
        $loginStore->user_id = $user_id;
        $loginStore->store_key = Security::randomString($this->keyLength);
        $i = 0;
        while($this->save($loginStore) === false) {
            $loginStore->store_key = Security::randomString($this->keyLength);
            if ($i++ > 100) {
                throw new \Exception(__d('baser', '不明なエラー'));
            }
        }
        return $loginStore;
    }

    /**
     * ログインキー削除
     *
     * @param string $prefix ログイン対象
     * @param int $user_id ユーザID
     * @return int 削除行数
     * @checked
     * @noTodo
     * @unitTest
     */
    public function removeKey(string $prefix, int $user_id): int
    {
        return $this->deleteAll([
            'prefix' => $prefix,
            'user_id' => $user_id
        ]);
    }

    /**
     * 有効キー取得
     *
     * @param string $key
     * @return Entity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEnableLoginStore($key): ?Entity
    {
        $loginStoreList = $this->find()
            ->where(['store_key' => $key])
            ->orderAsc('created');
        foreach($loginStoreList as $loginStore) {
            $expired = strtotime(self::EXPIRE, strtotime($loginStore->created));
            if ($expired < time()) {
                // 期限切れは削除
                $this->delete($loginStore);
            } else {
                return $loginStore;
            }
        }
        return null;
    }

    /**
     * ログインキーを新しくする
     *
     * @param string $prefix ログイン対象
     * @param int $user_id ユーザID
     * @return EntityInterface|null
     */
    public function refresh($prefix, $user_id): EntityInterface
    {
        $loginStore = $this->find('all')
            ->where([
                'prefix' => $prefix,
                'user_id' => $user_id,
            ])
            ->first();
        if ($loginStore === null) {
            throw new \Exception(__d('baser', '更新データが見つかりませんでした'));
        }
        $this->delete($loginStore);
        return $this->addKey($prefix, $user_id);
    }
}
