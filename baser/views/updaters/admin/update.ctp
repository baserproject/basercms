<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アップデート
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


<div class="corner10 panel-box section">
	<h2>現在のバージョン状況</h2>
	<ul class="version">
		<li><?php echo $updateTarget ?> のバージョン： <strong><?php echo $baserVer ?></strong></li>
		<li>現在のデータベースのバージョン： <strong><?php echo $siteVer ?></strong></li>
		<?php if($baserVerPoint === false || $siteVerPoint === false): ?>
		<li>α版、β版の場合はアップデートサポート外です</li>
		<?php elseif($baserVer != $siteVer || $scriptNum): ?>
			<?php if($scriptNum): ?>
			<li>アップデートプログラムが <strong><?php echo $scriptNum ?> 個</strong> あります。</li>
			<?php endif ?>
		<?php else: ?>
		<li>データベースのバージョンは最新です。</li>
		<?php endif ?>
	</ul>
<?php if(!($baserVerPoint === false || $siteVerPoint === false) && ($baserVer != $siteVer || $scriptNum)): ?>
	<p>「アップデート実行」をクリックしてデータベースのアップデートを完了させてください。</p>
		<?php echo $formEx->create(array('action' => $this->action, 'url' => array($plugin))) ?>
		<?php echo $formEx->input('Installation.update', array('type' => 'hidden', 'value' => true)) ?>
		<?php echo $formEx->end(array('label' => 'アップデート実行', 'class' => 'button btn-red')) ?>
<?php else: ?>
	<p>
		<?php if(!$plugin): ?>
			<?php $baser->link('管理画面に移動する', array('controller' => 'dashboard', 'action' => 'index'), array('class' => 'outside-link')) ?>
		<?php else: ?>
			<?php $baser->link('プラグイン一覧に移動する', array('controller' => 'plugins', 'action' => 'index'), array('class' => 'outside-link')) ?>
		<?php endif ?>
	</p>
<?php endif ?>
</div>


<?php if(!$scriptNum): ?>
<div class="corner10 panel-box">
	<div class="section">
	<h2>データベースのバックアップは行いましたか？</h2>
		<p>
			<?php if(!$plugin): ?>
			バックアップを行われていない場合は、アップデートを実行する前に、プログラムファイルを前のバージョンに戻しシステム設定よりデータベースのバックアップを行いましょう。<br />
			<?php else: ?>
			バックアップを行われていない場合は、アップデートを実行する前にデータベースのバックアップを行いましょう。<br />
			<?php endif ?>
			<small>※ アップデート処理は自己責任で行ってください。</small><br />

			<?php $baser->link('バックアップはこちらから', array('controller' => 'tools', 'action' => 'maintenance', 'backup'), array('class' => 'outside-link')) ?>

		</p>
	</div>
	<div class="section">
		<h2>リリースノートのアップデート時の注意事項は読まれましたか？</h2>
			<p>リリースバージョンによっては、追加作業が必要となる場合があるので注意が必要です。<br />公式サイトの <a href="http://basercms.net/news/archives/category/release" target="_blank" class="outside-link">リリースノート</a> を必ず確認してください。</p>
	</div>
</div>
<?php endif ?>