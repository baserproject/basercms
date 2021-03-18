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
?>


<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'サーバーキャッシュ削除') ?></h2>
	<p class="bca-main__text"><?php echo __d('baser', 'baserCMSは、表示速度向上の為、サーバーサイドのキャッシュ機構利用しています。<br>これによりテンプレートを直接編集した際など、変更内容が反映されない場合がありますので、その際には、サーバーサイドのキャッシュを削除します。') ?></p>
	<?php $this->BcBaser->link(__d('baser', 'サーバーキャッシュを削除する'), ['controller' => 'site_configs', 'action' => 'del_cache'], ['class' => 'submit-token button-small bca-btn', 'data-bca-btn-type' => 'clear', 'confirm' => __d('baser', 'サーバーキャッシュを削除します。いいですか？')]) ?>
	　
</div>

<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'コンテンツ管理') ?></h2>
	<p class="bca-main__text"><?php echo __d('baser', 'コンテンツ管理のツリー構造で並べ替えがうまくいかなくなった場合に、ツリー構造をリセットして正しいデータの状態に戻します。リセットを実行した場合、階層構造はリセットされてしまうのでご注意ください。') ?></p>
	<?php $this->BcBaser->link(__d('baser', 'ツリー構造をチェックする'), ['controller' => 'tools', 'action' => 'verity_contents_tree'], ['class' => 'submit-token button-small bca-btn']) ?>
	　
	<?php $this->BcBaser->link(__d('baser', 'ツリー構造リセット'), ['controller' => 'tools', 'action' => 'reset_contents_tree'], ['class' => 'submit-token button-small bca-btn', 'confirm' => __d('baser', 'コンテンツ管理のツリー構造をリセットします。本当によろしいですか？')]) ?>
	　
</div>

<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', '固定ページテンプレート') ?></h2>
	<p class="bca-main__text"><?php echo __d('baser', '別サーバーへの移設時には、固定ページ機能を正常動作させる為、固定ページテンプレート書出を実行してください。<br>また、固定ページテンプレートを直接編集した場合、データベースに反映する為には、固定ページテンプレート読込を実行します。') ?></p>
	<?php $this->BcBaser->link(__d('baser', '固定ページテンプレート書出'), ['controller' => 'pages', 'action' => 'write_page_files'], ['class' => 'submit-token button-small bca-btn', 'confirm' => __d('baser', 'データベース内のページデータを、ページテンプレートとして /app/View/Pages 内に全て書出します。本当によろしいですか？')]) ?>
	　
	<?php $this->BcBaser->link(__d('baser', '固定ページテンプレート読込'), ['controller' => 'pages', 'action' => 'entry_page_files'], ['class' => 'submit-token button-small bca-btn', 'confirm' => __d('baser', '/app/View/Pages フォルダ内のページテンプレートを全て読み込みます。本当によろしいですか？')]) ?>
	　
</div>

<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'アセットファイル') ?></h2>
	<p class="bca-main__text"><?php echo __d('baser', '管理システム用のアセットファイル（画像、CSS、Javascript）を削除したり、コアパッケージよりサイトルートフォルダに再配置します。<br>削除した場合、直接コアパッケージのアセットファイルを参照する事になりますが、表示速度が遅くなりますので注意が必要です。') ?></p>
	<?php $this->BcBaser->link(__d('baser', 'アセットファイル削除'), ['controller' => 'tools', 'action' => 'delete_admin_assets'], ['class' => 'submit-token button-small bca-btn', 'confirm' => __d('baser', 'サイトルートに配置された、管理システム用のアセットファイルを削除します。本当によろしいですか？')]) ?>
	　
	<?php $this->BcBaser->link(__d('baser', 'アセットファイル再配置'), ['controller' => 'tools', 'action' => 'deploy_admin_assets'], ['class' => 'submit-token button-small bca-btn', 'confirm' => __d('baser', '管理システム用のアセットファイルをサイトルートに再配置します。本当によろしいですか？')]) ?>
	　
</div>

<div class="section bca-main__section">
	<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'スペシャルサンクスクレジット') ?></h2>
	<p class="bca-main__text"><?php echo __d('baser', 'baserCMSの開発や運営、普及にご協力頂いた方々をご紹介します。') ?></p>
	<?php $this->BcBaser->link(__d('baser', 'クレジットを表示'), 'javascript:void(0)', ['class' => 'button-small bca-btn', 'id' => 'BtnCredit']) ?>
</div>
