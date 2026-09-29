<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Event;

use ArrayObject;
use BaserCore\Event\BcModelEventListener;
use BcMcp\OAuth2\Repository\OAuth2UserRepository;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;

/**
 * BcMcpModelEventListener
 *
 * ユーザーが無効化・削除された際に、そのユーザーの OAuth2 トークンを失効させる。
 * トークンの検証時にもユーザーの状態を確認するが、失効させておくことで
 * 無効化されたユーザーのトークンが DB に有効なまま残らないようにする
 */
class BcMcpModelEventListener extends BcModelEventListener
{

    /**
     * 登録イベント
     * @var array
     */
    public $events = [
        'BaserCore.Users.afterSave',
        'BaserCore.Users.afterDelete',
    ];

    /**
     * ユーザー保存後
     *
     * 無効化（status = false）された場合はトークンを失効させる
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return void
     */
    public function baserCoreUsersAfterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (!$entity->id) return;
        if (isset($entity->status) && !$entity->status) {
            OAuth2UserRepository::revokeTokensByUserId($entity->id);
        }
    }

    /**
     * ユーザー削除後
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return void
     */
    public function baserCoreUsersAfterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (!$entity->id) return;
        OAuth2UserRepository::revokeTokensByUserId($entity->id);
    }

}
