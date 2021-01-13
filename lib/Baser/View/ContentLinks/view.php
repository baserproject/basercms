<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
?>


<?php $this->Html->scriptStart(['inline' => false]); ?>
<?php if (isset($data['ContentLink']['url'][0])): ?>
	window.location.href = "<?php echo $data['ContentLink']['url'] ?>";
<?php endif ?>
<?php $this->Html->scriptEnd(); ?>
