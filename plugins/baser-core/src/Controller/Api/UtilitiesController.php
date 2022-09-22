<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;

/**
 * Class UtilitiesController
 *
 * https://localhost/baser/api/baser-core/utilities/action_name.json で呼び出す
 *
 */
class UtilitiesController extends BcApiController
{

    /**
     * [API] サーバーキャッシュを削除する
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function clear_cache()
    {
        BcUtil::clearAllCache();

        $this->set([
            'message' => __d('baser', 'サーバーキャッシュを削除しました。')
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
