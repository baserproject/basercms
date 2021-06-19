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
use Cake\Core\Configure;
use Cake\Routing\Router;
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
     * @param array $check チェックするURL
     * @return boolean True if the operation should continue, false if it should abort
     * @checked
     * @unitTest
     */
    public static function checkUrl($check)
    {
        if (!$check[key($check)]) {
            return true;
        }
        $url = $check[key($check)];

        if (preg_match('/^[^\/]/is', $url)) {
            $url = '/' . $url;
        }
        // ルーティング設定に合わせて変換
        // TODO: Routing.prefixesはBaser4系なので、変更する
        $url = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $url);
        if (preg_match('/^(\/[a-z_]+)\*$/is', $url, $matches)) {
            $url = $matches[1] . '/' . '*';
        }
        $params = Router::getRouteCollection()->parse($url);
        // TODO: $params['prefix']は出力されないので、別の方法で実装する
        if (empty($params['prefix'])) {
            return false;
        }
        return true;
    }
}