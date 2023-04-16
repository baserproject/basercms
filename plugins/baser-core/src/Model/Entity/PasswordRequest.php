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

declare(strict_types=1);

namespace BaserCore\Model\Entity;

use Cake\Utility\Security;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PasswordRequest
 * @property int $id
 * @property int $user_id
 * @property string $request_key
 * @property int $used
 * @property FrozenTime|null $modified
 * @property FrozenTime|null $created
 */
class PasswordRequest extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'user_id' => true,
        'auth_prefix' => true,
        'request_key' => true,
        'used' => true,
        'modified' => true,
        'created' => true,
    ];

    /**
     * Set Request Key
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setRequestKey(): void
    {
        $this->request_key = $this->makeRequestKey();
    }

    /**
     * Make Request Key
     *
     * @param int $length
     * @return false|string
     * @checked
     * @unitTest
     * @noTodo
     */
    private function makeRequestKey($length = 48): string
    {
        return Security::randomString($length);
    }

}
