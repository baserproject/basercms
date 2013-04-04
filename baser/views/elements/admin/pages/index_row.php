<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ一覧　行
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$classies = array();
if (!$bcPage->allowPublish($data)) {
	$classies = array('unpublish', 'disablerow', 'sortable');
} else {
	$classies = array('publish', 'sortable');
}
$class=' class="'.implode(' ', $classies).'"';

if(empty($data['PageCategory']['id']) || $data['PageCategory']['name'] == 'mobile') {
	$ownerId = $bcBaser->siteConfig['root_owner_id'];
} else {
	$ownerId = $data['PageCategory']['owner_id'];
}
$url = preg_replace('/index$/', '', $data['Page']['url']);
?>


<tr id="Row<?php echo $count ?>" <?php echo $class; ?>>
	<td class="row-tools" style="width:15%">
<?php if($sortmode): ?>
		<span class="sort-handle"><?php $bcBaser->img('sort.png', array('alt' => '並び替え', 'class' => 'sort-handle')) ?></span>
		<?php echo $bcForm->input('Sort.id'.$data['Page']['id'], array('type'	=> 'hidden', 'class' => 'id', 'value' => $data['Page']['id'])) ?>
<?php endif ?>
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['Page']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Page']['id'])) ?>
<?php endif ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '非公開', 'class' => 'btn')), array('action' => 'ajax_unpublish', $data['Page']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '公開', 'class' => 'btn')), array('action' => 'ajax_publish', $data['Page']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
<?php if(!preg_match('/^\/'.Configure::read('BcAgent.mobile.prefix').'\//is', $url) && !preg_match('/^\/'.Configure::read('BcAgent.smartphone.prefix').'\//is', $url)): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $url, array('title' => '確認', 'target' => '_blank')) ?>
<?php elseif(preg_match('/^\/'.Configure::read('BcAgent.mobile.prefix').'\//is', $url)): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $bcBaser->changePrefixToAlias($url, 'mobile'), array('title' => '確認', 'target' => '_blank')) ?>
<?php elseif(preg_match('/^\/'.Configure::read('BcAgent.smartphone.prefix').'\//is', $url)): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $bcBaser->changePrefixToAlias($url, 'smartphone'), array('title' => '確認', 'target' => '_blank')) ?>		
<?php endif ?>

		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['Page']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['Page']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>

<?php if(in_array($ownerId, $allowOwners)||(!empty($user) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId'))): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['Page']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
<?php endif ?>
	</td>
	<td style="width:5%"><?php echo $data['Page']['id']; ?></td>
	<td style="width:50%">
<?php if(!empty($data['PageCategory']['title'])): ?>
		<small><?php echo $data['PageCategory']['title']; ?></small>
<?php endif; ?>
		<br />
<?php if(!empty($data['Page']['title'])): ?>
		<?php echo $data['Page']['title']; ?>&nbsp;
<?php endif; ?>
		(&nbsp;<?php $bcBaser->link($data['Page']['name'], array('action' => 'edit', $data['Page']['id'])); ?>&nbsp;)
	</td>
	<td style="width:5%;" class="align-center status">
		<?php echo $bcText->booleanMark($data['Page']['status']); ?><br />
	</td>
	<td style="width:15%">
<?php if(isset($users[$data['Page']['author_id']])) : ?>
		<?php echo $users[$data['Page']['author_id']] ?>
<?php endif ?>
	</td>
	<td style="width:10%;white-space: nowrap">
		<?php echo $bcTime->format('Y-m-d',$data['Page']['created']) ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['Page']['modified']) ?>
	</td>
</tr>