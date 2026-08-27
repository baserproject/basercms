<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Validator;

use InvalidArgumentException;

/**
 * 登録時の redirect_uri を検証する
 *
 * 認可時の照合（league の RedirectUriValidator）とは責務が異なる。こちらは
 * 「そもそも登録を受け付けてよい URI か」を判定する。動的クライアント登録が
 * 無認証で開いているため、平文の http で任意のホストへコードを飛ばせる登録を
 * 入口で弾く必要がある。
 */
class RedirectUriRegistrationValidator
{

    /**
     * 登録できる redirect_uri の上限件数
     */
    public const MAX_URIS = 5;

    /**
     * http を許容するループバックホスト
     *
     * RFC 8252 は IP リテラルを推奨しているが、ローカル環境の baserCMS は
     * http で動くため localhost も許容する。リダイレクト先が利用者の端末内に
     * 限られるためリスクは小さい。
     */
    private const LOOPBACK_HOSTS = ['127.0.0.1', '::1', 'localhost'];

    /**
     * redirect_uri の配列を検証する
     *
     * 「登録を受け付けてよいか」の判定はこのクラスの責務のため、配列以外が
     * 渡された場合（JSON の redirect_uris が文字列や数値だった場合など）の
     * 型チェックもここで完結させる。呼び出し側の型ヒントを array にすると
     * TypeError（Exception を継承しないため呼び出し元の catch (Exception) で
     * 捕捉できず 500 になる）を招くため、引数は mixed で受けて内部で判定する。
     *
     * @param mixed $redirectUris redirect_uri の配列（を想定する値）
     * @return void
     * @throws \InvalidArgumentException
     */
    public function validate($redirectUris): void
    {
        if (!is_array($redirectUris)) {
            throw new InvalidArgumentException('redirect_uris must be an array');
        }
        if (!$redirectUris) {
            throw new InvalidArgumentException('redirect_uris is required');
        }
        if (count($redirectUris) > self::MAX_URIS) {
            throw new InvalidArgumentException(
                'redirect_uris must not exceed ' . self::MAX_URIS . ' entries'
            );
        }
        foreach($redirectUris as $uri) {
            $this->validateOne($uri);
        }
    }

    /**
     * 単一の redirect_uri を検証する
     *
     * @param mixed $uri redirect_uri
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateOne($uri): void
    {
        if (!is_string($uri) || !filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                'Invalid redirect_uri: ' . (is_string($uri)? $uri : gettype($uri))
            );
        }

        $parts = parse_url($uri);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            throw new InvalidArgumentException('Invalid redirect_uri: ' . $uri);
        }

        // RFC 6749: redirect_uri はフラグメントを含んではならない
        if (isset($parts['fragment'])) {
            throw new InvalidArgumentException('redirect_uri must not contain a fragment: ' . $uri);
        }

        $scheme = strtolower($parts['scheme']);
        if ($scheme === 'https') {
            return;
        }
        // parse_url は IPv6 のホストを角括弧付きで返す
        $host = strtolower(trim($parts['host'], '[]'));
        if ($scheme === 'http' && in_array($host, self::LOOPBACK_HOSTS, true)) {
            return;
        }

        throw new InvalidArgumentException(
            'redirect_uri must use https, or http with a loopback host: ' . $uri
        );
    }
}
