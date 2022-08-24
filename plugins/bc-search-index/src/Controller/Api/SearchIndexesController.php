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

namespace BcSearchIndex\Controller\Api;

use BaserCore\Controller\AppController;

/**
 * SearchIndicesController
 */
class SearchIndicesController extends AppController
{

    /**
     * [AJAX] 優先順位を変更する
     *
     * @return void
     * @throws Exception
     */
    public function admin_ajax_change_priority()
    {
        if ($this->request->getData()) {
            $this->SearchIndex->set($this->request->getData());
            if ($this->SearchIndex->save()) {
                echo true;
            }
        }
        exit();
    }

}
