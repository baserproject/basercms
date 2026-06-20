<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity;

use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * OAuth2 User (Protocol layer)
 */
class User implements UserEntityInterface
{

    /**
     * Identifier
     * @var string|int
     */
    protected string|int $identifier;

    /**
     * Get Identifier
     * @return string|int
     */
    public function getIdentifier(): string|int
    {
        return $this->identifier;
    }

    /**
     * Set Identifier
     * @param string|int $identifier
     */
    public function setIdentifier(string|int $identifier): void
    {
        $this->identifier = $identifier;
    }

}
