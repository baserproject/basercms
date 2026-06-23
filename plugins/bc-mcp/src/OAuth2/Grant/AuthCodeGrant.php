<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Grant;

use BcMcp\OAuth2\RedirectUriValidators\RedirectUriValidator;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant as LeagueAuthCodeGrant;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Auth Code Grant
 *
 * league/oauth2-server 8.x の AuthCodeGrant を継承し、リダイレクト URI の検証だけを
 * 自前の {@see RedirectUriValidator}（league/uri の非推奨 API を使わない実装）に差し替える。
 *
 * 親クラスの `validateRedirectUri()` は `new \League\OAuth2\Server\RedirectUriValidators\RedirectUriValidator()`
 * を直接生成しており差し替え不可（注入経路が無い）ため、メソッドごと override する。
 * 検証ロジック自体は親と同一で、内部で使う検証器のみ差し替える。
 *
 * @see \League\OAuth2\Server\Grant\AbstractGrant::validateRedirectUri()
 */
class AuthCodeGrant extends LeagueAuthCodeGrant
{
    /**
     * Validate redirectUri from the request.
     *
     * @param string $redirectUri
     * @param ClientEntityInterface $client
     * @param ServerRequestInterface $request
     * @throws OAuthServerException
     */
    protected function validateRedirectUri(
        string $redirectUri,
        ClientEntityInterface $client,
        ServerRequestInterface $request
    ) {
        $validator = new RedirectUriValidator($client->getRedirectUri());
        if (!$validator->validateRedirectUri($redirectUri)) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidClient($request);
        }
    }
}
