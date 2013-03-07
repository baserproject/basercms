<?php
/* SVN FILE: $Id$ */
/**
 * baserCMS初期化ページ
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
$adminPrefix = Configure::read('Routing.admin');
?>

<script type="text/javascript">
$(function(){
	$("#ResetForm").submit(function(){
		if(confirm('本当にbaserCMSを初期化してもよろしいですか？')){
			return true;
		}else{
			return false;
		}
	});
});
</script>

<?php if(!$complete): ?>
<p>baserCMSを初期化します。データベースのデータも全て削除されます。</p>
	<?php if(BC_INSTALLED): ?>
<p>データベースのバックアップをとられていない場合は必ずバックアップを保存してから実行してください。</p>
<ul><li><?php $bcBaser->link('バックアップはこちらから', 	array('admin' => true, 'controller' => 'tools', 'action' => 'maintenance', 'backup')) ?></li></ul>
	<?php endif ?>
	<?php echo $bcForm->create(array('action' => 'reset')) ?>
	<?php echo $bcForm->input('Installation.reset', array('type' => 'hidden', 'value' => true)) ?>
	<?php echo $bcForm->end(array('label' => '初期化する', 'class' => 'button btn-gray')) ?>
<?php else: ?>
<div class="section">
<p>引き続きbaserCMSのインストールを行うには、「インストールページへ」ボタンをクリックしてください。</p>
</div>
<div class="submit">
	<?php $bcBaser->link('インストールページへ', '/', array('class' => 'button btn-red')) ?>
</div>
<?php endif ?>