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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomLink $customLink
 * @var \BcCustomContent\Model\Entity\CustomLink|null $parent
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->CustomContentAdmin->beforeHead($customLink) ?>
<?php echo $this->CustomContentAdmin->control($customLink) ?>
<?php echo $this->CustomContentAdmin->afterHead($customLink) ?>
<?php echo $this->CustomContentAdmin->description($customLink) ?>
<?php echo $this->CustomContentAdmin->attention($customLink) ?>
<?php echo $this->CustomContentAdmin->error($customLink, ['parent' => $parent]) ?>
