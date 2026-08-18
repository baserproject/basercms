<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.7
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\OAuth2\Jwt;

use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

/**
 * RFC 9068 準拠の JWT ビルダー
 *
 * JSON Web Token (JWT) Profile for OAuth 2.0 Access Tokens (RFC 9068) に準拠した
 * JWTを構築するためのビルダークラス
 */
class Rfc9068JwtBuilder
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * コンストラクタ
     *
     * @param Configuration $configuration JWT設定
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->builder = $configuration->builder();
    }

    /**
     * iss (issuer) クレームを設定
     * RFC 9068では必須
     *
     * @param string $issuer 発行者のURL
     * @return self
     */
    public function issuedBy(string $issuer): self
    {
        $this->builder = $this->builder->issuedBy($issuer);
        return $this;
    }

    /**
     * aud (audience) クレームを設定
     * RFC 9068では必須（クライアントID）
     *
     * @param string $audience 対象者
     * @return self
     */
    public function permittedFor(string $audience): self
    {
        $this->builder = $this->builder->permittedFor($audience);
        return $this;
    }

    /**
     * jti (JWT ID) クレームを設定
     * RFC 9068では必須（ユニークなトークン識別子）
     *
     * @param string $id JWT ID
     * @return self
     */
    public function identifiedBy(string $id): self
    {
        $this->builder = $this->builder->identifiedBy($id);
        return $this;
    }

    /**
     * iat (issued at) クレームを設定
     * RFC 9068では必須
     *
     * @param DateTimeImmutable $issuedAt 発行日時
     * @return self
     */
    public function issuedAt(DateTimeImmutable $issuedAt): self
    {
        $this->builder = $this->builder->issuedAt($issuedAt);
        return $this;
    }

    /**
     * nbf (not before) クレームを設定
     * RFC 9068では推奨
     *
     * @param DateTimeImmutable $notBefore 有効開始日時
     * @return self
     */
    public function canOnlyBeUsedAfter(DateTimeImmutable $notBefore): self
    {
        $this->builder = $this->builder->canOnlyBeUsedAfter($notBefore);
        return $this;
    }

    /**
     * exp (expires at) クレームを設定
     * RFC 9068では必須
     *
     * @param DateTimeImmutable $expiration 有効期限
     * @return self
     */
    public function expiresAt(DateTimeImmutable $expiration): self
    {
        $this->builder = $this->builder->expiresAt($expiration);
        return $this;
    }

    /**
     * sub (subject) クレームを設定
     * RFC 9068では推奨（ユーザー識別子）
     *
     * @param string $subject サブジェクト
     * @return self
     */
    public function relatedTo(string $subject): self
    {
        $this->builder = $this->builder->relatedTo($subject);
        return $this;
    }

    /**
     * カスタムクレームを設定
     * RFC 9068では、アプリケーション固有のクレームを追加可能
     *
     * @param string $name クレーム名
     * @param mixed $value クレーム値
     * @return self
     */
    public function withClaim(string $name, $value): self
    {
        $this->builder = $this->builder->withClaim($name, $value);
        return $this;
    }

    /**
     * JWTヘッダーにkid (Key ID) を設定
     *
     * @param string $kid Key ID
     * @return self
     */
    public function withHeader(string $name, string $value): self
    {
        $this->builder = $this->builder->withHeader($name, $value);
        return $this;
    }

    /**
     * JWTトークンを生成
     * RFC 9068に準拠したトークンを作成
     *
     * @param Signer $signer 署名アルゴリズム
     * @param Key $key 署名キー
     * @return Token
     */
    public function getToken(Signer $signer, Key $key): Token
    {
        return $this->builder->getToken($signer, $key);
    }

}
