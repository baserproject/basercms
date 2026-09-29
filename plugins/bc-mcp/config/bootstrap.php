<?php

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Log\Log;
use Psr\Log\LogLevel;

$vendor = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendor)) {
    require_once $vendor;
}

if (BcUtil::isConsole() || BcUtil::isTest()) return;
if (!Configure::read('debug')) return;

// デバッグ時のリクエスト記録は MCP のエンドポイントに限定する。
// 全リクエストを記録すると、管理画面のログインやパスワード変更等の生ボディ（パスワードを含む）まで
// mcp.log に平文で残ってしまう
$requestUri = (string)($_SERVER['REQUEST_URI'] ?? '');
$requestPath = (string)parse_url($requestUri, PHP_URL_PATH);
if (!preg_match('#(^|/)bc-mcp(/|$)#', $requestPath)) return;

Log::setConfig(Configure::consume('Log'));

/**
 * 記録内容から秘密情報（トークン・パスワード・PKCE の検証値等）をマスクする
 *
 * @param string $content
 * @return string
 */
$maskSecrets = function(string $content): string {
    $keys = 'password|passwd|secret|token|authorization|code_verifier|code_challenge|refresh_token|access_token|client_secret|code';
    // JSON 形式（"key": "value"）
    $content = preg_replace('/("(?:[a-z_]*(?:' . $keys . ')[a-z_]*)"\s*:\s*")([^"]*)(")/i', '$1***$3', $content) ?? $content;
    // フォーム形式（key=value）
    $content = preg_replace('/((?:^|[&?])(?:[a-z_]*(?:' . $keys . ')[a-z_]*)=)([^&\s]*)/i', '$1***', $content) ?? $content;
    // Bearer トークン
    $content = preg_replace('/(Bearer\s+)[A-Za-z0-9._~+\/=-]+/i', '$1***', $content) ?? $content;
    return $content;
};

Log::write(LogLevel::INFO, '-----------------------------------------------', 'mcp');
Log::write(LogLevel::INFO, $maskSecrets($requestUri), 'mcp');
Log::write(LogLevel::INFO, (string)($_SERVER['REQUEST_METHOD'] ?? ''), 'mcp');
$resource = fopen('php://input', 'r');
$content = $resource? stream_get_contents($resource) : '';
if ($content) {
    Log::write(LogLevel::INFO, $maskSecrets($content), 'mcp');
}
if (!empty($_POST)) {
    Log::write(LogLevel::INFO, $maskSecrets(json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), 'mcp');
}
Log::write(LogLevel::INFO, '-----------------------------------------------', 'mcp');
