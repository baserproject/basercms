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
 * コンテンツリンク
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcContentLink\Model\Entity\ContentLink $contentLink
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php if ($contentLink->url): ?>
<?php $this->BcBaser->scriptStart(['block' => true]); ?>
window.location.href = "<?php echo $contentLink->url ?>";
<?php $this->BcBaser->scriptEnd(); ?>
<?php endif ?>
