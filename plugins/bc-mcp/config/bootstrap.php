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

Log::setConfig(Configure::consume('Log'));

Log::write(LogLevel::INFO, '-----------------------------------------------', 'mcp');
Log::write(LogLevel::INFO, $_SERVER['REQUEST_URI'], 'mcp');
Log::write(LogLevel::INFO, $_SERVER['REQUEST_METHOD'], 'mcp');
$resource = fopen('php://input', 'r');
$content = stream_get_contents($resource);
if ($content) {
    Log::write(LogLevel::INFO, $content, 'mcp');
}
if (!empty($_POST)) {
    Log::write(LogLevel::INFO, json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'mcp');
}
Log::write(LogLevel::INFO, '-----------------------------------------------', 'mcp');
