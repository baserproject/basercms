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

use Cake\Datasource\EntityInterface;
use Cake\Utility\Security;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\ORM\RulesChecker;

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
