<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] インストーラー Step5
 */
$adminPrefix = Configure::read('Routing.prefixes.0');
?>


<div class="step-5">

	<div
		class="em-box"> <?php echo __d('baser', 'おめでとうございます！baserCMSのインストールが無事完了しました！ <br />管理用メールアドレスへインストール完了メールを送信しています。') ?></div>
	<h2><?php echo __d('baser', '次は何をしますか？') ?></h2>
	<div class="panel-box corner10">
		<div class="section">
			<ul>
				<li>
					<a href="<?php echo $this->request->base . '/' . $adminPrefix ?>/dashboard/"><?php echo __d('baser', '管理者ダッシュボードに移動する') ?></a>
				</li>
				<li><a href="<?php echo str_replace('/index.php', '', $this->request->base . '/') ?>" target="_blank"
					   class="outside-link"><?php echo __d('baser', 'トップページを確認する') ?></a></li>
				<li><a href="https://basercms.net" title="baserCMS公式サイト" target="_blank"
					   class="outside-link"><?php echo __d('baser', 'baserCMS公式サイトで情報を探す') ?></a></li>
				<li><a href="https://forum.basercms.net" title="baserCMSユーザーズフォーラム" target="_blank"
					   class="outside-link"><?php echo __d('baser', 'フォーラムにインストールの不具合を報告する') ?></a></li>
				<li><a href="https://twitter.com/#!/basercms" title="baserCMS公式Twitter" target="_blank"
					   class="outside-link"><?php echo __d('baser', '公式Twitterをフォローする') ?></a></li>
				<li><a href="https://facebook.com/basercms" title="baserCMS公式Facebookページ" target="_blank"
					   class="outside-link"><?php echo __d('baser', 'Facebookでいいね！する') ?></a></li>
			</ul>
		</div>
	</div>
</div>
