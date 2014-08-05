<?php
/**
 * baserCMS初期化ページ
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$adminPrefix = Configure::read('Routing.prefixes.0');
?>

<script type="text/javascript">
$(function(){
	$("#InstallationResetForm").submit(function(){
		if(confirm('本当にbaserCMSを初期化してもよろしいですか？')){
			return true;
		}else{
			return false;
		}
	});
});
</script>

<?php if (!$complete): ?>

	<p>baserCMSを初期化します。データベースのデータも全て削除されます。</p>
	<?php if (BC_INSTALLED): ?>
		<p>データベースのバックアップをとられていない場合は必ずバックアップを保存してから実行してください。</p>
		<ul><li><?php $this->BcBaser->link('バックアップはこちらから', array('admin' => true, 'controller' => 'tools', 'action' => 'maintenance', 'backup')) ?></li></ul>
	<?php endif ?>
	<?php echo $this->BcForm->create('Installation', array('action' => 'reset')) ?>
	<?php echo $this->BcForm->input('Installation.reset', array('type' => 'hidden', 'value' => true)) ?>
	<?php echo $this->BcForm->end(array('label' => '初期化する', 'class' => 'button')) ?>

<?php else: ?>

	<div class="section">
		<p>引き続きbaserCMSのインストールを行うには、「インストールページへ」ボタンをクリックしてください。</p>
	</div>
	<div class="submit">
		<?php $this->BcBaser->link('インストールページへ', '/', array('class' => 'button btn-red')) ?>
	</div>
<?php endif ?>