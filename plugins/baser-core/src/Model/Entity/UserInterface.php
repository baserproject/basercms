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

namespace BaserCore\Model\Entity;

/**
 * Interface UserInterface
 */
interface UserInterface
{

    /**
     * スーパーユーザーかどうか
     * @return bool
     */
    public function isSuper(): bool;

    /**
     * 管理者ユーザーかどうか
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * 表示名称を取得
     * @return mixed
     */
    public function getDisplayName(): string;

    /**
     * 認証領域のプレフィックスを配列で取得する
     * @return mixed
     */
    public function getAuthPrefixes(): array;

}
