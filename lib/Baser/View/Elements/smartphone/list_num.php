<?php

/**
 * [SMARTPHONE] リスト設定リンク
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
include BASER_VIEWS . 'Elements' . DS . 'list_num' . $this->ext;
$this->request->params['action'] = 'smartphone_' . $this->request->params['action'];