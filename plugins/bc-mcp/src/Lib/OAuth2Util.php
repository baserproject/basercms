<?php

namespace BcMcp\Lib;

use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;

class OAuth2Util
{

    /**
     * 認可サーバーの issuer 識別子を取得する
     *
     * RFC 8414 のメタデータで公開する issuer と、RFC 9207 で認可レスポンスに
     * 付与する iss は同一の値でなければならないため、導出処理をここに集約する。
     *
     * @param \Cake\Http\ServerRequest $request リクエスト
     * @return string
     */
    public static function getIssuer(\Cake\Http\ServerRequest $request): string
    {
        $scheme = $request->is('https')? 'https' : 'http';
        $host = $request->getHeaderLine('Host');
        if (!$host) {
            $host = $request->getEnv('HTTP_HOST')?: 'localhost';
        }
        return $scheme . '://' . $host . '/bc-mcp';
    }

    /**
     * URL に iss クエリを付与する
     *
     * RFC 9207。認可レスポンスに issuer を含める事で mix-up 攻撃を防ぐ。
     * 2026-07-28 のクライアントは iss があれば検証が MUST とされている。
     *
     * @param string $url 対象の URL
     * @param string $issuer issuer 識別子
     * @return string
     */
    public static function addIssuerToUrl(string $url, string $issuer): string
    {
        $fragment = '';
        $hashPos = strpos($url, '#');
        if ($hashPos !== false) {
            $fragment = substr($url, $hashPos);
            $url = substr($url, 0, $hashPos);
        }
        $separator = str_contains($url, '?')? '&' : '?';
        return $url . $separator . 'iss=' . rawurlencode($issuer) . $fragment;
    }

    /**
     * CakePHPリクエストをPSR-7リクエストに変換
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function createPsr7Request(\Cake\Http\ServerRequest $request): \Psr\Http\Message\ServerRequestInterface
    {
        // 環境変数からサイトURLを取得
        $siteUrl = env('SITE_URL', 'https://localhost');
        $uri = $siteUrl . $request->getRequestTarget();

        // ヘッダーを取得
        $headers = [];
        foreach($request->getHeaders() as $name => $values) {
            if ($values) {
                $headers[$name] = $values;
            }
        }

        // ボディに載ったパラメータをそのまま引き継ぐ
        $postData = [];
        if ($request->is('post')) {
            $postData = $request->getData();
        }

        // ボディコンテンツを取得
        $body = Stream::create('');
        if ($request->is('post')) {
            if (!empty($postData)) {
                $bodyContent = http_build_query($postData);
                $body = Stream::create($bodyContent);
                $headers['Content-Type'] = ['application/x-www-form-urlencoded'];
            }
        }

        // PSR-7リクエストを作成
        $psrRequest = new ServerRequest(
            $request->getMethod(),
            $uri,
            $headers,
            $body
        );

        // クエリパラメータを設定（PKCEパラメータなどを含む）
        $queryParams = $request->getQueryParams();
        if ($request->getData('scope')) {
            // スコープがPOSTデータに含まれている場合、クエリパラメータに追加
            $queryParams['scope'] = $request->getData('scope');
        }
        if (!empty($queryParams)) {
            $psrRequest = $psrRequest->withQueryParams($queryParams);
        }

        // POSTデータをparsedBodyとして設定
        if ($request->is('post') && !empty($postData)) {
            $psrRequest = $psrRequest->withParsedBody($postData);
        }

        return $psrRequest;
    }
}
