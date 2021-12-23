<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * サブサイト一覧（行）
 */
$classies = [];
if ($data['Site']['status']) {
	$classies = ['publish'];
} else {
	$classies = ['unpublish', 'disablerow'];
}
$class = ' class="' . implode(' ', $classies) . '"';
$url = $this->BcContents->getUrl('/' . $data['Site']['alias'] . '/', true, $data['Site']['use_subdomain']);
?>


<tr id="Row<?php echo $count ?>" <?php echo $class; ?>>
	<td class="bca-table-listup__tbody-td" style="width:5%"><?php echo $data['Site']['id']; ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['Site']['display_name']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($data['Site']['name'], ['action' => 'edit', $data['Site']['id']]); ?>
		<br>
		<?php echo $data['Site']['alias'] ?>
	</td>
	<td class="bca-table-listup__tbody-td bc-align-center status" style="width:5%;">
		<?php echo $this->BcText->booleanMark($data['Site']['status']); ?><br>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo $this->BcText->arrayValue($data['Site']['device'], $devices, ''); ?><br>
		<?php echo $this->BcText->arrayValue($data['Site']['lang'], $langs, ''); ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($this->BcText->arrayValue($data['Site']['main_site_id'], $mainSites, '')); ?>
		<br>
		<?php echo $this->BcText->noValue($data['Site']['theme'], $this->BcBaser->siteConfig['theme']) ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['Site']['created']) ?><br>
		<?php echo $this->BcTime->format('Y-m-d', $data['Site']['modified']) ?>
	</td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions" style="width:15%">
		<?php $this->BcBaser->link('', ['action' => 'ajax_unpublish', $data['Site']['id']], ['title' => __d('baser', '非公開'), 'class' => 'btn-unpublish bca-btn-icon', 'data-bca-btn-type' => 'unpublish', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['action' => 'ajax_publish', $data['Site']['id']], ['title' => __d('baser', '公開'), 'class' => 'btn-publish bca-btn-icon', 'data-bca-btn-type' => 'publish', 'data-bca-btn-size' => 'lg']) ?>
		<?php if ($data['Site']['status']) : ?>
			<?php $this->BcBaser->link('', $url, ['title' => __d('baser', '確認'), 'target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
		<?php $this->BcBaser->link('', ['action' => 'edit', $data['Site']['id']], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
	</td>
</tr>
