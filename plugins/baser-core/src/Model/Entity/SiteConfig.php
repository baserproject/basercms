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

use Cake\ORM\Entity as EntityAlias;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SiteConfig
 */
class SiteConfig extends EntityAlias
{

    /**
     * Accessible
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Hidden
     *
     * SMTP パスワードを API レスポンス（toArray() / JSON）に含めない。
     * プロパティの直接参照（BcMailer の SMTP 設定）や管理画面のフォームには影響しない。
     *
     * @var array
     */
    protected array $_hidden = [
        'smtp_password',
    ];

}
