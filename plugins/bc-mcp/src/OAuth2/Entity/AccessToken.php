<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity;

use BcMcp\OAuth2\Entity\Trait\Rfc9068AccessTokenTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * OAuth2 Access Token (Protocol layer)
 * RFC 9068 準拠のアクセストークンを生成
 */
class AccessToken implements AccessTokenEntityInterface
{

    /**
     * Trait
     */
    use Rfc9068AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    /**
     * Set Client
     * @var ClientEntityInterface
     */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Add Scope
     * @param ScopeEntityInterface $scope
     * @return void
     */
    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scopes[$scope->getIdentifier()] = $scope;
    }

    /**
     * Set User Identifier
     * @param string|int|null $identifier
     */
    public function setUserIdentifier($identifier): void
    {
        $this->userIdentifier = $identifier;
    }

}
