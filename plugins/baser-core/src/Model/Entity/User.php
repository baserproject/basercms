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

namespace BaserCore\Model\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\I18n\Time as TimeAlias;
use Cake\ORM\Entity as EntityAlias;

/**
 * Class User
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $real_name_1
 * @property string $real_name_2
 * @property string $email
 * @property int $user_group_id
 * @property string $nickname
 * @property TimeAlias $created
 * @property TimeAlias $modified
 */
class User extends EntityAlias
{
    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Hidden
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

	/**
	 * Set Password
	 *
	 * @param $value
	 * @return bool|string
	 */
	protected function _setPassword($value)
    {
        if ($value) {
            $hasher = new DefaultPasswordHasher();
            return $hasher->hash($value);
        } else {
            return false;
        }
	}

}
