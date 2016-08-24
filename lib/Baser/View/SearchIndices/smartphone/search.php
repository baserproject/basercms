<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] グロバールメニュー
 */
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
include BASER_VIEWS . 'SearchIndices' . DS . 'search' . $this->ext;
$this->request->params['action'] = 'smartphone_' . $this->request->params['action'];