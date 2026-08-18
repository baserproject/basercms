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

namespace BcMcp\Mcp;

/**
 * MCP リクエストのコンテキスト
 *
 * MCP のツールは JSON-RPC の引数だけを受け取るため、認証済みの操作者を知る
 * 手段がない。リクエストボディに引数を注入する方式は、2026-07-28 でヘッダと
 * ボディの一致が検証されるようになったため採らず、同一プロセス内のコンテキスト
 * として保持する。
 *
 * 値は必ず認証後に設定し、リクエストの終わりに clear() する。
 */
class McpContext
{

    /**
     * ログインユーザーID
     * @var int|null
     */
    private static ?int $loginUserId = null;

    /**
     * ログインユーザーIDを設定する
     *
     * @param int|null $userId ユーザーID
     * @return void
     */
    public static function setLoginUserId(?int $userId): void
    {
        self::$loginUserId = $userId;
    }

    /**
     * ログインユーザーIDを取得する
     *
     * @return int|null
     */
    public static function getLoginUserId(): ?int
    {
        return self::$loginUserId;
    }

    /**
     * コンテキストを破棄する
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$loginUserId = null;
    }

}
