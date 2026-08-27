<?php
declare(strict_types=1);

namespace BcMcp\Service;

use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * クライアント登録のレート制限
 *
 * 動的クライアント登録は無認証で開いているため、無制限に行を増やせる。
 * IP 単位で回数を数え、上限を超えた登録を拒否する。
 *
 * 枠はキャッシュの有効期限（1時間）で切れる。登録のたびに書き直すため、
 * 実際には「最後の登録から1時間」で枠がリセットされる。
 *
 * 呼び出し側が渡すクライアント IP は、TRUST_PROXY（config/bootstrap.php 参照）が
 * 有効な場合 X-Forwarded-For ヘッダに依存する。前段プロキシが当該ヘッダを
 * 適切に上書きしない構成では、送信元がヘッダを偽装してこの制限を回避できる。
 */
class RegistrationRateLimiter
{

    /**
     * キャッシュ設定名
     */
    public const CACHE_CONFIG = 'bc_mcp_registration';

    /**
     * 上限に達しているかを判定する
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return bool
     */
    public function isExceeded(string $clientIp): bool
    {
        return $this->readCount($clientIp) >= $this->getMaxPerHour();
    }

    /**
     * 登録回数を1つ進める
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return void
     */
    public function hit(string $clientIp): void
    {
        Cache::write($this->buildKey($clientIp), $this->readCount($clientIp) + 1, self::CACHE_CONFIG);
    }

    /**
     * 現在の登録回数を取得する
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return int
     */
    private function readCount(string $clientIp): int
    {
        return (int)Cache::read($this->buildKey($clientIp), self::CACHE_CONFIG);
    }

    /**
     * キャッシュキーを組み立てる
     *
     * IP アドレスをそのままキーにすると使用できない文字が混ざるため、
     * ハッシュ化する。
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return string
     */
    private function buildKey(string $clientIp): string
    {
        return 'count_' . sha1($clientIp);
    }

    /**
     * 1時間あたりの上限件数を取得する
     *
     * @return int
     */
    private function getMaxPerHour(): int
    {
        return (int)Configure::read('BcMcp.registration.maxPerHour', 10);
    }
}
