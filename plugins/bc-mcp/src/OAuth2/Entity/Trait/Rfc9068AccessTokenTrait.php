<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Entity\Trait;

use BcMcp\OAuth2\Jwt\Rfc9068JwtBuilder;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * RFC 9068 対応のAccessTokenTrait
 *
 * JSON Web Token (JWT) Profile for OAuth 2.0 Access Tokens (RFC 9068) に準拠した
 * アクセストークンを生成するためのTrait
 */
trait Rfc9068AccessTokenTrait
{
    /**
     * @var CryptKey
     */
    private $privateKey;

    /**
     * @var Configuration
     */
    private $jwtConfiguration;

    /**
     * Set the private key used to encrypt this access token.
     */
    public function setPrivateKey(CryptKey $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * Initialise the JWT Configuration.
     */
    public function initJwtConfiguration()
    {
        $this->jwtConfiguration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->privateKey->getKeyContents(), $this->privateKey->getPassPhrase() ?? ''),
            InMemory::plainText('empty', 'empty')
        );
    }

    /**
     * RFC 9068準拠のアクセストークンのためのissuer URLを取得
     *
     * @return string
     */
    private function getIssuer(): string
    {
        return env('SITE_URL') . 'bc-mcp/oauth2';
    }

    /**
     * RFC 9068準拠のアクセストークンのためのResource URLを取得
     * @return string
     */
    private function getResource(): string
    {
        return env('SITE_URL') . 'bc-mcp';
    }

    /**
     * Generate a JWT from the access token (RFC 9068 compliant)
     *
     * @return Token
     */
    private function convertToJWT()
    {
        $this->initJwtConfiguration();

        // kidを生成（公開鍵のSHA-256ハッシュを使用）
        $kid = $this->generateKid();

        // RFC 9068のBuilderを使用
        $builder = new Rfc9068JwtBuilder($this->jwtConfiguration);
        $scope = $this->getScopeString();
        return $builder
            ->withHeader('kid', $kid)                                   // kid (Key ID)
            ->issuedBy($this->getIssuer())                              // iss (issuer)
            ->permittedFor($this->getResource())         // aud (audience)
            ->identifiedBy($this->getIdentifier())                      // jti (JWT ID)
            ->issuedAt(new DateTimeImmutable())                         // iat (issued at)
            ->canOnlyBeUsedAfter(new DateTimeImmutable())               // nbf (not before)
            ->expiresAt($this->getExpiryDateTime())                     // exp (expires at)
            ->relatedTo((string)$this->getUserIdentifier())           // sub (subject)
            ->withClaim('client_id', $this->getClient()->getIdentifier()) // client_id (RFC 9068 必須)
            ->withClaim('scopes', $scope)               // scopes oauth2-server 2.0 互換
            ->withClaim('scope', $scope)               // scope (RFC 9068 推奨、文字列形式)
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    /**
     * 公開鍵からkid (Key ID) を生成
     *
     * @return string
     */
    private function generateKid(): string
    {
        // 公開鍵の取得
        $publicKeyPath = CONFIG . 'jwt.pem';
        $publicKey = file_get_contents($publicKeyPath);
        $details = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));

        // kidを生成（公開鍵のSHA-256ハッシュを使用）
        $publicKeyDer = $details['key'];
        return rtrim(strtr(base64_encode(hash('sha256', $publicKeyDer, true)), '+/', '-_'), '=');
    }

    /**
     * スコープを文字列形式で取得（RFC 9068準拠）
     *
     * @return string
     */
    private function getScopeString(): string
    {
        $scopes = $this->getScopes();
        $scopeNames = [];

        foreach($scopes as $scope) {
            $scopeNames[] = $scope->getIdentifier();
        }

        return implode(' ', $scopeNames);
    }

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return $this->convertToJWT()->toString();
    }

    /**
     * @return ClientEntityInterface
     */
    abstract public function getClient();

    /**
     * @return DateTimeImmutable
     */
    abstract public function getExpiryDateTime();

    /**
     * @return string|int
     */
    abstract public function getUserIdentifier();

    /**
     * @return ScopeEntityInterface[]
     */
    abstract public function getScopes();

    /**
     * @return string
     */
    abstract public function getIdentifier();
}
