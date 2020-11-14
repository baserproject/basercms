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
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
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

    public function setRequestKey()
    {
        $this->request_key = $this->makeRequestKey();
    }

    private function makeRequestKey($length = 48)
    {
        return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
    }

}
