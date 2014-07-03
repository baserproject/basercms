<?php

/**
 * [MOBILE] ページネーション
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if (!empty($this->Paginator)) {
	$this->request->params['action'] = str_replace('mobile_', '', $this->request->params['action']);
	if ($this->Paginator->counter(array('format' => '%pages%')) > 1) {
		echo $this->Paginator->prev('<<', null, null, null) . '&nbsp;';
		echo $this->Paginator->numbers(array('separator' => '&nbsp;', 'modulus' => 4)) . '&nbsp;';
		echo $this->Paginator->next('>>', null, null, null);
	}
	$this->request->params['action'] = 'mobile_' . $this->request->params['action'];
}
