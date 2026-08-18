<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * OAuth2 Client (Protocol layer)
 */
class Client implements ClientEntityInterface
{

    /**
     * Trait
     */
    use EntityTrait, ClientTrait;

    /**
     * Name
     * @var
     */
    protected $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isConfidential = true;
    }

    /**
     * Set Name
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set Redirect URI
     * @param array<string> $uri
     */
    public function setRedirectUri(array $uri): void
    {
        $this->redirectUri = $uri;
    }

    /**
     * Get Redirect URI
     * @return array<string>
     */
    public function getRedirectUri(): array
    {
        return $this->redirectUri;
    }

    /**
     * Set Confidential Client
     * @var bool
     */
    public function setIsConfidential(bool $isConfidential): void
    {
        $this->isConfidential = $isConfidential;
    }

    /**
     * Is Confidential
     * @return bool
     */
    public function isConfidential(): bool
    {
        return $this->isConfidential;
    }

}
