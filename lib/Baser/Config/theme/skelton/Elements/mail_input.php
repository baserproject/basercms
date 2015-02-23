<?php

/**
 * [PUBLISH] メールフォーム本体
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if (!isset($blockStart)) {
	$blockStart = 0;
}
if (!isset($blockEnd)) {
	$blockEnd = null;
}
$data = array(
	'blockStart' => $blockStart,
	'blockEnd' => $blockEnd
);
$this->BcBaser->includeCore('Mail.Elements/mail_input', $data);
