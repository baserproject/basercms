<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

/**
 * OAuth2 Refresh Token (Protocol layer)
 */
class RefreshToken implements RefreshTokenEntityInterface
{

    /**
     * Trait
     */
    use RefreshTokenTrait, EntityTrait;

}
