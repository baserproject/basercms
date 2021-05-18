<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] ダッシュボード
 */
$this->BcBaser->js(['admin/libs/jquery.bcDashboard', 'admin/dashboard/index'], false);
?>


<div id="AlertMessage" class="message" style="display:none"></div>

<div class="bca-panel">
	<?php if ($panels): ?>
		<?php foreach($panels as $key => $templates): ?>
			<?php foreach($templates as $template): ?>
				<div class="panel-box bca-panel-box">
					<?php if ($template): ?>
                        <?php echo $this->BcBaser->element($key . '.Admin/Dashboard/' . $template) ?>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		<?php endforeach ?>
	<?php endif ?>
</div>
