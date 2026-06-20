<?php
declare(strict_types=1);

namespace BcMcp\Model\Entity;

use Cake\ORM\Entity;

/**
 * Oauth2AuthCode Entity
 *
 * @property int $id
 * @property string $code
 * @property string $user_id
 * @property string $client_id
 * @property string $redirect_uri
 * @property string|null $scopes
 * @property bool $revoked
 * @property \Cake\I18n\DateTime $expires_at
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class Oauth2AuthCode extends Entity
{

    /**
     * accessible properties
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'code' => true,
        'user_id' => true,
        'client_id' => true,
        'redirect_uri' => true,
        'scopes' => true,
        'revoked' => true,
        'expires_at' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * スコープを配列として取得
     *
     * @return array
     */
    public function getScopesArray(): array
    {
        if (empty($this->scopes)) {
            return [];
        }
        return explode(' ', trim($this->scopes));
    }

    /**
     * スコープを文字列として設定
     *
     * @param array $scopes
     * @return void
     */
    public function setScopesFromArray(array $scopes): void
    {
        $this->scopes = implode(' ', $scopes);
    }

}
