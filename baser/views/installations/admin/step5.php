<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー Step5
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

<div class="step-5">

<div class="em-box"> おめでとうございます！baserCMSのインストールが無事完了しました！ <br />
管理用メールアドレスへインストール完了メールを送信しています。</div>
	<h2>次は何をしますか？</h2>
	<div class="panel-box corner10">
		<div class="section">
			<ul>
				<li><a href="<?php echo $this->base.'/'.$adminPrefix ?>/dashboard">管理者ダッシュボードに移動する</a></li>
				<li><a href="<?php echo str_replace('/index.php','',$this->base.'/') ?>" target="_blank" class="outside-link">トップページを確認する</a></li>
				<li><a href="http://basercms.net" title="baserCMS公式サイト" target="_blank" class="outside-link">baserCMS公式サイトで情報を探す</a></li>
				<li><a href="http://forum.basercms.net" title="baserCMSユーザーズフォーラム" target="_blank" class="outside-link">フォーラムにインストールの不具合を報告する</a></li>
				<li><a href="http://twitter.com/#!/basercms" title="baserCMS公式Twitter" target="_blank" class="outside-link">公式Twitterをフォローする</a></li>
				<li><a href="http://facebook.com/basercms" title="baserCMS公式Facebookページ" target="_blank" class="outside-link">Facebookでいいね！する</a></li>
			</ul>
		</div>
	</div>
</div>