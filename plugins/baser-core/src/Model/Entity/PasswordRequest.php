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

declare(strict_types=1);

namespace BaserCore\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Class PasswordRequest
 * @package BaserCore\Model\Entity
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
     */
    private function makeRequestKey($length = 48): string
    {
        return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
    }

}
