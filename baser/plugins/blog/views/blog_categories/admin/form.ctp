<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカテゴリ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<?php if($this->action == 'admin_edit'): ?>
<div class="em-box align-left">
<p><strong>このカテゴリのURL：<?php $baser->link($baser->getUri('/'.$blogContent['BlogContent']['name'].'/archives/category/'.$formEx->value('BlogCategory.name')),'/'.$blogContent['BlogContent']['name'].'/archives/category/'.$formEx->value('BlogCategory.name'),array('target'=>'_blank')) ?></strong></p>
</div>
<?php endif ?>


<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action == 'admin_add'): ?>
<?php echo $formEx->create('BlogCategory', array('url' => array('controller' => 'blog_categories', 'action' => 'add', $blogContent['BlogContent']['id']))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $formEx->create('BlogCategory', array('url' => array('controller' => 'blog_categories', 'action' => 'edit', $blogContent['BlogContent']['id'], $formEx->value('BlogCategory.id'), 'id' => false))) ?>
<?php endif; ?>

<?php echo $formEx->input('BlogCategory.id', array('type' => 'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogCategory.no', 'NO') ?></th>
			<td class="col-input">
				<?php echo $formEx->value('BlogCategory.no') ?>
				<?php echo $formEx->input('BlogCategory.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogCategory.name', 'ブログカテゴリ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogCategory.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogCategory.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>URLに利用されます</li>
						<li>半角のみで入力してください</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogCategory.title', 'ブログカテゴリタイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogCategory.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $formEx->error('BlogCategory.title') ?>
			</td>
		</tr>
	<?php if($parents): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogCategory.parent_id', '親カテゴリ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogCategory.parent_id', array(
						'type'		=> 'select',
						'options'	=> $parents,
						'escape'	=> false)) ?>
				<?php echo $formEx->error('BlogCategory.parent_id') ?>
			</td>
		</tr>
	<?php else: ?>
		<?php echo $formEx->input('BlogCategory.parent_id', array('type' => 'hidden')) ?>
	<?php endif ?>
	<?php if($baser->siteConfig['category_permission']): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogCategory.owner_id', '管理グループ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogCategory.owner_id', array(
						'type'		=> 'select',
						'options'	=> $formEx->getControlSource('BlogCategory.owner_id'),
						'empty'		=> '指定しない')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpOwnerId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogCategory.owner_id') ?>
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
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除',
			array('action' => 'delete', $blogContent['BlogContent']['id'], $formEx->value('BlogCategory.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogCategory.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>