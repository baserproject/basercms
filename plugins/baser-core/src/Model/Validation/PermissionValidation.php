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

namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;
use Cake\Routing\Router;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcValidation
 * @package BaserCore\Model\Validation
 */
class PermissionValidation extends Validation
{
    /**
     * 権限の必要なURLかチェックする
     *
     * @param string $url チェックするURL
     * @return boolean True if the operation should continue, false if it should abort
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkUrl($url): bool
    {

        if (preg_match('/^[^\/]/is', $url)) {
            $url = '/' . $url;
        }
        // ルーティング設定に合わせて変換
        $url = preg_replace('/^\/baser\/admin\//', BcUtil::getPrefix() . '/', $url);
        if (preg_match('/^(\/[a-z_]+)\*$/is', $url, $matches)) {
            $url = $matches[1] . '/' . '*';
        }
        $params = Router::getRouteCollection()->parse($url);
        if (empty($params['prefix'])) {
            return false;
        }
        return true;
    }
}