<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ一覧　行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if($data['name']==$bcBaser->siteConfig['theme']): ?>
	<?php $class=' class="activerow"' ?>
<?php else: ?>
	<?php $class=''; ?>
<?php endif; ?>
<tr<?php echo $class; ?>>
	<td class="row-tools" style="width:110px;text-align: right">
<?php if($data['name'] != 'core' && $data['name']!=$bcBaser->siteConfig['theme']): ?>
	<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['name'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['name'])) ?>
	<?php endif ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_apply.png', array('width' => 24, 'height' => 24, 'alt' => '適用', 'class' => 'btn')), array('action' => 'apply', $data['name']), array('title' => '適用')) ?>
<?php endif ?>		
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_manage.png', array('width' => 24, 'height' => 24, 'alt' => '管理', 'class' => 'btn')), array('controller'=>'theme_files','action' => 'index', $data['name']), array('title' => '管理')) ?>
<?php if($data['name'] != 'core'): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['name']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['name']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
<?php endif ?>

<?php if($data['name'] != 'core'): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['name']), array('title' => '削除', 'class' => 'btn-delete')) ?>
<?php endif ?>
<?php if(!$data['is_writable_pages'] && $data['name'] != 'core'): ?>
		<br /><div class="error-message lastChild clearfix" style="clear:both">「pages」フォルダに書き込み権限を与えてください。</div>
<?php endif ?>
	</td>
	<td><?php echo $data['name'] ?></td>
	<td style="width:220px">
		<?php if($data['name']!='core' && $data['screenshot']): ?>
		<?php /* ↓↓↓ スマートURLオフの場合、HtmlHelper::link では、正しいリンク先が取得できないので直接記述 ↓↓↓ */ ?>
		<a href="<?php echo $html->webroot('/themed/'.$data['name'].'/screenshot.png') ?>" rel="colorbox" class="test">
			<?php $bcBaser->img('/themed/'.$data['name'].'/screenshot.png',array(
						'alt'=>$data['title'],
						'width'=>'80px',
						'style'=>'float:left;margin-right:10px;border:1px solid #e2e2e2'
			)) ?>
		</a>
		<?php endif ?>
		<?php echo $data['title'] ?>
	</td>
	<td style="width:70px"><?php echo $data['version'] ?></td>
	<td style="width:170px"><?php echo $data['description'] ?></td>
	<td><?php if(!empty($data['url']) && !empty($data['author'])): ?>
		<?php $bcBaser->link($data['author'],$data['url'],array('target'=>'_blank')) ?>
		<?php else: ?>
		<?php echo $data['author'] ?>
		<?php endif ?>
	</td>
</tr>
