<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サブサイト一覧（行）
 */
$classies = array();
if ($data['Site']['status']) {
	$classies = array('publish');
} else {
	$classies = array('unpublish', 'disablerow');
}
$class = ' class="' . implode(' ', $classies) . '"';
if($data['Site']['alias']) {
	$url = '/' . $data['Site']['alias'] . '/';
} else {
	$url = '/' . $data['Site']['name'] . '/';
}
?>


<tr id="Row<?php echo $count ?>" <?php echo $class; ?>>
	<td class="row-tools" style="width:15%">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '非公開', 'class' => 'btn')), array('action' => 'ajax_unpublish', $data['Site']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '公開', 'class' => 'btn')), array('action' => 'ajax_publish', $data['Site']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $url, array('title' => '確認', 'target' => '_blank')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['Site']['id']), array('title' => '編集')) ?>
	</td>
	<td style="width:5%"><?php echo $data['Site']['id']; ?></td>
	<td><?php $this->BcBaser->link($data['Site']['name'], array('action' => 'edit', $data['Site']['id'])); ?></td>
	<td><?php echo $data['Site']['display_name'] ?></td>
	<td><?php echo $data['Site']['alias'] ?></td>
	<td style="width:5%;" class="align-center status">
		<?php echo $this->BcText->booleanMark($data['Site']['status']); ?><br />
	</td>
	<td style="width:5%;" class="align-center">
		<?php echo $this->BcText->arrayValue($data['Site']['main_site_id'], $mainSites, ''); ?>
	</td>
	<td><?php echo $this->BcText->noValue($data['Site']['theme'], $this->BcBaser->siteConfig['theme']) ?></td>
	<td style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['Site']['created']) ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['Site']['modified']) ?>
	</td>
</tr>