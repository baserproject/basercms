<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Lib;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Lib\OAuth2Util;
use Cake\Http\ServerRequest;

/**
 * OAuth2UtilTest
 */
class OAuth2UtilTest extends BcTestCase
{

    /**
     * test getIssuer が認可サーバーの issuer 識別子を返す
     *
     * RFC 8414 のメタデータで公開する issuer と同一の値でなければならない
     */
    public function testGetIssuer()
    {
        // SITE_URL が設定されている場合は Host ヘッダに関わらず SITE_URL から導出する
        $request = new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'evil.example.com',
                'HTTPS' => 'on',
                'HTTP_X_FORWARDED_PROTO' => 'https',
            ],
        ]);
        $siteUrl = rtrim((string)env('SITE_URL', ''), '/');
        $this->assertNotEmpty($siteUrl);
        $this->assertEquals($siteUrl . '/bc-mcp', OAuth2Util::getIssuer($request));
        $this->assertEquals($siteUrl, OAuth2Util::getBaseUrl($request));

        // SITE_URL が無い場合のみリクエストから導出する
        $this->withoutSiteUrl(function() {
            $this->assertHostBasedIssuer();
        });
    }

    /**
     * SITE_URL を一時的に外して処理を実行する
     */
    private function withoutSiteUrl(callable $callback): void
    {
        $original = env('SITE_URL');
        putenv('SITE_URL');
        unset($_ENV['SITE_URL'], $_SERVER['SITE_URL']);
        try {
            $callback();
        } finally {
            if ($original !== null && $original !== false) {
                putenv('SITE_URL=' . $original);
                $_ENV['SITE_URL'] = $original;
                $_SERVER['SITE_URL'] = $original;
            }
        }
    }

    private function assertHostBasedIssuer(): void
    {
        // baserCMS は TRUST_PROXY が有効な場合 https 検出器を静的に差し替え、
        // HTTPS ではなく X-Forwarded 系を参照する。どちらの環境でも https と
        // 判定されるよう両方を渡す
        $request = new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'example.com',
                'HTTPS' => 'on',
                'HTTP_X_FORWARDED_PROTO' => 'https',
            ],
        ]);

        $this->assertEquals('https://example.com/bc-mcp', OAuth2Util::getIssuer($request));
    }

    /**
     * test getIssuer は HTTP でもスキームを正しく扱う
     */
    public function testGetIssuerWithHttp()
    {
        $this->withoutSiteUrl(function() {
            $request = new ServerRequest([
                'environment' => [
                    'HTTP_HOST' => 'localhost:8080',
                    'HTTPS' => 'off',
                    'HTTP_X_FORWARDED_PROTO' => 'http',
                ],
            ]);

            $this->assertEquals('http://localhost:8080/bc-mcp', OAuth2Util::getIssuer($request));
        });
    }

    /**
     * test addIssuerToUrl が iss クエリを付与する
     *
     * RFC 9207。認可レスポンスに issuer を含める事で mix-up 攻撃を防ぐ
     */
    public function testAddIssuerToUrl()
    {
        $result = OAuth2Util::addIssuerToUrl(
            'https://claude.ai/callback?code=abc&state=xyz',
            'https://example.com/bc-mcp'
        );

        parse_str((string)parse_url($result, PHP_URL_QUERY), $query);
        $this->assertEquals('https://example.com/bc-mcp', $query['iss']);
        // 既存のクエリは保持される
        $this->assertEquals('abc', $query['code']);
        $this->assertEquals('xyz', $query['state']);
    }

    /**
     * test addIssuerToUrl はクエリが無い URL にも付与できる
     */
    public function testAddIssuerToUrlWithoutQuery()
    {
        $result = OAuth2Util::addIssuerToUrl(
            'https://claude.ai/callback',
            'https://example.com/bc-mcp'
        );

        parse_str((string)parse_url($result, PHP_URL_QUERY), $query);
        $this->assertEquals('https://example.com/bc-mcp', $query['iss']);
    }

    /**
     * test addIssuerToUrl はフラグメントを壊さない
     */
    public function testAddIssuerToUrlWithFragment()
    {
        $result = OAuth2Util::addIssuerToUrl(
            'https://claude.ai/callback#code=abc',
            'https://example.com/bc-mcp'
        );

        $this->assertStringContainsString('iss=', $result);
        $this->assertStringEndsWith('#code=abc', $result);
    }

}
