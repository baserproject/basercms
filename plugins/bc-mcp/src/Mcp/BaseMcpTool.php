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
     * @param \Exception|null $exception 例外オブジェクト（トレース情報用）
     * @return array MCP仕様に準拠したエラーレスポンス
     */
    protected function createErrorResponse(string $message, ?\Exception $exception = null): array
    {
        $response = [
            'content' => $message
        ];
        if ($exception) {
            $response['trace'] = $exception->getTraceAsString();
        }
        return $response;
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
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), $e);
        }
    }

    /**
     * 値がファイルアップロード可能な形式かどうかを判定
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

        // チャンクファイル名の場合（拡張子があるファイル名）
        if (!empty($value) && preg_match('/\.[a-zA-Z0-9]{2,4}$/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * ファイルアップロード処理
     *
     * @param string $fileData ファイルパス、URL、またはbase64エンコードされたデータ
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

            if (!empty($fileData)) {
                return $this->processChunkFile($fileData);
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
     * チャンクファイルを処理
     *
     * @param string $fileData チャンクファイル名
     * @return array アップロード情報の配列
     * @throws \Exception
     */
    public function processChunkFile(string $fileData): array
    {
        $filePath = TMP . 'mcp_uploads' . DS . $fileData;
        if (!file_exists($filePath)) {
            throw new \Exception('チャンクファイルが存在しません');
        }

        // ファイル情報を取得
        $fileSize = filesize($filePath);
        $fileName = basename($fileData);

        // ファイル拡張子を取得
        $pathInfo = pathinfo($fileName);
        $extension = strtolower($pathInfo['extension'] ?? '');

        // 許可された拡張子かチェック
        if (!$this->isAllowedExtension($extension)) {
            throw new \Exception('サポートされていないファイル形式です: ' . $extension);
        }

        // MIMEタイプを取得
        $mimeType = $this->getMimeTypeFromExtension($extension);

        // アップロード情報として返す
        return [
            'name' => $fileName,
            'type' => $mimeType,
            'tmp_name' => $filePath,
            'error' => UPLOAD_ERR_OK,
            'size' => $fileSize,
            'ext' => $extension
        ];
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

        // ファイル拡張子を取得
        $extension = $this->getExtensionFromMimeType($mimeType);

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

        // ユーザーエージェントを設定してファイルをダウンロード
        $option = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: baserCMS-MCP-Client/1.0\r\n",
                'timeout' => 30,
                'follow_location' => true,
                'max_redirects' => 3
            ]
        ];
        if (BcUtil::isTest()) {
            $option['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false
            ];
        }
        $context = stream_context_create($option);
        $fileData = @file_get_contents($url, false, $context);

        if ($fileData === false) {
            throw new \Exception('URLからファイルをダウンロードできませんでした: ' . $url);
        }

        // ファイルサイズをチェック（10MBまで）
        $fileSize = strlen($fileData);
        if ($fileSize > 10 * 1024 * 1024) {
            throw new \Exception('ファイルサイズが大きすぎます（10MB以下にしてください）');
        }

        // レスポンスヘッダーからContent-Typeを取得
        $headerMimeType = 'application/octet-stream';
        if(function_exists('http_get_last_response_headers')) {
            $http_response_header = http_get_last_response_headers();
        }
        if (isset($http_response_header)) {
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
        $allowedExtensions = [
            // 画像
            'gif', 'jpg', 'jpeg', 'png', 'webp', 'svg', 'bmp', 'ico',
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
     * @param string $imageData 画像ファイルパス、URL、またはbase64エンコードされたデータ
     * @return array|false アップロード情報の配列、失敗時はfalse
     */
    protected function processImageUpload(string $imageData): array|false
    {
        $result = $this->processFileUpload($imageData, 'image');

        // 配列の場合は画像ファイルかチェック
        if (is_array($result)) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
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
