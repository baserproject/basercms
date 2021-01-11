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
 * [PUBLISH] フッター
 */
?>


<div id="Footer">
	<p id="Copyright"> Copyright(C)
		<?php $this->BcBaser->copyYear(2008) ?>
		baserCMS All rights Reserved. <a href="https://basercms.net/"
										 target="_blank"><?php echo $this->BcHtml->image('baser.power.gif', ['alt' => 'baserCMS : Based Website Development Project', 'border' => "0"]); ?></a>&nbsp;
		<a href="http://cakephp.org/"
		   target="_blank"><?php echo $this->BcHtml->image('cake.power.gif', ['alt' => 'CakePHP(tm) : Rapid Development Framework', 'border' => "0"]); ?></a>
	</p>
</div>
