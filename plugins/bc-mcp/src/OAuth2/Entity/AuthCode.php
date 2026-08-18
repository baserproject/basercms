<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * OAuth2 Authorization Code (Protocol layer)
 */
class AuthCode implements AuthCodeEntityInterface
{

    /**
     * Trait
     */
    use AuthCodeTrait, EntityTrait, TokenEntityTrait;

    /**
     * Redirect URI
     * @var string
     */
    protected $redirectUri;

    /**
     * Code Challenge
     * @var string|null
     */
    protected $codeChallenge;

    /**
     * Code Challenge Method
     * @var string
     */
    protected $codeChallengeMethod = 'plain';

    /**
     * Get Redirect URI
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * Set Redirect URI
     * @param string $uri
     * @return void
     */
    public function setRedirectUri($uri): void
    {
        $this->redirectUri = $uri;
    }

    /**
     * Get Code Challenge
     * @return string|null
     */
    public function getCodeChallenge(): ?string
    {
        return $this->codeChallenge;
    }

    /**
     * Set Code Challenge
     * @param string|null $codeChallenge
     * @return void
     */
    public function setCodeChallenge(?string $codeChallenge): void
    {
        $this->codeChallenge = $codeChallenge;
    }

    /**
     * Get Code Challenge Method
     * @return string
     */
    public function getCodeChallengeMethod(): string
    {
        return $this->codeChallengeMethod;
    }

    /**
     * Set Code Challenge Method
     * @param string $codeChallengeMethod
     * @return void
     */
    public function setCodeChallengeMethod(string $codeChallengeMethod): void
    {
        $this->codeChallengeMethod = $codeChallengeMethod;
    }

}
