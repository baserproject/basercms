<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\RedirectUriValidators;

use League\OAuth2\Server\RedirectUriValidators\RedirectUriValidatorInterface;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\Uri;

/**
 * Redirect URI Validator
 *
 * league/oauth2-server 8.x 同梱の RedirectUriValidator と同等の検証を行うが、
 * league/uri 7.x で非推奨となった `Uri::createFromString()` を使わず、
 * 非推奨でない `Uri::new()` を使用する。
 *
 * oauth2-server 9.x（psr/http-message ^2.0 必須）へ上げられない依存事情
 * （php-mcp/server → react/http が psr/http-message ^1.0 に固定）により、
 * PHP 8.1 互換を維持したまま当該非推奨を発生させないための差し替え実装。
 *
 * @see \League\OAuth2\Server\RedirectUriValidators\RedirectUriValidator
 */
class RedirectUriValidator implements RedirectUriValidatorInterface
{
    /**
     * @var array
     */
    private array $allowedRedirectUris;

    /**
     * New validator instance for the given uri
     *
     * @param string|array $allowedRedirectUri
     */
    public function __construct($allowedRedirectUri)
    {
        if (is_string($allowedRedirectUri)) {
            $this->allowedRedirectUris = [$allowedRedirectUri];
        } elseif (is_array($allowedRedirectUri)) {
            $this->allowedRedirectUris = $allowedRedirectUri;
        } else {
            $this->allowedRedirectUris = [];
        }
    }

    /**
     * Validates the redirect uri.
     *
     * @param string $redirectUri
     * @return bool Return true if valid, false otherwise
     */
    public function validateRedirectUri($redirectUri)
    {
        if ($this->isLoopbackUri($redirectUri)) {
            return $this->matchUriExcludingPort($redirectUri);
        }

        return $this->matchExactUri($redirectUri);
    }

    /**
     * According to section 7.3 of rfc8252, loopback uris are:
     *   - "http://127.0.0.1:{port}/{path}" for IPv4
     *   - "http://[::1]:{port}/{path}" for IPv6
     *
     * @param string $redirectUri
     * @return bool
     */
    private function isLoopbackUri($redirectUri)
    {
        try {
            $uri = Uri::new($redirectUri);
        } catch (SyntaxError $e) {
            return false;
        }

        return $uri->getScheme() === 'http'
            && (in_array($uri->getHost(), ['127.0.0.1', '[::1]'], true));
    }

    /**
     * Find an exact match among allowed uris
     *
     * @param string $redirectUri
     * @return bool Return true if an exact match is found, false otherwise
     */
    private function matchExactUri($redirectUri)
    {
        return in_array($redirectUri, $this->allowedRedirectUris, true);
    }

    /**
     * Find a match among allowed uris, allowing for different port numbers
     *
     * @param string $redirectUri
     * @return bool Return true if a match is found, false otherwise
     */
    private function matchUriExcludingPort($redirectUri)
    {
        $parsedUrl = $this->parseUrlAndRemovePort($redirectUri);

        foreach ($this->allowedRedirectUris as $allowedRedirectUri) {
            if ($parsedUrl === $this->parseUrlAndRemovePort($allowedRedirectUri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse an url like \parse_url, excluding the port
     *
     * @param string $url
     * @return string
     */
    private function parseUrlAndRemovePort($url)
    {
        $uri = Uri::new($url);

        return (string)$uri->withPort(null);
    }
}
