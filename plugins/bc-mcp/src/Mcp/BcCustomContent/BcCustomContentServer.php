<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcCustomContent;

/**
 * カスタムコンテンツ機能用MCPサーバー
 *
 * カスタムコンテンツ関連の全てのツールクラス名を提供
 */
class BcCustomContentServer
{

    /**
     * 利用可能なカスタムコンテンツツールクラス名の配列を返却
     *
     * @return array<string> ツールクラス名の配列
     */
    public static function getToolClasses(): array
    {
        return [
            CustomFieldsTool::class,
            CustomTablesTool::class,
            CustomContentsTool::class,
            CustomEntriesTool::class,
            CustomLinksTool::class,
        ];
    }

}
