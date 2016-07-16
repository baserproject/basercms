<?php
/**
 * [ADMIN] 統合コンテンツ編集
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
 ?>


<?php echo $this->BcForm->create() ?>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>
<?php echo $this->BcForm->end() ?>
