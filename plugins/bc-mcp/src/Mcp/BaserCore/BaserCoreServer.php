<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BaserCore;

/**
 * baserCore機能用MCPサーバー
 *
 * baserCore関連の全てのツールクラス名・リソースクラス名を提供
 */
class BaserCoreServer
{

    /**
     * 利用可能なツールクラス名の配列を返却
     *
     * @return array<string> ツールクラス名の配列
     */
    public static function getToolClasses(): array
    {
        return [
            // SearchIndexesTool::class, // ChatGPTで動作しないため一旦、停止
            // FileUploadTool::class // AI側のメッセージ制限によりチャンクによるアップロードを実装したが、それでも、現実的でなかったため、一旦、停止
        ];
    }

}
