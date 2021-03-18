<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 検索インデックス一覧
 */
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config',
	'admin/search_indices/index'
]);
echo $this->BcBaser->i18nScript([
	'reconstructSearchTitle' => __d('baser', '確認'),
	'reconstructSearchMessage' => __d('baser', '現在の検索インデックスを消去して、再構築します。本当にいいですか？'),
], ['inline' => true]);
?>


<script>
	$(function () {
		$("#BtnReconstruct").click(function () {
			$.bcConfirm.show({
				title: bcI18n.reconstructSearchTitle,
				message: bcI18n.reconstructSearchMessage,
				ok: function () {
					$.bcUtil.showLoader();
					location.href = $("#BtnReconstruct").attr('href');
				}
			});
			return false;
		});
	});
</script>


<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'search_indices', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="AjaxChangePriorityUrl"
	 class="display-none"><?php echo $this->BcBaser->url(['action' => 'ajax_change_priority']) ?></div>
<div id="SearchIndexOpen" class="display-none"><?php echo $this->BcForm->value('SearchIndex.open') ?></div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('search_indices/index_list') ?></div>


<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php $this->BcBaser->link(__d('baser', '検索インデックス再構築'), ['controller' => 'search_indices', 'action' => 'reconstruct'], [
			'id' => 'BtnReconstruct',
			'class' => 'bca-btn bca-actions__item',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
</div>


