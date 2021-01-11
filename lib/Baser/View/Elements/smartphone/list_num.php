<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [SMARTPHONE] リスト設定リンク
 */
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
include BASER_VIEWS . 'Elements' . DS . 'list_num' . $this->ext;
$this->request->params['action'] = 'smartphone_' . $this->request->params['action'];
