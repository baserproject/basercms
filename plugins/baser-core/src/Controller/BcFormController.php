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

namespace BaserCore\Controller;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcFormController
 */
class BcFormController extends AppController
{

    /**
     * セキュリティトークンを取得する
     *
     * @return mixed
     * @checked
     * @unitTest
     * @noTodo
     */
    public function get_token()
    {
        $this->autoRender = false;
        return $this->response->withStringBody($this->request->getAttribute('csrfToken'));
    }

}
