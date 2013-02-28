<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカテゴリ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$owners = $bcForm->getControlSource('BlogCategory.owner_id');
?>


<script type="text/javascript">
$(window).load(function() {
	$("#BlogCategoryName").focus();
});
</script>


<?php if($this->action == 'admin_edit'): ?>
<div class="em-box align-left">
<p><strong>このカテゴリのURL：<?php $bcBaser->link($bcBaser->getUri('/'.$blogContent['BlogContent']['name'].'/archives/category/'.$bcForm->value('BlogCategory.name')),'/'.$blogContent['BlogContent']['name'].'/archives/category/'.$bcForm->value('BlogCategory.name'),array('target'=>'_blank')) ?></strong></p>
</div>
<?php endif ?>


<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action == 'admin_add'): ?>
<?php echo $bcForm->create('BlogCategory', array('url' => array('controller' => 'blog_categories', 'action' => 'add', $blogContent['BlogContent']['id']))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $bcForm->create('BlogCategory', array('url' => array('controller' => 'blog_categories', 'action' => 'edit', $blogContent['BlogContent']['id'], $bcForm->value('BlogCategory.id'), 'id' => false))) ?>
<?php endif; ?>

<?php echo $bcForm->input('BlogCategory.id', array('type' => 'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogCategory.no', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('BlogCategory.no') ?>
				<?php echo $bcForm->input('BlogCategory.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogCategory.name', 'ブログカテゴリ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogCategory.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogCategory.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>URLに利用されます</li>
						<li>半角のみで入力してください</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogCategory.title', 'ブログカテゴリタイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogCategory.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $bcForm->error('BlogCategory.title') ?>
			</td>
		</tr>
	<?php if($parents): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogCategory.parent_id', '親カテゴリ') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogCategory.parent_id', array(
						'type'		=> 'select',
						'options'	=> $parents,
						'escape'	=> false)) ?>
				<?php echo $bcForm->error('BlogCategory.parent_id') ?>
			</td>
		</tr>
	<?php else: ?>
		<?php echo $bcForm->input('BlogCategory.parent_id', array('type' => 'hidden')) ?>
	<?php endif ?>
<?php if($bcBaser->siteConfig['category_permission']): ?>	
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogCategory.owner_id', '管理グループ') ?></th>
			<td class="col-input">
	<?php if($bcAdmin->isSystemAdmin()): ?>
				<?php echo $bcForm->input('BlogCategory.owner_id', array(
						'type'		=> 'select',
						'options'	=> $owners,
						'empty'		=> '指定しない')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpOwnerId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogCategory.owner_id') ?>
	<?php else: ?>
				<?php echo $bcText->arrayValue($this->data['BlogCategory']['owner_id'], $owners) ?>
				<?php echo $bcForm->input('BlogCategory.owner_id', array('type' => 'hidden')) ?>
	<?php endif ?>
				<div id="helptextOwnerId" class="helptext">
					<ul>
						<li>管理グループを指定した場合、このカテゴリに属した記事は、管理グループのユーザーしか編集する事ができなくなります。</li>
					</ul>
				</div>
			</td>
		</tr>
<?php endif ?>
	</table>
</div>


<!-- button -->
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除',
			array('action' => 'delete', $blogContent['BlogContent']['id'], $bcForm->value('BlogCategory.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('BlogCategory.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>