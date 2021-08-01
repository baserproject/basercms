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

namespace BaserCore\Controller\Api;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * ToolsController
 */
class ToolsController extends BcApiController
{

    /**
     * クレジット表示用データをレンダリング
     */
    public function credit()
    {

        $this->layout = 'ajax';
        Configure::write('debug', 0);

        $specialThanks = [];
        if (!Configure::read('Cache.disable') && Configure::read('debug') == 0) {
            $specialThanks = Cache::read('special_thanks', '_bc_env_');
        }

        if ($specialThanks) {
            $json = json_decode($specialThanks);
        } else {
            try {
                $json = file_get_contents(Configure::read('BcApp.specialThanks'), true);
            } catch (Exception $ex) {
            }
            if ($json) {
                if (!Configure::read('Cache.disable')) {
                    Cache::write('special_thanks', $json, '_bc_env_');
                }
                $json = json_decode($json);
            } else {
                $json = null;
            }

        }

        if ($json == false) {
            $this->ajaxError(500, __d('baser', 'スペシャルサンクスデータが取得できませんでした。'));
        }
        $this->set('credits', $json);

    }

}
