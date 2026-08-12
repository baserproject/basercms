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

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * MCPサーバー用ロガー
 *
 * MCPサーバーは常駐プロセスとして動作しており、ツール実行時の例外は
 * メッセージのみに丸められてクライアントへ返却されるため、そのままでは
 * 発生箇所を追跡できない。
 * 例外のトレースまで含めてログに記録する事で、発生箇所を追跡できるようにする。
 */
class McpLogger extends AbstractLogger
{

    /**
     * ログファイルのパス
     * @var string
     */
    private string $logFile;

    /**
     * 記録対象のログレベル
     * @var array
     */
    private array $levels;

    /**
     * コンストラクタ
     *
     * @param string $logFile ログファイルのパス
     * @param array $levels 記録対象のログレベル
     */
    public function __construct(string $logFile, array $levels = ['emergency', 'alert', 'critical', 'error', 'warning'])
    {
        $this->logFile = $logFile;
        $this->levels = $levels;
    }

    /**
     * ログを記録する
     *
     * @param mixed $level
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (!in_array((string)$level, $this->levels, true)) return;

        $log = sprintf('%s %s: %s', date('Y-m-d H:i:s'), strtoupper((string)$level), (string)$message);
        if (!empty($context['tool'])) {
            $log .= ' (tool: ' . $context['tool'] . ')';
        }
        if (!empty($context['exception']) && $context['exception'] instanceof \Throwable) {
            $exception = $context['exception'];
            $log .= PHP_EOL . sprintf(
                '%s: %s in %s(%s)',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            $log .= PHP_EOL . $exception->getTraceAsString();
        }
        file_put_contents($this->logFile, $log . PHP_EOL, FILE_APPEND);
    }

}
