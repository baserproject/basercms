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

/**
 * メンテナンスコントローラー
 */
class MaintenanceController extends BcFrontAppController
{

    /**
     * メンテナンス中ページを表示する
     *
     * @return void
     * @access    public
     */
    public function index()
    {
        $this->setTitle(__d('baser', 'メンテナンス中'));
        $this->response->statusCode(503);
    }

    /**
     * [スマートフォン] メンテナンス中ページを表示する
     *
     * @return void
     * @access public
     */
    public function smartphone_index()
    {
        $this->setAction('index');
    }

}
