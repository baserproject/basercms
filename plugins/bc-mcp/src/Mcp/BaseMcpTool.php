<?php
declare(strict_types=1);

namespace BcMcp\Mcp;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * MCPツール・リソースの基底クラス
 *
 * 共通の戻り値作成メソッドとエラーハンドリングを提供
 */
abstract class BaseMcpTool
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * ツールの戻り値のスキーマ
     *
     * 各ツールは配列（エンティティを配列化したものや一覧）を返す。SDK は
     * outputSchema を宣言したツールに限り配列の戻り値を許容し、その値を
     * structuredContent として返しつつ JSON を TextContent にも載せる
     * （SEP-2106）。宣言が無いと戻り値は文字列か CallToolResult に限られる。
     *
     * 個々のツールの戻り値の構造はエンティティの構成に依存するため、
     * ここでは型のみを宣言する。
     */
    protected const OUTPUT_SCHEMA = ['type' => 'object'];

    /**
     * 読み取り専用ツールの注釈
     *
     * クライアントが読み取りと書き込みを区別できるようにする。Claude の
     * Research はツール呼び出しに都度承認を挟まないため、区別できる情報を
     * 提供する意味がある。readOnlyHint が true のとき、destructiveHint と
     * idempotentHint は意味を持たないため宣言しない。
     */
    protected const ANNOTATION_READ = [
        'readOnlyHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * 追加系ツールの注釈
     *
     * 追加のみで既存データを壊さない。同じ引数で繰り返すと重複が増えるため
     * 冪等ではない。
     */
    protected const ANNOTATION_CREATE = [
        'readOnlyHint' => false,
        'destructiveHint' => false,
        'idempotentHint' => false,
        'openWorldHint' => false,
    ];

    /**
     * 更新系ツールの注釈
     *
     * 既存データを上書きするため破壊的とみなす。同じ引数なら結果は同じ。
     */
    protected const ANNOTATION_UPDATE = [
        'readOnlyHint' => false,
        'destructiveHint' => true,
        'idempotentHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * 削除系ツールの注釈
     *
     * 削除済みのものを再度削除しても結果は変わらない。
     */
    protected const ANNOTATION_DELETE = [
        'readOnlyHint' => false,
        'destructiveHint' => true,
        'idempotentHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * 自身が提供するツールをサーバーに登録する
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    abstract public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer;

    /**
     * 操作者のユーザーIDを解決する
     *
     * MCP のツールは JSON-RPC の引数しか受け取らないため、認証済みの操作者は
     * McpContext から取得する。コンテキストが設定されている場合は常にそれを使い、
     * 引数の値は無視する（SDK は inputSchema に無いキーも名前一致でコールバック引数へ
     * 束縛するため、引数を優先するとクライアントが操作者を詐称できてしまう）。
     * 引数はコンテキストを持たない経路（テスト等）のためにのみ使う。
     *
     * @param int|null $loginUserId 引数で渡されたユーザーID
     * @return int|null
     */
    protected function resolveLoginUserId(?int $loginUserId = null): ?int
    {
        return McpContext::getLoginUserId() ?? $loginUserId;
    }

    /**
     * 作成者・投稿者として保存するユーザーIDを解決する
     *
     * 操作者以外を作成者に指定できるのはシステム管理者のみ。
     * それ以外のユーザーが別のユーザーを指定した場合は例外とする。
     *
     * @param int|null $requestedUserId 引数で指定されたユーザーID
     * @param int|null $loginUserId 操作者のユーザーID
     * @return int|null
     * @throws \Exception
     */
    protected function resolveActorId(?int $requestedUserId, ?int $loginUserId): ?int
    {
        if ($requestedUserId === null || $requestedUserId === $loginUserId) {
            return $loginUserId ?? $requestedUserId;
        }
        if ($loginUserId === null) {
            return $requestedUserId;
        }
        if (!$this->isAdminLoginUser($loginUserId)) {
            throw new \Exception('作成者に他のユーザーを指定できるのはシステム管理者のみです');
        }
        return $requestedUserId;
    }

    /**
     * 操作者がシステム管理者かどうかを判定する
     *
     * @param int|null $loginUserId
     * @return bool
     */
    protected function isAdminLoginUser(?int $loginUserId): bool
    {
        if (!$loginUserId) return false;
        $user = TableRegistry::getTableLocator()->get('BaserCore.Users')
            ->find()->where(['Users.id' => $loginUserId])->contain(['UserGroups'])->first();
        return $user? BcUtil::isAdminUser($user) : false;
    }

    /**
     * 一覧取得の件数を上限内に収める
     *
     * limit に上限が無いと全件を取得してメモリを消費させることができるため、上限でクランプする
     *
     * @param int|null $limit
     * @param int $default 省略時の件数
     * @param int $max 上限
     * @return int
     */
    protected function clampLimit(?int $limit, int $default = 20, int $max = 100): int
    {
        if ($limit === null || $limit < 1) return $default;
        return min($limit, $max);
    }

    /**
     * 成功時の戻り値を作成
     *
     * @param mixed $content 戻り値のコンテンツ
     * @param array $meta 追加のメタデータ（paginationなど）
     * @return array MCP仕様に準拠した成功レスポンス
     */
    protected function createSuccessResponse($content, array $meta = [], $message = '', $userId = null): array
    {
        if($message) {
            $this->saveDblog($userId, $message);
        }
        return array_merge($content, $meta);
    }

    /**
     * 操作ログを保存する
     * @param $userId
     * @param $message
     * @return void
     */
    protected function saveDblog($userId, $message)
    {
        try {
            // 操作ログの操作者は認証済みのコンテキストを優先し、引数で詐称できないようにする
            $userId = McpContext::getLoginUserId() ?? $userId;
            $data = [
                'message' => $message,
                'controller' => 'McpProxy',
                'action' => 'index',
                'user_id' => $userId
            ];
            $dbLogsTable = TableRegistry::getTableLocator()->get('BaserCore.Dblogs');
            $dblog = $dbLogsTable->newEntity($data);
            $dbLogsTable->saveOrFail($dblog);
        } catch (\Exception) {}
    }

    /**
     * エラー時の戻り値を作成
     *
     * @param string $message エラーメッセージ
     * @param \Throwable|null $exception 例外オブジェクト（トレース情報用）
     * @return array MCP仕様に準拠したエラーレスポンス
     */
    protected function createErrorResponse(string $message, ?\Throwable $exception = null): array
    {
        if ($exception) {
            // スタックトレースはクライアントへ返さずログに記録する。
            // DB や PHP 内部の例外は内部情報（テーブル名・パス等）を含むため、メッセージも定型文に丸める
            \Cake\Log\Log::write('error', $message . "\n" . $exception->getTraceAsString(), ['scope' => ['mcp']]);
            if ($exception instanceof \Error
                || $exception instanceof \PDOException
                || $exception instanceof \Cake\Database\Exception\DatabaseException) {
                $message = 'ツールの実行中にエラーが発生しました。詳細はサーバーのログを確認してください。';
            }
        }
        return [
            'content' => $message
        ];
    }

    /**
     * try-catchブロックを共通化してエラーハンドリングを実行
     *
     * @param callable $callback 実行する処理
     * @return array MCP仕様に準拠したレスポンス
     */
    protected function executeWithErrorHandling(callable $callback): array
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            // \Error を捕捉しない場合、MCPサーバー側でトレースが失われ、
            // 発生箇所を特定できなくなるため、\Throwable にて捕捉する
            return $this->createErrorResponse($e->getMessage(), $e);
        }
    }

    /**
     * 値がファイルアップロード可能な形式かどうかを判定
     *
     * ファイルアップロード可能な形式は data: URI と http(s) URL の2方式のみ
     * （チャンクアップロードは廃止したため、拡張子付きの文字列は対象外）
     *
     * @param mixed $value 判定対象の値
     * @return bool ファイルアップロード可能な形式の場合true
     */
    protected function isFileUploadable($value): bool
    {
        if (is_array($value)) {
            return true;
        }

        // Base64データの場合
        if (strpos($value, 'data:') === 0) {
            return true;
        }

        // URLの場合（http/httpsで始まる）
        if (preg_match('/^https?:\/\//', $value)) {
            return true;
        }

        return false;
    }

    /**
     * ファイルアップロード処理
     *
     * @param string $fileData 画像の URL、または data: URI 形式の base64 データ
     * @param string $fieldName フィールド名（ログ用）
     * @return array|false アップロード情報の配列、失敗時はfalse
     */
    protected function processFileUpload(string $fileData, string $fieldName = 'file'): array|false
    {
        try {
            // Base64データの場合
            if (strpos($fileData, 'data:') === 0) {
                return $this->processBase64File($fileData);
            }

            // URLの場合はダウンロードして処理
            if (preg_match('/^https?:\/\//', $fileData)) {
                return $this->processUrlFile($fileData);
            }

            throw new \Exception('不正なファイルデータ形式です: ' . $fileData);

        } catch (\Exception $e) {
            // エラーログを出力
            if (!BcUtil::isTest()) {
                error_log($fieldName . 'の処理に失敗しました: ' . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Base64エンコードされたファイルデータを処理
     *
     * @param string $base64Data base64エンコードされたファイルデータ
     * @return array アップロード情報の配列
     * @throws \Exception
     */
    protected function processBase64File(string $base64Data): array
    {
        // data:mime/type;base64,... の形式から必要な情報を抽出
        if (!preg_match('/^data:([^;]+);base64,(.+)$/', $base64Data, $matches)) {
            throw new \Exception('不正なbase64ファイル形式です');
        }

        $mimeType = $matches[1];
        $encodedData = $matches[2];

        // base64として有効かチェック
        if (!preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $encodedData)) {
            throw new \Exception('base64デコードに失敗しました');
        }

        $decodedData = base64_decode($encodedData, true);

        if ($decodedData === false) {
            throw new \Exception('base64デコードに失敗しました');
        }

        // 申告された MIME タイプは信用せず、内容から判定できる場合はそれを優先する。
        // 内容がテキストなのに画像等の MIME タイプが申告された場合（HTML を画像として
        // 保存させる等）は不一致として拒否する
        $actualMimeType = $this->detectMimeTypeFromContent($decodedData);
        if ($actualMimeType === 'text/plain') {
            if (!in_array($mimeType, ['text/plain', 'text/csv'], true)) {
                throw new \Exception('サポートされていないファイル形式です: ' . $mimeType);
            }
        } elseif ($actualMimeType !== 'application/octet-stream') {
            $mimeType = $actualMimeType;
        }

        // ファイル拡張子を取得
        $extension = $this->getExtensionFromMimeType($mimeType);

        // ファイル形式のチェック（URL 取り込みと同じ許可リストを適用する）
        if (!$this->isAllowedExtension($extension)) {
            throw new \Exception('サポートされていないファイル形式です: ' . $extension);
        }

        // 一意のファイル名を生成
        $fileName = 'upload_' . uniqid() . '.' . $extension;
        $tmpPath = sys_get_temp_dir() . '/' . $fileName;

        // 一時ファイルに保存
        if (file_put_contents($tmpPath, $decodedData) === false) {
            throw new \Exception('一時ファイルの作成に失敗しました');
        }

        // アップロード情報として返す
        return [
            'name' => $fileName,
            'type' => $mimeType,
            'tmp_name' => $tmpPath,
            'error' => UPLOAD_ERR_OK,
            'size' => strlen($decodedData),
            'ext' => $extension
        ];
    }

    /**
     * URLからファイルをダウンロードして処理
     *
     * @param string $url ファイルのURL
     * @return array アップロード情報の配列
     * @throws \Exception
     */
    protected function processUrlFile(string $url): array
    {
        // URLの妥当性チェック
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('不正なURL形式です: ' . $url);
        }

        // HTTPSまたはHTTPのみ許可
        if (!preg_match('/^https?:\/\//', $url)) {
            throw new \Exception('HTTPまたはHTTPSのURLのみサポートされています: ' . $url);
        }

        // 取得元の URL を検証したうえでダウンロードする（SSRF 対策）
        [$fileData, $http_response_header] = $this->fetchUrl($url);

        // ファイルサイズをチェック（10MBまで）
        $fileSize = strlen($fileData);
        if ($fileSize > 10 * 1024 * 1024) {
            throw new \Exception('ファイルサイズが大きすぎます（10MB以下にしてください）');
        }

        // レスポンスヘッダーからContent-Typeを取得
        $headerMimeType = 'application/octet-stream';
        foreach($http_response_header as $header) {
            if (stripos($header, 'content-type:') === 0) {
                $headerMimeType = trim(substr($header, 13));
                // パラメータを除去（例: "image/jpeg; charset=utf-8" -> "image/jpeg"）
                if (strpos($headerMimeType, ';') !== false) {
                    $headerMimeType = trim(explode(';', $headerMimeType)[0]);
                }
                break;
            }
        }

        // ファイル内容から実際のMIMEタイプを検出
        $actualMimeType = $this->detectMimeTypeFromContent($fileData);

        // URLから拡張子を推測
        $urlPath = parse_url($url, PHP_URL_PATH);
        $urlExtension = '';
        if ($urlPath) {
            $pathInfo = pathinfo($urlPath);
            $urlExtension = strtolower($pathInfo['extension'] ?? '');
        }

        // 最終的なMIMEタイプと拡張子を決定（優先順位: ファイル内容 > URL拡張子 > HTTPヘッダー）
        $mimeType = $actualMimeType;
        $extension = $this->getExtensionFromMimeType($actualMimeType);

        // ファイル内容から検出できなかった場合、URL拡張子を使用
        if ($actualMimeType === 'application/octet-stream' && !empty($urlExtension)) {
            $extension = $urlExtension;
            $mimeType = $this->getMimeTypeFromExtension($urlExtension);
        }

        // それでも不明な場合はHTTPヘッダーを使用
        if ($mimeType === 'application/octet-stream' && $headerMimeType !== 'application/octet-stream') {
            $mimeType = $headerMimeType;
            if (empty($extension)) {
                $extension = $this->getExtensionFromMimeType($headerMimeType);
            }
        }

        // ファイル形式のチェック
        if (!$this->isAllowedExtension($extension)) {
            throw new \Exception('サポートされていないファイル形式です: ' . $extension);
        }

        // 一意のファイル名を生成
        $fileName = 'download_' . uniqid() . '.' . $extension;
        $tmpPath = sys_get_temp_dir() . '/' . $fileName;

        // 一時ファイルに保存
        if (file_put_contents($tmpPath, $fileData) === false) {
            throw new \Exception('一時ファイルの作成に失敗しました');
        }

        return [
            'name' => $fileName,
            'type' => $mimeType,
            'tmp_name' => $tmpPath,
            'error' => UPLOAD_ERR_OK,
            'size' => $fileSize,
            'ext' => $extension
        ];
    }

    /**
     * URL からファイルを取得する（SSRF 対策付き）
     *
     * MCP クライアントが指定した URL をサーバーが取得するため、宛先を検証しないと
     * ループバック・プライベートネットワーク・クラウドのメタデータサービス等の
     * 内部リソースへ到達させられ、取得内容が公開ディレクトリへ保存されてしまう。
     *
     * - ホスト名を解決した IP アドレスが公開アドレスであることを確認する
     * - 検証した IP へ直接接続し、Host ヘッダと SNI に元のホスト名を使う
     *   （検証後に DNS の応答が変わる DNS リバインディングを防ぐ）
     * - リダイレクトは自動追従せず、リダイレクト先も同じ検証を通す
     * - 取得サイズに上限を設ける
     *
     * @param string $url
     * @return array [取得したデータ, レスポンスヘッダの配列]
     * @throws \Exception
     */
    protected function fetchUrl(string $url): array
    {
        $maxRedirects = 3;
        $maxSize = 10 * 1024 * 1024;
        // 内部の到達状況（接続の成否・ステータス）を推測させないため、失敗時のメッセージは統一する
        $failureMessage = 'URLからファイルをダウンロードできませんでした';

        for ($i = 0; $i <= $maxRedirects; $i++) {
            $parts = parse_url($url);
            if (!$parts || empty($parts['host']) || !in_array($parts['scheme'] ?? '', ['http', 'https'], true)
                || isset($parts['user']) || isset($parts['pass'])) {
                throw new \Exception($failureMessage);
            }
            $host = $parts['host'];
            $ip = $this->resolvePublicIp($host);
            if ($ip === null) {
                throw new \Exception($failureMessage);
            }
            $scheme = $parts['scheme'];
            $port = $parts['port'] ?? ($scheme === 'https'? 443 : 80);
            $ipHost = (strpos($ip, ':') !== false)? '[' . $ip . ']' : $ip;
            $requestUrl = $scheme . '://' . $ipHost . ':' . $port
                . ($parts['path'] ?? '/')
                . (isset($parts['query'])? '?' . $parts['query'] : '');
            $hostHeader = $host . (isset($parts['port'])? ':' . $parts['port'] : '');

            $option = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: baserCMS-MCP-Client/1.0\r\nHost: {$hostHeader}\r\n",
                    'timeout' => 30,
                    'follow_location' => 0,
                    'ignore_errors' => true,
                ],
                'ssl' => [
                    'peer_name' => $host,
                    'SNI_enabled' => true,
                    'verify_peer' => !BcUtil::isTest(),
                    'verify_peer_name' => !BcUtil::isTest(),
                ]
            ];
            $context = stream_context_create($option);
            $fileData = @file_get_contents($requestUrl, false, $context, 0, $maxSize + 1);
            $headers = function_exists('http_get_last_response_headers')?
                (http_get_last_response_headers() ?? []) : ($http_response_header ?? []);
            if ($fileData === false || !$headers) {
                throw new \Exception($failureMessage);
            }
            $status = 0;
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', (string)$headers[0], $matches)) {
                $status = (int)$matches[1];
            }
            if (in_array($status, [301, 302, 303, 307, 308], true)) {
                $location = null;
                foreach($headers as $header) {
                    if (stripos($header, 'location:') === 0) {
                        $location = trim(substr($header, 9));
                        break;
                    }
                }
                if ($location === null) {
                    throw new \Exception($failureMessage);
                }
                // 相対 URL は元の URL を基準に解決する
                if (!preg_match('/^https?:\/\//i', $location)) {
                    $base = $scheme . '://' . $hostHeader;
                    if (str_starts_with($location, '/')) {
                        $location = $base . $location;
                    } else {
                        $dir = rtrim(dirname($parts['path'] ?? '/'), '/');
                        $location = $base . $dir . '/' . $location;
                    }
                }
                $url = $location;
                continue;
            }
            if ($status < 200 || $status >= 300) {
                throw new \Exception($failureMessage);
            }
            if (strlen($fileData) > $maxSize) {
                throw new \Exception('ファイルサイズが大きすぎます（10MB以下にしてください）');
            }
            return [$fileData, $headers];
        }
        throw new \Exception($failureMessage);
    }

    /**
     * ホスト名を解決し、公開アドレスの場合のみ IP アドレスを返す
     *
     * 解決結果に 1 つでも非公開アドレスが含まれる場合は null を返す。
     *
     * @param string $host
     * @return string|null
     */
    protected function resolvePublicIp(string $host): ?string
    {
        $host = trim($host, '[]');
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->isPublicIp($host)? $host : null;
        }
        if (strcasecmp($host, 'localhost') === 0 || str_ends_with(strtolower($host), '.localhost')) {
            return null;
        }
        $ips = [];
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if (is_array($records)) {
            foreach($records as $record) {
                if (!empty($record['ip'])) $ips[] = $record['ip'];
                if (!empty($record['ipv6'])) $ips[] = $record['ipv6'];
            }
        }
        if (!$ips) {
            $ip = gethostbyname($host);
            if ($ip !== $host) $ips[] = $ip;
        }
        if (!$ips) return null;
        foreach($ips as $ip) {
            if (!$this->isPublicIp($ip)) return null;
        }
        return $ips[0];
    }

    /**
     * 公開アドレスかどうかを判定する
     *
     * プライベート・ループバック・リンクローカル（クラウドのメタデータサービス 169.254.169.254 を含む）・
     * 予約済み・マルチキャスト・共有アドレス空間（100.64.0.0/10）を拒否する。
     *
     * @param string $ip
     * @return bool
     */
    protected function isPublicIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $long = ip2long($ip);
            // 100.64.0.0/10（共有アドレス空間）、224.0.0.0/4（マルチキャスト）、255.255.255.255
            if (($long & 0xFFC00000) === 0x64400000) return false;
            if (($long & 0xF0000000) === 0xE0000000) return false;
            if ($long === 0xFFFFFFFF) return false;
            return true;
        }
        $packed = inet_pton($ip);
        if ($packed === false) return false;
        $first = ord($packed[0]);
        // ff00::/8（マルチキャスト）
        if ($first === 0xFF) return false;
        // ::ffff:0:0/96（IPv4 射影）は IPv4 側の判定に委ねる
        if (substr($packed, 0, 12) === "\0\0\0\0\0\0\0\0\0\0\xff\xff") {
            return $this->isPublicIp(inet_ntop(substr($packed, 12)));
        }
        // 64:ff9b::/96（NAT64）も IPv4 側の判定に委ねる
        if (substr($packed, 0, 12) === "\x00\x64\xff\x9b\0\0\0\0\0\0\0\0") {
            return $this->isPublicIp(inet_ntop(substr($packed, 12)));
        }
        return true;
    }

    /**
     * ファイル内容からMIMEタイプを検出
     *
     * @param string $fileData ファイルのバイナリデータ
     * @return string MIMEタイプ
     */
    protected function detectMimeTypeFromContent(string $fileData): string
    {
        // ファイルデータが空の場合
        if (empty($fileData)) {
            return 'application/octet-stream';
        }

        // マジックナンバーを確認してファイル形式を判定
        $header = substr($fileData, 0, 20); // 最初の20バイトを取得

        // JPEG
        if (substr($header, 0, 3) === "\xFF\xD8\xFF") {
            return 'image/jpeg';
        }

        // PNG
        if (substr($header, 0, 8) === "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
            return 'image/png';
        }

        // GIF87a, GIF89a
        if (substr($header, 0, 6) === 'GIF87a' || substr($header, 0, 6) === 'GIF89a') {
            return 'image/gif';
        }

        // WebP
        if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP') {
            return 'image/webp';
        }

        // BMP
        if (substr($header, 0, 2) === 'BM') {
            return 'image/bmp';
        }

        // SVG (XMLなのでテキストベース)
        if (strpos($header, '<?xml') === 0 || strpos($header, '<svg') !== false) {
            // より詳細にチェック
            $sample = substr($fileData, 0, 1024);
            if (strpos($sample, '<svg') !== false || strpos($sample, 'xmlns="http://www.w3.org/2000/svg"') !== false) {
                return 'image/svg+xml';
            }
        }

        // ICO
        if (substr($header, 0, 4) === "\x00\x00\x01\x00") {
            return 'image/x-icon';
        }

        // PDF
        if (substr($header, 0, 5) === '%PDF-') {
            return 'application/pdf';
        }

        // ZIP (DOCX, XLSX, PPTXなどもZIPベース)
        if (substr($header, 0, 4) === "PK\x03\x04" || substr($header, 0, 4) === "PK\x05\x06" || substr($header, 0, 4) === "PK\x07\x08") {
            // より詳細な判定のため、ファイル内容を確認
            $sample = substr($fileData, 0, 1024);
            if (strpos($sample, 'word/') !== false) {
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            } elseif (strpos($sample, 'xl/') !== false) {
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            } elseif (strpos($sample, 'ppt/') !== false) {
                return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            }
            return 'application/zip';
        }

        // RAR
        if (substr($header, 0, 7) === "Rar!\x1A\x07\x00") {
            return 'application/x-rar-compressed';
        }

        // GZIP
        if (substr($header, 0, 2) === "\x1F\x8B") {
            return 'application/gzip';
        }

        // MP3
        if (substr($header, 0, 3) === 'ID3' || substr($header, 0, 2) === "\xFF\xFB" || substr($header, 0, 2) === "\xFF\xF3" || substr($header, 0, 2) === "\xFF\xF2") {
            return 'audio/mpeg';
        }

        // WAV
        if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WAVE') {
            return 'audio/wav';
        }

        // MP4 (ftypで始まるMOVやMP4)
        if (substr($header, 4, 4) === 'ftyp') {
            $type = substr($header, 8, 4);
            if (in_array($type, ['mp41', 'mp42', 'isom', 'dash'])) {
                return 'video/mp4';
            } elseif ($type === 'qt  ') {
                return 'video/quicktime';
            }
        }

        // AVI
        if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'AVI ') {
            return 'video/x-msvideo';
        }

        // Microsoft Office旧形式 (OLE2)
        if (substr($header, 0, 8) === "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
            // より詳細な判定が必要だが、とりあえず汎用的に
            return 'application/msword'; // DOC, XLS, PPTなどの可能性
        }

        // プレーンテキスト（ASCIIまたはUTF-8）
        if (mb_check_encoding($fileData, 'UTF-8') && ctype_print(str_replace(["\n", "\r", "\t"], '', substr($fileData, 0, 100)))) {
            return 'text/plain';
        }

        // 判定できない場合
        return 'application/octet-stream';
    }

    /**
     * 拡張子からMIMEタイプを取得
     *
     * @param string $extension ファイル拡張子
     * @return string MIMEタイプ
     */
    protected function getMimeTypeFromExtension(string $extension): string
    {
        $mimeTypes = [
            // 画像
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'ico' => 'image/x-icon',

            // ドキュメント
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'csv' => 'text/csv',

            // アーカイブ
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',

            // 音声・動画
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * MIMEタイプから拡張子を取得
     *
     * @param string $mimeType MIMEタイプ
     * @return string ファイル拡張子
     */
    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $extensions = [
            // 画像
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
            'image/x-icon' => 'ico',

            // ドキュメント
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            'text/csv' => 'csv',

            // アーカイブ
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'application/x-tar' => 'tar',
            'application/gzip' => 'gz',

            // 音声・動画
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'video/mp4' => 'mp4',
            'video/x-msvideo' => 'avi',
            'video/quicktime' => 'mov',
        ];

        return $extensions[$mimeType] ?? 'bin';
    }

    /**
     * 許可された拡張子かチェック
     *
     * @param string $extension ファイル拡張子
     * @return bool 許可されている場合はtrue
     */
    protected function isAllowedExtension(string $extension): bool
    {
        // デフォルトで許可する拡張子（baserCMSの設定を参考）
        // SVG はスクリプトを埋め込めるため、MCP 経由のアップロードでは受け付けない
        $allowedExtensions = [
            // 画像
            'gif', 'jpg', 'jpeg', 'png', 'webp', 'bmp', 'ico',
            // ドキュメント
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
            // アーカイブ
            'zip', 'rar', 'tar', 'gz',
            // 音声・動画（必要に応じて有効化）
            // 'mp3', 'wav', 'mp4', 'avi', 'mov'
        ];

        return in_array(strtolower($extension), $allowedExtensions);
    }

    /**
     * 画像ファイル専用のアップロード処理
     *
     * @param string $imageData 画像の URL、または data: URI 形式の base64 データ
     * @return array|false アップロード情報の配列、失敗時はfalse
     */
    protected function processImageUpload(string $imageData): array|false
    {
        $result = $this->processFileUpload($imageData, 'image');

        // 配列の場合は画像ファイルかチェック
        if (is_array($result)) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico'];
            if (!in_array($result['ext'], $imageExtensions)) {
                throw new \Exception('画像ファイルではありません: ' . $result['ext']);
            }
        }

        return $result;
    }

    /**
     * 一時ファイルをクリーンアップ
     *
     * @param string $tmpPath 一時ファイルのパス
     */
    protected function cleanupTempFile(string $tmpPath): void
    {
        if (file_exists($tmpPath) && strpos($tmpPath, sys_get_temp_dir()) === 0) {
            unlink($tmpPath);
        }
    }

    /**
     * 配列データからCakePHPのUploadedFileオブジェクトを作成
     *
     * @param array $fileData ファイル情報の配列
     * @return \Psr\Http\Message\UploadedFileInterface
     */
    protected function createUploadedFileFromArray(array $fileData): \Psr\Http\Message\UploadedFileInterface
    {
        // ファイルストリームを作成
        $stream = fopen($fileData['tmp_name'], 'r');

        return new \Laminas\Diactoros\UploadedFile(
            $stream,                  // stream
            $fileData['size'],        // size
            $fileData['error'],       // error
            $fileData['name'],        // clientFilename
            $fileData['type']         // clientMediaType
        );
    }

}
