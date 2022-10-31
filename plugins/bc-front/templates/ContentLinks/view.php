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
/**
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BaserCore\Model\Entity\ContentLink $contentLink
 * @checked
 * @noTodo
 */
?>


<?php if ($contentLink->url): ?>
<?php $this->Html->scriptStart(['block' => true]); ?>
window.location.href = "<?php echo $contentLink->url ?>";
<?php $this->Html->scriptEnd(); ?>
<?php endif ?>
