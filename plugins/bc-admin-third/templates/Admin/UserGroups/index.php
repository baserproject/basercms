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

/**
 * UserGroups index
 * @var \BaserCore\View\AppView $this
 */

// TODO 一覧をどうやって読み込ませるか検討が必要
// $this->BcBaser->i18nScript([
// 	'message1' => __d('baser', "このデータを本当に削除してもいいですか？\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。")
// ]);
// $this->BcBaser->js([
// 	'admin/libs/jquery.baser_ajax_data_list',
// 	'admin/libs/jquery.baser_ajax_batch',
// 	'admin/libs/baser_ajax_data_list_config',
// 	'admin/libs/baser_ajax_batch_config'
// ]);

// 新規作成ボタン
// $this->BcAdmin->addAdminMainBodyHeaderLinks([
// 	'url' => ['action' => 'add'],
// 	'title' => __d('baser', '新規追加'),
// ]);

$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add'],
	'title' => __d('baser', '新規追加'),
]);
?>

<!-- TODO 一覧をどうやって 読み込ませるか検討が必要 -->
<script type="text/javascript">
// 	$(function(){
// 		$.baserAjaxDataList.config.methods.del.confirm = bcI18n.message1;
// 		$.baserAjaxDataList.init();
// 		$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
// 	});
</script>


<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(['controller' => 'user_groups', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" hidden></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('Admin/user_groups/index_list') ?></div>
