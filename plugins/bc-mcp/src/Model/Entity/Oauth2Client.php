<?php
declare(strict_types=1);

namespace BcMcp\Model\Entity;

use Cake\ORM\Entity;

/**
 * Oauth2Client Entity
 *
 * @property int $id
 * @property string $client_id
 * @property string|null $client_secret
 * @property string $name
 * @property array $redirect_uris
 * @property array $grants
 * @property array $scopes
 * @property bool $is_confidential
 * @property string|null $registration_access_token
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class Oauth2Client extends Entity
{

    /**
     * accessible properties
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'client_id' => true,
        'client_secret' => true,
        'name' => true,
        'redirect_uris' => true,
        'grants' => true,
        'scopes' => true,
        'is_confidential' => true,
        'registration_access_token' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * hidden properties
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'client_secret',
        'registration_access_token',
    ];

    /**
     * json fields
     *
     * @var array<string>
     */
    protected array $_jsonFields = [
        'redirect_uris',
        'grants',
        'scopes',
    ];

    /**
     * Dynamic Client Registration response payload（RFC 7591）
     *
     * メモ:
     * - client_secret は「登録時のみ」返すのが原則（再取得・更新時は返さない）。
     * - client_secret_expires_at は有効期限がない場合 0 を返す実装もあるが、本実装では未設定時は省略。
     * - token_endpoint_auth_method は is_confidential に応じて既定値を補完（true=client_secret_basic / false=none）。
     * - 以下の項目は任意（クライアントメタデータ）。提供された場合のみ反映する:
     *   contacts, client_uri, logo_uri, tos_uri, policy_uri, software_id, software_version
     *
     * @return array
     */
    public function toRegistrationResponse(): array
    {
        $scopes = $this->scopes ?? [];
        // 追加の一時プロパティは存在すれば利用
        $registrationClientUri = $this->get('registration_client_uri');
        $tokenEndpointAuthMethod = $this->get('token_endpoint_auth_method') ?? ($this->is_confidential? 'client_secret_basic' : 'none');
        $clientIdIssuedAt = $this->get('client_id_issued_at') ?? ($this->created? $this->created->getTimestamp() : null);
        $clientSecretExpiresAt = $this->get('client_secret_expires_at');
        $contacts = $this->get('contacts');
        $clientUri = $this->get('client_uri');
        $logoUri = $this->get('logo_uri');
        $tosUri = $this->get('tos_uri');
        $policyUri = $this->get('policy_uri');
        $softwareId = $this->get('software_id');
        $softwareVersion = $this->get('software_version');

        $response = [
            'client_id' => $this->client_id,
            // シークレットは登録時のみ返す仕様だが、ここでは保持していれば返す
            'client_secret' => $this->client_secret ?? null,
            'client_id_issued_at' => $clientIdIssuedAt,
            'client_secret_expires_at' => $clientSecretExpiresAt,
            'registration_access_token' => $this->registration_access_token ?? null,
            'registration_client_uri' => $registrationClientUri,
            'token_endpoint_auth_method' => $tokenEndpointAuthMethod,
            'client_name' => $this->name,
            'redirect_uris' => $this->redirect_uris ?? [],
            'grant_types' => $this->grants ?? [],
            'scope' => implode(' ', $scopes),
            // 任意メタデータ（提供時のみ出力）
            'contacts' => $contacts,
            'client_uri' => $clientUri,
            'logo_uri' => $logoUri,
            'tos_uri' => $tosUri,
            'policy_uri' => $policyUri,
            'software_id' => $softwareId,
            'software_version' => $softwareVersion,
        ];

        // null を含めたくないキーをフィルタ（client_secret_expires_at は null を許可）
        foreach(['client_secret', 'registration_access_token', 'registration_client_uri', 'contacts', 'client_uri', 'logo_uri', 'tos_uri', 'policy_uri', 'software_id', 'software_version'] as $nullableKey) {
            if ($response[$nullableKey] === null) {
                unset($response[$nullableKey]);
            }
        }

        return $response;
    }

    // 旧サービス層からの呼び出しに対応するための簡易ゲッター
    public function getName(): string
    {
        return (string)$this->name;
    }

    public function getRedirectUri(): array
    {
        return (array)($this->redirect_uris ?? []);
    }

    public function getGrants(): array
    {
        return (array)($this->grants ?? []);
    }

    public function getScopes(): array
    {
        return (array)($this->scopes ?? []);
    }

    public function getRegistrationAccessToken(): ?string
    {
        return $this->registration_access_token ?? null;
    }

    public function getRegistrationClientUri(): ?string
    {
        return $this->get('registration_client_uri');
    }

    public function getClientIdIssuedAt(): ?int
    {
        return $this->get('client_id_issued_at');
    }

    public function getClientSecretExpiresAt(): ?int
    {
        return $this->get('client_secret_expires_at');
    }

    public function getTokenEndpointAuthMethod(): ?string
    {
        return $this->get('token_endpoint_auth_method');
    }

    public function getContacts(): array
    {
        return (array)($this->get('contacts') ?? []);
    }

    public function getClientUri(): ?string
    {
        return $this->get('client_uri');
    }

    public function getLogoUri(): ?string
    {
        return $this->get('logo_uri');
    }

    public function getTosUri(): ?string
    {
        return $this->get('tos_uri');
    }

    public function getPolicyUri(): ?string
    {
        return $this->get('policy_uri');
    }

    public function getSoftwareId(): ?string
    {
        return $this->get('software_id');
    }

    public function getSoftwareVersion(): ?string
    {
        return $this->get('software_version');
    }

    public function getSecret(): ?string
    {
        return $this->client_secret ?? null;
    }

    public function getIdentifier(): string
    {
        return (string)$this->client_id;
    }
}
