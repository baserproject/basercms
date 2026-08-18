<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * OAuth2 Scope (Protocol layer)
 */
class Scope implements ScopeEntityInterface
{

    /**
     * Trait
     */
    use EntityTrait;

    /**
     * Description
     * @var string
     */
    private string $description;

    /**
     * Constructor
     * @param string $identifier
     * @param string $description
     */
    public function __construct(string $identifier, string $description = '')
    {
        $this->identifier = $identifier;
        $this->description = $description;
    }

    /**
     * Get Description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * JSON Serialize
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->getIdentifier();
    }

}
