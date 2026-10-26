<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcFavorite\Model\Validation;

use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Utility\BcContainer;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcValidation
 */
class FavoriteValidation extends Validation
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * サイト内のパスかチェックする
     *
     * お気に入りの URL はそのままリンクとして出力されるため、サイト内のパス以外を許可しない
     *
     * @param string $url
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isInternalPath($url)
    {
        // `javascript:` などのスキーム付きの URL や、相対パスを許可しない
        if (!is_string($url) || !str_starts_with($url, '/')) return false;
        // 2文字目が `/`・`\` だと、ブラウザが他のホストへのリンクとして扱う。
        // 空白はブラウザが取り除くため、`/<タブ>/example.com` も `//example.com` になる
        $second = substr($url, 1, 1);
        return !in_array($second, ['/', '\\'], true) && !preg_match('/\s/', $second);
    }

    /**
     * アクセス権があるかチェックする
     *
     * 管理者グループは全て true を返却
     *
     * @param array $check
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isPermitted($url)
    {
        if (BcUtil::isAdminUser()) {
            return true;
        }
        $userGroups = BcUtil::loginUserGroup();
        if (!$userGroups) {
            return false;
        }
        $permissionService = BcContainer::get()->get(PermissionsServiceInterface::class);
        return $permissionService->check($url, array_column($userGroups, 'id'));
    }
}
