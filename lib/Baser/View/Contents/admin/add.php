<?php
/**
 * [ADMIN] 検索インデックス登録フォーム
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(window).load(function() {
	$("#ContentUrl").focus();
});
</script>

<?php echo $this->BcForm->create('Content') ?>


<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Content.title', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Content.title', array('type' => 'text', 'size' => 60, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('Content.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Content.url', 'URL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Content.url', array('type' => 'text', 'size' => 60, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Content.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li>サイト内で検索インデックスとして登録したいURLを指定します。</li>
						<li>baserCMSの設置URL部分は省略する事ができます。<br />
							http://{baserCMS設置URL}/company/index<br />
							→ /company/index<br />
							<small>※ 省略時、スマートURLオフの場合、URL上の「/index.php」 は含めないようにします。</small>
						</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'btnSave')) ?>
</div>

<?php echo $this->BcForm->end() ?>