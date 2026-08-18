<?php
declare(strict_types=1);

namespace BcMcp\View\Helper;

use Cake\View\Helper;

/**
 * OAuth2 Helper
 *
 * OAuth2関連のビューヘルパー
 */
class OAuth2Helper extends Helper
{

    /**
     * スコープの説明を取得
     *
     * @param string $scope
     * @return string
     */
    public function getScopeDescription(string $scope): string
    {
        $descriptions = [
            'read' => 'データの読み取り',
            'write' => 'データの書き込み',
        ];

        return $descriptions[$scope] ?? $scope;
    }

}
