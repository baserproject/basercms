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
 * [ADMIN] テーマ一覧
 */
$this->BcBaser->js(array(
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/baser_ajax_data_list_config',
	'admin/themes/index'
));
?>


<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'themes', 'action' => 'ajax_batch')) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>

<div id="tabs">
	<ul>
		<li><a href="#DataList">所有テーマ</a></li>
		<li><a href="#BaserMarket">baserマーケット</a></li>
	</ul>
	<div id="DataList"><?php $this->BcBaser->element('themes/index_list') ?></div>
	<div id="BaserMarket"><div style="padding:20px;text-align:center;"><?php $this->BcBaser->img('admin/ajax-loader.gif', array('alt' => 'Loading...')) ?></div></div>
</div>
