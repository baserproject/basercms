<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツ編集 
 */
 ?>


<?php echo $this->BcForm->create() ?>
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['class' => 'button', 'div' => false]) ?>
</div>
<?php echo $this->BcForm->end() ?>
