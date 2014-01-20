<?php

/* SVN FILE: $Id$ */
/**
 * [SMARTPHONE] リスト設定リンク
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
include BASER_VIEWS . 'Elements' . DS . 'list_num' . $this->ext;
$this->request->params['action'] = 'smartphone_' . $this->request->params['action'];