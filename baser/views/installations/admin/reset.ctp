<?php
/* SVN FILE: $Id$ */
/**
 * baserCMS初期化ページ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
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

<h2><?php $baser->contentsTitle() ?></h2>

<?php if(!$complete): ?>
<p>baserCMSを初期化します。データベースのデータも全て削除されます。</p>
	<?php if(isInstalled()): ?>
<p>データベースのバックアップをとられていない場合は必ずバックアップを保存してから実行してください。</p>
<p><?php $baser->link('≫ バックアップはこちらから', 	array('admin' => true, 'controller' => 'tools', 'action' => 'maintenance', 'backup')) ?></p>
	<?php endif ?>
	<?php echo $formEx->create(array('action' => 'reset')) ?>
	<?php echo $formEx->input('Installation.reset', array('type' => 'hidden', 'value' => true)) ?>
	<?php echo $formEx->end(array('label' => '初期化する', 'class' => 'button btn-gray')) ?></p>
<?php else: ?>
<p>引き続きbaserCMSのインストールを行うには、「インストール実行」ボタンをクリックしてください。</p>
<div class="align-center">
	<?php $baser->link('インストール実行', '/', array('class' => 'button btn-red')) ?>
</div>
<?php endif ?>