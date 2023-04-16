<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * メンテナンスページ
 *
 * 呼出箇所：メンテナンス設定時
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */
?>


<h2 class="bs-maintenance-title"><?php $this->BcBaser->contentsTitle() ?></h2>
<section class="bs-maintenance-body">
	<p>
		<?php echo __d('baser_core', 'ご迷惑をおかけしております。') ?><br>
		<?php echo __d('baser_core', '現在メンテナンス中です。') ?><br>
		<?php echo __d('baser_core', 'もうしばらくお待ちください。') ?></p>
</section>
