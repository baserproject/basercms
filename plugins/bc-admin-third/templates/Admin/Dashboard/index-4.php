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
					<?php if ($key == 'Core'): ?>
						<?php echo $this->BcBaser->element('admin/dashboard/' . $template) ?>
					<?php else: ?>
						<?php echo $this->BcBaser->element($key . '.admin/dashboard/' . $template) ?>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		<?php endforeach ?>
	<?php endif ?>
</div>
