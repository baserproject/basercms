<?php
declare(strict_types=1);

namespace BcMcp\Model\Entity;

use Cake\ORM\Entity;

/**
 * Oauth2RefreshToken Entity
 *
 * @property int $id
 * @property string $token_id
 * @property string $access_token_id
 * @property bool $revoked
 * @property \Cake\I18n\DateTime $expires_at
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class Oauth2RefreshToken extends Entity
{

    /**
     * accessible properties
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'token_id' => true,
        'access_token_id' => true,
        'revoked' => true,
        'expires_at' => true,
        'created' => true,
        'modified' => true,
    ];

}
