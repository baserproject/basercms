<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\View\{AppView as AppViewAlias};

/**
 * Users index
 * @var AppViewAlias $this
 */

$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add'],
	'title' => __d('baser', '新規追加'),
]);
$this->BcBaser->js('admin/users/index.bundle');
// TODO 一覧をどうやって読み込ませるか検討が必要
//$this->BcBaser->js([
//	'admin/libs/jquery.baser_ajax_data_list',
//	'admin/libs/jquery.baser_ajax_batch',
//	'admin/libs/baser_ajax_data_list_config',
//	'admin/libs/baser_ajax_batch_config'
//]);

// 新規作成ボタン
// $this->BcAdmin->addAdminMainBodyHeaderLinks([
// 	'url' => ['action' => 'add'],
// 	'title' => __d('baser', '新規追加'),
// ]);

echo $this->BcHtml->link(__d('baser', '新規追加'), ['action' => 'add', 'class' => 'bca-btn']);
?>

<!-- TODO 一覧をどうやって 読み込ませるか検討が必要 -->
<script type="text/javascript">
// $(function(){
// 	$.baserAjaxDataList.init();
// 	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
// });
</script>

<div id="AjaxBatchUrl" hidden><?php $this->BcBaser->url(['controller' => 'users', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" hidden></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>
<div id="DataList"><?php $this->BcBaser->element('Admin/users/index_list') ?></div>
