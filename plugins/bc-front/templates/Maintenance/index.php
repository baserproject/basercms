<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * メンテナンスページ
 * 呼出箇所：メンテナンス設定時
 * @var \BaserCore\View\BcFrontAppView $this
 */
$this->BcBaser->setTitle(__d('baser_core', 'メンテナンス中'));
?>


<h2 class="bs-maintenance-title"><?php echo __d('baser_core', 'メンテナンス中') ?></h2>
<section class="bs-maintenance-body">
	<p>
		<?php echo __d('baser_core', 'ご迷惑をおかけしております。') ?><br>
		<?php echo __d('baser_core', '現在メンテナンス中です。') ?><br>
		<?php echo __d('baser_core', 'もうしばらくお待ちください。') ?></p>
</section>
